@php($usesSellerShell = auth()->check() && auth()->user()->account_type === 'seller')
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Zran Market') · Espace de gestion</title>
    <link rel="icon" href="{{ asset('zran-market.svg') }}" type="image/svg+xml">
    <style>
        :root{--ink:#172b3a;--muted:#526473;--border:#dce4e8;--accent:#174e45;--soft:#eaf3ee;--bg:#f5f7f6}
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.6 system-ui,sans-serif}a{color:var(--accent)}button,input,select,textarea{font:inherit}button,a,input,select,textarea{touch-action:manipulation}:focus-visible{outline:3px solid #b4651b;outline-offset:3px}
        header{background:#fff;border-bottom:1px solid var(--border);padding:18px max(20px,calc((100vw - 1200px)/2));display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap}.brand{font-size:22px;font-weight:800;text-decoration:none;letter-spacing:-.6px}.brand small{font-size:12px;display:block;color:var(--muted);font-weight:500;letter-spacing:0}nav{display:flex;gap:8px;flex-wrap:wrap;align-items:center}nav a{padding:10px;text-decoration:none}nav a.active{background:var(--soft);border-radius:8px;font-weight:700}
        main{max-width:1200px;margin:auto;padding:32px 20px 64px}h1{font-size:30px;letter-spacing:-.8px;line-height:1.25;margin:0 0 10px}h2{font-size:20px;margin:0 0 18px}h3{font-size:16px}p{margin:0 0 16px}.muted{color:var(--muted)}.heading{display:flex;justify-content:space-between;gap:20px;align-items:center;margin-bottom:26px;flex-wrap:wrap}.card{background:white;border:1px solid var(--border);border-radius:14px;padding:26px;margin-bottom:20px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.full{grid-column:1/-1}label{display:block;font-size:14px;font-weight:600;margin-bottom:7px}input:not([type=checkbox]),select,textarea{width:100%;padding:11px 12px;border:1px solid #b5c4cb;border-radius:7px;background:white;color:var(--ink)}textarea{min-height:110px}input[type=checkbox]{width:19px;height:19px;vertical-align:middle}.btn,button{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:10px 18px;background:var(--accent);color:white;border:1px solid var(--accent);border-radius:7px;font-weight:600;text-decoration:none;cursor:pointer}.secondary{background:white;color:var(--accent);border-color:var(--border)}.row{display:flex;gap:12px;align-items:center;flex-wrap:wrap}.notice{padding:16px;border-radius:8px;background:var(--soft);margin-bottom:20px}.error{background:#fff0ee;color:#8c2017}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;text-align:left}th{font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);background:#f6f8f7}th,td{padding:15px 12px;border-bottom:1px solid var(--border);vertical-align:top}.tag{display:inline-block;padding:4px 9px;background:var(--soft);border-radius:5px;font-size:12px}.price{font-weight:700;white-space:nowrap}.photo{width:54px;height:64px;object-fit:contain;border-radius:6px;background:var(--bg)}.empty{padding:50px 20px;text-align:center;color:var(--muted)}.pre{white-space:pre-line;overflow-wrap:anywhere}.pagination{display:flex;justify-content:space-between;padding-top:20px}.narrow{max-width:650px;margin:20px auto}.attributes{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px}
        @media(max-width:760px){.grid{grid-template-columns:1fr}.card{padding:20px}header{padding:16px 20px}h1{font-size:26px}nav{font-size:13px}main{padding-top:24px}.heading .btn{width:100%}}
    </style>
@if($usesSellerShell)
<link rel="stylesheet" href="{{ asset('css/seller-dashboard.css') }}?v=3">
<link rel="stylesheet" href="{{ asset('css/seller-shell.css') }}?v=1">
@endif
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v=1">
@stack('styles')
</head>
<body @class(['seller-page' => $usesSellerShell])>
@if($usesSellerShell)
@include('marketplace.seller-sidebar')
<div class="dash-workspace">
@include('marketplace.seller-topbar')
@else
<header class="management-header">
    <a class="brand" href="{{ auth()->check() ? route(auth()->user()->account_type === 'admin' ? 'marketplace.products.index' : 'seller.products.index') : route('seller.register') }}">Zran Market<small>@auth {{ auth()->user()->account_type === 'admin' ? 'Administration' : 'Espace vendeur' }} @else Ouvrez votre boutique @endauth</small></a>
    <nav aria-label="Navigation principale">
    @auth
        <a href="{{ route(auth()->user()->account_type === 'admin' ? 'marketplace.inventory' : 'seller.inventory') }}">{{ auth()->user()->account_type === 'admin' ? 'Stocks' : 'Mon stock' }}</a>
        @if(auth()->user()->account_type === 'admin')
            <a href="{{ route('marketplace.advertisements.index') }}">Publicités</a>
            <a href="{{ route('marketplace.products.index') }}" @class(['active' => request()->routeIs('marketplace.products.*')])>Produits</a>
            <a href="{{ route('marketplace.sellers') }}">Vendeurs</a><a href="{{ route('marketplace.orders') }}">Commandes</a><a href="{{ route('marketplace.settings') }}">Paramètres</a><a href="{{ route('dashboard') }}">Ancien backoffice</a>
        @else
            <a href="{{ route('seller.dashboard') }}">Tableau de bord</a>
            <a href="{{ route('seller.products.index') }}">Mes produits</a><a href="{{ route('seller.orders') }}">Mes commandes</a><a href="{{ route('seller.shop') }}">Ma boutique</a>
        @endif
        <form method="post" action="{{ route('logout') }}">@csrf<button class="secondary">Déconnexion</button></form>
    @else
        <a href="{{ route('login') }}">Se connecter</a>
    @endauth
    </nav>
@include('marketplace.mobile-admin-navigation')
</header>
@endif
<main>
    @if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="notice error" role="alert"><strong>Vérifiez les informations suivantes :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
@if($usesSellerShell)</div>@endif
@stack('scripts')
</body></html>
