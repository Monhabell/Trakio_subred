<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Format;

class FormatFactory extends Factory
{
    protected $model = Format::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'header_time' => $this->faker->numberBetween(5, 10),
            'body_time' => $this->faker->numberBetween(2, 10),
            'environment_id' => \App\Models\Entorno::all()->random()->id
        ];
    }

    public function createAll()
    {
        $bases = [
            ['name' => 'CSA', 'header_time' => 7, 'body_time' => 2],
            ['name' => 'Sesiones colectivas', 'header_time' => 5, 'body_time' => 6],
            ['name' => 'NNA', 'header_time' => 6, 'body_time' => 6],
            ['name' => 'Escala abreviada', 'header_time' => 5, 'body_time' => 9],
            ['name' => 'Prevención del embarazo', 'header_time' => 8, 'body_time' => 8],
        ];

        foreach ($bases as $base) {
            Format::create([
                'name' => $base['name'],
                'header_time' => $base['header_time'],
                'body_time' => $base['body_time'],
                'environment_id' => \App\Models\Entorno::all()->random()->id
            ]);
        }
    }
}
