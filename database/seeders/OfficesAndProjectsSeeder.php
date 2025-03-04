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
            'focal_person' => 'RD, Engr. Pinky T. Jimenez'
        ]);

        Office::create([
            'name' => 'Technical Operations Division',
            'abbreviation' => 'TOD',
            'focal_person' => 'Engr. Magdalena D. Gomez'
        ]);

        Office::create([
            'name' => 'Admin and Finance Division',
            'abbreviation' => 'AFD',
            'focal_person' => 'Ms. Mina Flor T. Villafuerte'
        ]);

        Office::create([
            'name' => 'Batanes Provincial Office',
            'abbreviation' => 'BPO',
            'focal_person' => 'Engr. Ronald S. Bariuan'
        ]);

        Office::create([
            'name' => 'Isabela Provincial Office',
            'abbreviation' => 'IPO',
            'focal_person' => 'Mr. Cirilo N. Gazzingan Jr.'
        ]);

        Office::create([
            'name' => 'Quirino Provincial Office',
            'abbreviation' => 'QPO',
            'focal_person' => 'Engr. Edison Agaoid'
        ]);

        Office::create([
            'name' => 'Nueva Vizcaya Provincial Office',
            'abbreviation' => 'NVPO',
            'focal_person' => 'Ms. Johanna F. Tulauan'
        ]);

        Office::create([
            'name' => 'Cagayan Provincial Office',
            'abbreviation' => 'CPO',
            'focal_person' => 'Engr. Rogie Layugan'
        ]);


        Project::create([
            'name' => 'National Broadband Plan',
            'abbreviation' => 'NBP',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National Government Data Center',
            'abbreviation' => 'NGDC',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Digital Transformation Centers',
            'abbreviation' => 'DTC',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Free Wi-Fi for All',
            'abbreviation' => 'Free Wi-fi for All',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'ICT Literacy and Competency Development Beurau',
            'abbreviation' => 'ILCDB',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Philippine National Public Key Infrastracture',
            'abbreviation' => 'PNPKI',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National Government Portal',
            'abbreviation' => 'NGP',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Electronics Business Permits and Licensing System',
            'abbreviation' => 'eBPLS',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Philippine Business Hub - Central Business Portal',
            'abbreviation' => 'PBH-CBP',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'Medium-Term Information and Communications Technology Harmonization Initiative',
            'abbreviation' => 'MITHI',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National ICT Household Survey',
            'abbreviation' => 'NICTHS',
            'focal_person' => 'Sample Focal'
        ]);

        Project::create([
            'name' => 'National ICT Ecosystem Framework',
            'abbreviation' => 'NICTEF',
            'focal_person' => 'Sample Focal'
        ]);
    }
}
