<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with(['author', 'categories'])
            ->when(request('q'), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('isbn', 'like', '%'.$search.'%')
                        ->orWhereHas('author', fn ($query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when(request('format'), fn ($query, string $format) => $query->where('format', $format))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.books.index', compact('books'));
    }

    public function create(): View
    {
        return view('admin.books.create', [
            'authors' => Author::orderBy('name')->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        if ($request->hasFile('cover_file')) {
            $data['cover_image'] = $request->file('cover_file')->store('book-covers', 'public');
        }

        $data['created_by'] = Auth::id();

        $book = Book::create($data);
        $book->categories()->sync($categoryIds);

        return redirect()->route('admin.books.index')->with('success', 'Livre ajoute avec succes.');
    }

    public function show(Book $book): View
    {
        $book->load(['author', 'categories', 'creator']);

        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $book->load('categories');

        return view('admin.books.edit', [
            'book' => $book,
            'authors' => Author::orderBy('name')->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'selectedCategories' => $book->categories->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validatedData($request, $book);
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        if ($request->hasFile('cover_file')) {
            if ($book->cover_image && ! Str::startsWith($book->cover_image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $data['cover_image'] = $request->file('cover_file')->store('book-covers', 'public');
        }

        $book->update($data);
        $book->categories()->sync($categoryIds);

        return redirect()->route('admin.books.index')->with('success', 'Livre modifie avec succes.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Livre supprime avec succes.');
    }

    private function validatedData(Request $request, ?Book $book = null): array
    {
        $bookId = $book?->id ?? 'NULL';

        $data = $request->validate([
            'author_id' => ['nullable', 'exists:authors,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:220'],
            'slug' => ['nullable', 'string', 'max:240', 'unique:books,slug,'.$bookId],
            'isbn' => ['nullable', 'string', 'max:30', 'unique:books,isbn,'.$bookId],
            'description' => ['nullable', 'string'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'date'],
            'page_count' => ['nullable', 'integer', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:80'],
            'translator' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:80'],
            'collection' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'cover_file' => ['nullable', 'image', 'max:4096'],
            'cover_background' => ['nullable', 'string', 'max:255'],
            'cover_emoji' => ['nullable', 'string', 'max:20'],
            'format' => ['required', 'string', 'max:80'],
            'book_formats' => ['nullable', 'array'],
            'book_formats.*.name' => ['nullable', 'string', 'max:80'],
            'book_formats.*.price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['rating'] = $data['rating'] ?? 0;
        $data['review_count'] = $data['review_count'] ?? 0;
        $data['book_formats'] = $this->normalizeFormats($data['book_formats'] ?? [], $data['format'], (float) $data['price']);

        return $data;
    }

    private function normalizeFormats(array $formats, string $baseFormat, float $basePrice): array
    {
        $formats[] = ['name' => $baseFormat, 'price' => $basePrice];

        return collect($formats)
            ->filter(fn ($format): bool => filled($format['name'] ?? null) && isset($format['price']) && is_numeric($format['price']))
            ->map(fn ($format): array => [
                'name' => trim((string) $format['name']),
                'price' => (float) $format['price'],
            ])
            ->unique('name')
            ->values()
            ->all();
    }
}
