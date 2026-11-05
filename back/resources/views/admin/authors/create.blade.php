@extends('layouts.app')

@section('title', 'Nouvel auteur')
@section('page-title', 'Nouvel auteur')

@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">Ajouter un auteur</h1></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.authors.store') }}">
                @include('admin.authors._form', ['author' => new \App\Models\Author(), 'submitLabel' => 'Enregistrer'])
            </form>
        </div>
    </div>
@endsection
