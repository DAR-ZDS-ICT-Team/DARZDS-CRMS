<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            [
                'id' => 1,
                'name' => 'ZDS PROVINCIAL OFFICE',
                'code' => 'ZDS',
                'short_name' => 'ZDS PROVINCIAL OFFICE',
                'order' => 'ZDS',
            ],

        ];
        Office::insert($offices);
    }
}
