@extends('layouts.app')
@section('title', 'Configuration mail')
@section('page-title', 'Configuration mail')
@section('content')
<div class="page-header"><div><h1>Configuration mail</h1><p>Configurez l’envoi des liens de validation aux vendeurs.</p></div></div>
@if(session('success'))<div class="flash" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="flash" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form class="panel" style="padding:24px;max-width:850px" method="post" action="{{ route('admin.mail-settings.update') }}">
    @csrf @method('put')
    <div class="form-grid">
        <div><label class="form-label" for="host">Serveur SMTP</label><input class="form-control" id="host" name="host" value="{{ old('host', $settings->host) }}" required></div>
        <div><label class="form-label" for="port">Port</label><select class="form-control" id="port" name="port">@foreach([587,465] as $port)<option value="{{ $port }}" @selected(old('port', $settings->port) == $port)>{{ $port }}</option>@endforeach</select></div>
        <div><label class="form-label" for="encryption">Chiffrement</label><select class="form-control" id="encryption" name="encryption"><option value="tls" @selected(old('encryption', $settings->encryption) === 'tls')>STARTTLS (port 587)</option><option value="ssl" @selected(old('encryption', $settings->encryption) === 'ssl')>TLS implicite (port 465)</option></select></div>
        <div><label class="form-label" for="username">Identifiant SMTP</label><input class="form-control" id="username" name="username" value="{{ old('username', $settings->username) }}" required autocomplete="off"></div>
        <div><label class="form-label" for="password">Mot de passe SMTP</label><input class="form-control" id="password" name="password" type="password" autocomplete="new-password"><p class="form-help">Laissez vide pour conserver le mot de passe actuel. Les modifications sont stockées chiffrées.</p></div>
        <div><label class="form-label" for="from_address">Adresse d’expédition</label><input class="form-control" id="from_address" name="from_address" type="email" value="{{ old('from_address', $settings->from_address) }}" required></div>
        <div><label class="form-label" for="from_name">Nom de l’expéditeur</label><input class="form-control" id="from_name" name="from_name" value="{{ old('from_name', $settings->from_name) }}" required></div>
    </div>
    <div class="action-row"><button class="btn btn-primary" type="submit">Enregistrer la configuration</button></div>
</form>
@endsection
