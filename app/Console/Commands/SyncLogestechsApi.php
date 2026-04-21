<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncLogestechsApi extends Command
{
    protected $signature = 'logestechs:sync-api';

    protected $description = 'Sync Locations (Regions, Cities, Villages) from LogesTechs API';

    public function handle()
    {
        $this->info('Fetching data from LogesTechs API...');

        $companyId = env('LOGESTECHS_COMPANY_ID', 186);
        $url = 'https://apisv2.logestechs.com/api/addresses/villages';

        try {
            $response = Http::withHeaders([
                'company-id' => $companyId,
                'Accept' => 'application/json',
            ])->get($url);

            if (! $response->successful()) {
                $this->error('Failed to fetch data. Status: '.$response->status());

                return;
            }

            $villages = $response->json('data');

            if (empty($villages)) {
                $this->warn('API returned empty data.');

                return;
            }

            $this->info('Found '.count($villages).' villages. Syncing databases...');

            $jordan = Country::firstOrCreate(
                ['code' => 'JO'],
                [
                    'name' => 'Jordan',
                    'dial_code' => '+962',
                    'is_active' => true,
                ]
            );

            $bar = $this->output->createProgressBar(count($villages));
            $bar->start();

            foreach ($villages as $village) {

                $governorate = Governorate::updateOrCreate(
                    ['logestechs_id' => $village['regionId']],
                    [
                        'country_id' => $jordan->id,
                        'name_en' => $village['regionName'],
                        'name_ar' => $village['regionName'],
                        'is_active' => true,
                    ]
                );

                $city = City::updateOrCreate(
                    ['logestechs_id' => $village['cityId']],
                    [
                        'governorate_id' => $governorate->id,
                        'name_en' => $village['cityName'],
                        'name_ar' => $village['cityName'],
                        'is_active' => true,
                    ]
                );

                Area::updateOrCreate(
                    ['logestechs_id' => $village['id']],
                    [
                        'city_id' => $city->id,
                        'name_en' => $village['name'],
                        'name_ar' => $village['arabicName'] ?? $village['name'],
                        'is_active' => true,
                    ]
                );

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Sync completed successfully! 🚀');

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }
}
