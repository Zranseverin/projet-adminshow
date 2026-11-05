@extends('layouts.app')

@section('title', 'Nouvelle categorie')
@section('page-title', 'Nouvelle categorie')

@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">Ajouter une categorie</h1></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @include('admin.categories._form', ['category' => new \App\Models\Category(), 'submitLabel' => 'Enregistrer'])
            </form>
        </div>
    </div>
@endsection
