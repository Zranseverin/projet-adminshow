<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    public function index(): View
    {
        $advertisements = Advertisement::whereNotNull('placement')->orderBy('sort_order')->orderBy('placement')->get();

        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'advertisements' => ['required', 'array'],
            'advertisements.*.label' => ['nullable', 'string', 'max:160'],
            'advertisements.*.title' => ['required', 'string', 'max:220'],
            'advertisements.*.subtitle' => ['nullable', 'string', 'max:1000'],
            'advertisements.*.image_url' => ['nullable', 'url', 'max:700'],
            'advertisements.*.button_text' => ['nullable', 'string', 'max:120'],
            'advertisements.*.button_url' => ['nullable', 'url', 'max:700'],
            'advertisements.*.background' => ['nullable', 'string', 'max:255'],
            'advertisements.*.button_background' => ['nullable', 'string', 'max:120'],
            'advertisements.*.button_color' => ['nullable', 'string', 'max:80'],
            'advertisements.*.is_active' => ['nullable', 'boolean'],
            'advertisements.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($data['advertisements'] as $id => $advertisementData) {
            Advertisement::whereKey($id)->update([
                'label' => $advertisementData['label'] ?? null,
                'title' => $advertisementData['title'],
                'subtitle' => $advertisementData['subtitle'] ?? null,
                'image_url' => $advertisementData['image_url'] ?? null,
                'button_text' => $advertisementData['button_text'] ?? null,
                'button_url' => $advertisementData['button_url'] ?? null,
                'background' => $advertisementData['background'] ?? null,
                'button_background' => $advertisementData['button_background'] ?? null,
                'button_color' => $advertisementData['button_color'] ?? null,
                'is_active' => (bool) ($advertisementData['is_active'] ?? false),
                'sort_order' => $advertisementData['sort_order'] ?? 0,
            ]);
        }

        return redirect()->route('admin.advertisements.index')->with('success', 'Publicites mises a jour.');
    }
}
