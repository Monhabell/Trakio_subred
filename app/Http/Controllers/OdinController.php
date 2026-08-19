<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\License;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\MpPayment;
// Mercado Pago SDK classes (require "mercadopago/dx-php")
// use MercadoPago\SDK;
// use MercadoPago\Preference;
// use MercadoPago\Item;

class OdinController extends Controller
{

private $path;

    public function __construct()
    {
        $this->path = public_path('js/validators');
    }

public function guestHome()
{
        $user = Auth::user();

        // Obtener la licencia del usuario para el programa ODIN
        $dataLicence = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            ->first();

        // Si no hay licencia
        if (!$dataLicence) {
            return view('guest.index', [
                'licenceData' => null,
                'daysLeft' => null,
                'progress' => 0,
                'message' => 'No se encontraron datos para este usuario.'
            ]);
        }

         // --- FECHAS ---
        $startDate = Carbon::parse($dataLicence->activated_at ?? $dataLicence->created_at); // inicio real de licencia
        $expiresAt = Carbon::parse($dataLicence->expires_at);

        // Total de días de la licencia
        $totalDays = $startDate->diffInDays($expiresAt);

        // Días restantes desde hoy
        $daysLeft = Carbon::today()->diffInDays($expiresAt, false);
        $daysLeft = max(0, $daysLeft); // nunca negativo

        // Días usados
        $daysUsed = $totalDays - $daysLeft;
        $daysUsed = max(0, $daysUsed); // nunca negativo

        // Porcentaje
        $progress = $totalDays > 0 ? round(($daysUsed / $totalDays) * 100) : 0;
        $progress = min(100, $progress);
        
        return view('guest.index', [
            'licenceData' => $dataLicence,
            'daysLeft' => $daysLeft,
            'progress' => $progress,
            'message' => null
        ]);
    
    }


    public function resetLicense(Request $request)
{
    $user = Auth::user();

    $license = License::where('user_id', $user->id)->first();

    $installerPath = storage_path('app/public/Validador_instalador/OdinIstaller.exe');

    // Verificar si existe el instalador
    if (!file_exists($installerPath)) {
        return back()->with(
            'error',
            'No se encontró el instalador de Odin.'
        );
    }

    // Crear licencia FREE si no existe
    if (!$license) {

        // Generar licencia tipo:
        // PW-2026-X982-BOG-001

        $year = date('Y');

        $random = strtoupper(substr(
            str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
            0,
            4
        ));

        $city = 'BOG';

        $sequence = str_pad($user->id, 3, '0', STR_PAD_LEFT);

        $licenseKey = "OD-{$year}-{$random}-{$city}-{$sequence}";

        $license = License::create([
            'user_id'          => $user->id,
            'program_name'     => 'ODIN',
            'plan_name'        => 'free',
            'tokens_available' => 100,
            'license_key'     => $licenseKey,
            'client_name'    => $user->name .  ' ' . $user->subnet->name,
            'type'           => 'trial',
            'expires_at'     => now()->addDays(15),
            'is_active'        => 1,
            'has_hc'          => 1,
            'has_gesiform'    => 0,
            'cronicos'       => 0,
            'has_validator_bd' => 0,
            'has_comprobador' => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        if (!$license) {
            return back()->with(
                'error',
                'Error al crear la licencia.'
            );
        }
    }

    // Descargar instalador
    return response()->download(
        $installerPath,
        'OdinInstaller.exe'
    );
}

// funcion para mostrar la vista del validador de odin, con la info de la licencia
public function validatorOdin()
{
    $user = Auth::user();

    $license = License::where('user_id', $user->id)
        ->where('program_name', 'ODIN')
        // expired_ad no debe ser menor a hoy
        ->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', Carbon::now());
        })
        ->where('has_validator_bd', 1)
        ->first();

    // Validar si no existe licencia
    if (!$license) {
        return view('validator', [
            'license' => null,
            'tokens' => 0,
            'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
        ]);
    }

    // Validar tokens
    if ($license->tokens_available <= 0) {
        return view('validator', [
            'license' => $license,
            'tokens' => 0,
            'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
        ]);
    }

    return view('validator', [
        'license' => $license,
        'tokens' => $license->tokens_available,
        'message' => null
    ]);
}


public function updateTokens(Request $request)
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            ->first();

        if (!$license) {
            return response()->json(['error' => 'Licencia no encontrada.'], 404);
        }

