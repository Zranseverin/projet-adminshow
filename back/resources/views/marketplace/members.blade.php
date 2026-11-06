@extends('marketplace.layout')
@section('title', 'Membres de ma boutique')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller-members.css') }}?v=1">
@endpush
@section('content')
<div class="heading"><div><span class="members-eyebrow">MA BOUTIQUE / ÉQUIPE</span><h1>Les membres de votre boutique</h1><p class="muted">Travaillez ensemble, avec un compte personnel pour chacun.</p></div><span class="tag">{{ $members->total() }} {{ $members->total() > 1 ? 'membres' : 'membre' }}</span></div>
<div class="members-layout">
    <section class="card" aria-labelledby="team-title"><h2 id="team-title">Votre équipe</h2><p class="muted">Les membres accèdent uniquement aux produits, stocks et commandes de votre boutique.</p>
        <div class="members-list">
        @forelse($members as $member)
            <article class="member-row"><div class="member-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}</div><div class="member-info"><h3>{{ $member->name }}</h3><p>{{ $member->email }}</p><div class="member-status"><span class="tag">{{ $member->member_is_active ? 'Accès actif' : 'Accès suspendu' }}</span><span class="muted">{{ $member->hasVerifiedEmail() ? 'E-mail validé' : 'E-mail à valider' }}</span></div></div><form method="post" action="{{ route('seller.members.update', $member) }}">@csrf @method('put')<input type="hidden" name="is_active" value="{{ $member->member_is_active ? '0' : '1' }}"><button class="secondary" type="submit" aria-label="{{ $member->member_is_active ? 'Suspendre' : 'Réactiver' }} l’accès de {{ $member->name }}">{{ $member->member_is_active ? 'Suspendre' : 'Réactiver' }}</button></form></article>
        @empty
            <div class="members-empty"><svg aria-hidden="true" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M19 8v8M15 12h8"/></svg><h3>Votre équipe commence ici</h3><p>Créez le premier compte membre avec le formulaire.</p></div>
        @endforelse
        </div>
        @if($members->hasPages())<div class="pagination">@if($members->previousPageUrl())<a href="{{ $members->previousPageUrl() }}">Précédent</a>@else<span></span>@endif<span>{{ $members->currentPage() }} / {{ $members->lastPage() }}</span>@if($members->nextPageUrl())<a href="{{ $members->nextPageUrl() }}">Suivant</a>@endif</div>@endif
    </section>
    <aside><section class="card" aria-labelledby="create-member-title"><h2 id="create-member-title">Ajouter un membre</h2><p class="muted">Son adresse e-mail lui servira d’identifiant.</p>
        <form class="member-form" method="post" action="{{ route('seller.members.store') }}">@csrf
            <div><label for="name">Nom du membre</label><input id="name" name="name" value="{{ old('name') }}" required maxlength="160" autocomplete="off" @error('name') aria-invalid="true" @enderror>@error('name')<p class="member-error">{{ $message }}</p>@enderror</div>
            <div><label for="email">Adresse e-mail</label><input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="off" @error('email') aria-invalid="true" @enderror>@error('email')<p class="member-error">{{ $message }}</p>@enderror</div>
            <div><label for="password">Mot de passe</label><div class="member-password"><input id="password" name="password" type="password" required minlength="10" autocomplete="new-password" aria-describedby="member-password-help" @error('password') aria-invalid="true" @enderror><button type="button" class="member-eye" data-member-toggle aria-controls="password" aria-label="Afficher le mot de passe" aria-pressed="false"><svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div><p class="member-help" id="member-password-help">Au moins 10 caractères. Communiquez ce mot de passe au membre par un canal privé.</p>@error('password')<p class="member-error">{{ $message }}</p>@enderror</div>
            <div><label for="password_confirmation">Confirmer le mot de passe</label><input id="password_confirmation" name="password_confirmation" type="password" required minlength="10" autocomplete="new-password"></div>
            <div class="member-access-note"><strong>Accès membre</strong><p>Produits, stocks et commandes uniquement. La gestion des membres et les réglages restent réservés au propriétaire.</p></div>
            <button type="submit">Créer le compte membre</button><p class="member-help">Un lien de validation sera envoyé à cette adresse. Le membre devra se connecter et valider son e-mail avant d’accéder à la boutique.</p>
        </form>
    </section></aside>
</div>
<script>document.querySelectorAll('[data-member-toggle]').forEach(button=>button.addEventListener('click',()=>{const input=document.getElementById(button.getAttribute('aria-controls'));const visible=input.type==='password';input.type=visible?'text':'password';button.setAttribute('aria-pressed',String(visible));button.setAttribute('aria-label',visible?'Masquer le mot de passe':'Afficher le mot de passe');}));</script>
@endsection
