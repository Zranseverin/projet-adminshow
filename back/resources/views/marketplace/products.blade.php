@extends('marketplace.layout')
@section('title', 'Produits')
@section('content')
@php($statuses = ['draft' => 'Brouillon', 'pending' => 'À valider', 'published' => 'Publié', 'rejected' => 'Refusé'])
<div class="heading"><div><h1>{{ $prefix === 'seller' ? 'Mes produits' : 'Tous les produits' }}</h1><p class="muted">{{ $prefix === 'seller' ? 'Vos offres, leurs stocks et leur publication.' : 'Gérez le catalogue et validez les propositions des vendeurs.' }}</p></div><a class="btn" href="{{ route($prefix.'.products.create') }}">Ajouter un produit</a></div>
<div class="card">
    <form class="row" method="get" style="margin-bottom:22px"><input aria-label="Rechercher un produit" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit" style="flex:1;min-width:160px"><select aria-label="Statut" name="status" style="width:auto"><option value="">Tous les statuts</option>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select><button class="secondary">Filtrer</button></form>
    <div class="table-wrap"><table><thead><tr><th>Produit</th><th>Boutique</th><th>Prix</th><th>Stock</th><th>Publication</th><th>Action</th></tr></thead><tbody>
    @forelse($products as $product)
        <tr><td><div class="row">@if($product->image_url)<img class="photo" src="{{ $product->image_url }}" alt="" loading="lazy">@endif<div><strong>{{ $product->name }}</strong><div class="muted">{{ $product->category?->name ?? 'Sans catégorie' }}</div></div></div></td><td>{{ $product->seller?->name ?? 'Plateforme' }}</td><td class="price">{{ number_format($product->price_minor / 100, 0, ',', ' ') }} FCFA</td><td>{{ $product->stock }}</td><td><span class="tag">{{ $statuses[$product->status] ?? $product->status }}</span>@if($product->review_note)<p class="muted">{{ $product->review_note }}</p>@endif</td><td><a class="btn secondary" href="{{ route($prefix.'.products.edit', $product) }}">{{ $prefix === 'marketplace' && $product->status === 'pending' ? 'Examiner' : 'Modifier' }}</a></td></tr>
    @empty<tr><td colspan="6" class="empty">Aucun produit pour le moment. Ajoutez votre premier produit.</td></tr>@endforelse
    </tbody></table></div>
    <div class="pagination">@if($products->previousPageUrl())<a href="{{ $products->previousPageUrl() }}">← Précédent</a>@else<span></span>@endif<span>{{ $products->currentPage() }} / {{ $products->lastPage() }}</span>@if($products->nextPageUrl())<a href="{{ $products->nextPageUrl() }}">Suivant →</a>@endif</div>
</div>
@endsection
