@extends('marketplace.layout')
@section('title', 'Devenir vendeur')
@section('content')
<div class="narrow">
    <p class="muted">VOTRE PROCHAINE AVENTURE COMMENCE ICI</p><h1>Une boutique à votre nom.</h1><p class="muted">Présentez vos produits, recevez des commandes et développez votre activité.</p>
    <form class="card grid" method="post" action="{{ route('seller.register.store') }}">@csrf
        <div><label for="name">Votre nom</label><input id="name" name="name" value="{{ old('name') }}" required autocomplete="name"></div>
        <div><label for="shop_name">Nom de la boutique</label><input id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required maxlength="160"></div>
        <div><label for="email">Adresse e-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></div>
        <div><label for="phone">Téléphone professionnel</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"></div>
        <div><label for="password">Mot de passe (10 caractères minimum)</label><input id="password" type="password" name="password" minlength="10" required autocomplete="new-password"></div>
        <div><label for="password_confirmation">Confirmez le mot de passe</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <p class="full muted">Après inscription, renseignez les instructions de paiement de votre boutique. Les produits pourront nécessiter une validation avant publication.</p>
        <button class="full">Créer ma boutique</button>
    </form>
</div>
@endsection
