<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Load states from JSON
        $statesJson = file_get_contents(database_path('seeders/states.json'));
        $allStates = json_decode($statesJson, true);

        // Create a mapping of country codes to IDs from the countries table
        $countries = DB::table('countries')->select('id', 'code')->get();
        $countryMap = [];
        foreach ($countries as $country) {
            $countryMap[$country->code] = $country->id;
        }

        $states = [];
        $now = now();

        foreach ($allStates as $state) {
            $countryCode = $state['country_code'];
            
            // Only add states for countries that exist in our countries table
            if (isset($countryMap[$countryCode])) {
                $states[] = [
                    'name' => $state['name'],
                    'country_id' => $countryMap[$countryCode],
                    'country_code' => $countryCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert states in batches of 500 to avoid memory issues
        $chunks = array_chunk($states, 500);
        
        foreach ($chunks as $chunk) {
            DB::table('states')->insert($chunk);
        }

        $this->command->info('Seeded ' . count($states) . ' states successfully!');
    }
}
