<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Table::insert([
            [
                'Name' => 'Tabel 1 SLS',
                'Description' => 'Tabel Pertama',
                'ERPID' => 1
            ],
            [
                'Name' => 'Tabel 1 Realting',
                'Description' => 'Tabel Kedua',
                'ERPID' => 2
            ],
            [
                'Name' => 'Tabel 1 HRIS',
                'Description' => 'Tabel Ketiga',
                'ERPID' => 3
            ],
            [
                'Name' => 'Tabel 1 SMIS',
                'Description' => 'Tabel Keempat',
                'ERPID' => 4
            ],
            [
                'Name' => 'Tabel 1 SWMS',
                'Description' => 'Tabel Kelima',
                'ERPID' => 5
            ],
            [
                'Name' => 'Tabel 1 Helpdesk',
                'Description' => 'Tabel Keenam',
                'ERPID' => 6
            ],
            [
                'Name' => 'Tabel 1 Inventory',
                'Description' => 'Tabel Ketujuh',
                'ERPID' => 7
            ],
            [
                'Name' => 'Tabel 1 SOS',
                'Description' => 'Tabel Kedelapan',
                'ERPID' => 8
            ],
        ]);
    }
}
