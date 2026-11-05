@csrf
@isset($method)
    @method($method)
@endisset

<div class="form-grid">
    <div class="form-field">
        <label class="form-label" for="name">Nom</label>
        <input class="form-control" id="name" name="name" value="{{ old('name', $author->name) }}" required>
        @error('name') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="slug">Slug</label>
        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $author->slug) }}" placeholder="genere automatiquement si vide">
        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field full">
        <label class="form-label" for="bio">Biographie</label>
        <textarea class="form-control" id="bio" name="bio">{{ old('bio', $author->bio) }}</textarea>
        @error('bio') <div class="form-error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="action-row">
    <a class="btn btn-ghost" href="{{ route('admin.authors.index') }}">Annuler</a>
    <button class="btn btn-amber" type="submit">{{ $submitLabel }}</button>
</div>
