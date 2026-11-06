<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Valider mon e-mail · Zran Market</title><link rel="icon" href="{{ asset('zran-market.svg') }}" type="image/svg+xml"><link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=1"></head>
<body><main class="login-card" style="width:min(100%,520px)">
    <div class="brand-logo" style="color:var(--navy);margin-bottom:24px"><img src="{{ asset('zran-market.svg') }}" alt=""> Zran Market</div>
    <h1 style="font-size:24px;margin-bottom:16px">Validez votre adresse e-mail</h1>
    <p class="subtitle">Ouvrez le message envoyé à <strong>{{ auth()->user()->email }}</strong> et cliquez sur « Valider mon adresse e-mail » pour accéder à votre boutique. Pensez à vérifier les courriers indésirables.</p>
    @if(session('success'))<p role="status" style="margin-bottom:16px">{{ session('success') }}</p>@endif
    @if($errors->any())<div class="alert" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <form method="post" action="{{ route('verification.send') }}">@csrf<button class="btn-login" type="submit">Renvoyer le lien de validation</button></form>
    <form method="post" action="{{ route('logout') }}" style="margin-top:18px"><button class="link" style="border:0;background:none;cursor:pointer;min-height:44px" type="submit">Se déconnecter</button>@csrf</form>
</main></body></html>
