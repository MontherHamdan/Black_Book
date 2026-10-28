<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Statistic;
use Illuminate\Http\JsonResponse;

class StatisticController extends Controller
{
    /**
     * Get active statistics for "Our Story" section.
     */
    public function index(): JsonResponse
    {
        $stats = Statistic::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($stat) {
                return [
                    'id' => $stat->id,
                    'value' => $stat->value,
                    'title' => $stat->title_ar,
                    'title_ar' => $stat->title_ar,
                    'title_en' => $stat->title_en,
                    'icon' => $stat->icon,
                    'sort_order' => $stat->sort_order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'title' => 'أين نحن اليوم؟',
                'note' => 'ونعمل حالياً على تجهيز الفروع القادمة لنكون أقرب إليكم.',
                'stats' => $stats,
            ],
        ]);
    }
}
