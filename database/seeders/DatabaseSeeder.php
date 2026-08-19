<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Factories\EnvironmentFactory;
use Database\Factories\RoleFactory;
use Database\Factories\SubnetFactory;
use Database\Factories\FormatFactory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        (new RoleFactory())->createAll();
        (new SubnetFactory())->createAll();
        (new EnvironmentFactory())->createAll();
        // User::factory()->count(5)->create();
        // (new FormatFactory())->createAll();
    }
}
