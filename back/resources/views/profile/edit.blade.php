@extends('layouts.app')

@section('title', 'Modifier mon profil')
@section('page-title', 'Modifier mon profil')

@section('content')
<section class="card" style="max-width:900px;margin:0 auto">
    <div class="card-header"><div><h1 class="card-title">Informations du compte</h1><p class="form-help">Mettez à jour vos informations personnelles et vos accès.</p></div></div>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-grid">
                <div class="form-field"><label class="form-label" for="name">Nom d'utilisateur *</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label class="form-label" for="name_complet">Nom complet</label><input class="form-control" id="name_complet" name="name_complet" value="{{ old('name_complet', $user->name_complet) }}">@error('name_complet')<div class="form-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label class="form-label" for="email">Adresse e-mail *</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email')<div class="form-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label class="form-label" for="telephone">Téléphone</label><input class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}">@error('telephone')<div class="form-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label class="form-label" for="adresse">Adresse</label><input class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $user->adresse) }}">@error('adresse')<div class="form-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label class="form-label" for="genre_id">Genre</label><select class="form-control" id="genre_id" name="genre_id"><option value="">Non renseigné</option>@foreach($genres as $genre)<option value="{{ $genre->id }}" @selected((string) old('genre_id', $user->genre_id) === (string) $genre->id)>{{ $genre->libelle }}</option>@endforeach</select>@error('genre_id')<div class="form-error">{{ $message }}</div>@enderror</div>
            </div>

            <div class="profile-password-section">
                <h2>Changer le mot de passe</h2>
                <p>Laissez ces champs vides pour conserver le mot de passe actuel.</p>
                <div class="form-grid">
                    <div class="form-field full"><label class="form-label" for="current_password">Mot de passe actuel</label><input class="form-control" id="current_password" type="password" name="current_password" autocomplete="current-password">@error('current_password')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-field"><label class="form-label" for="password">Nouveau mot de passe</label><input class="form-control" id="password" type="password" name="password" autocomplete="new-password">@error('password')<div class="form-error">{{ $message }}</div>@enderror</div>
                    <div class="form-field"><label class="form-label" for="password_confirmation">Confirmation</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"></div>
                </div>
            </div>
            <div class="action-row"><a class="btn btn-ghost" href="{{ route('profile.show') }}">Annuler</a><button class="btn btn-amber" type="submit">Enregistrer les modifications</button></div>
        </div>
    </form>
</section>
@endsection

@push('styles')
<style>
.profile-password-section{margin-top:26px;padding-top:22px;border-top:1px solid var(--border)}.profile-password-section h2{color:var(--navy);font-size:15px}.profile-password-section>p{margin:5px 0 16px;color:var(--muted);font-size:12px}
</style>
@endpush