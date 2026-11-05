@extends('layouts.app')

@section('title', 'Modifier auteur')
@section('page-title', 'Modifier auteur')

@section('content')
    <div class="card">
        <div class="card-header"><h1 class="card-title">{{ $author->name }}</h1></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.authors.update', $author) }}">
                @include('admin.authors._form', ['method' => 'PUT', 'submitLabel' => 'Mettre a jour'])
            </form>
        </div>
    </div>
@endsection
