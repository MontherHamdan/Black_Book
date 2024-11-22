<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;
use App\Models\Major;

class UniversityMajorSeeder extends Seeder
{
    public function run()
    {
        // Define sample universities and majors
        $universities = [
            [
                'name' => 'Harvard University',
                'majors' => ['Computer Science', 'Business Administration', 'Law'],
            ],
            [
                'name' => 'Stanford University',
                'majors' => ['Engineering', 'Medicine', 'Psychology'],
            ],
            [
                'name' => 'Massachusetts Institute of Technology (MIT)',
                'majors' => ['Data Science', 'Mechanical Engineering', 'Physics'],
            ],
            [
                'name' => 'University of Oxford',
                'majors' => ['Philosophy', 'History', 'Biology'],
            ],
        ];

        // Insert data into the database
        foreach ($universities as $universityData) {
            $university = University::create(['name' => $universityData['name']]);

            foreach ($universityData['majors'] as $major) {
                Major::create([
                    'name' => $major,
                    'university_id' => $university->id,
                ]);
            }
        }
    }
}
