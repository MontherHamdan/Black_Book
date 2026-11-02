<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PageOptionController extends Controller
{
    /**
     * Display a listing of active page options.
     */
    public function index(): JsonResponse
    {
        $options = Cache::remember('active_page_options', 3600, function () {
            return PageOption::active()
                ->get()
                ->map(function ($option) {
                    $item = [
                        'id' => (int) $option->id,
                        'pages' => (int) $option->pages,
                        'label' => (string) $option->label,
                        'extra_price' => (float) $option->extra_price == (int) $option->extra_price
                            ? (int) $option->extra_price
                            : (float) $option->extra_price,
                        'is_default' => (bool) $option->is_default,
                    ];

                    if (! empty($option->badge)) {
                        $item['badge'] = (string) $option->badge;
                    }

                    return $item;
                })
                ->values();
        });

        return response()->json([
            'status' => true,
            'data' => $options,
        ]);
    }
}
