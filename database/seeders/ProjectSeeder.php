<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'name' => 'e-Government',
            'abbreviation' => 'eGov',
            'head' => 'Engr. Ronald S. Bariuan',
        ]);

        Project::create([
            'name' => 'Electronic Local Government Unit',
            'abbreviation' => 'eLGU',
            'head' => 'Engr. Ronald S. Bariuan',
        ]);
    }
}
