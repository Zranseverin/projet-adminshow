@php
    $isActive = old('is_active', $category->exists ? $category->is_active : true);
@endphp

@csrf
@isset($method)
    @method($method)
@endisset

<div class="form-grid">
    <div class="form-field">
        <label class="form-label" for="name">Nom</label>
        <input class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        @error('name') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
        <label class="form-label" for="slug">Slug</label>
        <input class="form-control" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="genere automatiquement si vide">
        @error('slug') <div class="form-error">{{ $message }}</div> @enderror
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
    <a class="btn btn-ghost" href="{{ route('admin.categories.index') }}">Annuler</a>
    <button class="btn btn-amber" type="submit">{{ $submitLabel }}</button>
</div>
