<?php

namespace Database\Factories;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Process\FakeProcessResult;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            'Digitador(a)',
            'Auxiliar de enfermería',
            'Enfermero(a)',
            'Especialista en salud ocupacional',
            'Nutricionista dietista',
            'Psicólogo(a)',
            'Técnico administrativo',
            'Tecnólogo en salud ocupacional',
            'Trabajador(a) social',
            'Ingeniero',
            'Profesional de apoyo',
            'Técnico de sistemas',
            'Referente'
        ];

        return [
            'name' => $this->faker->randomElement($roles)
        ];
    }

    public function createAll()
    {
        $roles = [
            'Digitador(a)',
            'Auxiliar de enfermería',
            'Enfermero(a)',
            'Especialista en salud ocupacional',
            'Nutricionista dietista',
            'Psicólogo(a)',
            'Técnico administrativo',
            'Tecnólogo en salud ocupacional',
            'Trabajador(a) social',
            'Ingeniero',
            'Profesional de apoyo',
            'Técnico de sistemas',
            'Referente'
        ];

        foreach ($roles as $role){
            Role::create([
                'name' => $role
            ]);
        }
    }
}