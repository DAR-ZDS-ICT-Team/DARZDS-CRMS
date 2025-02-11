<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'SUPER ADMIN';
        $user->email = 'dar.zds@gmail.com';
        $user->account_type = 'admin';
        $user->password = bcrypt('dar-superadmin');
        $user->office_id = 1;
        $user->save();

        $user = new User();
        $user->name = 'DAR PLANNING';
        $user->email = 'dar.planning@gmail.com';
        $user->account_type = 'planning';
        $user->password = bcrypt('dar-planning');
        $user->office_id = 1;
        $user->save();
    }
}
