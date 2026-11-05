@extends('layouts.app')

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')

@section('content')
    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <section class="profile-shell">
        <article class="card profile-summary">
            <div class="profile-cover"></div>
            <div class="profile-identity">
                <div class="profile-avatar">{{ strtoupper(mb_substr($user->name_complet ?: $user->name, 0, 2)) }}</div>
                <div>
                    <h1>{{ $user->name_complet ?: $user->name }}</h1>
                    <p>{{ $user->role?->libelle ?? 'Administrateur' }}</p>
                </div>
                <a class="btn btn-amber" href="{{ route('profile.edit') }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    Modifier le profil
                </a>
            </div>
        </article>

        <article class="card">
            <div class="card-header"><h2 class="card-title">Informations personnelles</h2></div>
            <div class="card-body detail-grid">
                <div class="detail-item"><div class="detail-label">Nom d'utilisateur</div><div class="detail-value">{{ $user->name }}</div></div>
                <div class="detail-item"><div class="detail-label">Nom complet</div><div class="detail-value">{{ $user->name_complet ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Adresse e-mail</div><div class="detail-value">{{ $user->email }}</div></div>
                <div class="detail-item"><div class="detail-label">Téléphone</div><div class="detail-value">{{ $user->telephone ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Adresse</div><div class="detail-value">{{ $user->adresse ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Genre</div><div class="detail-value">{{ $user->genre?->libelle ?? '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Code utilisateur</div><div class="detail-value">{{ $user->code ?: $user->user_code ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Compte créé le</div><div class="detail-value">{{ $user->created_at?->format('d/m/Y à H:i') }}</div></div>
            </div>
        </article>
    </section>
@endsection

@push('styles')
<style>
    .profile-shell { max-width: 980px; display:grid; gap:20px; margin:0 auto; }
    .profile-summary { overflow:visible; }
    .profile-cover { height:105px; border-radius:9px 9px 0 0; background:#23384d; }
    .profile-identity { min-height:100px; display:flex; align-items:center; gap:16px; padding:0 24px 18px; }
    .profile-avatar { width:76px; height:76px; flex:none; display:grid; place-items:center; margin-top:-34px; border:5px solid #fff; border-radius:50%; color:#fff; background:#c8832a; font-size:22px; font-weight:700; }
    .profile-identity h1 { color:var(--navy); font-size:20px; }
    .profile-identity p { margin-top:4px; color:var(--muted); font-size:12px; }
    .profile-identity .btn { margin-left:auto; }
    @media(max-width:620px){.profile-identity{align-items:flex-start;flex-wrap:wrap}.profile-identity .btn{width:100%;margin-left:0}.profile-avatar{width:64px;height:64px}}
</style>
@endpush