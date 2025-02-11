<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShowDateCsfForm;

class ShowDateCSFFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'is_displayed' => 0,
            ],
        ];

        ShowDateCsfForm::insert($data);
    }
}
