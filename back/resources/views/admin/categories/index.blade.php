@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Genres et categories</h1>
            <a class="btn btn-amber btn-sm" href="{{ route('admin.categories.create') }}">Nouvelle categorie</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Livres</th>
                        <th>Visible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->books_count ?? $category->books()->count() }}</td>
                            <td><span class="badge {{ $category->is_active ? 'badge-stock' : 'badge-muted' }}">{{ $category->is_active ? 'Oui' : 'Non' }}</span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.categories.show', $category) }}">Voir</a>
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.categories.edit', $category) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette categorie ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">Aucune categorie enregistree.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="pagination-wrap">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection
