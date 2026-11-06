@extends('marketplace.layout')
@section('title', 'Paramètres de la marketplace')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/marketplace-settings.css') }}?v=1">
@endpush
@section('content')
<div class="settings-page">
    <div class="settings-heading"><span class="settings-eyebrow">Administration / Configuration</span><h1>Paramètres de la marketplace</h1><p class="muted">Définissez les règles de publication et de paiement de vos boutiques.</p></div>
    <form method="post" action="{{ route('marketplace.settings.update') }}">
        @csrf @method('put')
        <div class="settings-layout"><div class="settings-sections">
            <section class="settings-card" aria-labelledby="publication-title">
                <div class="settings-section-heading"><span class="settings-step" aria-hidden="true">01</span><div><h2 id="publication-title">Publication des produits</h2><p class="muted">Gardez le contrôle sur les produits mis en ligne.</p></div></div>
                <label for="publication_mode">Mode de publication par défaut</label>
                <select id="publication_mode" name="publication_mode" aria-describedby="publication-help" @error('publication_mode') aria-invalid="true" @enderror>
                    <option value="review" @selected(old('publication_mode', $settings->publication_mode) === 'review')>Validation par l’administrateur</option>
                    <option value="immediate" @selected(old('publication_mode', $settings->publication_mode) === 'immediate')>Publication immédiate</option>
                </select>
                <p class="settings-help" id="publication-help">Ce choix s’applique par défaut. Une règle différente peut être définie pour chaque vendeur.</p>
                @error('publication_mode')<p class="settings-field-error">{{ $message }}</p>@enderror
            </section>
            <section class="settings-card" aria-labelledby="commission-title">
                <div class="settings-section-heading"><span class="settings-step" aria-hidden="true">02</span><div><h2 id="commission-title">Commission plateforme</h2><p class="muted">Fixez votre rémunération sur les ventes encaissées.</p></div></div>
                <label for="commission">Taux de commission (%)</label>
                <div class="settings-percentage"><input id="commission" name="commission" type="number" min="0" max="100" step="0.01" required value="{{ old('commission', $settings->commission_bps / 100) }}" aria-describedby="commission-help" @error('commission') aria-invalid="true" @enderror><span aria-hidden="true">%</span></div>
                <p class="settings-help" id="commission-help">De 0 à 100 %. Applicable aux nouvelles commandes encaissées par la plateforme. Les paiements directs au vendeur sont sans commission.</p>
                @error('commission')<p class="settings-field-error">{{ $message }}</p>@enderror
            </section>
            <section class="settings-card" aria-labelledby="payment-title">
                <div class="settings-section-heading"><span class="settings-step" aria-hidden="true">03</span><div><h2 id="payment-title">Modes de paiement</h2><p class="muted">Choisissez les options proposées à vos clients.</p></div></div>
                <fieldset class="settings-payment-options" aria-describedby="payment-help"><legend>Modes de paiement autorisés</legend>
                    <label class="settings-option" for="platform_payment"><input id="platform_payment" type="checkbox" name="platform_payment" value="1" @checked(old('platform_payment', session()->hasOldInput() ? false : $settings->platform_payment))><span><strong>Encaissement par la plateforme</strong><span>Vous recevez le paiement, puis reversez la part du vendeur.</span></span></label>
                    <label class="settings-option" for="direct_payment"><input id="direct_payment" type="checkbox" name="direct_payment" value="1" @checked(old('direct_payment', session()->hasOldInput() ? false : $settings->direct_payment))><span><strong>Paiement direct au vendeur</strong><span>Le vendeur encaisse lui-même, sans commission plateforme.</span></span></label>
                    <p class="settings-help" id="payment-help">Activez au moins un mode de paiement.</p>
                    @error('payment')<p class="settings-field-error">{{ $message }}</p>@enderror
                    @error('platform_payment')<p class="settings-field-error">{{ $message }}</p>@enderror
                    @error('direct_payment')<p class="settings-field-error">{{ $message }}</p>@enderror
                </fieldset>
                <div class="settings-instructions"><label for="payment_instructions">Instructions de paiement à la plateforme</label><p class="settings-help" id="instructions-help">Précisez le compte Mobile Money, la référence à indiquer et un contact. Ces informations sont affichées après la commande et sont nécessaires pour proposer ce mode de paiement.</p><textarea id="payment_instructions" name="payment_instructions" rows="5" maxlength="4000" aria-describedby="instructions-help" placeholder="Ex. : envoyez le montant au numéro Mobile Money…&#10;Précisez la référence de votre commande." @error('payment_instructions') aria-invalid="true" @enderror>{{ old('payment_instructions', $settings->payment_instructions) }}</textarea>
                @error('payment_instructions')<p class="settings-field-error">{{ $message }}</p>@enderror</div>
            </section>
        </div>
        <aside class="settings-aside" aria-label="Informations sur les paramètres"><div class="settings-guide"><span class="settings-eyebrow">À retenir</span><h2>Vous gardez la main</h2><p>Les paiements sont vérifiés manuellement, quel que soit le mode choisi.</p><ul><li>Vérifiez la réception des fonds avant de confirmer un paiement.</li><li>Une déclaration du client ne marque jamais une commande comme payée.</li><li>Aucun prélèvement automatique de commission n’est effectué.</li></ul></div><a class="settings-seller-link" href="{{ route('marketplace.sellers') }}">Gérer les règles par vendeur <span aria-hidden="true">→</span></a></aside>
        </div>
        <div class="settings-actions"><p>Appliquez vos modifications en les enregistrant.</p><button type="submit">Enregistrer les paramètres</button></div>
    </form>
</div>
@endsection
