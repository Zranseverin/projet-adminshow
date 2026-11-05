<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BookCatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $coverUrl = $this->cover_image;
        $price = (float) $this->price;

        if ($coverUrl && ! Str::startsWith($coverUrl, ['http://', 'https://'])) {
            $coverUrl = route('media.show', ['path' => $coverUrl]);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'publisher' => $this->publisher,
            'publication_date' => $this->publication_date?->toDateString(),
            'page_count' => $this->page_count,
            'dimensions' => $this->dimensions,
            'translator' => $this->translator,
            'language' => $this->language,
            'collection' => $this->collection,
            'author' => $this->author?->name ?? 'Auteur inconnu',
            'author_name' => $this->author?->name ?? 'Auteur inconnu',
            'format' => $this->format,
            'price' => $price,
            'old_price' => $this->old_price !== null ? (float) $this->old_price : null,
            'available_formats' => $this->availableFormats($price),
            'stock' => $this->stock_quantity,
            'stock_quantity' => $this->stock_quantity,
            'stars' => (float) $this->rating,
            'rating' => (float) $this->rating,
            'reviews' => $this->review_count,
            'review_count' => $this->review_count,
            'emoji' => $this->cover_emoji ?: 'B',
            'cover' => $coverUrl,
            'cover_image' => $coverUrl,
            'bg' => $this->cover_background ?: 'linear-gradient(145deg,#2A5F8E,#1a3a5c)',
            'cover_background' => $this->cover_background,
            'is_featured' => $this->is_featured,
            'categories' => $this->categories->map(fn ($category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ])->values(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }

    private function availableFormats(float $price): array
    {
        if (is_array($this->book_formats) && count($this->book_formats) > 0) {
            return collect($this->book_formats)
                ->filter(fn ($format): bool => filled($format['name'] ?? null) && isset($format['price']))
                ->map(fn ($format): array => [
                    'name' => (string) $format['name'],
                    'price' => (float) $format['price'],
                ])
                ->values()
                ->all();
        }

        $baseFormat = $this->format ?: 'Poche';
        $multipliers = [
            'Poche' => 1,
            'Broche' => 1.45,
            'Ebook' => 0.65,
            'Grand format' => 1.75,
        ];

        $formats = collect(array_unique([$baseFormat, 'Poche', 'Broche', 'Ebook', 'Grand format']));

        return $formats->map(function (string $format) use ($baseFormat, $multipliers, $price): array {
            return [
                'name' => $format,
                'price' => $format === $baseFormat
                    ? $price
                    : round($price * ($multipliers[$format] ?? 1), -2),
            ];
        })->values()->all();
    }
}
