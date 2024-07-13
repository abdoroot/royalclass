<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert normal users
        $users = [
            [
                'name' => 'Abdelhadi',
                'email' => 'abdelhadi@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('123456'),
                'is_admin' => 0,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Normal User 2',
                'email' => 'user2@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('123456'),
                'is_admin' => 0,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert admin user
        $adminUser = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'is_admin' => 1,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert users and posts
        foreach ($users as $userData) {
            $user = DB::table('users')->insertGetId($userData);

            // Insert three posts for each user
            for ($i = 1; $i <= 3; $i++) {
                DB::table('posts')->insert([
                    'user_id' => $user,
                    'title' => "Post {$i} by {$userData['name']}",
                    'content' => "Content of Post {$i} by {$userData['name']}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Insert admin user
        DB::table('users')->insert($adminUser);
    }
}
