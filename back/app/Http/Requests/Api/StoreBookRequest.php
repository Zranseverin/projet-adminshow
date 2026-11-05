<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $book = $this->route('book');
        $bookId = $book?->id ?? 'NULL';
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            'author_id' => ['nullable', 'exists:authors,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'title' => [$required, 'string', 'max:220'],
            'slug' => ['nullable', 'string', 'max:240', Rule::unique('books', 'slug')->ignore($bookId)],
            'isbn' => ['nullable', 'string', 'max:30', Rule::unique('books', 'isbn')->ignore($bookId)],
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
            'format' => [$required, 'string', 'max:80'],
            'book_formats' => ['nullable', 'array'],
            'book_formats.*.name' => ['nullable', 'string', 'max:80'],
            'book_formats.*.price' => ['nullable', 'numeric', 'min:0'],
            'price' => [$required, 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => [$required, 'integer', 'min:0'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if (is_array($data) && array_key_exists('title', $data) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (is_array($data) && isset($data['format'], $data['price'])) {
            $formats = $data['book_formats'] ?? [];
            $formats[] = ['name' => $data['format'], 'price' => $data['price']];
            $data['book_formats'] = collect($formats)
                ->filter(fn ($format): bool => filled($format['name'] ?? null) && isset($format['price']) && is_numeric($format['price']))
                ->map(fn ($format): array => [
                    'name' => trim((string) $format['name']),
                    'price' => (float) $format['price'],
                ])
                ->unique('name')
                ->values()
                ->all();
        }

        return $data;
    }
}
