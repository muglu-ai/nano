<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryStateSeeder extends Seeder
{
    private string $apiKey;
    private string $apiBaseUrl = 'https://api.countrystatecity.in/v1';

    public function __construct()
    {
        $this->apiKey = env('COUNTRY_STATE_API_KEY', '');
    }

    public function run(): void
    {
        // Check if countries already exist
        if (DB::table('countries')->count() > 0) {
            if (!$this->command->confirm('Countries already exist. Do you want to re-seed from API?', false)) {
                $this->command->warn('Skipping...');
                return;
            }
            // Clear existing data if user wants to re-seed
            DB::table('states')->truncate();
            DB::table('countries')->truncate();
        }

        // Try API first if key is provided
        if (!empty($this->apiKey)) {
            $this->command->info('🌍 Fetching countries and states from API...');
            if ($this->seedFromApi()) {
                return;
            }
            $this->command->warn('API seeding failed. Trying fallback methods...');
        } else {
            $this->command->warn('⚠️  COUNTRY_STATE_API_KEY not found in .env file.');
            $this->command->info('💡 Add COUNTRY_STATE_API_KEY=your_api_key to your .env file to use API.');
        }

        // Fallback: Try to load from JSON file if exists
        $jsonPath = database_path('seeders/data/countries_states.json');
        
        if (File::exists($jsonPath)) {
            $this->seedFromJson($jsonPath);
        } else {
            // Seed basic countries and states
            $this->seedBasicCountries();
        }
    }

    private function seedFromApi(): bool
    {
        try {
            // Fetch all countries
            $this->command->info('Fetching countries...');
            $countriesResponse = Http::withHeaders([
                'X-CSCAPI-KEY' => $this->apiKey,
            ])->timeout(60)->get("{$this->apiBaseUrl}/countries");

            if (!$countriesResponse->successful()) {
                $this->command->error('Failed to fetch countries from API. Status: ' . $countriesResponse->status());
                Log::error('CountryStateSeeder API Error', [
                    'status' => $countriesResponse->status(),
                    'body' => $countriesResponse->body(),
                ]);
                return false;
            }

            $countries = $countriesResponse->json();
            
            if (!is_array($countries) || empty($countries)) {
                $this->command->error('Invalid response from countries API.');
                return false;
            }

            $this->command->info("Found " . count($countries) . " countries. Fetching states...");
            $this->command->newLine();

            $progressBar = $this->command->getOutput()->createProgressBar(count($countries));
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $progressBar->setMessage('Starting...');
            $progressBar->start();

            $totalCountries = 0;
            $totalStates = 0;

            foreach ($countries as $country) {
                $countryCode = $country['iso2'] ?? null;
                $countryName = $country['name'] ?? 'Unknown';
                
                if (!$countryCode) {
                    $progressBar->setMessage("Skipping {$countryName} (no code)");
                    $progressBar->advance();
                    continue;
                }

                // Insert country with API fields
                $countryId = DB::table('countries')->insertGetId([
                    'name' => $countryName,
                    'code' => $countryCode,
                    'phonecode' => $country['phonecode'] ?? null,
                    'flag' => $country['emoji'] ?? null,
                    'iso3' => $country['iso3'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $totalCountries++;

                // Fetch states for this country
                try {
                    $statesResponse = Http::withHeaders([
                        'X-CSCAPI-KEY' => $this->apiKey,
                    ])->timeout(30)->get("{$this->apiBaseUrl}/countries/{$countryCode}/states");

                    if ($statesResponse->successful()) {
                        $states = $statesResponse->json();
                        
                        if (is_array($states) && !empty($states)) {
                            foreach ($states as $state) {
                                DB::table('states')->insert([
                                    'country_id' => $countryId,
                                    'name' => $state['name'] ?? 'Unknown',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $totalStates++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Continue with next country if states fetch fails
                    Log::warning("Failed to fetch states for {$countryName}", [
                        'error' => $e->getMessage(),
                    ]);
                }

                $progressBar->setMessage("Processed: {$countryName}");
                $progressBar->advance();
            }

            $progressBar->setMessage('Complete!');
            $progressBar->finish();
            $this->command->newLine(2);
            $this->command->info("✅ Successfully seeded {$totalCountries} countries and {$totalStates} states from API!");
            $this->command->newLine();

            return true;
        } catch (\Exception $e) {
            $this->command->error('Error fetching from API: ' . $e->getMessage());
            Log::error('CountryStateSeeder Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    private function seedFromJson(string $jsonPath): void
    {
        $data = json_decode(File::get($jsonPath), true);
        
        if (!$data) {
            $this->command->warn('Invalid JSON file. Using basic seed data.');
            $this->seedBasicCountries();
            return;
        }

        foreach ($data as $country) {
            $countryData = [
                'name' => $country['name'],
                'code' => $country['code'] ?? $country['iso2'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add optional fields if they exist
            if (isset($country['phonecode'])) $countryData['phonecode'] = $country['phonecode'];
            if (isset($country['emoji'])) $countryData['flag'] = $country['emoji'];
            if (isset($country['iso3'])) $countryData['iso3'] = $country['iso3'];
            
            $countryId = DB::table('countries')->insertGetId($countryData);

            if (isset($country['states']) && is_array($country['states'])) {
                foreach ($country['states'] as $state) {
                    DB::table('states')->insert([
                        'country_id' => $countryId,
                        'name' => $state['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Countries and states seeded from JSON file.');
    }

    private function seedBasicCountries(): void
    {
        // Seed some common countries
        $countries = [
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'China', 'code' => 'CN'],
            ['name' => 'Singapore', 'code' => 'SG'],
        ];

        foreach ($countries as $country) {
            $countryData = [
                'name' => $country['name'],
                'code' => $country['code'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Add optional fields if they exist in the data structure
            if (isset($country['iso3'])) $countryData['iso3'] = $country['iso3'];
            if (isset($country['numeric_code'])) $countryData['numeric_code'] = $country['numeric_code'];
            if (isset($country['phonecode'])) $countryData['phonecode'] = $country['phonecode'];
            if (isset($country['emoji'])) $countryData['flag'] = $country['emoji'];
            if (isset($country['capital'])) $countryData['capital'] = $country['capital'];
            if (isset($country['currency'])) $countryData['currency'] = $country['currency'];
            if (isset($country['currency_name'])) $countryData['currency_name'] = $country['currency_name'];
            if (isset($country['currency_symbol'])) $countryData['currency_symbol'] = $country['currency_symbol'];
            if (isset($country['region'])) $countryData['region'] = $country['region'];
            if (isset($country['subregion'])) $countryData['subregion'] = $country['subregion'];
            
            $countryId = DB::table('countries')->insertGetId($countryData);

            // Seed some Indian states
            if ($country['name'] === 'India') {
                $indianStates = [
                    'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                    'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
                    'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
                    'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
                    'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
                    'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Delhi', 'Puducherry'
                ];

                foreach ($indianStates as $state) {
                    DB::table('states')->insert([
                        'country_id' => $countryId,
                        'name' => $state,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Basic countries and states seeded.');
    }
}
