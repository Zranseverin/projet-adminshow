@extends('layouts.app')
@section('title', 'Configuration mail')
@section('page-title', 'Configuration mail')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/mail-settings.css') }}?v=1">
@endpush
@section('content')
<div class="mail-page">
    <div class="mail-heading"><span class="mail-eyebrow">Administration / Configuration</span><h1>Configuration mail</h1><p>Une adresse d’expédition pour accueillir vos vendeurs et valider leur compte.</p></div>
    @if(session('success'))<div class="mail-message" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mail-message mail-error" role="alert"><strong>Vérifiez les informations suivantes.</strong><ul>@foreach($errors->messages() as $field => $messages)@foreach($messages as $error)<li><a href="#{{ $field }}">{{ $error }}</a></li>@endforeach @endforeach</ul></div>@endif
    <form method="post" action="{{ route('admin.mail-settings.update') }}">
        @csrf @method('put')
        <div class="mail-layout"><div class="mail-sections">
            <section class="mail-card" aria-labelledby="sender-title">
                <div class="mail-section-title"><span class="mail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/></svg></span><div><h2 id="sender-title">Identité de l’expéditeur</h2><p>Le nom et l’adresse affichés dans les messages reçus.</p></div></div>
                <div class="mail-fields">
                    <div><label for="from_name">Nom de l’expéditeur</label><input id="from_name" name="from_name" value="{{ old('from_name', $settings->from_name) }}" required maxlength="160" placeholder="Zran Market" @error('from_name') aria-invalid="true" aria-describedby="from-name-error" @enderror>@error('from_name')<p class="mail-field-error" id="from-name-error">{{ $message }}</p>@enderror</div>
                    <div><label for="from_address">Adresse d’expédition</label><input id="from_address" name="from_address" type="email" value="{{ old('from_address', $settings->from_address) }}" required placeholder="contact@votre-domaine.ci" @error('from_address') aria-invalid="true" aria-describedby="from-address-error" @enderror>@error('from_address')<p class="mail-field-error" id="from-address-error">{{ $message }}</p>@enderror</div>
                </div>
            </section>
            <section class="mail-card" aria-labelledby="smtp-title">
                <div class="mail-section-title"><span class="mail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6h.01M7 17h.01M11 6h6M11 17h6"/></svg></span><div><h2 id="smtp-title">Connexion au serveur</h2><p>Les paramètres fournis par votre hébergeur mail.</p></div></div>
                <div class="mail-fields">
                    <div class="mail-full"><label for="host">Serveur SMTP</label><input id="host" name="host" value="{{ old('host', $settings->host) }}" required placeholder="smtp.votre-domaine.ci" autocapitalize="none" spellcheck="false" @error('host') aria-invalid="true" aria-describedby="host-error" @enderror>@error('host')<p class="mail-field-error" id="host-error">{{ $message }}</p>@enderror</div>
                    <div><label for="port">Port du serveur</label><select id="port" name="port" @error('port') aria-invalid="true" @enderror>@foreach([587,465] as $port)<option value="{{ $port }}" @selected(old('port', $settings->port) == $port)>{{ $port }}</option>@endforeach</select>@error('port')<p class="mail-field-error">{{ $message }}</p>@enderror</div>
                    <div><label for="encryption">Chiffrement</label><select id="encryption" name="encryption" @error('encryption') aria-invalid="true" @enderror><option value="tls" @selected(old('encryption', $settings->encryption) === 'tls')>STARTTLS · port 587</option><option value="ssl" @selected(old('encryption', $settings->encryption) === 'ssl')>TLS implicite · port 465</option></select>@error('encryption')<p class="mail-field-error">{{ $message }}</p>@enderror</div>
                </div>
                <div class="mail-divider"></div>
                <div class="mail-fields">
                    <div class="mail-full"><label for="username">Identifiant SMTP</label><input id="username" name="username" value="{{ old('username', $settings->username) }}" required autocomplete="off" autocapitalize="none" spellcheck="false" aria-describedby="username-help @error('username') username-error @enderror" @error('username') aria-invalid="true" @enderror><p id="username-help" class="mail-help">Généralement, l’adresse e-mail complète de la boîte d’envoi.</p>@error('username')<p class="mail-field-error" id="username-error">{{ $message }}</p>@enderror</div>
                    <div class="mail-full"><label for="password">Mot de passe SMTP <span class="mail-optional">Facultatif si déjà configuré</span></label><div class="mail-password"><input id="password" name="password" type="password" autocomplete="new-password" placeholder="Saisir un nouveau mot de passe" aria-describedby="password-help @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror><button type="button" id="mail-password-toggle" aria-label="Afficher le mot de passe SMTP" aria-controls="password" aria-pressed="false"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="mail-eye-slash" d="m3 3 18 18"/></svg></button></div><p id="password-help" class="mail-help">Laissez ce champ vide pour conserver le mot de passe actuel. Tout nouveau mot de passe est enregistré chiffré.</p>@error('password')<p class="mail-field-error" id="password-error">{{ $message }}</p>@enderror</div>
                </div>
            </section>
        </div>
        <aside class="mail-aside"><section class="mail-guide"><span class="mail-eyebrow">Parcours vendeur</span><h2>Un compte, une adresse vérifiée.</h2><ol><li><strong>Création du compte</strong><span>Le vendeur renseigne son adresse e-mail.</span></li><li><strong>Envoi du lien</strong><span>Un message de validation est envoyé depuis votre boîte.</span></li><li><strong>Accès à la boutique</strong><span>Le vendeur valide son adresse en cliquant sur le lien reçu.</span></li></ol><p class="mail-expiry">Le lien expire après 60 minutes. Le vendeur peut demander un nouvel envoi.</p></section><div class="mail-note"><strong>Avant d’enregistrer</strong><p>Vérifiez que le port et le chiffrement correspondent aux paramètres de votre hébergeur.</p></div></aside>
        </div>
        <div class="mail-actions"><p>Ces réglages seront utilisés pour les prochains envois.</p><button type="submit">Enregistrer la configuration <span aria-hidden="true">→</span></button></div>
    </form>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('mail-password-toggle').addEventListener('click', function () {
        const field = document.getElementById('password');
        const visible = field.type === 'password';
        field.type = visible ? 'text' : 'password';
        this.setAttribute('aria-pressed', String(visible));
        this.setAttribute('aria-label', visible ? 'Masquer le mot de passe SMTP' : 'Afficher le mot de passe SMTP');
    });
</script>
@endpush
