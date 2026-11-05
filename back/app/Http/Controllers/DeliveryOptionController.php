<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryOptionController extends Controller
{
    public function index(): View
    {
        $deliveryOptions = DeliveryOption::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.delivery-options.index', compact('deliveryOptions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'options' => ['required', 'array'],
            'options.*.name' => ['required', 'string', 'max:160'],
            'options.*.description' => ['nullable', 'string', 'max:255'],
            'options.*.price' => ['required', 'numeric', 'min:0'],
            'options.*.is_active' => ['nullable', 'boolean'],
            'options.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($data['options'] as $id => $optionData) {
            DeliveryOption::whereKey($id)->update([
                'name' => $optionData['name'],
                'description' => $optionData['description'] ?? null,
                'price' => $optionData['price'],
                'is_active' => (bool) ($optionData['is_active'] ?? false),
                'sort_order' => $optionData['sort_order'] ?? 0,
            ]);
        }

        return redirect()->route('admin.delivery-options.index')->with('success', 'Prix de livraison mis a jour.');
    }
}
