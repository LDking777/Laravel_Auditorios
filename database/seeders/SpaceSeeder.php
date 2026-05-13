<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Space;

class SpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Space::create([
            'name' => 'Auditorio Magistral Magna',
            'slug' => 'auditorio-magistral-magna',
            'type' => 'Magno',
            'capacity' => 250,
            'description' => 'El auditorio más grande de la institución, equipado con sonido envolvente Dolby Atmos, proyector láser 4K y aire acondicionado de última generación. Ideal para ceremonias de graduación y simposios internacionales.',
            'price_per_hour' => 120000.00, // Precio de ejemplo en tu moneda local
            'is_active' => true,
        ]);

        Space::create([
            'name' => 'Sala de Conferencias Acústica',
            'slug' => 'sala-conferencias-acustica',
            'type' => 'Acústico',
            'capacity' => 80,
            'description' => 'Espacio diseñado con paneles de absorción acústica de madera, ideal para ponencias magistrales, debates o grabaciones de audio en vivo. Cuenta con micrófonos inalámbricos y retorno en escenario.',
            'price_per_hour' => 75000.00,
            'is_active' => true,
        ]);

        Space::create([
            'name' => 'Auditorio de Posgrados',
            'slug' => 'auditorio-posgrados',
            'type' => 'Tecnológico',
            'capacity' => 45,
            'description' => 'Un ambiente ejecutivo moderno con conectividad de alta velocidad, pantallas interactivas individuales y mesas con conexiones eléctricas integradas. Optimizado para clases de maestría y capacitaciones corporativas.',
            'price_per_hour' => 50000.00,
            'is_active' => true,
        ]);
    }
}