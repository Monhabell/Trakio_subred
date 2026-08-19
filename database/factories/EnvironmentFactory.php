<?php

namespace Database\Factories;

use App\Models\Entorno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entorno>
*/ 
class EnvironmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {        
        $environments = [
            'Gesi',
            'Hogar',
            'Laboral',
            'Educativo',
            'Comunitario',
            'Institucional',
        ];
        
        return [
            'entorno' => $this->faker->randomElement($environments),
        ];
    }

    public function createAll()
    {
        $environments = [
            ['id' => 1, 'entorno' => 'Hogar'],
            ['id' => 2, 'entorno' => 'Laboral'],
            ['id' => 3, 'entorno' => 'Educativo'],
            ['id' => 4, 'entorno' => 'Comunitario'],
            ['id' => 5, 'entorno' => 'Gesi'],
            ['id' => 6, 'entorno' => 'Institucional']
        ];

        foreach ($environments as $environment) {
            Entorno::create([
                'id' => $environment['id'],
                'entorno' => $environment['entorno']
            ]);
        }
    }
}
