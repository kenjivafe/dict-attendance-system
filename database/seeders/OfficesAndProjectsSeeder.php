<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficesAndProjectsSeeder extends Seeder
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


        Project::create([
            'name' => 'National Broadband Plan',
            'abbreviation' => 'NBP',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National Government Data Center',
            'abbreviation' => 'NGDC',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Digital Transformation Centers',
            'abbreviation' => 'DTC',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Free Wi-Fi for All',
            'abbreviation' => 'Free Wi-fi for All',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'ICT Literacy and Competency Development Beurau',
            'abbreviation' => 'ILCDB',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Philippine National Public Key Infrastracture',
            'abbreviation' => 'PNPKI',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National Government Portal',
            'abbreviation' => 'NGP',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Electronics Business Permits and Licensing System',
            'abbreviation' => 'eBPLS',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Philippine Business Hub - Central Business Portal',
            'abbreviation' => 'PBH-CBP',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Medium-Term Information and Communications Technology Harmonization Initiative',
            'abbreviation' => 'MITHI',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National ICT Household Survey',
            'abbreviation' => 'NICTHS',
            'head' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National ICT Ecosystem Framework',
            'abbreviation' => 'NICTEF',
            'head' => 'Sample Focal'
        ]);
    }
}
