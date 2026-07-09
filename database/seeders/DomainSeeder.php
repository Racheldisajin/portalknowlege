<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        Domain::create(['name' => 'Programming']);
        Domain::create(['name' => 'Design']);
        Domain::create(['name' => 'DevOps']);
    }
}
