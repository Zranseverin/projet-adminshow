<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::with(['book', 'user'])->latest()->paginate(15);
        $recentMovements = StockMovement::with(['book', 'user'])->latest()->limit(8)->get();
        $books = Book::with('author')
            ->withSum('orderItems as sold_units', 'quantity')
            ->orderBy('title')
            ->get();
        $lowStockBooks = $books->where('stock_quantity', '<', 10)->sortBy('stock_quantity')->values();
        $totalSoldMonth = abs((int) StockMovement::where('type', 'sale')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity'));
        $availableBooksCount = $books
            ->filter(fn (Book $book): bool => $book->is_active && (int) $book->stock_quantity > 0)
            ->count();

        return view('admin.stocks.index', [
            'movements' => $movements,
            'recentMovements' => $recentMovements,
            'books' => $books,
            'lowStockBooks' => $lowStockBooks,
            'totalSoldMonth' => $totalSoldMonth,
            'availableBooksCount' => $availableBooksCount,
        ]);
    }

    public function create(): View
    {
        return view('admin.stocks.create', [
            'movement' => new StockMovement(),
            'books' => Book::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'source' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data): void {
            $book = Book::lockForUpdate()->findOrFail($data['book_id']);
            $before = (int) $book->stock_quantity;
            $delta = $data['type'] === 'out' ? -((int) $data['quantity']) : (int) $data['quantity'];
            $after = max(0, $before + $delta);

            $book->update(['stock_quantity' => $after]);

            StockMovement::create([
                ...$data,
                'quantity' => $delta,
                'stock_before' => $before,
                'stock_after' => $after,
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.stocks.index')->with('success', 'Stock mis a jour avec succes.');
    }

    public function show(StockMovement $stock): View
    {
        $stock->load(['book', 'user']);

        return view('admin.stocks.show', ['movement' => $stock]);
    }

    public function destroy(StockMovement $stock): RedirectResponse
    {
        $stock->delete();

        return redirect()->route('admin.stocks.index')->with('success', 'Mouvement supprime.');
    }
}
