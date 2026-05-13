<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Aquí llamamos a nuestro SpaceSeeder para que meta los auditorios a la base de datos
        $this->call([
            SpaceSeeder::class,
        ]);
    }
}
