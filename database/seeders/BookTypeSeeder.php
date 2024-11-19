<?php

namespace Database\Seeders;

use App\Models\BookType;
use App\Models\BookTypeSubMedia;
use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BookTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables
        BookTypeSubMedia::truncate();
        BookType::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $images = [
            'صورة المنتج الأول_دفتر مع قاعدة عادية_دفتر تخرج اون لاين-08.webp',
            'دفتر تخرج_مع قاعدة لاكجري_وتصاميم دفاتر تخرج_وعبارات لدفتر تخرج_شراء دفتر تخرج اون لاين-08.webp',
            'دفتر تخرج_مع قاعدة اسم اكرليك_مع تصاميم دفتر تخرج_إمكانية شراء دفتر التخرج اون لاين_مع عبارات دفتر تخرج-08.webp',
            'دفتر تخرج_مع قاعدة اسم اكرليك بالعرض_مع تصاميم دفتر تخرج_إمكانية شراء دفتر التخرج اون لاين_مع عبارات دفتر تخرج-08.webp'
        ];

        $descriptionsEn = [
            'Hi-This book type costs- only 13 JOD.',
            'Hi-This book type costs- only 12 JOD.',
            'Hi-This book type costs- only 11 JOD.',
            'Hi-This book type costs- only 10 JOD.',
        ];

        $descriptionsAr = [
            'اهلا-الدفتر سعره-فقط 13 دينار',
            'اهلا-الدفتر سعره-فقط 12 دينار',
            'اهلا-الدفتر سعره-فقط 11 دينار',
            'اهلا-الدفتر سعره-فقط 10 دينار',
        ];

        $prices = [13, 12, 11, 10];

        foreach ($images as $key => $image) {
            // Main book type image
            $imagePath = public_path('images/bookType/' . $image);

            $storedImage = Storage::disk('public')->putFileAs('book_types', new File($imagePath), $image);
            $imageUrl = url('storage/book_types/' . $image);

            // Create main BookType entry
            $bookType = BookType::create([
                'image' => $imageUrl,
                'price' => $prices[$key],
                'description_en' => $descriptionsEn[$key],
                'description_ar' => $descriptionsAr[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sub-images for each book type
            $subFolder = 'sub_' . ($key + 1);
            $subImagePath = public_path('images/bookType/' . $subFolder);
            $subImages = glob($subImagePath . '/*');

            if (!empty($subImages)) {
                foreach ($subImages as $subImage) {
                    // Determine file type based on extension
                    $type = strtolower(pathinfo($subImage, PATHINFO_EXTENSION))  === 'mp4' ? 'video' : 'image';
                    $subFileName = pathinfo($subImage, PATHINFO_BASENAME);

                    // Store sub-image or video in storage
                    $storedSubImage = Storage::disk('public')->putFileAs('book_type_sub_images', new File($subImage), $subFileName);
                    $subImageUrl = url('storage/book_type_sub_images/' . $subFileName);

                    // Insert sub-image or video entry
                    BookTypeSubMedia::create([
                        'book_type_id' => $bookType->id,
                        'media' => $subImageUrl,
                        'type' => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
