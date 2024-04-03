<?php

namespace Database\Seeders;

use App\Models\DetailTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DetailTable::insert([
            [
                'TableID' => 1,
                'Name' => 'Field 1',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
            [
                'TableID' => 1,
                'Name' => 'Field 2',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
            [
                'TableID' => 1,
                'Name' => 'Field 3',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],


            [
                'TableID' => 2,
                'Name' => 'Field 4',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
            [
                'TableID' => 2,
                'Name' => 'Field 5',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
            [
                'TableID' => 2,
                'Name' => 'Field 6',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],



            [
                'TableID' => 3,
                'Name' => 'Field 7',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
            [
                'TableID' => 3,
                'Name' => 'Field 8',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
            [
                'TableID' => 3,
                'Name' => 'Field 9',
                'Description' => 'Something something',
                'DataType' => 'Integer',
                'AllowNull' => 1,
                'DefaultValue' => 'Anggaplah 3'
            ],
        ]);
    }
}
