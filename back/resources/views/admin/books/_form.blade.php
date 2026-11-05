@php
    $selectedCategories = collect(old('category_ids', $selectedCategories ?? $book?->categories?->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id)->all();
    $isActive = old('is_active', $book->exists ? $book->is_active : true);
    $isFeatured = old('is_featured', $book->is_featured ?? false);
    $formatRows = collect(old('book_formats', $book->book_formats ?: [
        ['name' => $book->format ?: 'Poche', 'price' => $book->price ?? 0],
    ]))->values();
@endphp

@csrf
@isset($method)
    @method($method)
@endisset

<div class="form-grid">
    <div class="form-field">
        <label class="form-label" for="title">Titre</label>
        <input class="form-control" id="title" name="title" value="{{ old('title', $book->title) }}" required>
        @error('title') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="slug">Slug</label>
        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $book->slug) }}" placeholder="genere automatiquement si vide">
        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="author_id">Auteur</label>
        <select class="form-control" id="author_id" name="author_id">
            <option value="">Auteur inconnu</option>
            @foreach ($authors as $author)
                <option value="{{ $author->id }}" @selected((int) old('author_id', $book->author_id) === $author->id)>{{ $author->name }}</option>
            @endforeach
        </select>
        @error('author_id') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="category_ids">Categories</label>
        <select class="form-control" id="category_ids" name="category_ids[]" multiple>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, $selectedCategories, true))>{{ $category->name }}</option>
            @endforeach
        </select>
        <div class="form-help">Maintenir Ctrl pour choisir plusieurs categories.</div>
        @error('category_ids') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="isbn">ISBN</label>
        <input class="form-control" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}">
        @error('isbn') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="publisher">Editeur</label>
        <input class="form-control" id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}" placeholder="Robert Laffont / Pocket">
        @error('publisher') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="publication_date">Date de parution</label>
        <input class="form-control" id="publication_date" name="publication_date" type="date" value="{{ old('publication_date', $book->publication_date?->format('Y-m-d')) }}">
        @error('publication_date') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="page_count">Nombre de pages</label>
        <input class="form-control" id="page_count" name="page_count" type="number" min="0" value="{{ old('page_count', $book->page_count) }}" placeholder="896">
        @error('page_count') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="dimensions">Dimensions</label>
        <input class="form-control" id="dimensions" name="dimensions" value="{{ old('dimensions', $book->dimensions) }}" placeholder="12 x 17.5 cm">
        @error('dimensions') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="translator">Traducteur</label>
        <input class="form-control" id="translator" name="translator" value="{{ old('translator', $book->translator) }}" placeholder="Michel Demuth">
        @error('translator') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="language">Langue</label>
        <input class="form-control" id="language" name="language" value="{{ old('language', $book->language) }}" placeholder="Francais">
        @error('language') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="collection">Collection</label>
        <input class="form-control" id="collection" name="collection" value="{{ old('collection', $book->collection) }}" placeholder="Pocket Science-Fiction">
        @error('collection') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="format">Format</label>
        <select class="form-control" id="format" name="format" required>
            @foreach (['Poche', 'Broche', 'Ebook', 'Audio'] as $format)
                <option value="{{ $format }}" @selected(old('format', $book->format ?: 'Poche') === $format)>{{ $format }}</option>
            @endforeach
        </select>
        @error('format') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="price">Prix</label>
        <input class="form-control" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $book->price ?? 0) }}" required>
        @error('price') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field full">
        <label class="form-label">Formats disponibles pour ce livre</label>
        <div class="form-help">Le premier format/prix reste le format principal. Le client verra tous ces formats dans la fiche produit.</div>
        <div id="bookFormatsRows" style="display:grid;gap:10px;margin-top:10px">
            @foreach ($formatRows as $index => $row)
                <div class="format-row" style="display:grid;grid-template-columns:1fr 160px auto;gap:10px;align-items:center">
                    <input class="form-control" name="book_formats[{{ $index }}][name]" value="{{ $row['name'] ?? '' }}" placeholder="Poche, Broche, Ebook...">
                    <input class="form-control" name="book_formats[{{ $index }}][price]" type="number" step="0.01" min="0" value="{{ $row['price'] ?? 0 }}" placeholder="Prix FCFA">
                    <button class="btn btn-ghost btn-sm" type="button" onclick="this.closest('.format-row').remove()">Retirer</button>
                </div>
            @endforeach
        </div>
        <button class="btn btn-ghost btn-sm" type="button" style="margin-top:10px" onclick="addBookFormatRow()">Ajouter un format</button>
        @error('book_formats') <div class="form-error">{{ $message }}</div> @enderror
        @error('book_formats.*.name') <div class="form-error">{{ $message }}</div> @enderror
        @error('book_formats.*.price') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="old_price">Ancien prix</label>
        <input class="form-control" id="old_price" name="old_price" type="number" step="0.01" min="0" value="{{ old('old_price', $book->old_price) }}">
        @error('old_price') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="stock_quantity">Stock</label>
        <input class="form-control" id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $book->stock_quantity ?? 0) }}" required>
        @error('stock_quantity') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="rating">Note</label>
        <input class="form-control" id="rating" name="rating" type="number" step="0.1" min="0" max="5" value="{{ old('rating', $book->rating ?? 0) }}">
        @error('rating') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="review_count">Nombre d'avis</label>
        <input class="form-control" id="review_count" name="review_count" type="number" min="0" value="{{ old('review_count', $book->review_count ?? 0) }}">
        @error('review_count') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="cover_emoji">Marque couverture</label>
        <input class="form-control" id="cover_emoji" name="cover_emoji" value="{{ old('cover_emoji', $book->cover_emoji ?: 'B') }}" maxlength="20">
        @error('cover_emoji') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field full">
        <label class="form-label" for="cover_file">Importer une couverture</label>
        <input class="form-control" id="cover_file" name="cover_file" type="file" accept="image/*">
        <div class="form-help">Formats image uniquement, 4 Mo maximum.</div>
        @error('cover_file') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field full">
        <label class="form-label" for="cover_image">Ou URL directe de couverture</label>
        <input class="form-control" id="cover_image" name="cover_image" value="{{ old('cover_image', $book->cover_image) }}" placeholder="https://exemple.com/couverture.jpg">
        <div class="form-help">Utiliser une URL directe vers une image, pas une page web.</div>
        @error('cover_image') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    @if ($book->cover_image)
        <div class="form-field full">
            <label class="form-label">Apercu actuel</label>
            <div class="book-cell">
                <span class="cover-thumb" style="background: {{ $book->cover_background ?: 'linear-gradient(145deg,#2A5F8E,#1a3a5c)' }}">
                    <img src="{{ \Illuminate\Support\Str::startsWith($book->cover_image, ['http://', 'https://']) ? $book->cover_image : asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }}">
                </span>
                <span class="book-author">{{ $book->cover_image }}</span>
            </div>
        </div>
    @endif

    <div class="form-field full">
        <label class="form-label" for="cover_background">Fond couverture</label>
        <input class="form-control" id="cover_background" name="cover_background" value="{{ old('cover_background', $book->cover_background ?: 'linear-gradient(145deg,#2A5F8E,#1a3a5c)') }}">
        @error('cover_background') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field full">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description">{{ old('description', $book->description) }}</textarea>
        @error('description') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <input type="hidden" name="is_featured" value="0">
        <label class="checkbox-row">
            <input type="checkbox" name="is_featured" value="1" @checked((bool) $isFeatured)>
            Mettre en avant
        </label>
    </div>

    <div class="form-field">
        <input type="hidden" name="is_active" value="0">
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked((bool) $isActive)>
            Visible sur le site
        </label>
    </div>
</div>

<div class="action-row">
    <a class="btn btn-ghost" href="{{ route('admin.books.index') }}">Annuler</a>
    <button class="btn btn-amber" type="submit">{{ $submitLabel }}</button>
</div>

@push('scripts')
    <script>
        function addBookFormatRow() {
            const container = document.getElementById('bookFormatsRows');
            const index = container.querySelectorAll('.format-row').length;
            const row = document.createElement('div');
            row.className = 'format-row';
            row.style = 'display:grid;grid-template-columns:1fr 160px auto;gap:10px;align-items:center';
            row.innerHTML = `
                <input class="form-control" name="book_formats[${index}][name]" placeholder="Poche, Broche, Ebook...">
                <input class="form-control" name="book_formats[${index}][price]" type="number" step="0.01" min="0" placeholder="Prix FCFA">
                <button class="btn btn-ghost btn-sm" type="button" onclick="this.closest('.format-row').remove()">Retirer</button>
            `;
            container.appendChild(row);
        }
    </script>
@endpush
