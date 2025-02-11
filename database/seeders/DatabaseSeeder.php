<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OfficeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DimensionSeeder::class);
        $this->call(CcQuestionSeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(SectionSeeder::class);
        $this->call(ShowDateCSFFormSeeder::class);
     
    }

    
}
