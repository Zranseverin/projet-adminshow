<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\JsonResponse;

class AdvertisementController extends Controller
{
    public function index(): JsonResponse
    {
        $advertisements = Advertisement::whereNotNull('placement')
            ->orderBy('sort_order')
            ->orderBy('placement')
            ->get()
            ->mapWithKeys(fn (Advertisement $advertisement): array => [
                $advertisement->placement => [
                    'placement' => $advertisement->placement,
                    'label' => $advertisement->label,
                    'title' => $advertisement->title,
                    'subtitle' => $advertisement->subtitle,
                    'image_url' => $advertisement->image_url,
                    'button_text' => $advertisement->button_text,
                    'button_url' => $advertisement->button_url,
                    'background' => $advertisement->background,
                    'button_background' => $advertisement->button_background,
                    'button_color' => $advertisement->button_color,
                    'is_active' => $advertisement->is_active,
                    'sort_order' => $advertisement->sort_order,
                ],
            ]);

        return response()->json(['data' => $advertisements]);
    }
}
