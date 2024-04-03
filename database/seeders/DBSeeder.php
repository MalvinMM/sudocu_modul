<?php

namespace Database\Seeders;

use App\Models\Database;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Database::insert([
            [
                'ERPID' => 1,
                'DbName' => 'DB SLS',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db1'),
            ],
            [
                'ERPID' => 2,
                'DbName' => 'DB Realting',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db2'),
            ],
            [
                'ERPID' => 3,
                'DbName' => 'DB HRIS',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db3'),
            ],
            [
                'ERPID' => 4,
                'DbName' => 'DB SMIS',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db4'),
            ],
            [
                'ERPID' => 5,
                'DbName' => 'DB SWMS',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db5'),
            ],
            [
                'ERPID' => 6,
                'DbName' => 'DB Helpdes',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db6'),
            ],
            [
                'ERPID' => 7,
                'DbName' => 'DB Inventory ITE',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db7'),
            ],
            [
                'ERPID' => 8,
                'DbName' => 'DB SOS',
                'DbServerLoc' => 'local',
                'DbUserName' => 'user123',
                'DbPassword' => Hash::make('db8'),
            ]
        ]);
    }
}
