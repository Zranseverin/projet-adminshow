@extends('layouts.app')
@section('title', 'Nouvelle promotion')
@section('page-title', 'Nouvelle promotion')
@section('content')
    <div class="card"><div class="card-header"><h1 class="card-title">Creer un code promo</h1></div><div class="card-body">
        <form method="POST" action="{{ route('admin.promotions.store') }}">
            @include('admin.promotions._form', ['submitLabel' => 'Enregistrer'])
        </form>
    </div></div>
@endsection
