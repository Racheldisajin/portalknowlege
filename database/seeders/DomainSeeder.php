<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        Domain::create(['name' => 'Programming', 'slug' => 'programming']);
        Domain::create(['name' => 'Design', 'slug' => 'design']);
        Domain::create(['name' => 'DevOps', 'slug' => 'devops']);
    }
}
