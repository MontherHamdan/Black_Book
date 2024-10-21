<?php

namespace Database\Seeders;

use App\Models\BookType;
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
        // Store the image in the public disk and get the path
        $imagePath = public_path('images/bookType/book1.png');
        $storedImage = Storage::disk('public')->putFile('book_types', new File($imagePath));

        // Generate the full URL to the image (with domain or IP)
        $imageUrl = url('storage/' . $storedImage);

        // Insert the data into the database
        BookType::insert([
            [
                'image' => $imageUrl,
                'price' => 13,
                'description' => 'اهلا-الدفتر سعره-فقط 13 دينار',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => $imageUrl,
                'price' => 13,
                'description' => 'اهلا-الدفتر سعره-فقط 13 دينار',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
