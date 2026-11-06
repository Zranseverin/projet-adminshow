<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('zran-market.svg') }}?v=1">
    <title>Connexion · Zran Market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v=1">
</head>
<body>
    <main class="login-shell">
        <section class="brand-panel" aria-label="Zran Market — Espace de gestion">
            <div class="brand-logo">
                <img src="{{ asset('zran-market.svg') }}" alt="">
                Zran Market
            </div>

            <div class="brand-copy">
                <h1>Votre boutique, un seul espace.</h1>
                <p>Retrouvez vos produits, suivez vos commandes et gérez vos stocks. Administrateurs et vendeurs, connectez-vous à votre espace de gestion.</p>
            </div>

            <div class="brand-meta">
                <span>Administration &amp; vendeurs</span>
                <span>•</span>
                <span>Espace de gestion</span>
            </div>
        </section>

        <section class="form-panel">
            <form class="login-card" method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="mobile-brand">
                    <img src="{{ asset('zran-market.svg') }}" alt="">
                    Zran Market
                </div>

                <h2>Connexion</h2>
                <p class="subtitle">Renseignez votre adresse mail et votre mot de passe.</p>

                @if ($errors->any())
                    <div class="alert">Veuillez vérifier les informations saisies.</div>
                @endif

                <div class="field">
                    <label for="email">Mail</label>
                    <div class="input-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="vous@exemple.com" required autofocus autocomplete="email">
                    </div>
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1">
                        Se souvenir de moi
                    </label>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px">
                        <a class="link" href="#">Mot de passe oublié ?</a>
                        <a class="link" href="{{ route('seller.register') }}">Créer mon compte vendeur</a>
                    </div>
                </div>

                <button class="btn-login" type="submit">
                    Se connecter
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>
        </section>
    </main>
</body>
</html>
