<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::when(request('q'), function ($query, string $search): void {
            $query->where('code', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%');
        })->latest()->paginate(15)->withQueryString();

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        return view('admin.promotions.create', ['promotion' => new Promotion()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Promotion::create($this->validatedData($request));

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion ajoutee avec succes.');
    }

    public function show(Promotion $promotion): View
    {
        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion): View
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($this->validatedData($request, $promotion));

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion modifiee avec succes.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion supprimee avec succes.');
    }

    private function validatedData(Request $request, ?Promotion $promotion = null): array
    {
        $promotionId = $promotion?->id ?? 'NULL';

        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:60', 'unique:promotions,code,'.$promotionId],
            'name' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code'] ?: Str::slug($data['name'], '').random_int(10, 99));
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
