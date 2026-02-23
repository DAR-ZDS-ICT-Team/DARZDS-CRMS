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
                'office_id' => 1,
                'division_name' => 'SUPPORT TO OPERATIONS DIVISION (STOD)',
                'slug' => 'support-to-operations-division',
            ],
            [
                'office_id' => 1,
                'division_name' => 'PROGRAM BENEFICIARIES DEVELOPMENT DIVISION (PBDD)',
                'slug' => 'program-beneficiaries-development-division',
            ],
            [
                'office_id' => 1,
                'division_name' => 'LEGAL DIVISION',
                'slug' => 'legal-division',
            ],
            [
                'office_id' => 1,
                'division_name' => 'DEPARTMENT OF AGRARIAN REFORM ADJUDICATION BOARD (DARAB)',
                'slug' => 'department-of-agrarian-reform-adjudication-board',
            ],
            [
                'office_id' => 1,
                'division_name' => 'DEPARTMENT OF AGRARIAN REFORM MUNICIPAL OFFICES (DARMO)',
                'slug' => 'department-of-agrarian-reformmunicipal-offices',
            ],
            [
                'office_id' => 1,
                'division_name' => 'LAND TENURE IMPROVEMENT DIVISION (LTID)',
                'slug' => 'land-tenure-improvement-division',
            ],
            [
                'office_id' => 1,
                'division_name' => 'PROVINCIAL AGRARIAN REFORM PROGRAM OFFICER (PARPO’S office)',
                'slug' => 'provincial-agrarian-reform-program-officer',
            ],

        ];

        Division::insert($divisions);
    }
}
