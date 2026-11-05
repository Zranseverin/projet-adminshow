@extends('marketplace.layout')
@section('title', 'Paramètres de la marketplace')
@section('content')
<div class="heading"><div><h1>Les règles de votre marketplace</h1><p class="muted">Choisissez comment publier, encaisser et rémunérer les vendeurs.</p></div></div>
<form method="post" action="{{ route('marketplace.settings.update') }}">@csrf @method('put')
<div class="card grid"><div><label for="publication_mode">Publication par défaut</label><select id="publication_mode" name="publication_mode"><option value="review" @selected(old('publication_mode', $settings->publication_mode) === 'review')>Validation par l’administrateur</option><option value="immediate" @selected(old('publication_mode', $settings->publication_mode) === 'immediate')>Publication immédiate</option></select><p class="muted">Un réglage spécifique peut être défini pour chaque vendeur.</p></div>
<div><label for="commission">Commission plateforme (%)</label><input id="commission" name="commission" type="number" min="0" max="100" step="0.01" required value="{{ old('commission', $settings->commission_bps / 100) }}"><p class="muted">Appliquée aux nouvelles commandes encaissées par la plateforme. Aucun prélèvement automatique.</p></div></div>
<div class="card"><h2>Paiement manuel</h2><p class="muted">Confirmez un paiement uniquement après avoir vérifié sa réception. Les commandes ne sont jamais marquées payées par la déclaration du client.</p>
<p><label><input type="checkbox" name="platform_payment" value="1" @checked(old('platform_payment', $settings->platform_payment))> Encaissement par la plateforme</label></p>
<p><label><input type="checkbox" name="direct_payment" value="1" @checked(old('direct_payment', $settings->direct_payment))> Paiement direct au vendeur, sans commission plateforme</label></p>
<label for="payment_instructions">Instructions de paiement à la plateforme</label><textarea id="payment_instructions" name="payment_instructions" placeholder="Compte Mobile Money, référence à indiquer, contact…">{{ old('payment_instructions', $settings->payment_instructions) }}</textarea><p class="muted">Ces instructions seront affichées aux clients après leur commande. Leur saisie est nécessaire pour accepter ce mode.</p></div>
<button>Enregistrer les paramètres</button></form>
@endsection
