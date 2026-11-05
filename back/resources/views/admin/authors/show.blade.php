@extends('layouts.app')

@section('title', $author->name)
@section('page-title', $author->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Details auteur</h1>
            <div class="table-actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.authors.index') }}">Retour</a>
                <a class="btn btn-amber btn-sm" href="{{ route('admin.authors.edit', $author) }}">Modifier</a>
            </div>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Nom</div><div class="detail-value">{{ $author->name }}</div></div>
                <div class="detail-item"><div class="detail-label">Slug</div><div class="detail-value">{{ $author->slug }}</div></div>
                <div class="detail-item"><div class="detail-label">Livres</div><div class="detail-value">{{ $author->books->count() }}</div></div>
                <div class="detail-item" style="grid-column: 1 / -1"><div class="detail-label">Biographie</div><div class="detail-value">{{ $author->bio ?: '-' }}</div></div>
            </div>
        </div>
    </div>
@endsection
