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
        $now = now();
        $dimensions = [
            [
                'name' => 'Overall',
                'description' => 'I am satisfied with the service I availed',
                'slug' => 'Overall',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Responsiveness',
                'description' => 'I spent a reasonable amount of time for my transacton',
                'slug' => 'responsiveness',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Reliability',
                'description' => 'The office followed the transaction requirements and steps based on the information provided',
                'slug' => 'reliability',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Access & Facilities',
                'description' => 'The steps(including payment) I needed for my transaction were easy and simple',
                'slug' => 'access-and-facilities',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Communication',
                'description' => 'I easily found information about my transaction from the office website',
                'slug' => 'communication',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Costs',
                'description' => 'I paid a reasonable amount of fees for my transaction.(if the service is free mark the N/A option',
                'slug' => 'costs',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Integrity',
                'description' => 'I feel the office was fair to everyone, or "walang palakasan", during my transaction.',
                'slug' => 'integrity',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name' => 'Assurance',
                'description' => "I was treated courteously by the staff, and (if asked for help) the staff was helpful.",
                'slug' => 'assurance',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Outcome',
                'description' => 'I got what I needed from the government office , or (if denied) denial of request was sufficiently explained to me',
                'slug' => 'outcome',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Dimension::upsert(
            $dimensions,
            ['slug'],
            ['name', 'description', 'is_active', 'updated_at']
        );
    }
}
