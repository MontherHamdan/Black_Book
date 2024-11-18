<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookDesign;
use App\Models\BookDesignCategory;
use App\Models\BookDesignSubCategory;

class BookDesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories with Arabic names
        $categories = [
            ['name' => 'Palestine', 'arabic_name' => 'فلسطين'],
            ['name' => 'multiple image', 'arabic_name' => 'صور متعددة'],
            ['name' => 'Classy and simple', 'arabic_name' => 'أنيق وبسيط'],
            ['name' => 'joy of graduation', 'arabic_name' => 'فرحة التخرج'],
            ['name' => 'law', 'arabic_name' => 'القانون'],
            ['name' => 'Medical majors', 'arabic_name' => 'التخصصات الطبية'],
            ['name' => 'Sports', 'arabic_name' => 'الرياضة']
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $createdCategory = BookDesignCategory::create([
                'name' => $category['name'],
                'arabic_name' => $category['arabic_name']
            ]);
            $categoryIds[$category['name']] = $createdCategory->id;
        }

        // Create subcategories for 'multiple image' with Arabic names
        $multipleImageSubCategories = [
            ['name' => 'many pictures', 'arabic_name' => 'العديد من الصور'],
            ['name' => 'four pictures on the back', 'arabic_name' => 'أربع صور على الخلف'],
            ['name' => 'three pictures on the back', 'arabic_name' => 'ثلاث صور على الخلف'],
            ['name' => 'two pictures on the back', 'arabic_name' => 'صورتان على الخلف'],
            ['name' => 'one picture on the back', 'arabic_name' => 'صورة واحدة على الخلف']
        ];

        foreach ($multipleImageSubCategories as $subCategory) {
            BookDesignSubCategory::create([
                'name' => $subCategory['name'],
                'arabic_name' => $subCategory['arabic_name'],
                'category_id' => $categoryIds['multiple image']
            ]);
        }

        // Create subcategories for 'Medical majors' with Arabic names
        $medicalMajorsSubCategories = [
            ['name' => 'Human medicine', 'arabic_name' => 'الطب البشري'],
            ['name' => 'dentistry', 'arabic_name' => 'طب الأسنان'],
            ['name' => 'Nursing', 'arabic_name' => 'التمريض'],
            ['name' => 'Pharmacy', 'arabic_name' => 'الصيدلة'],
            ['name' => 'Medical laboratories', 'arabic_name' => 'المختبرات الطبية']
        ];

        foreach ($medicalMajorsSubCategories as $subCategory) {
            BookDesignSubCategory::create([
                'name' => $subCategory['name'],
                'arabic_name' => $subCategory['arabic_name'],
                'category_id' => $categoryIds['Medical majors']
            ]);
        }

        // Additional designs with subcategories
        $bookDesigns = [
            // Multiple Images
            [
                'image' => 'images/design2.jpg',
                'category_id' => $categoryIds['multiple image'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'four pictures on the back')->first()->id
            ],
            [
                'image' => 'images/design_multi1.jpg',
                'category_id' => $categoryIds['multiple image'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'three pictures on the back')->first()->id
            ],
            [
                'image' => 'images/design_multi2.jpg',
                'category_id' => $categoryIds['multiple image'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'two pictures on the back')->first()->id
            ],

            // Medical Majors
            [
                'image' => 'images/design6.jpg',
                'category_id' => $categoryIds['Medical majors'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'Pharmacy')->first()->id
            ],
            [
                'image' => 'images/design_medical1.jpg',
                'category_id' => $categoryIds['Medical majors'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'Human medicine')->first()->id
            ],
            [
                'image' => 'images/design_medical2.jpg',
                'category_id' => $categoryIds['Medical majors'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'dentistry')->first()->id
            ],
            [
                'image' => 'images/design_medical3.jpg',
                'category_id' => $categoryIds['Medical majors'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'Nursing')->first()->id
            ],

            // Sports
            [
                'image' => 'images/design_sports1.jpg',
                'category_id' => $categoryIds['Sports'],
                'sub_category_id' => null
            ],
            [
                'image' => 'images/design_sports2.jpg',
                'category_id' => $categoryIds['Sports'],
                'sub_category_id' => null
            ],
            [
                'image' => 'images/design_sports3.jpg',
                'category_id' => $categoryIds['Sports'],
                'sub_category_id' => null
            ]
        ];

        foreach ($bookDesigns as $bookDesignData) {
            BookDesign::create($bookDesignData);
        }
    }
}
