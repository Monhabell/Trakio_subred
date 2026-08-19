<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateFileNumberException;
use App\Exceptions\PackageAlreadyAssignedException;
use App\Services\AnthropicChatService;
use App\Services\ChatKnowledgeBaseService;
use App\Services\ChatUserDirectoryService;
use App\Services\PackageAssignmentService;
use App\Services\PdfKnowledgeService;
use App\Services\ReceptionCorrectionService;
use App\Support\ChatConversationSession;
use App\Support\ChatIntentClassifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function __construct(
        private AnthropicChatService $ai,
        private PdfKnowledgeService $pdfKnowledge,
        private ChatKnowledgeBaseService $knowledgeBase,
        private PackageAssignmentService $packages,
        private ReceptionCorrectionService $receptions,
        private ChatUserDirectoryService $userDirectory,
        private ChatIntentClassifier $classifier,
        private ChatConversationSession $chatSession,
    ) {
    }

    /**
     * Endpoint principal del chatbot
     */
    public function getResponse(Request $request)
    {
        try {
            $userMessage = trim($request->input('message'));
            $userId = Auth::id();

            if (empty($userMessage)) {
                return response()->json(['response' => 'Por favor, envía un mensaje válido.']);
            }

            // 1. PRIMERO verificar sesiones activas (tiene máxima prioridad)
            $sessionResponse = $this->handleActiveSessions($userMessage);
            if ($sessionResponse) {
                return $sessionResponse;
            }

            // 2. LUEGO verificar si es una consulta sobre el documento
            if ($this->classifier->isDocumentQuery($userMessage)) {
                return $this->handleDocumentQuery($userMessage);
            }

            // 3. DETECCIÓN DIRECTA de intenciones específicas (SIN IA primero)
            $directIntent = $this->classifier->detectDirect($userMessage);
            if ($directIntent) {
                return $this->executeDirectIntent($directIntent, $userMessage);
            }

            // 4. Actualizar contexto de conversación
            $this->chatSession->appendUserContext($userId, $userMessage);

            // 5. FINALMENTE usar IA para intenciones más complejas
            $intent = $this->ai->classifyIntent($userMessage);
            return $this->executeIntentAction($intent, $userMessage);
        } catch (\Exception $e) {
            Log::error('Error en getResponse: ' . $e->getMessage());
            return response()->json(['response' => 'Lo siento, hubo un error procesando tu mensaje. Por favor, intenta nuevamente.']);
        }
    }

    /**
     * Ejecuta intención directa detectada
     */
    private function executeDirectIntent($intent, $userMessage)
    {
        switch ($intent) {
            case 'asignar_paquete':
                return $this->initPackageAssignment();

            case 'corregir_ficha':
                return $this->initCorrectionProcess();

            case 'consultar_ficha':
                return $this->handleFileQuery($userMessage);

            case 'consultar_ficha_numerica':
                if (preg_match('/\b(\d{6,})\b/', $userMessage, $matches)) {
                    return $this->handleFileQuery($matches[1]);
                }
                return $this->handleFileQuery($userMessage);

            case 'nuevo_conocimiento':
                return $this->initNewKnowledge($userMessage);

            default:
                return $this->handleGeneralQuery($userMessage);
        }
    }

    /**
     * Maneja sesiones activas
     */
    private function handleActiveSessions($userMessage)
    {
        $handlers = [
            'new_patterns' => 'handleNewPatterns',
            'respuesta_New_conocimiento' => 'saveNewKnowledge',
            'confirm_number' => 'handleConfirmation',
            'waiting_for_incorrect_number' => 'askForCorrectNumber',
            'yes_no' => 'confirmYesNo',
            'numnero_correct' => 'processCorrection',
            'Respuesta_pkg' => 'handlePackageAssignment',
        ];

        $activeKey = $this->chatSession->activeHandlerKey();

        if ($activeKey !== null) {
            return $this->{$handlers[$activeKey]}($userMessage);
        }

        return null;
    }

    /**
     * Ejecuta acción según intención detectada
     */
    private function executeIntentAction($intent, $userMessage)
    {
        $actions = [
            'nuevo_conocimiento' => 'initNewKnowledge',
            'corregir_ficha' => 'initCorrectionProcess',
            'asignar_paquete' => 'initPackageAssignment',
            'consultar_documento' => 'handleDocumentQuery',
            'consultar_ficha' => 'handleFileQuery',
            'consultar_usuarios' => 'handleUserQuery',
            'general_query' => 'handleGeneralQuery',
        ];

        if (isset($actions[$intent]) && method_exists($this, $actions[$intent])) {
            return $this->{$actions[$intent]}($userMessage);
        }

        return $this->handleGeneralQuery($userMessage);
    }

    /**
     * Sistema de asignación de paquetes
     */
    protected function initPackageAssignment()
    {
        $this->chatSession->startPackageAssignment();
        return response()->json([
            'response' => "📦 **Asignación de Paquetes**\n\nPor favor, ingresa el **número de paquete** o **número de ficha** que deseas asignar:\n\n• Para número de paquete: Ejemplo: 12345\n• Para número de ficha: Ejemplo: 1234567890\n\nTambién puedes decir 'cancelar' para salir.",
            'options' => ['cancelar'],
            'quick_replies' => [
                ['title' => '📋 Ver paquetes disponibles', 'payload' => 'paquetes disponibles'],
                ['title' => '❌ Cancelar', 'payload' => 'cancelar']
            ]
        ]);
    }

    protected function handlePackageAssignment($userMessage)
    {
        if ($this->classifier->normalize($userMessage) === 'cancelar') {
            $this->chatSession->clearPackageAssignment();
            return response()->json(['response' => '✅ Asignación de paquete cancelada.']);
        }

        if ($this->classifier->normalize($userMessage) === 'paquetes disponibles') {
            return $this->showAvailablePackages();
        }

        if (!is_numeric($userMessage)) {
            return response()->json([
                'response' => "❌ **Formato incorrecto**\n\nPor favor, ingresa solo números:\n\n• Número de paquete: 12345\n• Número de ficha: 1234567890\n\n¿Qué número deseas asignar?",
                'retry' => true,
                'quick_replies' => [
                    ['title' => '📋 Ver disponibles', 'payload' => 'paquetes disponibles'],
                    ['title' => '❌ Cancelar', 'payload' => 'cancelar']
                ]
            ]);
        }

        if ($userMessage[0] != '1') {
            return $this->assignPackageByNumber($userMessage);
        } else {
            return $this->assignPackageByFileNumber($userMessage);
        }
    }

    /**
     * Muestra paquetes disponibles
     */
    private function showAvailablePackages()
    {
        $availablePackages = $this->packages->availablePackages();

        if ($availablePackages->isEmpty()) {
            return response()->json([
                'response' => "📦 **Paquetes Disponibles**\n\nNo hay paquetes disponibles en este momento.\n\nPuedes intentar más tarde o contactar al administrador.",
                'quick_replies' => [
                    ['title' => '🔄 Reintentar', 'payload' => 'asignar paquete'],
                    ['title' => '❌ Cancelar', 'payload' => 'cancelar']
                ]
            ]);
        }

        $response = "📦 **Paquetes Disponibles**\n\n";
        foreach ($availablePackages as $package) {
            $reception = $package->receptions->first();
            $baseName = $reception?->bases?->name ?? 'N/A';
            $environment = $reception?->environment_file?->entorno ?? 'N/A';
            $response .= "• **Paquete {$package->num_package}** | {$baseName} | {$environment}\n";
        }

        $response .= "\nIngresa el **número de paquete** que deseas asignar:";

        return response()->json([
            'response' => $response,
            'quick_replies' => [
                ['title' => '🔄 Actualizar lista', 'payload' => 'paquetes disponibles'],
                ['title' => '❌ Cancelar', 'payload' => 'cancelar']
            ]
        ]);
    }

    /**
     * Asignación por número de paquete
     */
    private function assignPackageByNumber($packageNumber)
    {
        $package = $this->packages->findAssignableByPackageNumber($packageNumber);

        if (!$package) {
            return response()->json([
                'response' => "⚠️ El paquete {$packageNumber} no existe o no es posible asignarlo.",
                'suggestions' => ['Consultar otro paquete', 'Ver paquetes disponibles']
            ]);
        }

        return $this->processPackageAssignment($package);
    }

    /**
     * Asignación por número de ficha
     */
    private function assignPackageByFileNumber($fileNumber)
    {
        $reception = $this->packages->findActiveReceptionByFileNumber($fileNumber);

        if (!$reception) {
            $this->chatSession->clearPackageAssignment();
            return response()->json([
                'response' => "❌ No se encontró ficha activa con el número {$fileNumber}.",
                'suggestions' => ['Verificar número de ficha', 'Consultar fichas disponibles']
            ]);
        }

        if (empty($reception->packages)) {
            return response()->json([
                'response' => "⚠️ La ficha {$fileNumber} no tiene paquetes asociados o ya está asignado.",
                'suggestions' => ['Contactar al administrador']
            ]);
        }

        return $this->processPackageAssignment($reception->packages);
    }

    /**
     * Procesa la asignación final del paquete
     */
    private function processPackageAssignment($package)
    {
        $user = Auth::user();

        try {
            $this->packages->assign($user, $package);

            $this->chatSession->clearPackageAssignment();

            $packageInfo = $package->num_package ?? $package->reception->packages->num_package;

            return response()->json([
                'response' => "✅ **Paquete Asignado Exitosamente**\n\n📦 **Paquete:** {$packageInfo}\n 👤 **Asignado a:** {$user->name}\n🕒 **Fecha:** " . now()->format('d/m/Y H:i'),
                'assignment' => [
                    'package_number' => $packageInfo,
                    'assigned_to' => $user->name,
                    'timestamp' => now()->toISOString()
                ],
                'quick_replies' => [
                    ['title' => '📦 Asignar otro', 'payload' => 'asignar paquete'],
                    ['title' => '📁 Consultar ficha', 'payload' => 'consultar ficha'],
                    ['title' => '🏠 Inicio', 'payload' => 'hola']
                ]
            ]);
        } catch (PackageAlreadyAssignedException $e) {
            $this->chatSession->clearPackageAssignment();
            return response()->json([
                'response' => "⚠️ El paquete {$package->num_package} ya está asignado a ti.",
                'suggestions' => ['Consultar otro paquete', 'Verificar asignaciones']
            ]);
        } catch (\Exception $e) {
            Log::error('Error asignando paquete: ' . $e->getMessage());
            $this->chatSession->clearPackageAssignment();
            return response()->json([
                'response' => "❌ Error al asignar el paquete. Por favor, contacta al administrador.",
                'error' => true
            ]);
        }
    }

    /**
     * Sistema de corrección de fichas
     */
    protected function initCorrectionProcess()
    {
        $this->chatSession->startCorrection();
        return response()->json([
            'response' => "✏️ **Corrección de Número de Ficha**\n\nPor favor, ingresa el **número de ficha actual** que deseas corregir:\n\nEjemplo: 1234567890\n\nTambién puedes decir 'cancelar' para salir.",
            'options' => ['cancelar'],
            'quick_replies' => [
                ['title' => '❌ Cancelar', 'payload' => 'cancelar']
            ]
        ]);
    }

    protected function askForCorrectNumber($incorrectFileNumber)
    {
        if ($this->classifier->normalize($incorrectFileNumber) === 'cancelar') {
            $this->chatSession->clearCorrection();
            return response()->json(['response' => '✅ Corrección cancelada.']);
        }

        if (!is_numeric($incorrectFileNumber)) {
            return response()->json([
                'response' => "❌ Por favor, ingresa solo números para el número de ficha.",
                'retry' => true
            ]);
        }

        $searchResults = $this->receptions->searchByFileNumber($incorrectFileNumber);

        if ($searchResults->isEmpty()) {
            $this->chatSession->clearCorrection();
            return response()->json([
                'response' => "❌ No se encontraron fichas con el número: {$incorrectFileNumber}",
                'suggestions' => ['Verificar el número', 'Consultar fichas disponibles']
            ]);
        }

        $responseMessage = "📋 **Fichas Encontradas:** {$searchResults->count()}\n\n";
        $options = [];

        foreach ($searchResults as $index => $search) {
            $packageNumber = $search->packages->num_package ?? 'N/A';
            $baseName = $search->bases->name ?? 'N/A';
            $environment = $search->environment_file->entorno ?? 'N/A';

            $responseMessage .= "**" . ($index + 1) . ".** 📁 Ficha: {$search->file_number} | 📦 Paquete: {$packageNumber} | 🏷️ Base: {$baseName} | 🌐 Entorno: {$environment}\n";
            $options[] = [
                'id' => $search->id,
                'index' => $index + 1
            ];
        }

        $this->chatSession->setSearchResults($incorrectFileNumber, $options);

        $responseMessage .= "\n🔢 **Selecciona el número de la ficha a corregir:**";

        return response()->json([
            'response' => $responseMessage,
            'options' => range(1, count($options))
        ]);
    }

    protected function handleConfirmation($userMessage)
    {
        if ($this->classifier->normalize($userMessage) === 'cancelar') {
            $this->chatSession->clearCorrection();
            return response()->json(['response' => '✅ Corrección cancelada.']);
        }

        if (!is_numeric($userMessage)) {
            return response()->json([
                'response' => "❌ Por favor, selecciona un número de la lista.",
                'retry' => true
            ]);
        }

        $options = $this->chatSession->searchResultOptions();
        $userSelection = intval($userMessage);

        if ($userSelection < 1 || $userSelection > count($options)) {
            return response()->json([
                'response' => "❌ Selección inválida. Por favor, elige un número entre 1 y " . count($options),
                'retry' => true
            ]);
        }

        $selectedRecordId = $options[$userSelection - 1]['id'];
        $selectedReception = $this->receptions->findWithDetails($selectedRecordId);

        if (!$selectedReception) {
            $this->chatSession->clearCorrection();
            return response()->json(['response' => '❌ La ficha seleccionada no existe.']);
        }

        $this->chatSession->confirmSelection($selectedRecordId);

        $packageNumber = $selectedReception->packages->num_package ?? 'N/A';
        $baseName = $selectedReception->bases->name ?? 'N/A';

        return response()->json([
            'response' => "✅ **Ficha Seleccionada:**\n\n📁 **Número:** {$selectedReception->file_number}\n📦 **Paquete:** {$packageNumber}\n🏷️ **Base:** {$baseName}\n\n¿Confirmas que esta es la ficha que deseas corregir?",
            'options' => ['Sí', 'No']
        ]);
    }

    protected function confirmYesNo($message)
    {
        $normalizedMessage = $this->classifier->normalize($message);

        if ($normalizedMessage === 'si' || $normalizedMessage === 'sí') {
            $this->chatSession->acceptCorrection();
            return response()->json([
                'response' => "✏️ **Corrección de Ficha**\n\nPor favor, ingresa el NUEVO número correcto para la ficha:",
                'options' => ['cancelar']
            ]);
        } else {
            $this->chatSession->clearCorrection();
            return response()->json(['response' => '✅ Corrección cancelada.']);
        }
    }

    protected function processCorrection($correctFileNumber)
    {
        if ($this->classifier->normalize($correctFileNumber) === 'cancelar') {
            $this->chatSession->clearCorrection();
            return response()->json(['response' => '✅ Corrección cancelada.']);
        }

        if (!is_numeric($correctFileNumber)) {
            return response()->json([
                'response' => "❌ Por favor, ingresa solo números para el nuevo número de ficha.",
                'retry' => true
            ]);
        }

        $selectedRecordId = $this->chatSession->selectedRecordId();
        $ficha = $selectedRecordId ? $this->receptions->find($selectedRecordId) : null;

        if ($ficha) {
            try {
                $oldNumber = $this->receptions->renumber($ficha, $correctFileNumber);
            } catch (DuplicateFileNumberException $e) {
                return response()->json([
                    'response' => "❌ El número {$correctFileNumber} ya existe en otra ficha.",
                    'retry' => true
                ]);
            }

            $this->chatSession->clearCorrection();

            return response()->json([
                'response' => "✅ **Ficha Corregida Exitosamente**\n\n📁 **Número anterior:** {$oldNumber}\n📁 **Número nuevo:** {$correctFileNumber}\n🕒 **Fecha:** " . now()->format('d/m/Y H:i'),
                'correction' => [
                    'old_number' => $oldNumber,
                    'new_number' => $correctFileNumber,
                    'timestamp' => now()->toISOString()
                ],
                'quick_replies' => [
                    ['title' => '✏️ Otra corrección', 'payload' => 'corregir ficha'],
                    ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                    ['title' => '🏠 Inicio', 'payload' => 'hola']
                ]
            ]);
        }

        $this->chatSession->clearCorrection();
        return response()->json(['response' => '❌ Error: La ficha no pudo ser encontrada.']);
    }

    /**
     * Sistema de conocimiento
     */
    protected function initNewKnowledge($userMessage)
    {
        $user = Auth::user();

        if ($user->position_id != 6) {
            return response()->json([
                'response' => "❌ Solo los administradores pueden agregar nuevo conocimiento.",
                'suggestions' => ['Contactar al administrador']
            ]);
        }

        $this->chatSession->startNewKnowledge();
        return response()->json([
            'response' => "🧠 **Sistema de Aprendizaje Trakio**\n\nPor favor, ingresa las preguntas o patrones separados por comas.\nEjemplo: ¿cómo asignar un paquete?, asignar paquete, quiero un paquete",
            'options' => ['cancelar']
        ]);
    }

    protected function handleNewPatterns($userMessage)
    {
        if ($this->classifier->normalize($userMessage) === 'cancelar') {
            $this->chatSession->clearKnowledgeLearning();
            return response()->json(['response' => '✅ Aprendizaje cancelado.']);
        }

        $patterns = array_map('trim', explode(',', $userMessage));
        $validPatterns = array_filter($patterns, function ($pattern) {
            return strlen(trim($pattern)) > 5;
        });

        if (empty($validPatterns)) {
            return response()->json([
                'response' => "❌ Los patrones deben tener al menos 5 caracteres. Por favor, ingresa patrones válidos.",
                'retry' => true
            ]);
        }

        $this->chatSession->setPatterns($validPatterns);

        return response()->json([
            'response' => "📝 **Patrones recibidos:** " . count($validPatterns) . "\n\nAhora, por favor ingresa la respuesta que debo dar cuando detecte estos patrones:",
            'options' => ['cancelar']
        ]);
    }

    public function saveNewKnowledge($userMessage)
    {
        if ($this->classifier->normalize($userMessage) === 'cancelar') {
            $this->chatSession->clearKnowledgeLearning();
            return response()->json(['response' => '✅ Aprendizaje cancelado.']);
        }

        $patterns = $this->chatSession->patterns();

        $this->knowledgeBase->learn($patterns, trim($userMessage), Auth::id());

        $this->chatSession->clearKnowledgeLearning();

        return response()->json([
            'response' => "🎉 **¡Nuevo Conocimiento Aprendido!**\n\nHe aprendido " . count($patterns) . " patrones nuevos.\nAhora puedo responder mejor a tus preguntas.",
            'learned' => [
                'patterns' => $patterns,
                'response' => $userMessage
            ]
        ]);
    }

    /**
     * Consulta de fichas
     */
    private function handleFileQuery($userMessage)
    {
        $fileNumber = null;
        if (preg_match('/\b(\d{6,})\b/', $userMessage, $matches)) {
            $fileNumber = $matches[1];
        } else {
            return response()->json([
                'response' => "📁 **Consulta de Ficha**\n\nPor favor, ingresa el **número de ficha** que deseas consultar:\n\nEjemplo: 1234567890",
                'options' => ['cancelar'],
                'quick_replies' => [
                    ['title' => '❌ Cancelar', 'payload' => 'cancelar']
                ]
            ]);
        }

        $fileInfo = $this->getFileInformation($fileNumber);

        if ($fileInfo) {
            return response()->json([
                'response' => $fileInfo,
                'source' => 'database',
                'quick_replies' => [
                    ['title' => '✏️ Corregir esta ficha', 'payload' => 'corregir ficha ' . $fileNumber],
                    ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                    ['title' => '🔍 Nueva consulta', 'payload' => 'consultar ficha']
                ]
            ]);
        }

        return response()->json([
            'response' => "❌ **Ficha No Encontrada**\n\nNo se encontró información para la ficha: **{$fileNumber}**\n\nVerifica el número e intenta nuevamente.",
            'quick_replies' => [
                ['title' => '🔄 Reintentar', 'payload' => 'consultar ficha'],
                ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                ['title' => '❌ Cancelar', 'payload' => 'cancelar']
            ]
        ]);
    }

    /**
     * Obtiene información de fichas
     */
    private function getFileInformation($fileNumber)
    {
        $reception = $this->receptions->findDetailedByFileNumber($fileNumber);

        if (!$reception) {
            return null;
        }

        $digitizedBy = optional($reception->typedBy)->name ?? 'No digitada';
        $packageNumber = optional($reception->packages)->num_package ?? 'Sin paquete';
        $observations = optional($reception->productivity)->observations ?? 'Sin observaciones';
        $returnDate = $reception->return_date ? Carbon::parse($reception->return_date)->format('d/m/Y') : 'No devuelta';
        $status = $reception->returned_by ? '🔴 Devuelta' : '🟢 Activa';

        return "📁 **Información de Ficha #{$fileNumber}**\n\n" .
            "{$status}\n" .
            "🏷️ **Base:** " . (optional($reception->bases)->name ?? 'Sin formato') . "\n" .
            "🌐 **Entorno:** " . (optional($reception->environment_file)->entorno ?? 'Sin entorno') . "\n" .
            "📦 **Paquete:** {$packageNumber}\n" .
            "👤 **Digitado por:** {$digitizedBy}\n" .
            "📅 **Fecha recepción:** " . $reception->created_at->format('d/m/Y') . "\n" .
            "📅 **Fecha devolución:** {$returnDate}\n" .
            "📋 **Observaciones:** {$observations}";
    }

    /**
     * Sistema de consulta de PDF inteligente
     */
    private function handleDocumentQuery($userMessage)
    {
        try {
            $pdfContent = $this->analyzePDFWithAI($userMessage);

            if ($pdfContent && !empty(trim($pdfContent))) {
                return response()->json([
                    'response' => $pdfContent,
                    'source' => 'document',
                    'has_content' => true,
                    'quick_replies' => [
                        ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                        ['title' => '📁 Consultar ficha', 'payload' => 'consultar ficha'],
                        ['title' => '🔍 Nueva consulta', 'payload' => 'consultar documento']
                    ]
                ]);
            } else {
                return response()->json([
                    'response' => "📄 **Consulta Documental**\n\nNo encontré información específica sobre \"{$userMessage}\" en la documentación.\n\nPuedo ayudarte con:\n• Entregables y lineamientos\n• Procedimientos de digitación\n• Guías de usuario\n• Estándares de calidad",
                    'suggestions' => [
                        '¿Qué es GESI?',
                        'Entregables del sistema',
                        'Lineamientos técnicos',
                        'Procedimientos de digitación'
                    ],
                    'has_content' => false,
                    'quick_replies' => [
                        ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                        ['title' => '📁 Consultar ficha', 'payload' => 'consultar ficha'],
                        ['title' => '🔍 Otra consulta', 'payload' => 'consultar documento']
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en handleDocumentQuery: ' . $e->getMessage());
            return response()->json([
                'response' => "❌ **Error de Consulta**\n\nNo pudo acceder a la documentación en este momento.",
                'error' => true
            ]);
        }
    }

    /**
     * Análisis de PDF con IA
     */
    private function analyzePDFWithAI($userQuery)
    {
        $cacheKey = 'pdf_analysis_' . md5($userQuery);

        return Cache::remember($cacheKey, 3600, function () use ($userQuery) {
            $allText = $this->pdfKnowledge->extractAllText();

            if ($allText === null) {
                return null;
            }

            try {
                return $this->ai->answerFromDocuments($allText, $userQuery);
            } catch (\Exception $e) {
                Log::error('Error analizando PDFs con IA: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Consulta de usuarios
     */
    private function handleUserQuery($userMessage)
    {
        $normalizedMessage = $this->classifier->normalize($userMessage);

        if (
            strpos($normalizedMessage, 'tecnicos gesi') !== false ||
            strpos($normalizedMessage, 'técnicos gesi') !== false ||
            strpos($normalizedMessage, 'quienes son los tecnicos') !== false
        ) {
            return $this->getTechniciansInfo();
        }

        return $this->getGeneralUserInfo();
    }

    /**
     * Obtiene información específica de técnicos GESI
     */
    private function getTechniciansInfo()
    {
        $technicians = $this->userDirectory->technicians();

        if ($technicians->isEmpty()) {
            return response()->json([
                'response' => "🔧 **Técnicos GESI**\n\nNo se encontraron técnicos GESI activos en el sistema.",
                'source' => 'users_database'
            ]);
        }

        $response = "🔧 **Técnicos GESI - Equipo de Soporte**\n\n";

        foreach ($technicians as $tech) {
            $environment = $tech->entorno->entorno ?? 'Sin entorno asignado';
            $response .= "👤 **{$tech->name} {$tech->last_name}**\n";
            $response .= "📧 {$tech->email}\n";
            $response .= "🌐 {$environment}\n";
            $response .= "---\n";
        }

        $response .= "\nTotal: {$technicians->count()} técnicos activos";

        return response()->json([
            'response' => $response,
            'source' => 'users_database',
            'technicians_count' => $technicians->count(),
            'quick_replies' => [
                ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                ['title' => '📁 Consultar ficha', 'payload' => 'consultar ficha'],
                ['title' => '👥 Ver todos', 'payload' => 'consultar usuarios']
            ]
        ]);
    }

    /**
     * Obtiene información general de usuarios
     */
    private function getGeneralUserInfo()
    {
        $summary = $this->userDirectory->usersSummary();

        $environments = collect($summary['environments'])
            ->map(fn ($env) => "🌐 {$env['name']}: {$env['count']} usuarios")
            ->implode("\n");

        return response()->json([
            'response' => "👥 **Información de Usuarios Trakio**\n\n" .
                "👤 **Total usuarios activos:** {$summary['total']}\n" .
                "⌨️ **Digitadores:** {$summary['digitizers']}\n" .
                "🔧 **Técnicos GESI:** {$summary['technicians']}\n\n" .
                "**Distribución por entorno:**\n{$environments}",
            'source' => 'users_database',
            'quick_replies' => [
                ['title' => '🔧 Ver técnicos', 'payload' => 'tecnicos gesi'],
                ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                ['title' => '📁 Consultar ficha', 'payload' => 'consultar ficha']
            ]
        ]);
    }

    /**
     * Consulta general con IA
     */
    private function handleGeneralQuery($userMessage)
    {
        $response = $this->getAIResponse($userMessage);
        return response()->json([
            'response' => $response,
            'quick_replies' => [
                ['title' => '📦 Asignar paquete', 'payload' => 'asignar paquete'],
                ['title' => '📁 Consultar ficha', 'payload' => 'consultar ficha'],
                ['title' => '📄 Documentación', 'payload' => 'consultar documento'],
                ['title' => '👥 Usuarios', 'payload' => 'consultar usuarios']
            ]
        ]);
    }

    /**
     * Respuesta de IA con contexto específico
     */
    public function getAIResponse($message)
    {
        try {
            $userId = Auth::id();

            $conversationHistory = $this->chatSession->conversationHistory($userId);
            $conversationHistory[] = ['role' => 'user', 'content' => $message];

            if (count($conversationHistory) > 6) {
                $conversationHistory = array_slice($conversationHistory, -6);
            }

            // La API de Claude exige que el primer mensaje sea del usuario
            $historyForApi = $conversationHistory;
            if (!empty($historyForApi) && $historyForApi[0]['role'] === 'assistant') {
                array_shift($historyForApi);
            }

            $responseText = $this->ai->chat(
                array_map(
                    fn ($entry) => ['role' => $entry['role'], 'content' => $entry['content']],
                    $historyForApi
                ),
                $this->buildTrakioSpecificPrompt()
            );

            $conversationHistory[] = ['role' => 'assistant', 'content' => $responseText];
            $this->chatSession->setConversationHistory($userId, $conversationHistory);

            return $responseText;
        } catch (\Exception $e) {
            Log::error('Error en getAIResponse: ' . $e->getMessage());
            return "Puedo ayudarte con las siguientes acciones:\n\n" .
                "📦 **Asignar paquete** — escribe: *asignar paquete*\n" .
                "📁 **Consultar ficha** — escribe: *consultar ficha*\n" .
                "✏️ **Corregir ficha** — escribe: *corregir ficha*\n" .
                "👥 **Consultar usuarios** — escribe: *consultar usuarios*\n\n" .
                "¿En qué puedo ayudarte?";
        }
    }

    /**
     * Prompt específico para Trakio - EVITA respuestas genéricas
     */
    private function buildTrakioSpecificPrompt()
    {
        return "Eres **Tara**, asistente especializada del sistema **Trakio** (gestión documental).

**CONTEXTO EXCLUSIVO TRAKIO:**
- Sistema de digitación y gestión documental
- Funcionalidades principales: asignación de paquetes, consulta de fichas, corrección de números de ficha
- No es un sistema general de documentos, es específico para procesos de digitación

**FUNCIONALIDADES PRINCIPALES:**
1. 📦 ASIGNAR PAQUETES: Los usuarios piden paquetes para digitar
2. 📁 CONSULTAR FICHAS: Buscar información de fichas por número
3. ✏️ CORREGIR FICHAS: Modificar números de ficha incorrectos
4. 📄 DOCUMENTACIÓN: Consultar manuales y procedimientos GESI

**INSTRUCCIONES CRÍTICAS:**
- Responde de manera CONCISA y ESPECÍFICA para Trakio
- NO des explicaciones genéricas de gestión documental
- Si el usuario menciona 'asignar', 'paquete', 'ficha', 'corregir', asume que es de Trakio
- Enfócate en las 4 funcionalidades principales mencionadas
- Si no es claro, pregunta específicamente sobre Trakio
- Usa emojis relevantes pero sé directa";
    }
}
