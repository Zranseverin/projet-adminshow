@extends('marketplace.layout')
@section('title', 'Vendeurs')
@section('content')
<div class="heading"><div><h1>Les boutiques partenaires</h1><p class="muted">Contrôlez l’accès et le mode de publication de chaque vendeur.</p></div></div>
@forelse($sellers as $seller)
<div class="card"><div class="heading"><div><h2>{{ $seller->name }}</h2><p class="muted">{{ $seller->user->name }} · {{ $seller->user->email }} · {{ $seller->phone }}<br>{{ $seller->products_count }} produits</p></div><span class="tag">{{ $seller->is_active ? 'Active' : 'Suspendue' }}</span></div><p class="pre">{{ $seller->description }}</p>
<p><a href="{{ route('marketplace.inventory', ['seller_id' => $seller->id]) }}">Voir le stock de cette boutique</a></p>
<p><a href="{{ route('marketplace.sellers.dashboard', $seller) }}">Ouvrir le tableau de bord</a></p>
<form class="grid" method="post" action="{{ route('marketplace.sellers.update', $seller) }}">@csrf @method('put')
<div><label for="mode-{{ $seller->id }}">Publication</label><select id="mode-{{ $seller->id }}" name="publication_mode">@foreach(['inherit'=>'Réglage général','review'=>'Validation obligatoire','immediate'=>'Immédiate'] as $key=>$label)<option value="{{ $key }}" @selected($seller->publication_mode === $key)>{{ $label }}</option>@endforeach</select></div>
<div><label for="active-{{ $seller->id }}">Accès à la boutique</label><select id="active-{{ $seller->id }}" name="is_active"><option value="1" @selected($seller->is_active)>Active</option><option value="0" @selected(!$seller->is_active)>Suspendue</option></select></div><button class="full secondary">Enregistrer</button></form></div>
@empty<div class="card empty">Aucun vendeur inscrit pour le moment.</div>@endforelse
<div class="pagination">@if($sellers->previousPageUrl())<a href="{{ $sellers->previousPageUrl() }}">Précédent</a>@endif @if($sellers->nextPageUrl())<a href="{{ $sellers->nextPageUrl() }}">Suivant</a>@endif</div>
@endsection
