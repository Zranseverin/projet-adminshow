@extends('layouts.app')
@section('title', $order->reference)
@section('page-title', $order->reference)
@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Details commande</h1>
            <a class="btn btn-amber btn-sm" href="{{ route('admin.orders.edit', $order) }}">Modifier</a>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Qui a paye ?</div><div class="detail-value">{{ $order->customer_name }}</div></div>
                <div class="detail-item"><div class="detail-label">Contact</div><div class="detail-value">{{ $order->customer_email ?: '-' }}{{ $order->customer_phone ? ' / '.$order->customer_phone : '' }}</div></div>
                <div class="detail-item"><div class="detail-label">Total paye</div><div class="detail-value">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div></div>
                <div class="detail-item"><div class="detail-label">Produits payes</div><div class="detail-value">{{ $order->items->sum('quantity') }}</div></div>
                <div class="detail-item"><div class="detail-label">Statut</div><div class="detail-value">{{ $order->status }}</div></div>
                <div class="detail-item"><div class="detail-label">Paiement</div><div class="detail-value">{{ $order->payment_status }}{{ $order->payment_method ? ' - '.$order->payment_method : '' }}</div></div>
                <div class="detail-item"><div class="detail-label">Reference paiement</div><div class="detail-value">{{ $order->payment_reference ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Date paiement</div><div class="detail-value">{{ $order->paid_at?->format('d/m/Y H:i') ?? '-' }}</div></div>
                <div class="detail-item" style="grid-column:1/-1"><div class="detail-label">Adresse livraison</div><div class="detail-value">{{ trim(($order->shipping_address ?: '').' '.($order->shipping_city ?: '').' '.($order->shipping_country ?: '')) ?: '-' }}</div></div>
                <div class="detail-item" style="grid-column:1/-1"><div class="detail-label">Notes</div><div class="detail-value">{{ $order->notes ?: '-' }}</div></div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:20px">
        <div class="card-header">
            <h2 class="card-title">Livres commandes</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Auteur</th>
                        <th>Format</th>
                        <th>Quantite</th>
                        <th>Prix unitaire</th>
                        <th>Total ligne</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->items as $item)
                        <tr>
                            <td><strong>{{ $item->book_title }}</strong></td>
                            <td>{{ $item->author_name ?: $item->book?->author?->name ?: '-' }}</td>
                            <td><span class="badge badge-format">{{ $item->format ?: '-' }}</span></td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">Aucun livre rattache a cette commande.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <section class="dashboard-grid" style="margin-top:20px">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Historique commande</h2></div>
            <div class="card-body">
                <div class="activity-item">
                    <div class="activity-icon" style="background:var(--blue-bg)">1</div>
                    <div class="activity-text"><strong>Commande creee</strong><br>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background:var(--amber-light)">2</div>
                    <div class="activity-text"><strong>Statut actuel</strong><br>{{ $order->status }}</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background:var(--green-bg)">3</div>
                    <div class="activity-text"><strong>Derniere mise a jour</strong><br>{{ $order->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2 class="card-title">Historique livraison</h2></div>
            <div class="card-body">
                <div class="activity-item">
                    <div class="activity-icon" style="background:var(--green-bg)">L</div>
                    <div class="activity-text">
                        <strong>Adresse livraison</strong><br>
                        {{ trim(($order->shipping_address ?: '').' '.($order->shipping_city ?: '').' '.($order->shipping_country ?: '')) ?: '-' }}
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background:var(--amber-light)">F</div>
                    <div class="activity-text"><strong>Frais livraison</strong><br>{{ number_format($order->shipping_total, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background:var(--blue-bg)">S</div>
                    <div class="activity-text"><strong>Statut livraison</strong><br>{{ $order->status }}</div>
                </div>
            </div>
        </div>
    </section>

    <div class="card" style="margin-top:20px">
        <div class="card-header"><h2 class="card-title">Historique paiement</h2></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Methode</div><div class="detail-value">{{ $order->payment_method ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Statut</div><div class="detail-value">{{ $order->payment_status }}</div></div>
                <div class="detail-item"><div class="detail-label">Reference paiement</div><div class="detail-value">{{ $order->payment_reference ?: '-' }}</div></div>
                <div class="detail-item"><div class="detail-label">Date paiement</div><div class="detail-value">{{ $order->paid_at?->format('d/m/Y H:i') ?? 'En attente' }}</div></div>
                <div class="detail-item"><div class="detail-label">Sous-total</div><div class="detail-value">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</div></div>
                <div class="detail-item"><div class="detail-label">Total paye / a payer</div><div class="detail-value">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div></div>
            </div>
        </div>
    </div>
@endsection
