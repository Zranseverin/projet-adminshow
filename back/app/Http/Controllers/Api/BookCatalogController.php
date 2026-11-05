<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookCatalogRequest;
use App\Http\Requests\Api\StoreBookRequest;
use App\Http\Resources\BookCatalogResource;
use App\Http\Resources\CategoryCatalogResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookCatalogController extends Controller
{
    public function index(BookCatalogRequest $request): AnonymousResourceCollection
    {
        $books = Book::with(['author', 'categories'])
            ->where('is_active', true)
            ->when($request->validated('q'), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('isbn', 'like', '%'.$search.'%')
                        ->orWhereHas('author', fn ($query) => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($this->listInput($request, 'format'), fn ($query, array $formats) => $query->whereIn('format', $formats))
            ->when($this->listInput($request, 'category'), function ($query, array $categories): void {
                $query->whereHas('categories', fn ($query) => $query->whereIn('slug', $categories)->orWhereIn('name', $categories));
            })
            ->when($this->listInput($request, 'author'), function ($query, array $authors): void {
                $query->whereHas('author', fn ($query) => $query->whereIn('slug', $authors)->orWhereIn('name', $authors));
            })
            ->when($request->validated('min_price'), fn ($query, $price) => $query->where('price', '>=', $price))
            ->when($request->validated('max_price'), fn ($query, $price) => $query->where('price', '<=', $price))
            ->when($request->boolean('featured'), fn ($query) => $query->where('is_featured', true))
            ->latest()
            ->paginate((int) $request->input('per_page', 24));

        return BookCatalogResource::collection($books);
    }

    public function show(Book $book): BookCatalogResource
    {
        abort_unless($book->is_active, 404);

        return new BookCatalogResource($book->load(['author', 'categories']));
    }

    public function related(Book $book): AnonymousResourceCollection
    {
        abort_unless($book->is_active, 404);

        $categoryIds = $book->categories()->pluck('categories.id');

        $books = Book::with(['author', 'categories'])
            ->where('is_active', true)
            ->whereKeyNot($book->id)
            ->when($categoryIds->isNotEmpty(), function ($query) use ($categoryIds): void {
                $query->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $categoryIds));
            })
            ->latest()
            ->limit(8)
            ->get();

        return BookCatalogResource::collection($books);
    }

    public function store(StoreBookRequest $request): BookCatalogResource
    {
        $data = $request->validated();
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['rating'] = $data['rating'] ?? 0;
        $data['review_count'] = $data['review_count'] ?? 0;

        if ($request->hasFile('cover_file')) {
            $data['cover_image'] = $request->file('cover_file')->store('book-covers', 'public');
        }

        $book = Book::create($data);
        $book->categories()->sync($categoryIds);

        return new BookCatalogResource($book->load(['author', 'categories']));
    }

    public function update(StoreBookRequest $request, Book $book): BookCatalogResource
    {
        $data = $request->validated();
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['category_ids']);

        if (array_key_exists('is_featured', $data)) {
            $data['is_featured'] = (bool) $data['is_featured'];
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        if ($request->hasFile('cover_file')) {
            if ($book->cover_image && ! Str::startsWith($book->cover_image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $data['cover_image'] = $request->file('cover_file')->store('book-covers', 'public');
        }

        $book->update($data);

        if ($categoryIds !== null) {
            $book->categories()->sync($categoryIds);
        }

        return new BookCatalogResource($book->load(['author', 'categories']));
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json([
            'message' => 'Livre supprime avec succes.',
        ]);
    }

    public function categories(): AnonymousResourceCollection
    {
        $categories = Category::withCount(['books' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return CategoryCatalogResource::collection($categories);
    }

    public function filters(): JsonResponse
    {
        $categories = Category::withCount(['books' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'books_count' => $category->books_count,
            ]);

        $authors = Author::withCount(['books' => fn ($query) => $query->where('is_active', true)])
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn (Author $author): array => [
                'id' => $author->id,
                'name' => $author->name,
                'slug' => $author->slug,
                'books_count' => $author->books_count,
            ]);

        $formats = Book::query()
            ->select('format', DB::raw('count(*) as books_count'))
            ->where('is_active', true)
            ->whereNotNull('format')
            ->groupBy('format')
            ->orderBy('format')
            ->get()
            ->map(fn (Book $book): array => [
                'name' => $book->format,
                'slug' => Str::slug($book->format),
                'books_count' => (int) $book->books_count,
            ]);

        return response()->json([
            'data' => [
                'categories' => $categories,
                'authors' => $authors,
                'formats' => $formats,
                'price' => [
                    'min' => (float) Book::where('is_active', true)->min('price'),
                    'max' => (float) Book::where('is_active', true)->max('price'),
                ],
            ],
        ]);
    }

    private function listInput(Request $request, string $key): array
    {
        $value = $request->input($key, []);

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        return collect($value)
            ->filter(fn ($item) => filled($item))
            ->map(fn ($item) => (string) $item)
            ->values()
            ->all();
    }
}