        $nuevoSaldo = max(0, $license->tokens_available - $request->tokens_consumed);
        $license->tokens_available = $nuevoSaldo;
        $license->save();

        return response()->json([
            'success' => true,
            'tokens_left' => $license->tokens_available,
            'message' => 'Tokens actualizados correctamente en el servidor.'
        ]);
    }

public function cronicosOdin(){

    $user = Auth::user();
    $license = License::where('user_id', $user->id)
        ->where('program_name', 'ODIN')
        // expired_at no debe ser menor a hoy
        ->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', Carbon::now());
        })
        ->where('cronicos', 1) // Verificar que el usuario tenga acceso a cronicos
        ->first();

    if (!$license) {
        return view('validator', [
            'license' => null,
            'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
        ]);
    }
    
    if ($license->tokens_available <= 0) {
        return view('validator', [
            'license' => $license,
            'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
        ]);
    }

    return view('cronicosV2', [
        'tokens' => $license->tokens_available,
        'message' => null
    ]);

}




public function getRules()
{
    $validatorsPath = public_path('js/validators');

    if (!File::exists($validatorsPath)) {

        return response()->json([
            'error' => 'Carpeta validators no encontrada'
        ], 404);
    }

    $response = [
        '_listas' => [],
        'formats' => [],
        'timestamp' => now()->getTimestamp()
    ];

    // Cargar listas.json si existe
    $listasPath = $validatorsPath . '/listas.json';
    if (File::exists($listasPath)) {
        $listasContent = json_decode(File::get($listasPath), true);
        if (isset($listasContent['_listas'])) {
            $response['_listas'] = $listasContent['_listas'];
        }
    }

    // Usar allFiles() para obtener archivos en subdirectorios también
    $files = File::allFiles($validatorsPath);

    foreach ($files as $file) {

        // Solo procesar archivos JSON
        if ($file->getExtension() !== 'json') {
            continue;
        }

        // Saltar listas.json ya que ya fue procesado
        if ($file->getFilename() === 'listas.json') {
            continue;
        }

        $content = json_decode(
            File::get($file),
            true
        );

        if (json_last_error() !== JSON_ERROR_NONE) {

            logger()->error(
                'JSON inválido: ' . $file->getFilename()
            );

            continue;
        }

        $response['formats'][] = $content;
    }

    return response()->json($response)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0')
        ->header('ETag', md5(json_encode($response)))
        ->header('X-Content-Type-Options', 'nosniff');
}


public function index()
{
    $files = [];
    $skip = ['listas.json', 'rules.json'];
    if (File::exists($this->path)) {
        foreach (File::files($this->path) as $file) {
            if (in_array($file->getFilename(), $skip)) continue;
            if ($file->getExtension() !== 'json') continue;
            $files[] = [
                'name' => $file->getFilename(),
                'id'   => strtolower($file->getFilenameWithoutExtension())
            ];
        }
    }
    return view('validator-builder.index', compact('files'));
}

