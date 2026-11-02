<?php

namespace Database\Seeders;

use App\Models\PageOption;
use Illuminate\Database\Seeder;

class PageOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            [
                'pages' => 100,
                'label' => '100 ورقة',
                'extra_price' => 0.00,
                'is_default' => false,
                'badge' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'pages' => 150,
                'label' => '150 ورقة',
                'extra_price' => 0.00,
                'is_default' => true,
                'badge' => 'الأكثر طلباً 🔥',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'pages' => 200,
                'label' => '200 ورقة',
                'extra_price' => 0.00,
                'is_default' => false,
                'badge' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($options as $option) {
            PageOption::updateOrCreate(
                ['pages' => $option['pages']],
                $option
            );
        }
    }
}
