@extends('layouts.app')

@section('title', 'Auteurs')
@section('page-title', 'Auteurs')

@section('content')
    @if (session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Gestion des auteurs</h1>
            <a class="btn btn-amber btn-sm" href="{{ route('admin.authors.create') }}">Nouvel auteur</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Livres</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($authors as $author)
                        <tr>
                            <td><strong>{{ $author->name }}</strong></td>
                            <td>{{ $author->slug }}</td>
                            <td>{{ $author->books_count ?? $author->books()->count() }}</td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.authors.show', $author) }}">Voir</a>
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.authors.edit', $author) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" onsubmit="return confirm('Supprimer cet auteur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state">Aucun auteur enregistre.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($authors->hasPages())
            <div class="pagination-wrap">{{ $authors->links() }}</div>
        @endif
    </div>
@endsection
