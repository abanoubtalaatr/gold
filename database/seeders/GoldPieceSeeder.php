<?php

namespace Database\Seeders;

use App\Models\GoldPiece;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GoldPieceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GoldPiece::factory()->count(10)->create();
    }
}
