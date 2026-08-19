<?php

namespace Tests\Feature;

use App\Http\Controllers\OdinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class OdinValidatorBuilderTest extends TestCase
{
    private string $validatorsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validatorsPath = public_path('js/validators');

        if (!File::exists($this->validatorsPath)) {
            File::makeDirectory($this->validatorsPath, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        $files = [
            $this->validatorsPath . '/test-edit-unique.json',
            $this->validatorsPath . '/nuevo-id-de-prueba.json',
        ];

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        parent::tearDown();
    }

    public function test_editing_existing_validator_reuses_original_filename(): void
    {
        $originalFile = 'test-edit-unique.json';
        $originalPath = $this->validatorsPath . '/' . $originalFile;
        $newPath = $this->validatorsPath . '/nuevo-id-de-prueba.json';

        File::put($originalPath, json_encode([
            'id' => 'old_id',
            'nombre' => 'Validador original',
            'code' => 'OLD',
            'desc' => 'Contenido anterior',
            'entornos' => ['laboral'],
            'rules' => [],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $request = Request::create('/validator-builder/save', 'POST', [
            'filename' => $originalFile,
            'id' => 'Nuevo ID de prueba',
            'nombre' => 'Validador actualizado',
            'code' => 'NEW',
            'desc' => 'Contenido actualizado',
            'entornos' => ['laboral', 'educativo'],
            'rules' => [
                'campo_1' => [
                    'tipo' => 'texto_nonempty',
                    'desc' => 'Campo actualizado',
                ],
            ],
        ]);

        $response = app(OdinController::class)->save($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'success' => true,
            'message' => 'Archivo guardado correctamente',
            'filename' => $originalFile,
        ], $response->getData(true));

        $this->assertFileExists($originalPath);
        $this->assertFileDoesNotExist($newPath);

        $updated = json_decode(File::get($originalPath), true);

        $this->assertSame('Nuevo ID de prueba', $updated['id']);
        $this->assertSame('Validador actualizado', $updated['nombre']);
        $this->assertSame('NEW', $updated['code']);
        $this->assertSame('Contenido actualizado', $updated['desc']);
        $this->assertSame(['laboral', 'educativo'], $updated['entornos']);
        $this->assertSame('Campo actualizado', $updated['rules']['campo_1']['desc']);
    }
}
