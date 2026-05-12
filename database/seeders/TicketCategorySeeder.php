<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rekrutmen SE',
                'slug' => 'rekrutmen-se',
                'icon' => 'user-plus',
                'pjs' => ['Respati Yekti Wibowo', 'Triana Pujilestari', 'Yasmina Salisa']
            ],
            [
                'name' => 'Lapangan SE',
                'slug' => 'lapangan-se',
                'icon' => 'map-pin',
                'pjs' => ['Eko Hardiyanto', 'Ika Rahmawati', "Nisa'ul Khusna"]
            ],
            [
                'name' => 'Aplikasi FASIH',
                'slug' => 'aplikasi-fasih',
                'icon' => 'smartphone',
                'pjs' => ['Eko Hardiyanto', 'Mohammad Ilham Nur Rohman']
            ],
        ];

        foreach ($categories as $cat) {
            $pjIds = \App\Models\User::whereIn('nama_lengkap', $cat['pjs'])->pluck('id')->toArray();
            
            \App\Models\TicketCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'pj_ids' => $pjIds,
                ]
            );
        }
    }
}
