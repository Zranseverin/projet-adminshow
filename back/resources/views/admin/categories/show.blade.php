@extends('layouts.app')

@section('title', $category->name)
@section('page-title', $category->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Details categorie</h1>
            <div class="table-actions">
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.categories.index') }}">Retour</a>
                <a class="btn btn-amber btn-sm" href="{{ route('admin.categories.edit', $category) }}">Modifier</a>
            </div>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Nom</div><div class="detail-value">{{ $category->name }}</div></div>
                <div class="detail-item"><div class="detail-label">Slug</div><div class="detail-value">{{ $category->slug }}</div></div>
                <div class="detail-item"><div class="detail-label">Visible</div><div class="detail-value">{{ $category->is_active ? 'Oui' : 'Non' }}</div></div>
                <div class="detail-item"><div class="detail-label">Livres</div><div class="detail-value">{{ $category->books->count() }}</div></div>
            </div>
        </div>
    </div>
@endsection
