<?php

namespace Database\Seeders;

use App\Models\ERP;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ERPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ERP::insert([
            [
                'Name' => 'S-- L-- S--',
                'Initials' => 'SLS',
            ],
            [
                'Name' => 'Realting Something',
                'Initials' => 'Realting',
            ],
            [
                'Name' => 'Human Resource Information System',
                'Initials' => 'HRIS',
            ],
            [
                'Name' => 'S-- M-- I-- S--',
                'Initials' => 'SMIS',
            ],
            [
                'Name' => 'S-- W-- M-- S--',
                'Initials' => 'SWMS',
            ],
            [
                'Name' => 'Helpdesk',
                'Initials' => 'Helpdesk',
            ],
            [
                'Name' => 'Inventory ITE',
                'Initials' => 'Inventory ITE',
            ],
            [
                'Name' => 'S-- O-- S--',
                'Initials' => 'SOS',
            ]
        ]);
    }
}