public function load($file)
{
    $path = $this->path . '/' . strtolower($file) . '.json';
    if (!File::exists($path)) {
        return response()->json(['error' => 'Archivo no encontrado: ' . $file], 404);
    }
    $content = json_decode(File::get($path), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json(['error' => 'JSON inválido: ' . json_last_error_msg()], 422);
    }
    return response()->json($content);
}

   public function save(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|string',
            'nombre' => 'required|string',
            'rules' => 'array'
        ]);

        $requestedFile = $request->input('filename');

        if (!empty($requestedFile)) {
            // Forzar minúsculas para evitar duplicados por case-sensitivity
            $fileName = strtolower($requestedFile);
            if (!str_ends_with($fileName, '.json')) {
                $fileName .= '.json';
            }
        } else {
            $fileName = Str::slug($request->id) . '.json';
        }

        $targetPath = $this->path . '/' . $fileName;

        if (!File::exists($this->path)) {
            File::makeDirectory($this->path, 0755, true);
        }

        $data = [
            'id'       => $request->id,
            'nombre'   => $request->nombre,
            'code'     => $request->code ?? '',
            'desc'     => $request->desc ?? '',
            'entornos' => $request->entornos ?? [],
            'rules'    => $request->rules ?? []
        ];

        File::put(
            $targetPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return response()->json([
            'success'  => true,
            'message'  => 'Archivo guardado correctamente',
            'filename' => $fileName
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validación: ' . json_encode($e->errors())
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error guardando validador: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    public function delete($file)
    {
        $path = $this->path . '/' . $file . '.json';

        if (File::exists($path)) {
            File::delete($path);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function saveListas(Request $request)
    {
        try {
            $data = $request->all();
            if (!isset($data['_listas'])) {
                return response()->json(['success' => false, 'message' => 'Estructura inválida, falta _listas'], 422);
            }

            if (!File::exists($this->path)) {
                File::makeDirectory($this->path, 0755, true);
            }

            $target = $this->path . '/listas.json';
            File::put($target, json_encode(['_listas' => $data['_listas']], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return response()->json(['success' => true, 'message' => 'Listas guardadas correctamente']);
        } catch (\Exception $e) {
            Log::error('Error guardando listas: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    // ----- Mercado Pago methods -----
    public function mpCheckout()
    {
        // Mostrar opciones de paquetes para compra
        return view('mercadopago.checkout', ['public_key' => env('MP_PUBLIC_KEY')]);
    }

    public function createMpPreference(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'No autenticado'], 401);

        // Paquetes predefinidos (precio en la moneda local, p. ej. COP)
        $packages = [
            'free' => ['tokens' => 100, 'price' => 0],
            'starter' => ['tokens' => 1000, 'price' => 9900],
            'pro'   => ['tokens' => 5000, 'price' => 19900],
            'business'    => ['tokens' => 20000, 'price' => 39900]
        ];

        $pkgKey = $request->input('package');
        if (!isset($packages[$pkgKey])) return response()->json(['success' => false, 'message' => 'Paquete inválido'], 422);
        $pkg = $packages[$pkgKey];

        // Try to use Mercado Pago SDK if installed
        try {
            if (!class_exists('\\MercadoPago\\Preference')) {
                // Fallback: create a basic preference via direct API call
                $accessToken = env('MP_ACCESS_TOKEN');
                if (!$accessToken) throw new \Exception('MP_ACCESS_TOKEN no configurado');

                $routeEnv = env('APP_URL'); // ruta base para logs, puede ser útil para identificar el entorno (local, staging, prod)
                
                $body = [
                    'items' => [[
                        'title' => 'Compra de tokens - ' . $pkg['tokens'],
                        'quantity' => 1,
                        'currency_id' => env('MP_CURRENCY','COP'),
                        'unit_price' => (float)$pkg['price']
                    ]],
                    'external_reference' => 'user_'.$user->id.'|'.$pkgKey,
                    'back_urls' => [
                        'success' => $routeEnv . '/validator-builder/mp/success',
                        'failure' => $routeEnv . '/validator-builder/mp/cancel',
                        'pending' => $routeEnv . '/validator-builder'
                    ],

                    'notification_url' => $routeEnv . '/validator-builder/mp/webhook',
                    'auto_return' => 'approved'
                ];

                $ch = curl_init('https://api.mercadopago.com/checkout/preferences');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                $resp = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($code >= 200 && $code < 300) {
                    $data = json_decode($resp, true);
                    return response()->json(['success' => true, 'init_point' => $data['init_point'], 'preference' => $data]);
                }

                Log::error('MP create preference failed: ' . $resp);
                return response()->json(['success' => false, 'message' => 'Error creando preferencia MP'], 500);
            }

            // If SDK available, use it (this branch will run when mercadopago/dx-php is installed)
            \MercadoPago\SDK::setAccessToken(env('MP_ACCESS_TOKEN'));
            $preference = new \MercadoPago\Preference();
            $item = new \MercadoPago\Item();
            $item->title = 'Compra de tokens - ' . $pkg['tokens'];
            $item->quantity = 1;
            $item->currency_id = env('MP_CURRENCY','COP');
            $item->unit_price = (float)$pkg['price'];
            $preference->items = array($item);
            $preference->external_reference = 'user_'.$user->id.'|'.$pkgKey;
            $preference->back_urls = [
                'success' => $routeEnv . '/validator-builder/mp/success',
                'failure' => $routeEnv . '/validator-builder/mp/cancel',
                'pending' => $routeEnv . '/validator-builder'
            ];
            $preference->auto_return = 'approved';
            $preference->save();

            return response()->json(['success' => true, 'init_point' => $preference->init_point, 'preference' => $preference]);

        } catch (\Exception $e) {
            Log::error('Error creando preferencia MP: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

   public function mpWebhook(Request $request)
{
    // Log payload para depuración si es necesario
    if ($request->input('topic') === 'merchant_order') {
        return response()->json(['success' => true]);
    }

    try {
        // Extraer ID de pago
        $paymentId = null;
        if ($request->has('data.id')) {
            $paymentId = $request->input('data.id');
        } elseif ($request->input('topic') === 'payment') {
            $paymentId = $request->input('resource');
        }

        if (!$paymentId) {
            return response()->json(['success' => true]);
        }

        $accessToken = env('MP_ACCESS_TOKEN');
        if (!$accessToken) {
            Log::error('MP Webhook: MP_ACCESS_TOKEN no configurado');
            return response()->json(['success' => false], 500);
        }

        // Consultar API de Mercado Pago
        $ch = curl_init('https://api.mercadopago.com/v1/payments/' . urlencode($paymentId) . '?access_token=' . $accessToken);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code < 200 || $code >= 300) {
            Log::error('MP Webhook: fallo al obtener el pago: ' . $resp);
            return response()->json(['success' => false], 500);
        }

        $payment = json_decode($resp, true);
        if (!isset($payment['status'])) {
            Log::error('MP Webhook: estructura de pago inesperada: ' . $resp);
            return response()->json(['success' => false], 500);
        }

        if (strtolower($payment['status']) !== 'approved') {
            return response()->json(['success' => true]);
        }

        // Buscar external_reference
        $externalRef = $payment['external_reference'] ?? null;
        if (!$externalRef && isset($payment['order']['id'])) {
            $prefId = $payment['preference_id'] ?? null;
            if ($prefId) {
                $ch2 = curl_init('https://api.mercadopago.com/checkout/preferences/' . urlencode($prefId) . '?access_token=' . $accessToken);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                $resp2 = curl_exec($ch2);
                $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                curl_close($ch2);
                if ($code2 >= 200 && $code2 < 300) {
                    $pref = json_decode($resp2, true);
                    $externalRef = $pref['external_reference'] ?? $externalRef;
                }
            }
        }

        if (!$externalRef) {
            Log::error('MP Webhook: external_reference no encontrado para el pago ' . $paymentId);
            return response()->json(['success' => true]);
        }

        // Formato external_reference: user_{id}|{pkgKey}
        $parts = explode('|', $externalRef);
        $userPart = $parts[0] ?? '';
        $pkgKey = $parts[1] ?? null;
        
        if (!$pkgKey || !preg_match('/^user_(\d+)$/', $userPart, $m)) {
            Log::error('MP Webhook: formato de external_reference inválido: ' . $externalRef);
            return response()->json(['success' => true]);
        }

        $userId = intval($m[1]);

        // Evitar duplicados de procesamiento de pagos
        $existingPayment = MpPayment::where('payment_id', $paymentId)->first();
        if ($existingPayment) {
            return response()->json(['success' => true]);
        }

        // Configuración de paquetes dinámicos
        $packages = [
            'free'     => ['tokens' => 100,  'price' => 0],
            'starter'  => ['tokens' => 1000, 'price' => 9900,  'has_comprobador' => 1, 'has_validator_bd' => 1],
            'pro'      => ['tokens' => 5000, 'price' => 19900, 'has_comprobador' => 1, 'has_validator_bd' => 1, 'cronicos' => 1],
            'business' => ['tokens' => 20000,'price' => 39900, 'has_comprobador' => 1, 'has_validator_bd' => 1, 'cronicos' => 1, 'has_gesiform' => 1, 'has_hc' => 1]
        ];

        if (!isset($packages[$pkgKey])) {
            Log::warning('MP Webhook: paquete desconocido ' . $pkgKey);
            return response()->json(['success' => true]);
        }

        $pkg = $packages[$pkgKey];

        // Buscar o crear la licencia usando la función nativa de Laravel
        $license = License::firstOrCreate(
            ['user_id' => $userId, 'program_name' => 'ODIN'],
            [
                'plan_name' => 'payg',
                'tokens_available' => 0,
                'is_active' => 1,
                'expires_at' => null
            ]
        );

        // --- ASIGNACIÓN DINÁMICA DE DATOS DESDE STARTER HASTA BUSINESS ---
        $license->tokens_available = max(0, ($license->tokens_available ?? 0) + $pkg['tokens']);
        
        if ($pkgKey !== 'free') {
            $license->plan_name        = $pkgKey;
            $license->is_active        = 1;
            
            // Asignar características mapeando directamente del array del paquete o por defecto 0
            $license->has_comprobador  = $pkg['has_comprobador'] ?? 0;
            $license->has_validator_bd = $pkg['has_validator_bd'] ?? 0;
            $license->cronicos         = $pkg['cronicos'] ?? 0;
            $license->has_gesiform     = $pkg['has_gesiform'] ?? 0;
            $license->has_hc           = $pkg['has_hc'] ?? 0;
            

            // Extender la expiración por 30 días de manera limpia
            $baseDate = $license->expires_at ? Carbon::parse($license->expires_at) : Carbon::now();
            $license->expires_at = $baseDate->addDays(30);
        }

        $license->save();

        // Registrar el pago de Mercado Pago
        MpPayment::create([
            'payment_id' => $paymentId,
            'user_id' => $userId,
            'amount' => $payment['transaction_amount'] ?? 0,
            'status' => $payment['status'] ?? null,
        ]);

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::warning('MP Webhook error: ' . $e->getMessage());
        // Nota: Si usas código HTTP 404 Mercado Pago reintentará enviar el webhook.
        // Si no quieres reintentos, cambia el código a 200.
        return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
    }
}

    public function mpSuccess()
    {
        return view('mercadopago.success');
    }

    public function mpCancel()
    {
        return view('mercadopago.cancel');
    }

    public function cronicosLab()
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            // expired_at no debe ser menor a hoy
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->where('cronicos', 1) // Verificar que el usuario tenga acceso a cronicos
            ->first();

        if (!$license) {
            return view('validator', [
                'license' => null,
                'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
            ]);
        }
        
        if ($license->tokens_available <= 0) {
            return view('validator', [
                'license' => $license,
                'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
            ]);
        }

        return view('cronicos_lab', [
            'tokens' => $license->tokens_available,
            'message' => null
        ]);
    }

    public function cronicosV2()
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            // expired_at no debe ser menor a hoy
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->where('cronicos', 1) // Verificar que el usuario tenga acceso a cronicos
            ->first();

        if (!$license) {
            return view('validator', [
                'license' => null,
                'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
            ]);
        }
        
        if ($license->tokens_available <= 0) {
            return view('validator', [
                'license' => $license,
                'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
            ]);
        }

        return view('cronicosV2', [
            'tokens' => $license->tokens_available,
            'message' => null
        ]);
    }

    public function prepararArchivo()
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            // expired_at no debe ser menor a hoy
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();

        if (!$license) {
            return view('validator', [
                'license' => null,
                'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
            ]);
        }
        
        if ($license->tokens_available <= 0) {
            return view('validator', [
                'license' => $license,
                'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
            ]);
        }

        return view('preparararchivo', [
            'tokens' => $license->tokens_available,
            'message' => null
        ]);
    }

    public function reportesGesi()
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            // expired_at no debe ser menor a hoy
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();

        if (!$license) {
            return view('validator', [
                'license' => null,
                'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
            ]);
        }
        
        if ($license->tokens_available <= 0) {
            return view('validator', [
                'license' => $license,
                'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
            ]);
        }

        return view('reportesGesi', [
            'tokens' => $license->tokens_available,
            'message' => null
        ]);
    }

    public function dataLaboral()
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            // expired_at no debe ser menor a hoy
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();

        if (!$license) {
            return view('validator', [
                'license' => null,
                'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
            ]);
        }
        
        if ($license->tokens_available <= 0) {
            return view('validator', [
                'license' => $license,
                'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
            ]);
        }

        return view('dataLaboralReport', [
            'tokens' => $license->tokens_available,
            'message' => null
        ]);
    }

    public function tamizajeLaboral()
    {
        $user = Auth::user();
        $license = License::where('user_id', $user->id)
            ->where('program_name', 'ODIN')
            // expired_at no debe ser menor a hoy
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->first();

        if (!$license) {
            return view('validator', [
                'license' => null,
                'message' => 'Tu licencia ODIN no está activa. Por favor, contacta al soporte'
            ]);
        }

        if ($license->tokens_available <= 0) {
            return view('validator', [
                'license' => $license,
                'message' => 'No tienes tokens disponibles. Por favor, adquiere más tokens para usar el validador.'
            ]);
        }

        return view('tamizajeLaboral', [
            'tokens' => $license->tokens_available,
            'message' => null
        ]);
    }

}



