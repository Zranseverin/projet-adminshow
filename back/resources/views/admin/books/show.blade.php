@extends('layouts.app')

@section('title', $book->title)
@section('page-title', $book->title)

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Details du livre</h1>
            <div class="table-actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.books.index') }}">Retour</a>
                <a class="btn btn-amber btn-sm" href="{{ route('admin.books.edit', $book) }}">Modifier</a>
            </div>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Titre</div><div class="detail-value">{{ $book->title }}</div></div>
                <div class="detail-item"><div class="detail-label">Auteur</div><div class="detail-value">{{ $book->author?->name ?? 'Auteur inconnu' }}</div></div>
                <div class="detail-item"><div class="detail-label">Prix</div><div class="detail-value">{{ number_format($book->price, 0, ',', ' ') }} FCFA</div></div>
                <div class="detail-item"><div class="detail-label">Format</div><div class="detail-value">{{ $book->format }}</div></div>
                <div class="detail-item" style="grid-column: 1 / -1">
                    <div class="detail-label">Formats disponibles</div>
                    <div class="detail-value">
                        @forelse (($book->book_formats ?: [['name' => $book->format, 'price' => $book->price]]) as $format)
                            <span class="badge badge-format">{{ $format['name'] ?? '-' }} : {{ number_format((float) ($format['price'] ?? 0), 0, ',', ' ') }} FCFA</span>
                        @empty
                            -
                        @endforelse
                    </div>
                </div>
                <div class="detail-item"><div class="detail-label">Stock</div><div class="detail-value">{{ $book->stock_quantity }}</div></div>
                <div class="detail-item"><div class="detail-label">ISBN</div><div class="detail-value">{{ $book->isbn ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Editeur</div><div class="detail-value">{{ $book->publisher ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Date de parution</div><div class="detail-value">{{ $book->publication_date?->format('d/m/Y') ?? '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Nombre de pages</div><div class="detail-value">{{ $book->page_count ? $book->page_count.' pages' : '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Dimensions</div><div class="detail-value">{{ $book->dimensions ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Traducteur</div><div class="detail-value">{{ $book->translator ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Langue</div><div class="detail-value">{{ $book->language ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Collection</div><div class="detail-value">{{ $book->collection ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Categories</div><div class="detail-value">{{ $book->categories->pluck('name')->join(', ') ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Statut</div><div class="detail-value">{{ $book->is_active ? 'Visible' : 'Masque' }}</div></div>
                <div class="detail-item full" style="grid-column: 1 / -1">
                    <div class="detail-label">Description</div>
                    <div class="detail-value">{{ $book->description ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
