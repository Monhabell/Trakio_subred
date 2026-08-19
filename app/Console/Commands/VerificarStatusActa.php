<?php

namespace App\Console\Commands;

//Modelos
use App\Models\Document;

// Mails
use App\Mail\EnviarInformeSemanal;

// Facades
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class VerificarStatusActa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-status-acta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica actas pendientes y envía correos los viernes a las 5:30 pm';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Buscar los documentos aprobados con el nombre 'report-weekly'
            $approvedDocuments = Document::where('status', '1')
                ->where('name', 'report-weekly')
                ->get();
        
            if ($approvedDocuments->isEmpty()) {
                $this->info('No se encontraron documentos aprobados.');
                return 0;
            }
        
            // Agrupar los documentos por entorno
            $documentsByEnvironment = $approvedDocuments->groupBy(function ($doc) {
                $pathParts = explode('/', $doc->path);
                return $pathParts[3] ?? 'unknown'; // Extrae el entorno del path
            });
        
            foreach ($documentsByEnvironment as $entorno => $documents) {
                if ($entorno === 'unknown') {
                    $this->warn('Se encontró un documento sin entorno definido.');
                    continue;
                }
        
                // Rutas de documentos para el entorno actual
                $rutas = [];
                $documentIds = [];
                $emailToSend = null;
        
                foreach ($documents as $doc) {
                    $rutas[] = storage_path("app/public/" . $doc->path);
                    $documentIds[] = $doc->id;
        
                    // Tomar el email del primer documento como referencia
                    if (is_null($emailToSend)) {
                        $emailToSend = $doc->email_to_send;
                    }
                }
        
                // Enviar el correo
                if (!empty($rutas) && $emailToSend) {
                    $this->enviarCorreo($rutas, 'Actas semanales - ' . $entorno, $emailToSend);
        
                    // Actualizar el estado de los documentos en este entorno
                    Document::whereIn('id', $documentIds)->update([
                        'status' => '2', // Cambia el estado a enviado
                        'updated_at' => now(),
                    ]);
        
                    $this->info("Correo enviado para el entorno '{$entorno}' a: {$emailToSend}");
                }
            }
        
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al manejar la tarea: ' . $e->getMessage());
            return 1;
        }        
    }


    public function enviarCorreo($filesPaths, $asunto, $email)
    {
        $destinatario = $email;

        // Inicializar las rutas de los archivos
        Mail::to($destinatario)->send(new EnviarInformeSemanal($filesPaths[0], $filesPaths[1], $asunto));
    }
}
