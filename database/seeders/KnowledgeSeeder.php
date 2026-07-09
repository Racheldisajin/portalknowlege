<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Knowledge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $programming = Domain::where('slug', 'programming')->first();
        $design = Domain::where('slug', 'design')->first();
        $devops = Domain::where('slug', 'devops')->first();

        $knowledge = Knowledge::create([
            'title' => 'Pengenalan Laravel',
            'text' => 'Laravel adalah framework PHP populer untuk pengembangan web.',
        ]);
        $knowledge->domains()->attach([$programming->id]);

        $knowledge = Knowledge::create([
            'title' => 'CSS Grid Layout',
            'text' => 'CSS Grid adalah sistem layout dua dimensi untuk halaman web.',
        ]);
        $knowledge->domains()->attach([$design->id, $programming->id]);

        $knowledge = Knowledge::create([
            'title' => 'Docker Dasar',
            'text' => 'Docker adalah platform containerization untuk aplikasi.',
        ]);
        $knowledge->domains()->attach([$devops->id, $programming->id]);
    }
}
