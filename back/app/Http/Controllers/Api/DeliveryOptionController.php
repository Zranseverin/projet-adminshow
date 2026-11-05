<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOption;
use Illuminate\Http\JsonResponse;

class DeliveryOptionController extends Controller
{
    public function index(): JsonResponse
    {
        $options = DeliveryOption::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (DeliveryOption $option): array => [
                'code' => $option->code,
                'name' => $option->name,
                'description' => $option->description,
                'price' => (float) $option->price,
            ]);

        return response()->json(['data' => $options]);
    }
}
