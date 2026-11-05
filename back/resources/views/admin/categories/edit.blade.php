@extends('layouts.app')

@section('title', 'Modifier categorie')
@section('page-title', 'Modifier categorie')

@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">{{ $category->name }}</h1></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @include('admin.categories._form', ['method' => 'PUT', 'submitLabel' => 'Mettre a jour'])
            </form>
        </div>
    </div>
@endsection
