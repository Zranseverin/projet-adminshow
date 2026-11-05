<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('logo-librairie-dominion.png') }}?v=1">
    <title>Connexion - {{ config('app.name', 'Chapitre') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1C2B3A;
            --ink: #2E3F50;
            --sand: #F7F3EE;
            --cream: #FDFAF6;
            --amber: #C8832A;
            --amber-light: #F4E4CC;
            --red: #C23B2E;
            --red-bg: #FEF0F0;
            --border: #DDD8D0;
            --bg: #F4F6F9;
            --muted: #7A8694;
            --white: #FFFFFF;
            --shadow-md: 0 4px 16px rgba(28, 43, 58, .1);
            --font-serif: 'Lora', Georgia, serif;
            --font-sans: 'Inter', system-ui, sans-serif;
            --radius: 6px;
            --radius-lg: 10px;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                linear-gradient(135deg, rgba(253, 250, 246, .96), rgba(247, 243, 238, .96)),
                radial-gradient(circle at top left, rgba(200, 131, 42, .18), transparent 34%),
                radial-gradient(circle at bottom right, rgba(28, 43, 58, .12), transparent 38%),
                #F1E9DD;
            color: var(--ink);
            font-family: var(--font-sans);
        }

        .login-shell {
            width: min(100%, 1040px);
            min-height: 590px;
            display: grid;
            grid-template-columns: .92fr 1.08fr;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .72);
            border-radius: var(--radius-lg);
            background: var(--white);
            box-shadow: 0 28px 80px rgba(28, 43, 58, .18);
        }

        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 34px;
            overflow: hidden;
            background:
                linear-gradient(160deg, rgba(28, 43, 58, .98), rgba(46, 63, 80, .94)),
                var(--navy);
            color: var(--white);
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            right: -90px;
            top: -70px;
            border-radius: 50%;
            background: rgba(200, 131, 42, .2);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            left: -70px;
            bottom: -80px;
            border-radius: 50%;
            border: 34px solid rgba(255, 255, 255, .05);
        }

        .brand-logo,
        .brand-copy,
        .brand-meta {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--white);
            font-family: var(--font-serif);
            font-size: 23px;
            font-weight: 600;
        }

        .brand-logo svg { color: var(--amber); }
        .brand-copy h1 {
            max-width: 360px;
            margin-bottom: 14px;
            font-family: var(--font-serif);
            font-size: 34px;
            font-weight: 600;
            line-height: 1.15;
        }

        .brand-copy p {
            max-width: 360px;
            color: rgba(255, 255, 255, .68);
            font-size: 14px;
            line-height: 1.7;
        }

        .brand-meta {
            display: flex;
            gap: 10px;
            color: rgba(255, 255, 255, .55);
            font-size: 12px;
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .82), rgba(253, 250, 246, .96)),
                var(--cream);
        }

        .login-card {
            width: min(100%, 390px);
            padding: 32px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--white);
            box-shadow: var(--shadow-md);
        }

        .login-card h2 {
            margin-bottom: 6px;
            color: var(--navy);
            font-family: var(--font-serif);
            font-size: 25px;
            font-weight: 600;
        }

        .login-card .subtitle {
            margin-bottom: 24px;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.6;
        }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 9px;
            margin-bottom: 20px;
            color: var(--navy);
            font-family: var(--font-serif);
            font-size: 21px;
            font-weight: 600;
        }

        .mobile-brand svg { color: var(--amber); }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 16px;
        }

        .field label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .field input {
            width: 100%;
            height: 44px;
            padding: 0 12px 0 40px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            outline: none;
            background: var(--cream);
            color: var(--navy);
            font-size: 14px;
            transition: background .15s, border-color .15s, box-shadow .15s;
        }

        .field input:focus {
            border-color: var(--amber);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(200, 131, 42, .12);
        }

        .error {
            margin-top: 6px;
            color: var(--red);
            font-size: 12px;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 22px;
            color: var(--muted);
            font-size: 13px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .remember input {
            width: 15px;
            height: 15px;
            accent-color: var(--amber);
        }

        .link {
            color: var(--amber);
            font-weight: 600;
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: var(--radius);
            background: var(--navy);
            color: var(--white);
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: background .15s, transform .15s;
        }

        .btn-login:hover {
            background: #2A3F52;
            transform: translateY(-1px);
        }

        .alert {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid var(--red-bg);
            border-radius: var(--radius);
            background: var(--red-bg);
            color: var(--red);
            font-size: 13px;
        }

        @media (max-width: 820px) {
            body {
                align-items: start;
                padding: 18px;
                background:
                    linear-gradient(180deg, #F6EFE5, #FDFAF6 42%, #F4F6F9),
                    #F6EFE5;
            }
            .login-shell {
                width: min(100%, 520px);
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .brand-panel { display: none; }
            .form-panel { padding: 24px; }
            .login-card {
                padding: 26px;
                box-shadow: none;
            }
            .mobile-brand { display: inline-flex; }
        }

        @media (max-width: 520px) {
            body { padding: 12px; }
            .login-shell { border-radius: 8px; }
            .form-panel { padding: 16px; }
            .login-card {
                padding: 22px;
                border: 0;
            }
            .login-card h2 { font-size: 23px; }
            .form-row { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="brand-panel" aria-label="Chapitre admin">
            <div class="brand-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Chapitre
            </div>

            <div class="brand-copy">
                <h1>Votre back-office librairie.</h1>
                <p>Connectez-vous pour gérer le catalogue, les commandes, les stocks et les promotions de la boutique.</p>
            </div>

            <div class="brand-meta">
                <span>Admin</span>
                <span>•</span>
                <span>Accès sécurisé</span>
            </div>
        </section>

        <section class="form-panel">
            <form class="login-card" method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="mobile-brand">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Chapitre
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
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="admin@chapitre.com" required autofocus autocomplete="email">
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
                    <a class="link" href="#">Mot de passe oublié ?</a>
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
