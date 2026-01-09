<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'PT Trias Sentosa',
                'code' => 'TRIAS'
            ],
            [
                'name' => 'PT B',
                'code' => 'PTB'
            ],
            [
                'name' => 'PT C',
                'code' => 'PTC'
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['code' => $company['code']],
                $company
            );
        }
    }
}
