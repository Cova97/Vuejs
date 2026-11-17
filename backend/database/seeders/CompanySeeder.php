<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Part;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parts = Part::all();

        Company::factory(5)->create()->each(function ($company) use ($parts) {
            foreach ($parts->random(3) as $part) {
                $company->parts()->attach($part->id, [
                    'stock' => fake()->numberBetween(0, 100),
                    'min_stock' => 10,
                ]);
            }
        });
    }
}
