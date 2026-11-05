@extends('layouts.app')
@section('title', 'Nouvelle commande')
@section('page-title', 'Nouvelle commande')
@section('content')
    <div class="card"><div class="card-header"><h1 class="card-title">Ajouter une commande</h1></div><div class="card-body">
        <form method="POST" action="{{ route('admin.orders.store') }}">
            @include('admin.orders._form', ['submitLabel' => 'Enregistrer'])
        </form>
    </div></div>
@endsection
