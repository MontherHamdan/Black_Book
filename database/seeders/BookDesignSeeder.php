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
        // Create categories
        $categories = [
            'Palestine',
            'multiple image',
            'Classy and simple',
            'joy of graduation',
            'law',
            'Medical majors'
        ];

        $categoryIds = [];
        foreach ($categories as $categoryName) {
            $category = BookDesignCategory::create(['name' => $categoryName]);
            $categoryIds[$categoryName] = $category->id;
        }

        // Create subcategories for 'multiple image'
        $multipleImageSubCategories = [
            'many pictures',
            'four pictures on the back',
            'three pictures on the back',
            'two pictures on the back',
            'one picture on the back'
        ];

        foreach ($multipleImageSubCategories as $subCategoryName) {
            BookDesignSubCategory::create([
                'name' => $subCategoryName,
                'category_id' => $categoryIds['multiple image']
            ]);
        }

        // Create subcategories for 'Medical majors'
        $medicalMajorsSubCategories = [
            'Human medicine',
            'dentistry',
            'Nursing',
            'Pharmacy',
            'Medical laboratories'
        ];

        foreach ($medicalMajorsSubCategories as $subCategoryName) {
            BookDesignSubCategory::create([
                'name' => $subCategoryName,
                'category_id' => $categoryIds['Medical majors']
            ]);
        }

        // Create some book designs with dummy image URLs and assign them to categories
        $bookDesigns = [
            [
                'image' => 'images/design1.jpg',
                'category_id' => $categoryIds['Palestine'],
                'sub_category_id' => null
            ],
            [
                'image' => 'images/design2.jpg',
                'category_id' => $categoryIds['multiple image'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'four pictures on the back')->first()->id
            ],
            [
                'image' => 'images/design3.jpg',
                'category_id' => $categoryIds['Classy and simple'],
                'sub_category_id' => null
            ],
            [
                'image' => 'images/design4.jpg',
                'category_id' => $categoryIds['joy of graduation'],
                'sub_category_id' => null
            ],
            [
                'image' => 'images/design5.jpg',
                'category_id' => $categoryIds['law'],
                'sub_category_id' => null
            ],
            [
                'image' => 'images/design6.jpg',
                'category_id' => $categoryIds['Medical majors'],
                'sub_category_id' => BookDesignSubCategory::where('name', 'Pharmacy')->first()->id
            ],
        ];

        foreach ($bookDesigns as $bookDesignData) {
            BookDesign::create($bookDesignData);
        }
    }
}
