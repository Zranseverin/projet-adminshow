@extends('layouts.app')

@section('title', 'Commandes')
@section('page-title', 'Gestion des commandes')

@section('content')
    @if (session('success')) <div class="flash">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-header">
            <form class="toolbar" method="GET" action="{{ route('admin.orders.index') }}" style="flex: 1">
                <label class="search-field">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Reference, client, email...">
                </label>
                <select class="select-filter" name="status" style="max-width: 180px">
                    <option value="">Tous les statuts</option>
                    @foreach (['pending' => 'En attente', 'processing' => 'En preparation', 'shipped' => 'Expediee', 'delivered' => 'Livree', 'cancelled' => 'Annulee'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-ghost btn-sm" type="submit">Filtrer</button>
            </form>
            <a class="btn btn-amber btn-sm" href="{{ route('admin.orders.create') }}">Nouvelle commande</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Reference</th><th>Qui a paye ?</th><th>Total paye</th><th>Paiement</th><th>Statut</th><th>Produits payes</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><strong>{{ $order->reference }}</strong></td>
                            <td>{{ $order->customer_name }}<div class="book-author">{{ $order->customer_email }}</div></td>
                            <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="badge {{ $order->payment_status === 'paid' ? 'badge-stock' : 'badge-low' }}">{{ $order->payment_status }}</span>
                                <div class="book-author">{{ $order->payment_method ?: '-' }}</div>
                            </td>
                            <td><span class="badge badge-format">{{ $order->status }}</span></td>
                            <td>{{ $order->products_paid_count ?? 0 }} produit{{ ($order->products_paid_count ?? 0) > 1 ? 's' : '' }}</td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.orders.show', $order) }}">Voir</a>
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.orders.edit', $order) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Supprimer cette commande ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">Aucune commande enregistree.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages()) <div class="pagination-wrap">{{ $orders->links() }}</div> @endif
    </div>

    <section class="dashboard-grid" style="margin-top:20px">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Historique des commandes</h2></div>
            <div class="card-body">
                @forelse ($orderHistory as $historyOrder)
                    <div class="activity-item">
                        <div class="activity-icon" style="background:var(--blue-bg)">CMD</div>
                        <div class="activity-text">
                            <strong>{{ $historyOrder->reference }}</strong> creee par {{ $historyOrder->customer_name }}
                            <br>
                            <span class="book-author">{{ number_format($historyOrder->total, 0, ',', ' ') }} FCFA · {{ $historyOrder->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <a class="btn btn-ghost btn-sm" href="{{ route('admin.orders.show', $historyOrder) }}">Voir</a>
                    </div>
                @empty
                    <div class="empty-state">Aucun historique de commande.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2 class="card-title">Historique livraison</h2></div>
            <div class="card-body">
                @forelse ($deliveryHistory as $deliveryOrder)
                    <div class="activity-item">
                        <div class="activity-icon" style="background:var(--green-bg)">LIV</div>
                        <div class="activity-text">
                            <strong>{{ $deliveryOrder->reference }}</strong> · {{ $deliveryOrder->status }}
                            <br>
                            <span class="book-author">
                                {{ trim(($deliveryOrder->shipping_city ?: '').' '.($deliveryOrder->shipping_country ?: '')) ?: 'Adresse non renseignee' }}
                                · Frais: {{ number_format($deliveryOrder->shipping_total, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div class="activity-time">{{ $deliveryOrder->updated_at->format('d/m H:i') }}</div>
                    </div>
                @empty
                    <div class="empty-state">Aucun historique de livraison.</div>
                @endforelse
            </div>
        </div>
    </section>

    <div class="card" style="margin-top:20px">
        <div class="card-header"><h2 class="card-title">Historique des paiements</h2></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Reference commande</th>
                        <th>Client</th>
                        <th>Methode</th>
                        <th>Statut paiement</th>
                        <th>Reference paiement</th>
                        <th>Montant</th>
                        <th>Date paiement</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paymentHistory as $paymentOrder)
                        <tr>
                            <td><strong>{{ $paymentOrder->reference }}</strong></td>
                            <td>{{ $paymentOrder->customer_name }}</td>
                            <td>{{ $paymentOrder->payment_method ?: '-' }}</td>
                            <td><span class="badge {{ $paymentOrder->payment_status === 'paid' ? 'badge-stock' : 'badge-low' }}">{{ $paymentOrder->payment_status }}</span></td>
                            <td>{{ $paymentOrder->payment_reference ?: '-' }}</td>
                            <td>{{ number_format($paymentOrder->total, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $paymentOrder->paid_at?->format('d/m/Y H:i') ?? 'En attente' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">Aucun historique de paiement.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
