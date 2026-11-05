@extends('marketplace.layout')
@section('title', 'Publicités du catalogue')
@section('content')
<div class="heading"><div><h1>Publicités du catalogue</h1><p class="muted">Gérez les annonces affichées sous les filtres de la boutique publique.</p></div><a class="btn" href="{{ route('marketplace.advertisements.create') }}">Ajouter une publicité</a></div>
<div class="card"><div class="table-wrap"><table><thead><tr><th>Aperçu</th><th>Publicité</th><th>Ordre</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
@forelse($advertisements as $advertisement)
<tr><td>@if($advertisement->public_image_url)<img src="{{ $advertisement->public_image_url }}" alt="{{ $advertisement->title }}" loading="lazy" width="180" height="95" style="object-fit:contain;background:#f4f6f8;max-width:100%">@endif</td><td><strong>{{ $advertisement->title }}</strong>@if($advertisement->button_url)<p class="muted" style="overflow-wrap:anywhere;max-width:260px">{{ $advertisement->button_url }}</p>@endif</td><td>{{ $advertisement->sort_order }}</td><td><span class="tag">{{ $advertisement->is_active ? 'Active' : 'Masquée' }}</span></td><td><div class="row"><a class="btn secondary" href="{{ route('marketplace.advertisements.edit', $advertisement) }}">Modifier</a><form method="post" action="{{ route('marketplace.advertisements.destroy', $advertisement) }}" onsubmit="return confirm('Supprimer cette publicité ?')">@csrf @method('delete')<button class="secondary">Supprimer</button></form></div></td></tr>
@empty<tr><td colspan="5" class="empty">Aucune publicité. Ajoutez votre première annonce.</td></tr>@endforelse
</tbody></table></div><div class="pagination">@if($advertisements->previousPageUrl())<a href="{{ $advertisements->previousPageUrl() }}">Précédent</a>@endif @if($advertisements->nextPageUrl())<a href="{{ $advertisements->nextPageUrl() }}">Suivant</a>@endif</div></div>
@endsection
