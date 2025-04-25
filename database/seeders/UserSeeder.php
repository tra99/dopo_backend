<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User 
        $user = [
            [
                'name' => 'Admin',
                'email' => 'email@example.com',
                'password' => bcrypt('12345678'),
                'school_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Hok Sochetra',
                'email' => 'chetra123@gmail.com',
                'password' => bcrypt('12345678'),
                'school_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Chharng Chhit',
                'email' => 'chhit@gmail.com',
                'password' => bcrypt('12345678'),
                'school_id' => 3,
                'status' => 1,
            ],
            [
                'name' => 'Hai Tongmeng',
                'email' => 'tongmeng@gmail.com',
                'password' => bcrypt('12345678'),
                'school_id' => 4,
                'status' => 1,
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }


        // Role
        $role = [
            [
                'name' => 'MoEYS Leaders',
                'status' => 1
            ],
            [
                'name' => 'Technical Department',
                'status' => 1
            ],
            [
                'name' => 'Provincial Department',
                'status' => 1
            ],
            [
                'name' => 'District Department',
                'status' => 1
            ],
            [
                'name' => 'School Department',
                'status' => 1
            ],
            [
                'name' => 'Teacher',
                'status' => 1
            ],
            [
                'name' => 'Student',
                'status' => 1
            ]
        ];

        DB::table('roles')->insert($role);

        $user_role = [
            [
                'user_id' => 1,
                'role_id' => 1,
            ],
            [
                'user_id' => 1,
                'role_id' => 2,
            ],
            [
                'user_id' => 1,
                'role_id' => 3,
            ],
            [
                'user_id' => 1,
                'role_id' => 4,
            ],
            [
                'user_id' => 2,
                'role_id' => 6,
            ],
            [
                'user_id' => 3,
                'role_id' => 7,
            ],
            [
                'user_id' => 4,
                'role_id' => 7,
            ],
        ];
        DB::table('user_role')->insert($user_role);
    }
}
