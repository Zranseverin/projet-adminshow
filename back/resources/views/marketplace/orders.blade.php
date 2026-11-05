@extends('marketplace.layout')
@section('title', 'Commandes')
@section('content')
@php($admin = auth()->user()->account_type === 'admin')
@php($prefix = $admin ? 'marketplace' : 'seller')
<div class="heading"><div><h1>{{ $admin ? 'Commandes et reversements' : 'Mes commandes' }}</h1><p class="muted">Chaque commande est répartie par boutique. Les paiements sont vérifiés manuellement.</p></div></div>
@forelse($orders as $order)
<section class="card"><div class="heading"><div><h2>{{ $order->seller_name }}</h2><p class="muted">Référence {{ $order->marketOrder->reference }}<br>{{ $order->created_at->format('d/m/Y H:i') }}</p></div><span class="tag">{{ $order->payment_status === 'paid' ? 'Paiement confirmé' : 'Paiement en attente' }}</span></div>
<div class="grid"><div><h3>Client et livraison</h3><p>{{ $order->marketOrder->customer_name }}<br>{{ $order->marketOrder->customer_phone }}<br>{{ $order->marketOrder->customer_email }}</p><p class="pre">{{ $order->marketOrder->address }}</p></div><div><h3>Paiement {{ $order->marketOrder->payment_mode === 'platform' ? 'à la plateforme' : 'direct au vendeur' }}</h3><p>Total : <strong>{{ number_format($order->total_minor / 100, 0, ',', ' ') }} FCFA</strong><br>Commission : {{ number_format($order->commission_minor / 100, 0, ',', ' ') }} FCFA<br>Part vendeur : {{ number_format($order->seller_due_minor / 100, 0, ',', ' ') }} FCFA</p>@if($order->payment_reference)<p>Référence du paiement : {{ $order->payment_reference }}</p>@endif</div></div>
<div class="table-wrap"><table><thead><tr><th>Produit</th><th>Quantité</th><th>Total</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td>{{ $item->name }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->total_minor / 100, 0, ',', ' ') }} FCFA</td></tr>@endforeach</tbody></table></div>
@if($order->status === 'cancelled')<div class="notice">Commande annulée. Les produits ont été remis en stock.</div>@else
<form class="row" method="post" action="{{ route($prefix.'.orders.update', $order) }}" style="margin-top:20px">@csrf @method('put')<input type="hidden" name="action" value="status"><label for="status-{{ $order->id }}">Traitement</label><select id="status-{{ $order->id }}" name="status" style="width:auto">@foreach(['pending'=>'À traiter','processing'=>'En préparation','shipped'=>'Expédiée','delivered'=>'Livrée'] as $key=>$label)<option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>@endforeach</select><button class="secondary">Mettre à jour</button></form>
@if($order->payment_status === 'pending' && ($admin || $order->marketOrder->payment_mode === 'direct'))
<form class="row" method="post" action="{{ route($prefix.'.orders.update', $order) }}" style="margin-top:20px">@csrf @method('put')<input type="hidden" name="action" value="payment"><input aria-label="Référence du paiement reçu" name="reference" required placeholder="Référence du paiement vérifié" style="flex:1;min-width:200px"><button>Confirmer le paiement reçu</button></form>
@endif
@if($order->seller_id && $order->marketOrder->payment_mode === 'platform')
<div class="notice" style="margin-top:20px">{{ $order->settled_at ? 'Reversement enregistré le '.$order->settled_at->format('d/m/Y').' · '.$order->payout_reference : 'Reversement au vendeur en attente' }}</div>
@if($admin && $order->payment_status === 'paid' && !$order->settled_at)<form class="row" method="post" action="{{ route('marketplace.orders.update', $order) }}">@csrf @method('put')<input type="hidden" name="action" value="payout"><input aria-label="Référence du reversement" required name="reference" placeholder="Référence du reversement effectué" style="flex:1"><button>Enregistrer le reversement</button></form>@endif
@endif
@if($order->payment_status === 'pending' && in_array($order->status, ['pending','processing'], true))<form method="post" action="{{ route($prefix.'.orders.update', $order) }}" style="margin-top:16px">@csrf @method('put')<input type="hidden" name="action" value="cancel"><button class="secondary">Annuler et remettre en stock</button></form>@endif
@endif
</section>
@empty<div class="card empty">Aucune commande pour le moment.</div>@endforelse
<div class="pagination">@if($orders->previousPageUrl())<a href="{{ $orders->previousPageUrl() }}">Précédent</a>@endif @if($orders->nextPageUrl())<a href="{{ $orders->nextPageUrl() }}">Suivant</a>@endif</div>
@endsection
