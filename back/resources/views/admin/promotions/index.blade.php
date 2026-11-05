@extends('layouts.app')

@section('title', 'Promotions')
@section('page-title', 'Promotions et codes promo')

@section('content')
    @if (session('success')) <div class="flash">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-header">
            <form class="toolbar" method="GET" action="{{ route('admin.promotions.index') }}" style="flex:1">
                <label class="search-field">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Code ou nom de promotion...">
                </label>
                <button class="btn btn-ghost btn-sm" type="submit">Filtrer</button>
            </form>
            <a class="btn btn-amber btn-sm" href="{{ route('admin.promotions.create') }}">Creer un code</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Code</th><th>Nom</th><th>Remise</th><th>Minimum</th><th>Utilisations</th><th>Expiration</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        <tr>
                            <td><strong>{{ $promotion->code }}</strong></td>
                            <td>{{ $promotion->name }}</td>
                            <td>{{ $promotion->type === 'percent' ? $promotion->value.' %' : number_format($promotion->value, 0, ',', ' ').' FCFA' }}</td>
                            <td>{{ $promotion->minimum_amount ? number_format($promotion->minimum_amount, 0, ',', ' ').' FCFA' : '-' }}</td>
                            <td>{{ $promotion->used_count }}{{ $promotion->usage_limit ? ' / '.$promotion->usage_limit : '' }}</td>
                            <td>{{ $promotion->expires_at?->format('d/m/Y') ?? '-' }}</td>
                            <td><span class="badge {{ $promotion->is_active ? 'badge-stock' : 'badge-muted' }}">{{ $promotion->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.promotions.show', $promotion) }}">Voir</a>
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.promotions.edit', $promotion) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" onsubmit="return confirm('Supprimer cette promotion ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="empty-state">Aucune promotion enregistree.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($promotions->hasPages()) <div class="pagination-wrap">{{ $promotions->links() }}</div> @endif
    </div>
@endsection
