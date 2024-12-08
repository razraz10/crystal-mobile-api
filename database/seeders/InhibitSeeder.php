<?php

namespace Database\Seeders;

use App\Models\Inhibit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InhibitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Inhibit::factory()->count(50)->create();
    }
}
