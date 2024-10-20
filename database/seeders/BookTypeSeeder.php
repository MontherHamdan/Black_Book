<?php

namespace Database\Seeders;

use App\Models\BookType;
use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\HttpCache\Store;

class BookTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $imagePath = public_path('images/bookType/book1.png');

        $storedImage = Storage::disk('public')->putFile('book_types', new File($imagePath));

        // Generate the URL to the image
        $imageUrl = Storage::url($storedImage);

        BookType::insert([
            [
                'image' => $imageUrl,
                'price' => 13,
                'description' => 'اهلا-الدفتر سعره-فقط 13 دينار',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => $storedImage,
                'price' => 13,
                'description' => 'اهلا-الدفتر سعره-فقط 13 دينار',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
