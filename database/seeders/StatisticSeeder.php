<?php

namespace Database\Seeders;

use App\Models\Statistic;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'value' => '40K +',
                'title_ar' => 'قاعدة جماهيرية واسعة عبر إنستغرام',
                'title_en' => 'Wide audience on Instagram',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'value' => '1st',
                'title_ar' => 'افتتحنا أول فرع رسمي لنا',
                'title_en' => 'Opened our first official branch',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'value' => '3',
                'title_ar' => 'مشاغل متكاملة لتلبية طلباتكم',
                'title_en' => 'Integrated workshops to meet your orders',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            Statistic::firstOrCreate(
                ['title_ar' => $item['title_ar']],
                $item
            );
        }
    }
}
