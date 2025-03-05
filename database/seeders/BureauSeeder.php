<?php

namespace Database\Seeders;

use App\Models\Bureau;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BureauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bureau::create([
            'name' => 'Government Digital Transformation Bureau',
            'abbreviation' => 'GDTB',
            'head' => 'June Vincent Manuel S. Gaudan'
        ]);


    }
}
