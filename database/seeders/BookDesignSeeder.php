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

        BookDesign::truncate();
        BookDesignSubCategory::truncate();
        BookDesignCategory::truncate();

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
            ['image' => 'images/design1.jpg', 'category_id' => $categoryIds['multiple image'], 'sub_category_id' => BookDesignSubCategory::where('name', 'many pictures')->first()->id],
            ['image' => 'images/design2.jpg', 'category_id' => $categoryIds['multiple image'], 'sub_category_id' => BookDesignSubCategory::where('name', 'four pictures on the back')->first()->id],
            ['image' => 'images/design3.jpg', 'category_id' => $categoryIds['multiple image'], 'sub_category_id' => BookDesignSubCategory::where('name', 'three pictures on the back')->first()->id],

            // Medical Majors
            ['image' => 'images/design4.jpg', 'category_id' => $categoryIds['Medical majors'], 'sub_category_id' => BookDesignSubCategory::where('name', 'Human medicine')->first()->id],
            ['image' => 'images/design5.jpg', 'category_id' => $categoryIds['Medical majors'], 'sub_category_id' => BookDesignSubCategory::where('name', 'Pharmacy')->first()->id],
            ['image' => 'images/design6.jpg', 'category_id' => $categoryIds['Medical majors'], 'sub_category_id' => BookDesignSubCategory::where('name', 'dentistry')->first()->id],

            // Palestine
            ['image' => 'images/design7.jpg', 'category_id' => $categoryIds['Palestine'], 'sub_category_id' => null],
            ['image' => 'images/design8.jpg', 'category_id' => $categoryIds['Palestine'], 'sub_category_id' => null],
            ['image' => 'images/design9.jpg', 'category_id' => $categoryIds['Palestine'], 'sub_category_id' => null],

            // Classy and Simple
            ['image' => 'images/design10.jpg', 'category_id' => $categoryIds['Classy and simple'], 'sub_category_id' => null],
            ['image' => 'images/design11.jpg', 'category_id' => $categoryIds['Classy and simple'], 'sub_category_id' => null],

            // Graduation
            ['image' => 'images/design12.jpg', 'category_id' => $categoryIds['joy of graduation'], 'sub_category_id' => null],
            ['image' => 'images/design13.jpg', 'category_id' => $categoryIds['joy of graduation'], 'sub_category_id' => null],

            // Law
            ['image' => 'images/design14.jpg', 'category_id' => $categoryIds['law'], 'sub_category_id' => null],
            ['image' => 'images/design15.jpg', 'category_id' => $categoryIds['law'], 'sub_category_id' => null],

            // Sports
            ['image' => 'images/design16.jpg', 'category_id' => $categoryIds['Sports'], 'sub_category_id' => null],
            ['image' => 'images/design17.jpg', 'category_id' => $categoryIds['Sports'], 'sub_category_id' => null],

            // Diverse Designs
            ['image' => 'images/design18.jpg', 'category_id' => $categoryIds['multiple image'], 'sub_category_id' => BookDesignSubCategory::where('name', 'one picture on the back')->first()->id],
            ['image' => 'images/design19.jpg', 'category_id' => $categoryIds['Medical majors'], 'sub_category_id' => BookDesignSubCategory::where('name', 'Medical laboratories')->first()->id],
            ['image' => 'images/design20.jpg', 'category_id' => $categoryIds['Classy and simple'], 'sub_category_id' => null]
        ];

        foreach ($bookDesigns as $bookDesignData) {
            BookDesign::create($bookDesignData);
        }
    }
}
