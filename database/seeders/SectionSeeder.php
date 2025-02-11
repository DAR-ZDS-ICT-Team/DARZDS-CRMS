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
                'division_id' => 1,
                'section_name' => 'Accounting',
            ],
            [
                'division_id' => 1,
                'section_name' => 'Cashiering',
            ],
            [
                'division_id' => 1,
                'section_name' => 'Budget',
            ],
            [
                'division_id' => 1,
                'section_name' => 'Planning',
            ],
            [
                'division_id' => 1,
                'section_name' => 'Supply',
            ],

            [
                'division_id' => 5,
                'section_name' => 'Cluster 1',
            ],
            [
                'division_id' => 5,
                'section_name' => 'Cluster 2',
            ],
            [
                'division_id' => 5,
                'section_name' => 'Cluster 3',
            ],
            [
                'division_id' => 5,
                'section_name' => 'Cluster 4',
            ],
            [
                'division_id' => 5,
                'section_name' => 'Cluster 5',
            ],
            [
                'division_id' => 5,
                'section_name' => 'Cluster 6',
            ],

       

        ];

        Section::insert($sections);
    }
}
