<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertisingController extends Controller
{
    private function authorizeAdvertisement(Advertisement $advertisement): void
    {
        abort_unless(str_starts_with($advertisement->placement, 'marketplace_catalog_'), 404);
    }

    public function index()
    {
        return view('marketplace.advertisements', ['advertisements' => Advertisement::whereRaw("placement LIKE ? ESCAPE '!'", ['marketplace!_catalog!_%'])->orderBy('sort_order')->orderBy('id')->paginate(15)]);
    }

    public function create()
    {
        return view('marketplace.advertisement-form', ['advertisement' => new Advertisement(['is_active' => true, 'sort_order' => 0])]);
    }

    public function edit(Advertisement $advertisement)
    {
        $this->authorizeAdvertisement($advertisement);

        return view('marketplace.advertisement-form', compact('advertisement'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new Advertisement);
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $this->authorizeAdvertisement($advertisement);

        return $this->save($request, $advertisement);
    }

    private function save(Request $request, Advertisement $advertisement)
    {
        $data = $request->validate(['title' => 'required|string|max:220', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_url' => 'nullable|url:http,https|max:700', 'button_url' => 'nullable|url:http,https|max:700',
            'sort_order' => 'required|integer|min:0|max:100000', 'is_active' => 'nullable|boolean']);
        if (! $advertisement->exists && ! $request->hasFile('image') && ! filled($data['image_url'] ?? null)) {
            return back()->withErrors(['image' => 'Ajoutez une image ou renseignez son adresse.'])->withInput();
        }
        $oldImage = $advertisement->image_url;
        $image = $oldImage;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('advertisements', 'public');
        } elseif (filled($data['image_url'] ?? null)) {
            $image = $data['image_url'];
        }
        $advertisement->fill(['title' => $data['title'], 'image_url' => $image,
            'button_url' => $data['button_url'] ?? null, 'sort_order' => $data['sort_order'],
            'is_active' => $request->boolean('is_active')]);
        if (! $advertisement->exists) {
            $advertisement->placement = 'marketplace_catalog_'.Str::uuid();
        }
        $advertisement->save();
        if ($oldImage !== $image && $oldImage && str_starts_with($oldImage, 'advertisements/')) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('marketplace.advertisements.index')->with('success', 'Publicité enregistrée.');
    }

    public function destroy(Advertisement $advertisement)
    {
        $this->authorizeAdvertisement($advertisement);
        $image = $advertisement->image_url;
        $advertisement->delete();
        if ($image && str_starts_with($image, 'advertisements/')) {
            Storage::disk('public')->delete($image);
        }

        return back()->with('success', 'Publicité supprimée.');
    }
}
