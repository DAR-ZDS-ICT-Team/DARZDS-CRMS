<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'office_id' => 1,
                'division_id' => 1,
                'section_name' => 'Accounting',
            ],
            [
                'office_id' => 1,
                'division_id' => 1,
                'section_name' => 'Cashiering',
            ],
            [
                'office_id' => 1,
                'division_id' => 1,
                'section_name' => 'Budget',
            ],
            [
                'office_id' => 1,
                'division_id' => 1,
                'section_name' => 'Planning',
            ],
            [
                'office_id' => 1,
                'division_id' => 1,
                'section_name' => 'Personnel',
            ],
            [
                'office_id' => 1,
                'division_id' => 1,
                'section_name' => 'Supply',
            ],

            [
                'office_id' => 1,
                'division_id' => 5,
                'section_name' => 'Cluster 1',
            ],
            [
                'office_id' => 1,
                'division_id' => 5,
                'section_name' => 'Cluster 2',
            ],
            [
                'office_id' => 1,
                'division_id' => 5,
                'section_name' => 'Cluster 3',
            ],
            [
                'office_id' => 1,
                'division_id' => 5,
                'section_name' => 'Cluster 4',
            ],
            [
                'office_id' => 1,
                'division_id' => 5,
                'section_name' => 'Cluster 5',
            ],
            [
                'office_id' => 1,
                'division_id' => 5,
                'section_name' => 'Cluster 6',
            ],

       

        ];

        Section::insert($sections);
    }
}
