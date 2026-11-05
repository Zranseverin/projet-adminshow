@extends('marketplace.layout')
@section('title', 'Ma boutique')
@section('content')
<div class="narrow"><h1>Votre boutique, votre identité.</h1><p class="muted">Les clients verront votre nom et votre présentation sur vos produits.</p>
<form class="card grid" method="post" action="{{ route('seller.shop.update') }}">@csrf @method('put')
<div class="full"><label for="name">Nom de la boutique</label><input id="name" name="name" required value="{{ old('name', $seller->name) }}"></div>
<div class="full"><label for="phone">Téléphone professionnel</label><input id="phone" name="phone" type="tel" required value="{{ old('phone', $seller->phone) }}"></div>
<div class="full"><label for="description">Présentation</label><textarea id="description" name="description">{{ old('description', $seller->description) }}</textarea></div>
<div class="full"><label for="payment_instructions">Instructions pour recevoir un paiement direct</label><textarea id="payment_instructions" name="payment_instructions" placeholder="Compte de paiement, nom du bénéficiaire, consignes…">{{ old('payment_instructions', $seller->payment_instructions) }}</textarea><p class="muted">Communiquées au client après commande. Ne renseignez jamais de mot de passe ni de code secret.</p></div>
<button class="full">Enregistrer ma boutique</button></form></div>
@endsection
