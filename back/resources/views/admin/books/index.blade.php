@extends('layouts.app')

@section('title', 'Catalogue livres')
@section('page-title', 'Catalogue livres')

@section('content')
    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <form class="toolbar" method="GET" action="{{ route('admin.books.index') }}" style="flex: 1">
                <label class="search-field">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un livre, un auteur...">
                </label>
                <select class="select-filter" name="format" style="max-width: 170px">
                    <option value="">Tous les formats</option>
                    @foreach (['Poche', 'Broche', 'Ebook', 'Audio'] as $format)
                        <option value="{{ $format }}" @selected(request('format') === $format)>{{ $format }}</option>
                    @endforeach
                </select>
                <button class="btn btn-ghost btn-sm" type="submit">Filtrer</button>
            </form>
            <a class="btn btn-amber btn-sm" href="{{ route('admin.books.create') }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nouveau livre
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Prix</th>
                        <th>Format</th>
                        <th>Note</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td>
                                <div class="book-cell">
                                    <span class="cover-thumb" style="background: {{ $book->cover_background ?: 'linear-gradient(145deg,#2A5F8E,#1a3a5c)' }}">
                                        @if ($book->cover_image)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : route('media.show', ['path' => $book->cover_image]) }}" alt="{{ $book->title }}">
                                        @else
                                            {{ $book->cover_emoji ?: 'B' }}
                                        @endif
                                    </span>
                                    <span>
                                        <span class="book-title">{{ $book->title }}</span>
                                        <span class="book-author">{{ $book->author?->name ?? 'Auteur inconnu' }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>{{ number_format($book->price, 0, ',', ' ') }} FCFA</td>
                            <td><span class="badge badge-format">{{ $book->format }}</span></td>
                            <td>{{ number_format($book->rating, 1, ',', ' ') }} / 5</td>
                            <td>
                                <span class="badge {{ $book->stock_quantity < 10 ? 'badge-low' : 'badge-stock' }}">
                                    {{ $book->stock_quantity }} unite{{ $book->stock_quantity > 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $book->is_active ? 'badge-stock' : 'badge-muted' }}">
                                    {{ $book->is_active ? 'Visible' : 'Masque' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.books.show', $book) }}">Voir</a>
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.books.edit', $book) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">Aucun livre enregistre pour le moment.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($books->hasPages())
            <div class="pagination-wrap">{{ $books->links() }}</div>
        @endif
    </div>
@endsection
