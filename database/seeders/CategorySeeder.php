<?php

namespace Database\Seeders;

use App\Models\ModuleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModuleCategory::insert([
            [
                'ERPID' => 1,
                'Name' => 'Sales'
            ],
            [
                'ERPID' => 1,
                'Name' => 'Accounting'
            ],
            [
                'ERPID' => 1,
                'Name' => 'Purchasing'
            ],
        ]);
    }
}
