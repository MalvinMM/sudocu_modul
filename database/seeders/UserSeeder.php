<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'UserName' => 'User1',
                'FullName' => 'User1 Bos',
                'NIK' => '027735',
                'password' => Hash::make('user1'),
                'Role' => 'Admin'
            ],
            [
                'UserName' => 'User2',
                'FullName' => 'User2 PIC',
                'NIK' => '8256639',
                'password' => Hash::make('user2'),
                'Role' => 'PIC'
            ],
            [
                'UserName' => 'User3',
                'FullName' => 'User3 End User',
                'NIK' => '9263735',
                'password' => Hash::make('user3'),
                'Role' => 'User'
            ]
        ]);
    }
}
