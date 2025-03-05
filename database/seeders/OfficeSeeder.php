<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::create([
            'name' => 'Region II - Regional Office',
            'abbreviation' => 'DICT Region II',
            'head' => 'RD, Engr. Pinky T. Jimenez'
        ]);

        Office::create([
            'name' => 'Technical Operations Division',
            'abbreviation' => 'TOD',
            'head' => 'Engr. Magdalena D. Gomez'
        ]);

        Office::create([
            'name' => 'Admin and Finance Division',
            'abbreviation' => 'AFD',
            'head' => 'Ms. Mina Flor T. Villafuerte'
        ]);

        Office::create([
            'name' => 'Batanes Provincial Office',
            'abbreviation' => 'BPO',
            'head' => 'Engr. Ronald S. Bariuan'
        ]);

        Office::create([
            'name' => 'Isabela Provincial Office',
            'abbreviation' => 'IPO',
            'head' => 'Mr. Cirilo N. Gazzingan Jr.'
        ]);

        Office::create([
            'name' => 'Quirino Provincial Office',
            'abbreviation' => 'QPO',
            'head' => 'Engr. Edison Agaoid'
        ]);

        Office::create([
            'name' => 'Nueva Vizcaya Provincial Office',
            'abbreviation' => 'NVPO',
            'head' => 'Ms. Johanna F. Tulauan'
        ]);

        Office::create([
            'name' => 'Cagayan Provincial Office',
            'abbreviation' => 'CPO',
            'head' => 'Engr. Rogie Layugan'
        ]);
    }
}
