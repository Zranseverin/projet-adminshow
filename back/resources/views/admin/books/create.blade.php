@extends('layouts.app')

@section('title', 'Nouveau livre')
@section('page-title', 'Nouveau livre')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Ajouter un livre au catalogue</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
                @include('admin.books._form', ['book' => new \App\Models\Book(), 'submitLabel' => 'Enregistrer'])
            </form>
        </div>
    </div>
@endsection
