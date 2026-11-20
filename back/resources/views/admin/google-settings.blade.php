@extends('layouts.app')
@section('title', 'Connexion Google')
@section('page-title', 'Connexion Google')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/google-settings.css') }}?v=1">
@endpush
@section('content')
<div class="google-settings-page">
    <div class="google-settings-heading"><span>Marketplace / Configuration</span><h1>Connexion Google vendeur</h1><p>Enregistrez les identifiants OAuth utilisés pour créer un compte vendeur avec Google.</p></div>

    @if(session('success'))<div class="google-settings-message" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="google-settings-message google-settings-error" role="alert"><strong>Vérifiez les informations saisies.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form class="google-settings-card" method="post" action="{{ route('admin.google-settings.update') }}">
        @csrf @method('put')
        <div class="google-settings-fields">
            <div class="google-settings-full"><label for="client_id">ID client Google</label><input id="client_id" name="client_id" value="{{ old('client_id', $settings->client_id) }}" required maxlength="255" autocomplete="off" placeholder="000000000000-abc.apps.googleusercontent.com" spellcheck="false">@error('client_id')<small class="google-settings-field-error">{{ $message }}</small>@enderror</div>
            <div class="google-settings-full"><label for="client_secret">Clé secrète du client</label><input id="client_secret" name="client_secret" type="password" value="" maxlength="2048" autocomplete="new-password" placeholder="{{ $hasSecret ? 'Déjà enregistrée — laissez vide pour la conserver' : 'Collez la clé secrète Google' }}">@error('client_secret')<small class="google-settings-field-error">{{ $message }}</small>@enderror<p class="google-settings-hint">La clé est chiffrée dans la base de données. Elle ne sera jamais réaffichée.</p></div>
            <div class="google-settings-full"><label for="redirect_uri">URI de redirection autorisée</label><input id="redirect_uri" name="redirect_uri" type="url" value="{{ old('redirect_uri', $settings->redirect_uri ?: route('seller.google.callback')) }}" required maxlength="2048" spellcheck="false">@error('redirect_uri')<small class="google-settings-field-error">{{ $message }}</small>@enderror<p class="google-settings-hint">Ajoutez exactement cette URL dans les URI autorisées de votre client OAuth Google.</p></div>
        </div>
        <div class="google-settings-callout"><strong>URI de retour attendue</strong><code>{{ route('seller.google.callback') }}</code></div>
        <div class="google-settings-actions"><span>La clé secrète existante est conservée si vous laissez son champ vide.</span><button type="submit">Enregistrer les identifiants</button></div>
    </form>
</div>
@endsection
