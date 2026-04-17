<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;

class SyncLogestechsLocations extends Command
{
        // Let's simplify the command so it doesn't need to pass the file path (since it's now fixed). Path is fixed to storage/app/cities.xlsx

    protected $signature = 'logestechs:sync';

    protected $description = 'Import and distribute provinces, cities, and regions from a LogesTechs Excel file';

    public function handle()
    {
        $filepath = storage_path('app/cities.xlsx');

        if (!file_exists($filepath)) {
            $this->error("The file does not exist in the path: " . $filepath);
            return;
        }

        $this->info("Reading Excel file (cities.xlsx) and distributing data...");

        // Using an anonymous class to read the file and convert it to an array
        $data = Excel::toArray(new class implements ToArray {
            public function array(array $array) { return $array; }
        }, $filepath);

        if (empty($data) || empty($data[0])) {
            $this->error("The file is empty or cannot be read!");
            return;
        }

        $rows = $data[0];
        
        // Remove the first row if it contains column headers
        if (!is_numeric($rows[0][0])) {
            array_shift($rows);
        }

        DB::beginTransaction();

        try {
            $count = 0;
            foreach ($rows as $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // 🔴 Sort the columns based on your message:
                // 0: Village Id, 1: Village Name, 2: Arabic Village Name
                // 3: City Id,    4: City Name, 
                // 5: Region Id,  6: Region Name

                $villageId = trim($row[0]);
                $villageEn = trim($row[1]);
                $villageAr = trim($row[2]);

                $cityId    = trim($row[3]);
                $cityEn    = trim($row[4]);
                // Their file does not have an Arabic city name (according to the order you sent), so we will use English as a fallback
                $cityAr    = trim($row[4]); 

                $regionId  = trim($row[5]);
                $regionEn  = trim($row[6]);
                // Their file does not have an Arabic governorate name, so we will use English as a fallback
                $regionAr  = trim($row[6]); 

                // 1️⃣ Processing the Governorate
                // We search for it by English or Arabic name to make sure it's not there
                $gov = DB::table('governorates')
                         ->where('name_en', $regionEn)
                         ->orWhere('name_ar', $regionAr)
                         ->first();

                if ($gov) {
                    // Update the delivery company's ID if the governorate exists
                    DB::table('governorates')->where('id', $gov->id)->update(['logestechs_id' => $regionId]);
                    $internalGovId = $gov->id;
                } else {
                    // Add the governorate if it doesn't exist
                    $internalGovId = DB::table('governorates')->insertGetId([
                        'name_ar' => $regionAr,
                        'name_en' => $regionEn,
                        'logestechs_id' => $regionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 2️⃣ Processing the City
                $city = DB::table('cities')->where('logestechs_id', $cityId)->first();
                if ($city) {
                    $internalCityId = $city->id;
                } else {
                    $internalCityId = DB::table('cities')->insertGetId([
                        'governorate_id' => $internalGovId,
                        'name_ar' => $cityAr,
                        'name_en' => $cityEn,
                        'logestechs_id' => $cityId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 3️⃣ Processing the Area/Village
                DB::table('areas')->updateOrInsert(
                    ['logestechs_id' => $villageId],
                    [
                        'city_id' => $internalCityId,
                        'name_ar' => $villageAr ?: $villageEn, // If no Arabic, use English
                        'name_en' => $villageEn,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $count++;
                
                // To print the progress in the Terminal every 100 areas
                if ($count % 100 == 0) {
                    $this->info("Imported {$count} areas...");
                }
            }

            DB::commit();
            $this->info("✨ Operation completed successfully! Imported {$count} areas distributed across governorates and cities.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ An error occurred during import: " . $e->getMessage());
            $this->error("The line that caused the problem: " . json_encode($row ?? []));
        }
    }
}