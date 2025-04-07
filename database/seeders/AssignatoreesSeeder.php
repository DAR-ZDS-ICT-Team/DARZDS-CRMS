<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assignatorees;

class AssignatoreesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignatorees = [
            [
                'office_id' => '1',
                'name' => 'Kamar P. Mindalano, Jr.',
                'designation' => 'PARPO II'
            ],
        ];

        Assignatorees::insert($assignatorees);
    }
}
