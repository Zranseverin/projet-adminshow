@extends('marketplace.layout')
@section('title', $product->exists ? 'Modifier le produit' : 'Nouveau produit')
@section('content')
<div class="heading"><div><h1>{{ $product->exists ? 'Modifier le produit' : 'Un nouveau produit' }}</h1><p class="muted">Présentez votre offre avec des informations précises et une photo.</p></div><a href="{{ route($prefix.'.products.index') }}">Retour au catalogue</a></div>
<form method="post" enctype="multipart/form-data" action="{{ $product->exists ? route($prefix.'.products.update', $product) : route($prefix.'.products.store') }}">@csrf @if($product->exists)@method('put')@endif
<div class="card grid">
    <div class="full"><label for="name">Nom du produit</label><input id="name" name="name" required maxlength="220" value="{{ old('name', $product->name) }}"></div>
    <div><label for="category_id">Catégorie</label><select id="category_id" name="category_id"><option value="">Sans catégorie</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div><label for="brand">Marque</label><input id="brand" name="brand" value="{{ old('brand', $product->brand) }}" maxlength="120"></div>
    <div><label for="sku">Référence / SKU</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" maxlength="100"></div>
    <div><label for="stock">Quantité en stock</label><input id="stock" type="number" name="stock" min="0" max="10000000" required value="{{ old('stock', $product->stock) }}"></div>
    <div><label for="price">Prix (FCFA)</label><input id="price" name="price" type="number" step="0.01" min="0" required value="{{ old('price', $product->price_minor !== null ? $product->price_minor / 100 : '') }}"></div>
    <div><label for="old_price">Ancien prix (facultatif)</label><input id="old_price" name="old_price" type="number" step="0.01" min="0" value="{{ old('old_price', $product->old_price_minor !== null ? $product->old_price_minor / 100 : '') }}"></div>
    <div class="full"><label for="description">Description</label><textarea id="description" name="description">{{ old('description', $product->description) }}</textarea></div>
    <div class="full"><label for="photo">Photo du produit</label>@if($product->image_url)<img class="photo" src="{{ $product->image_url }}" alt="Photo actuelle">@endif<input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp"><small class="muted">JPG, PNG ou WebP, 4 Mo maximum. Laissez vide pour conserver la photo actuelle.</small></div>
</div>
<div class="card"><h2>Caractéristiques</h2><p class="muted">Par exemple : couleur, taille, matière, capacité ou ISBN.</p>
    @php($attributes = $product->attributes ?? [])
    @for($i = 0; $i < max(6, count($attributes)); $i++)
        <div class="attributes"><input aria-label="Caractéristique {{ $i + 1 }}" name="attribute_names[]" placeholder="Caractéristique" value="{{ old('attribute_names.'.$i, array_keys($attributes)[$i] ?? '') }}"><input aria-label="Valeur {{ $i + 1 }}" name="attribute_values[]" placeholder="Valeur" value="{{ old('attribute_values.'.$i, array_values($attributes)[$i] ?? '') }}"></div>
    @endfor
</div>
<div class="card grid">
@if($prefix === 'marketplace')
    @if(!$product->exists)<div><label for="seller_id">Boutique</label><select id="seller_id" name="seller_id"><option value="">Boutique de la plateforme</option>@foreach($sellers as $seller)<option value="{{ $seller->id }}" @selected(old('seller_id') == $seller->id)>{{ $seller->name }}</option>@endforeach</select></div>@endif
    <div><label for="status">Publication</label><select id="status" name="status">@foreach(['draft'=>'Brouillon','pending'=>'À valider','published'=>'Publié','rejected'=>'Refusé'] as $key=>$label)<option value="{{ $key }}" @selected(old('status', $product->status) === $key)>{{ $label }}</option>@endforeach</select></div>
    <div class="full"><label for="review_note">Message au vendeur</label><textarea id="review_note" name="review_note">{{ old('review_note', $product->review_note) }}</textarea></div>
    <button class="full">Enregistrer le produit</button>
@else
    <p class="full muted">En soumettant ce produit, il sera publié directement ou placé en attente selon les règles de votre boutique.</p>
    <button type="submit" name="status" value="draft" class="secondary">Enregistrer en brouillon</button><button type="submit" name="status" value="submit">Soumettre le produit</button>
@endif
</div></form>
@endsection
