@extends('layouts.app')

@section('title', $book->title)
@section('page-title', 'Fiche du livre')

@push('styles')
<style>
    .book-detail { max-width: 1240px; margin: 0 auto; }
    .book-detail a:focus-visible { outline: 3px solid var(--amber); outline-offset: 4px; }
    .book-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
    .book-toolbar .btn { min-height: 44px; }
    .book-eyebrow { color: var(--muted); font-size: 12px; letter-spacing: .1em; text-transform: uppercase; font-weight: 600; }
    .book-layout { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: 24px; align-items: start; }
    .book-panel { background: var(--white); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; min-width: 0; }
    .book-cover { background: var(--sand); padding: 32px; display: grid; place-items: center; min-height: 330px; }
    .book-cover img { display: block; width: 100%; height: 280px; object-fit: contain; filter: drop-shadow(0 10px 12px rgba(28,43,58,.16)); }
    .book-cover-placeholder { text-align: center; color: var(--muted); display: grid; gap: 16px; justify-items: center; }
    .book-cover-placeholder svg { width: 64px; height: 64px; }
    .book-summary { padding: 24px; }
    .book-price { font-size: 28px; color: var(--navy); font-weight: 700; margin: 8px 0; }
    .book-price small { font-size: 14px; font-weight: 500; }
    .book-old-price { color: var(--muted); font-size: 14px; }
    .book-stock { border-top: 1px solid var(--border); margin-top: 20px; padding-top: 20px; display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
    .book-stock strong { font-size: 24px; }
    .book-main { display: grid; gap: 20px; min-width: 0; }
    .book-intro { padding: 28px; }
    .book-title { font-family: var(--font-serif); font-size: clamp(24px, 3vw, 34px); line-height: 1.25; margin: 14px 0 10px; overflow-wrap: anywhere; }
    .book-author { color: var(--muted); font-size: 16px; line-height: 1.6; }
    .book-tags { display: flex; flex-wrap: wrap; gap: 8px; }
    .book-tag { display: inline-block; padding: 6px 10px; border-radius: 6px; background: var(--sand); color: var(--ink); font-size: 12px; font-weight: 600; }
    .book-tag.visible { background: var(--green-bg); color: var(--green); }
    .book-tag.featured { background: var(--amber-light); color: #74470c; }
    .book-section { padding: 24px 28px; }
    .book-section h2 { font-size: 17px; color: var(--navy); margin: 0 0 20px; }
    .book-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px 28px; margin: 0; }
    .book-fields dt { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
    .book-fields dd { margin: 0; color: var(--ink); font-size: 14px; line-height: 1.6; overflow-wrap: anywhere; }
    .book-description { white-space: pre-line; overflow-wrap: anywhere; line-height: 1.85; color: var(--ink); font-size: 15px; }
    .book-formats { display: grid; gap: 10px; }
    .book-format { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 14px 16px; background: var(--cream); border: 1px solid var(--border); border-radius: 8px; font-size: 14px; }
    .book-format small { display: block; color: var(--muted); margin-top: 4px; }
    .book-categories { margin-top: 22px; padding-top: 22px; border-top: 1px solid var(--border); }
    .book-categories h3 { font-size: 12px; color: var(--muted); font-weight: 500; margin-bottom: 10px; }
    @media (max-width: 1050px) { .book-layout { grid-template-columns: 240px minmax(0, 1fr); gap: 16px; } .book-cover { padding: 24px; } }
    @media (max-width: 760px) { .book-layout { grid-template-columns: 1fr; } .book-cover img { height: 240px; } .book-cover { min-height: 280px; } .book-intro, .book-section { padding: 22px; } }
    @media (max-width: 440px) { .book-fields { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    @php
        $formats = $book->book_formats ?: [['name' => $book->format, 'price' => $book->price]];
        $bibliography = [
            'ISBN' => $book->isbn,
            'Éditeur' => $book->publisher,
            'Date de parution' => $book->publication_date?->format('d/m/Y'),
            'Nombre de pages' => $book->page_count !== null ? $book->page_count.' pages' : null,
            'Dimensions' => $book->dimensions,
            'Traducteur' => $book->translator,
            'Langue' => $book->language,
            'Collection' => $book->collection,
            'Format principal' => $book->format,
        ];
    @endphp
    <div class="book-detail">
        <div class="book-toolbar">
            <a class="btn btn-ghost" href="{{ route('admin.books.index') }}">← Retour aux livres</a>
            <a class="btn btn-amber" href="{{ route('admin.books.edit', $book) }}">Modifier le livre</a>
        </div>
        <div class="book-layout">
            <aside class="book-panel" aria-label="Couverture, prix et stock">
                <div class="book-cover">
                    @if ($book->cover_image)
                        <img src="{{ \Illuminate\Support\Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : route('media.show', ['path' => $book->cover_image]) }}" alt="Couverture de {{ $book->title }}" width="220" height="280">
                    @else
                        <div class="book-cover-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" aria-hidden="true"><path d="M12 5c-3-2-7-2-10-1v15c3-1 7-1 10 1 3-2 7-2 10-1V4c-3-1-7-1-10 1Zm0 0v15"/></svg>
                            <span>Couverture non renseignée</span>
                        </div>
                    @endif
                </div>
                <div class="book-summary">
                    <div class="book-eyebrow">Prix de vente</div>
                    <div class="book-price">{{ number_format((float) $book->price, 0, ',', ' ') }} <small>FCFA</small></div>
                    <div class="book-old-price">Ancien prix : @if ($book->old_price !== null)<s>{{ number_format((float) $book->old_price, 0, ',', ' ') }} FCFA</s>@else Non renseigné @endif</div>
                    <div class="book-stock"><span>{{ $book->stock_quantity > 0 ? 'En stock' : 'Rupture de stock' }}</span><strong>{{ $book->stock_quantity }} <small>ex.</small></strong></div>
                </div>
            </aside>
            <div class="book-main">
                <section class="book-panel book-intro" aria-labelledby="book-title">
                    <div class="book-tags">
                        <span class="book-tag {{ $book->is_active ? 'visible' : '' }}">{{ $book->is_active ? 'Visible en boutique' : 'Masqué en boutique' }}</span>
                        <span class="book-tag {{ $book->is_featured ? 'featured' : '' }}">{{ $book->is_featured ? 'Mis en avant' : 'Non mis en avant' }}</span>
                    </div>
                    <h1 class="book-title" id="book-title">{{ $book->title }}</h1>
                    <p class="book-author">{{ $book->author?->name ?? 'Auteur non renseigné' }}</p>
                    <div class="book-categories">
                        <h3>Catégories</h3>
                        <div class="book-tags">
                            @forelse ($book->categories as $category)
                                <span class="book-tag">{{ $category->name }}</span>
                            @empty
                                <span>Aucune catégorie renseignée</span>
                            @endforelse
                        </div>
                    </div>
                </section>
                <section class="book-panel book-section" aria-labelledby="book-description-title">
                    <h2 id="book-description-title">À propos du livre</h2>
                    <p class="book-description">{{ $book->description ?: 'Aucune description renseignée.' }}</p>
                </section>
                <section class="book-panel book-section" aria-labelledby="book-bibliography-title">
                    <h2 id="book-bibliography-title">Informations bibliographiques</h2>
                    <dl class="book-fields">
                        @foreach ($bibliography as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ filled($value) ? $value : 'Non renseigné' }}</dd></div>
                        @endforeach
                    </dl>
                </section>
                <section class="book-panel book-section" aria-labelledby="book-formats-title">
                    <h2 id="book-formats-title">Formats et tarifs</h2>
                    <div class="book-formats">
                        @foreach ($formats as $format)
                            <div class="book-format">
                                <div>{{ $format['name'] ?? 'Format non renseigné' }}@if (($format['name'] ?? null) === $book->format)<small>Format principal</small>@endif</div>
                                <strong>{{ number_format((float) ($format['price'] ?? 0), 0, ',', ' ') }} FCFA</strong>
                            </div>
                        @endforeach
                    </div>
                </section>
                <section class="book-panel book-section" aria-labelledby="book-tracking-title">
                    <h2 id="book-tracking-title">Évaluation et suivi</h2>
                    <dl class="book-fields">
                        <div><dt>Note moyenne</dt><dd>{{ number_format((float) $book->rating, 1, ',', ' ') }} / 5</dd></div>
                        <div><dt>Nombre d’avis</dt><dd>{{ $book->review_count ?? 0 }}</dd></div>
                        <div><dt>Référence interne</dt><dd>#{{ $book->id }}</dd></div>
                        <div><dt>Identifiant boutique (slug)</dt><dd>{{ $book->slug ?: 'Non renseigné' }}</dd></div>
                        <div><dt>Ajouté par</dt><dd>{{ $book->creator?->name ?? 'Non renseigné' }}</dd></div>
                        <div><dt>Date de création</dt><dd>{{ $book->created_at?->format('d/m/Y à H:i') ?? 'Non renseignée' }}</dd></div>
                        <div><dt>Dernière modification</dt><dd>{{ $book->updated_at?->format('d/m/Y à H:i') ?? 'Non renseignée' }}</dd></div>
                        <div><dt>Fond de couverture</dt><dd>{{ $book->cover_background ?: 'Non renseigné' }}</dd></div>
                        <div><dt>Symbole de couverture</dt><dd>{{ $book->cover_emoji ?: 'Non renseigné' }}</dd></div>
                        <div><dt>Fichier de couverture</dt><dd>{{ $book->cover_image ?: 'Non renseigné' }}</dd></div>
                    </dl>
                </section>
            </div>
        </div>
    </div>
@endsection
