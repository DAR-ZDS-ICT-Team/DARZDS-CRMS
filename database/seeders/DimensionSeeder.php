<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dimension;

class DimensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dimensions = [
            [
                'name' => 'Overall',
                'description' => 'I am satisfied with the service I availed',
                'slug' => 'Overall',
                'is_active' => 1,
            ],
            [
                'name' => 'Responsiveness',
                'description' => 'I spent a reasonable amount of time for my transacton',
                'slug' => 'responsiveness',
                'is_active' => 1,
            ],
            [
                'name' => 'Reliability',
                'description' => 'The office followed the transaction requirements and steps based on the information provided',
                'slug' => 'reliability',
                'is_active' => 1,
            ],
            [
                'name' => 'Access & Facilities',
                'description' => 'The steps(including payment) I needed for my transaction were easy and simple',
                'slug' => 'access-and-facilities',
                'is_active' => 1,
            ],
            [
                'name' => 'Communication',
                'description' => 'I easily found information about my transaction from the office website',
                'slug' => 'communication',
                'is_active' => 1,
            ],

            [
                'name' => 'Costs',
                'description' => 'I paid a reasonable amount of fees for my transaction.(if the service is free mark the N/A option',
                'slug' => 'costs',
                'is_active' => 1,
            ],

            [
                'name' => 'Integrity',
                'description' => 'I am confident my online transaction was secure',
                'slug' => 'integrity',
                'is_active' => 1,
            ],

            [
                'name' => 'Assurance',
                'description' => "The office's online support was available, and (if asked questions) online support was quick to respond",
                'slug' => 'assurance',
                'is_active' => 1,
            ],
            [
                'name' => 'Outcome',
                'description' => 'I got what I needed from the government office , or (if denied) denial of request was sufficiently explained to me',
                'slug' => 'outcome',
                'is_active' => 1,
            ],
        ];

        Dimension::insert($dimensions);
    }
}
