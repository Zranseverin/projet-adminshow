<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Devenir vendeur · Zran Market</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('zran-market.svg') }}?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/seller-register.css') }}?v=1">
</head>
<body>
    <main class="login-shell register-shell">
        <section class="brand-panel" aria-label="Zran Market — Espace vendeur">
            <div class="brand-logo"><img src="{{ asset('zran-market.svg') }}" alt="">Zran Market</div>
            <div class="brand-copy"><h1>Votre boutique commence ici.</h1><p>Présentez vos produits, recevez vos premières commandes et développez votre activité sur Zran Market.</p></div>
            <div class="brand-meta"><span>Votre boutique</span><span aria-hidden="true">•</span><span>Vos ambitions</span></div>
        </section>
        <section class="form-panel" aria-labelledby="register-title">
            <form id="seller-registration" class="login-card" autocomplete="on" method="post" action="{{ route('seller.register.store') }}">
                @csrf
                <div class="mobile-brand"><img src="{{ asset('zran-market.svg') }}" alt="">Zran Market</div>
                <h2 id="register-title">Créer mon compte vendeur</h2>
                <p class="subtitle">Quelques informations pour ouvrir votre boutique.</p>
                @if($errors->any())
                    <div class="alert" role="alert"><strong>Vérifiez les informations saisies.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <div class="register-grid">
                    <div class="field"><label for="name">Votre nom</label><input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>@error('name')<span class="error" id="name-error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="shop_name">Nom de la boutique</label><input id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required maxlength="160" autocomplete="organization" @error('shop_name') aria-invalid="true" aria-describedby="shop-error" @enderror>@error('shop_name')<span class="error" id="shop-error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="email">Adresse e-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="vous@exemple.com" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>@error('email')<span class="error" id="email-error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="phone">Téléphone professionnel</label><input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>@error('phone')<span class="error" id="phone-error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="password">Mot de passe</label><div class="password-control"><input id="password" type="password" name="password" minlength="10" required autocomplete="new-password" spellcheck="false" autocapitalize="none" aria-describedby="password-help password-strength-label password-strength-tip @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror><button class="password-toggle" type="button" aria-label="Afficher le mot de passe" aria-controls="password" aria-pressed="false" data-password-toggle data-field-label="le mot de passe"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="eye-slash" d="M3 3l18 18"/></svg></button></div><span id="password-help" class="register-help-text">Votre mot de passe doit contenir au moins 10 caractères.</span><div class="password-strength" id="password-strength" data-level="empty">
    <div class="password-strength-track" role="meter" aria-label="Robustesse estimée du mot de passe" aria-valuemin="0" aria-valuemax="3" aria-valuenow="0" aria-valuetext="Non renseigné"><span class="password-strength-fill"></span></div>
    <p id="password-strength-label" class="password-strength-label" aria-live="polite" aria-atomic="true">Robustesse : non renseignée</p>
    <p id="password-strength-tip" class="register-help-text">Privilégiez une phrase longue et évitez les mots de passe courants.</p>
</div>@error('password')<span class="error" id="password-error">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="password_confirmation">Confirmer le mot de passe</label><div class="password-control"><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" spellcheck="false" autocapitalize="none" @error('password_confirmation') aria-invalid="true" aria-describedby="confirmation-error" @enderror><button class="password-toggle" type="button" aria-label="Afficher la confirmation du mot de passe" aria-controls="password_confirmation" aria-pressed="false" data-password-toggle data-field-label="la confirmation du mot de passe"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="eye-slash" d="M3 3l18 18"/></svg></button></div>@error('password_confirmation')<span class="error" id="confirmation-error">{{ $message }}</span>@enderror</div>
                </div>
                <p class="register-help">Après inscription, validez votre adresse e-mail grâce au lien reçu pour accéder à votre boutique. Vous pourrez ensuite compléter vos instructions de paiement.</p>
                <button class="btn-login" type="submit">Créer ma boutique <span aria-hidden="true">→</span></button>
                <p class="register-login">Déjà un compte ? <a class="link" href="{{ route('login') }}">Se connecter</a></p>
            </form>
        </section>
    </main>
<script>
    const passwordField = document.getElementById('password');
    const strength = document.getElementById('password-strength');
    const meter = strength.querySelector('[role="meter"]');
    const strengthLabel = document.getElementById('password-strength-label');
    const strengthTip = document.getElementById('password-strength-tip');
    function updatePasswordStrength() {
        const value = passwordField.value;
        const length = Array.from(value).length;
        const variety = [/\p{Ll}/u, /\p{Lu}/u, /\p{N}/u, /[^\p{L}\p{N}]/u].filter((pattern) => pattern.test(value)).length;
        const common = /password|motdepasse|azerty|qwerty|123456|abcdef|zranmarket/i.test(value.replace(/[^\p{L}\p{N}]/gu, ''));
        const repetitive = new Set(value.toLowerCase()).size < 5 || /(.{1,4})\1{2,}/u.test(value);
        let score = 0;
        if (length) {
            score = 1;
            if (length >= 10 && !common && !repetitive && (variety >= 3 || length >= 14)) score = 2;
            if (length >= 14 && !common && !repetitive && (variety >= 3 || (length >= 20 && variety >= 2))) score = 3;
        }
        const labels = ['Non renseigné', 'Faible', 'Moyen', 'Fort'];
        strength.dataset.level = ['empty', 'weak', 'medium', 'strong'][score];
        meter.setAttribute('aria-valuenow', String(score));
        meter.setAttribute('aria-valuetext', labels[score]);
        strengthLabel.textContent = score ? `Robustesse estimée : ${labels[score]}` : 'Robustesse : non renseignée';
        strengthTip.textContent = !length
            ? 'Privilégiez une phrase longue et évitez les mots de passe courants.'
            : length < 10 ? 'Ajoutez des caractères pour atteindre le minimum de 10.'
            : common || repetitive ? 'Évitez les suites, les répétitions et les mots de passe courants.'
            : score < 3 ? 'Allongez votre mot de passe et variez les lettres, chiffres ou symboles.'
            : 'Bonne longueur et diversité. Utilisez un mot de passe unique pour ce compte.';
    }
    passwordField.addEventListener('input', updatePasswordStrength);
    passwordField.addEventListener('change', updatePasswordStrength);
    updatePasswordStrength();
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = document.getElementById(button.getAttribute('aria-controls'));
            const visible = field.type === 'password';
            field.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', `${visible ? 'Masquer' : 'Afficher'} ${button.dataset.fieldLabel}`);
        });
    });
</script>
</body>
</html>
