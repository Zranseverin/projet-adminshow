@extends('layouts.app')
@section('page-title')@yield('title', 'Administration')@endsection
@section('content-class', 'marketplace-admin')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/marketplace-admin.css') }}?v=1">
@endpush
@section('before-content')
    @if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="notice error" role="alert"><strong>Vérifiez les informations suivantes :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@endsection
