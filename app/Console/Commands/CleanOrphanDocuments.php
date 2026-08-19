<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOrphanDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:clean-orphans {--force : Elimina los registros huérfanos encontrados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista (o elimina con --force) registros de la tabla documents cuyo archivo ya no existe en storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $documents = Document::all();
        $orphans = $documents->filter(function ($document) {
            return !$document->path || !Storage::disk('public')->exists($document->path);
        });

        if ($orphans->isEmpty()) {
            $this->info('No se encontraron registros huérfanos.');
            return 0;
        }

        $this->table(
            ['ID', 'Usuario', 'Tipo', 'Mes', 'Año', 'Path'],
            $orphans->map(fn ($d) => [$d->id, $d->id_user, $d->name, $d->document_mes, $d->file_year, $d->path])
        );

        $this->warn($orphans->count() . ' registro(s) huérfano(s) encontrado(s).');

        if ($this->option('force')) {
            Document::whereIn('id', $orphans->pluck('id'))->delete();
            $this->info('Registros eliminados.');
        } else {
            $this->line('Ejecuta con --force para eliminarlos.');
        }

        return 0;
    }
}
