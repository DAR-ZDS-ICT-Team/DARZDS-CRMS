<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'division_name' => 'SUPPORT TO OPERATIONS DIVISION (STOD)',
                'slug' => 'support-to-operations-division',
            ],
            [
                'division_name' => 'PROGRAM BENEFICIARIES DEVELOPMENT DIVISION (PBDD)',
                'slug' => 'program-beneficiaries-development-division',
            ],
            [
                'division_name' => 'LEGAL DIVISION',
                'slug' => 'legal-division',
            ],
            [
                'division_name' => 'DEPARTMENT OF AGRARIAN REFORM ADJUDICATION BOARD (DARAB)',
                'slug' => 'department-of-agrarian-reform-adjudication-board',
            ],
            [
                'division_name' => 'DEPARTMENT OF AGRARIAN REFORM MUNICIPAL OFFICES (DARMO)',
                'slug' => 'department-of-agrarian-reformmunicipal-offices',
            ],
            [
                'division_name' => 'LAND TENURE IMPROVEMENT DIVISION (LTID)',
                'slug' => 'land-tenure-improvement-division',
            ],
            [
                'division_name' => 'PROVINCIAL AGRARIAN REFORM PROGRAM OFFICER (PARPO’S office)',
                'slug' => 'provincial-agrarian-reform-program-officer',
            ],

        ];

        Division::insert($divisions);
    }
}
