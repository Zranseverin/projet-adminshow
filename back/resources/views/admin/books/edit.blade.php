@extends('layouts.app')

@section('title', 'Modifier le livre')
@section('page-title', 'Modifier le livre')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">{{ $book->title }}</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
                @include('admin.books._form', ['method' => 'PUT', 'submitLabel' => 'Mettre a jour'])
            </form>
        </div>
    </div>
@endsection
