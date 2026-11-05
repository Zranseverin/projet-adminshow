@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Vue d’ensemble')

@push('styles')
<style>
    .content:has(.book-dashboard) { padding: 0; background: #f6f8fb; }
    .book-dashboard { --dash-blue:#1769d2; --dash-green:#27955b; --dash-orange:#f48132; --dash-red:#e5484d; --dash-purple:#6e56cf; color:#14213d; }
    .book-dashboard * { letter-spacing: 0; }
    .book-dashboard__toolbar { display:flex; align-items:center; gap:14px; min-height:72px; padding:14px 22px; border-bottom:1px solid #dfe5ee; background:#fff; }
    .book-dashboard__search { flex:1; max-width:680px; min-width:240px; height:42px; display:flex; align-items:center; gap:10px; padding:0 14px; border:1px solid #d7deea; border-radius:6px; color:#71809a; background:#fff; }
    .book-dashboard__search input { width:100%; border:0; outline:0; color:#14213d; font-size:13px; }
    .book-dashboard__period { height:42px; padding:0 12px; border:1px solid #d7deea; border-radius:6px; color:#34425b; background:#fff; font-size:12px; }
    .book-dashboard__export { height:42px; display:inline-flex; align-items:center; gap:8px; padding:0 16px; border:0; border-radius:6px; color:#fff; background:var(--dash-blue); font-weight:600; cursor:pointer; }
    .book-dashboard__body { padding:18px 20px 28px; }
    .book-dashboard__heading { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:16px; }
    .book-dashboard__heading h1 { color:#111a2e; font-size:20px; font-weight:700; }
    .book-dashboard__heading p { margin-top:4px; color:#77839a; font-size:12px; }
    .book-dashboard__date { color:#77839a; font-size:12px; }
    .book-dashboard__stats { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:10px; margin-bottom:12px; }
    .book-dashboard__stat { min-height:124px; padding:16px; border:1px solid #dfe5ee; border-radius:7px; background:#fff; }
    .book-dashboard__stat-top { display:flex; align-items:center; gap:9px; color:#4a566c; font-size:11px; font-weight:600; }
    .book-dashboard__stat-icon { width:30px; height:30px; display:grid; place-items:center; border-radius:50%; color:#fff; }
    .book-dashboard__stat-value { margin-top:17px; color:#111a2e; font-size:24px; font-weight:700; line-height:1; }
    .book-dashboard__stat-note { margin-top:7px; color:#77839a; font-size:11px; }
    .book-dashboard__stat-note.positive { color:var(--dash-green); }
    .book-dashboard__stat-note.warning { color:var(--dash-red); }
    .book-dashboard__tabs { display:flex; align-items:center; gap:4px; margin-bottom:12px; padding:0 12px; border:1px solid #dfe5ee; border-radius:7px; background:#fff; overflow-x:auto; }
    .book-dashboard__tab { min-height:50px; display:inline-flex; align-items:center; gap:7px; padding:0 14px; border:0; border-bottom:2px solid transparent; color:#4a566c; background:transparent; font-size:12px; white-space:nowrap; }
    .book-dashboard__tab.active { border-bottom-color:var(--dash-blue); color:var(--dash-blue); font-weight:600; }
    .book-dashboard__grid { display:grid; grid-template-columns:minmax(0,1.65fr) minmax(310px,.9fr); gap:12px; margin-bottom:12px; }
    .book-dashboard__panel { border:1px solid #dfe5ee; border-radius:7px; background:#fff; overflow:hidden; }
    .book-dashboard__panel-header { min-height:48px; display:flex; align-items:center; justify-content:space-between; gap:12px; padding:0 16px; border-bottom:1px solid #e6eaf0; }
    .book-dashboard__panel-title { color:#172039; font-size:12px; font-weight:700; }
    .book-dashboard__panel-link { color:var(--dash-blue); font-size:11px; font-weight:600; }
    .book-dashboard__overview { display:grid; grid-template-columns:260px minmax(0,1fr); min-height:265px; }
    .book-dashboard__donut-area { display:flex; align-items:center; gap:18px; padding:22px 16px; border-right:1px solid #e6eaf0; }
    .book-dashboard__donut { width:125px; aspect-ratio:1; flex:none; display:grid; place-items:center; position:relative; border-radius:50%; background:conic-gradient(var(--dash-blue) 0 63%, var(--dash-orange) 63% 82%, var(--dash-red) 82% 100%); }
    .book-dashboard__donut::before { content:''; position:absolute; inset:20px; border-radius:50%; background:#fff; }
    .book-dashboard__donut-center { position:relative; z-index:1; text-align:center; }
    .book-dashboard__donut-center strong { display:block; font-size:25px; }
    .book-dashboard__donut-center span { color:#68758c; font-size:10px; }
    .book-dashboard__legend { display:grid; gap:12px; width:100%; }
    .book-dashboard__legend-row { display:grid; grid-template-columns:10px 1fr auto; gap:7px; color:#4d5970; font-size:11px; }
    .book-dashboard__dot { width:9px; height:9px; border-radius:50%; }
    .book-dashboard table { width:100%; border-collapse:collapse; }
    .book-dashboard th { padding:9px 11px; border-bottom:1px solid #dfe5ee; color:#5b667b; background:#f5f7fa; font-size:10px; font-weight:700; letter-spacing:0; text-transform:none; }
    .book-dashboard td { padding:9px 11px; border-bottom:1px solid #e6eaf0; color:#29354d; font-size:10.5px; }
    .book-dashboard tbody tr:hover { background:#f8faff; }
    .book-dashboard__status { display:inline-flex; padding:4px 7px; border-radius:5px; font-size:9px; font-weight:600; }
    .book-dashboard__status.green { color:#23824e; background:#e9f7ef; }
    .book-dashboard__status.orange { color:#d96018; background:#fff0e7; }
    .book-dashboard__performance { padding:5px 16px; }
    .book-dashboard__metric { min-height:42px; display:grid; grid-template-columns:24px 1fr auto; align-items:center; gap:6px; border-bottom:1px solid #e6eaf0; color:#405069; font-size:11px; }
    .book-dashboard__metric:last-child { border-bottom:0; }
    .book-dashboard__metric-icon { color:var(--dash-blue); }
    .book-dashboard__metric strong { color:#111a2e; font-size:12px; }
    .book-dashboard__stars { color:#f4b400; letter-spacing:2px; white-space:nowrap; }
    .book-dashboard__financial { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; padding:14px 16px; }
    .book-dashboard__finance-card { padding:12px; border:1px solid #dfe5ee; border-radius:6px; background:#f8fafc; }
    .book-dashboard__finance-card span { display:block; margin-bottom:6px; color:#68758c; font-size:10px; }
    .book-dashboard__finance-card strong { color:#172039; font-size:14px; }
    .book-dashboard__finance-card.green { background:#f0faf5; } .book-dashboard__finance-card.green strong { color:#23824e; }
    .book-dashboard__finance-card.purple { background:#f4f2ff; } .book-dashboard__finance-card.purple strong { color:#5f46c7; }
    .book-dashboard__finance-card.orange { background:#fff5ee; } .book-dashboard__finance-card.orange strong { color:#dd681f; }
    .book-dashboard__stock-list { padding:4px 16px 10px; }
    .book-dashboard__stock-item { display:grid; grid-template-columns:1fr auto; gap:10px; padding:12px 0; border-bottom:1px solid #e6eaf0; }
    .book-dashboard__stock-item:last-child { border-bottom:0; }
    .book-dashboard__stock-title { color:#26334b; font-size:11px; font-weight:600; }
    .book-dashboard__stock-meta { margin-top:3px; color:#7a869b; font-size:9.5px; }
    .book-dashboard__stock-count { align-self:center; color:var(--dash-red); font-size:11px; font-weight:700; }
    .book-dashboard__bottom { display:grid; grid-template-columns:minmax(0,1.65fr) minmax(310px,.9fr); gap:12px; }
    @media(max-width:1250px){ .book-dashboard__stats{grid-template-columns:repeat(3,1fr)} .book-dashboard__grid,.book-dashboard__bottom{grid-template-columns:1fr} }
    @media(max-width:850px){ .book-dashboard__toolbar{flex-wrap:wrap}.book-dashboard__search{order:3;max-width:none;flex-basis:100%}.book-dashboard__stats{grid-template-columns:repeat(2,1fr)}.book-dashboard__overview{grid-template-columns:1fr}.book-dashboard__donut-area{border-right:0;border-bottom:1px solid #e6eaf0}.book-dashboard__financial{grid-template-columns:repeat(2,1fr)} }
    @media(max-width:560px){ .book-dashboard__body{padding:14px}.book-dashboard__heading{align-items:flex-start;flex-direction:column}.book-dashboard__stats{grid-template-columns:1fr}.book-dashboard__overview{display:block}.book-dashboard__donut-area{flex-direction:column}.book-dashboard__financial{grid-template-columns:1fr}.book-dashboard__export span{display:none} }
</style>
@endpush

@section('content')
<div class="book-dashboard">
    <form class="book-dashboard__toolbar" method="GET" action="{{ route('dashboard') }}">
        <label class="book-dashboard__search">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher une commande ou un client..." aria-label="Rechercher">
        </label>
        <select class="book-dashboard__period" name="period" aria-label="Période" onchange="this.form.submit()"><option value="week" @selected($period === 'week')>Cette semaine</option><option value="month" @selected($period === 'month')>Ce mois</option><option value="year" @selected($period === 'year')>Cette année</option></select>
        <button class="book-dashboard__export" type="submit"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg><span>Exporter le rapport</span></button>
    </form>

    <div class="book-dashboard__body">
        <div class="book-dashboard__heading">
            <div><h1>Tableau de bord</h1><p>Suivez l’activité de votre librairie en un coup d’œil.</p></div>
            <div class="book-dashboard__date">Mise à jour : {{ now()->format('d/m/Y à H:i') }}</div>
        </div>

        <section class="book-dashboard__stats" aria-label="Indicateurs clés">
            <article class="book-dashboard__stat"><div class="book-dashboard__stat-top"><span class="book-dashboard__stat-icon" style="background:#1769d2"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z"/></svg></span>Livres au catalogue</div><div class="book-dashboard__stat-value">{{ number_format($bookCount, 0, ',', ' ') }}</div><div class="book-dashboard__stat-note positive">+{{ $newBookCount }} nouveaux sur la période</div></article>
            <article class="book-dashboard__stat"><div class="book-dashboard__stat-top"><span class="book-dashboard__stat-icon" style="background:#27955b"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14h18V6l-3-4z"/><path d="M3 6h18"/></svg></span>Commandes</div><div class="book-dashboard__stat-value">{{ number_format($orderCount, 0, ',', ' ') }}</div><div class="book-dashboard__stat-note {{ $orderGrowth >= 0 ? 'positive' : 'warning' }}">{{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}% sur la période</div></article>
            <article class="book-dashboard__stat"><div class="book-dashboard__stat-top"><span class="book-dashboard__stat-icon" style="background:#f48132"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg></span>Chiffre d’affaires</div><div class="book-dashboard__stat-value">{{ number_format($revenue, 0, ',', ' ') }}</div><div class="book-dashboard__stat-note {{ $revenueGrowth >= 0 ? 'positive' : 'warning' }}">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% · FCFA</div></article>
            <article class="book-dashboard__stat"><div class="book-dashboard__stat-top"><span class="book-dashboard__stat-icon" style="background:#6e56cf"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>Nouveaux clients</div><div class="book-dashboard__stat-value">{{ number_format($customerCount, 0, ',', ' ') }}</div><div class="book-dashboard__stat-note {{ $customerGrowth >= 0 ? 'positive' : 'warning' }}">{{ $customerGrowth >= 0 ? '+' : '' }}{{ $customerGrowth }}% sur la période</div></article>
            <article class="book-dashboard__stat"><div class="book-dashboard__stat-top"><span class="book-dashboard__stat-icon" style="background:#e5484d"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 2.9 1.8 17a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></svg></span>Stock faible</div><div class="book-dashboard__stat-value">{{ $lowStockCount }}</div><div class="book-dashboard__stat-note warning">Articles à réapprovisionner</div></article>
        </section>

        <nav class="book-dashboard__tabs" aria-label="Sections du tableau de bord"><button class="book-dashboard__tab active">Vue d’ensemble</button><button class="book-dashboard__tab">Ventes</button><button class="book-dashboard__tab">Commandes</button><button class="book-dashboard__tab">Catalogue</button><button class="book-dashboard__tab">Clients</button></nav>

        <div class="book-dashboard__grid">
            <section class="book-dashboard__panel">
                <div class="book-dashboard__panel-header"><h2 class="book-dashboard__panel-title">Activité commerciale</h2><span class="book-dashboard__panel-link">{{ $start->format('d/m/Y') }} → {{ $end->format('d/m/Y') }}</span></div>
                <div class="book-dashboard__overview">
                    <div class="book-dashboard__donut-area"><div class="book-dashboard__donut"><div class="book-dashboard__donut-center"><strong>{{ $orderCount }}</strong><span>commandes</span></div></div><div class="book-dashboard__legend"><div class="book-dashboard__legend-row"><span class="book-dashboard__dot" style="background:#1769d2"></span><span>Livrées</span><strong>{{ $deliveredCount }}</strong></div><div class="book-dashboard__legend-row"><span class="book-dashboard__dot" style="background:#f48132"></span><span>En cours</span><strong>{{ $inProgressCount }}</strong></div><div class="book-dashboard__legend-row"><span class="book-dashboard__dot" style="background:#e5484d"></span><span>Annulées</span><strong>{{ $cancelledCount }}</strong></div></div></div>
                    <div><table><thead><tr><th>Date</th><th>Commande</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead><tbody>
@forelse($recentOrders as $order)
<tr><td>{{ $order->created_at->format('d/m/Y') }}</td><td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->reference }}</a></td><td>{{ $order->customer_name }}</td><td>{{ number_format((float) $order->total, 0, ',', ' ') }}</td><td><span class="book-dashboard__status {{ in_array(strtolower($order->status), ['delivered', 'completed', 'livree', 'livrée']) ? 'green' : 'orange' }}">{{ ucfirst($order->status) }}</span></td></tr>
@empty
<tr><td colspan="5" class="empty-state">Aucune commande trouvée.</td></tr>
@endforelse
</tbody></table></div>
                </div>
            </section>

            <section class="book-dashboard__panel"><div class="book-dashboard__panel-header"><h2 class="book-dashboard__panel-title">Performance</h2><select class="book-dashboard__period" style="height:32px"><option>Ce mois</option></select></div><div class="book-dashboard__performance"><div class="book-dashboard__metric"><span class="book-dashboard__metric-icon">✓</span><span>Taux de livraison</span><strong>{{ $deliveryRate }}%</strong></div><div class="book-dashboard__metric"><span class="book-dashboard__metric-icon">↗</span><span>Panier moyen</span><strong>{{ number_format($averageBasket, 0, ',', ' ') }} F</strong></div><div class="book-dashboard__metric"><span class="book-dashboard__metric-icon">◎</span><span>Commandes payées</span><strong>{{ $paidRate }}%</strong></div><div class="book-dashboard__metric"><span class="book-dashboard__metric-icon">↩</span><span>Annulations</span><strong>{{ $cancelledCount }}</strong></div><div class="book-dashboard__metric"><span class="book-dashboard__metric-icon">☆</span><span>Satisfaction client</span><strong>{{ number_format($averageRating, 1, ',', ' ') }}/5</strong></div><div class="book-dashboard__metric"><span class="book-dashboard__metric-icon">◷</span><span>Commandes en cours</span><strong>{{ $inProgressCount }}</strong></div></div></section>
        </div>

        <div class="book-dashboard__bottom">
            <section class="book-dashboard__panel"><div class="book-dashboard__panel-header"><h2 class="book-dashboard__panel-title">Synthèse financière</h2><a href="#" class="book-dashboard__panel-link">Voir le rapport</a></div><div class="book-dashboard__financial"><div class="book-dashboard__finance-card green"><span>Ventes encaissées</span><strong>{{ number_format($revenue, 0, ',', ' ') }} FCFA</strong></div><div class="book-dashboard__finance-card purple"><span>Promotions accordées</span><strong>{{ number_format($discountTotal, 0, ',', ' ') }} FCFA</strong></div><div class="book-dashboard__finance-card"><span>Frais de livraison</span><strong>{{ number_format($shippingTotal, 0, ',', ' ') }} FCFA</strong></div><div class="book-dashboard__finance-card orange"><span>Commandes en attente</span><strong>{{ number_format($pendingTotal, 0, ',', ' ') }} FCFA</strong></div></div><table><thead><tr><th>Canal</th><th>Commandes</th><th>Montant total</th><th>Part des ventes</th></tr></thead><tbody>
@forelse($paymentChannels as $channel)
<tr><td>{{ ucfirst($channel['name']) }}</td><td>{{ $channel['count'] }}</td><td>{{ number_format($channel['total'], 0, ',', ' ') }} FCFA</td><td>{{ $revenue > 0 ? number_format(($channel['total'] / $revenue) * 100, 1, ',', ' ') : 0 }}%</td></tr>
@empty
<tr><td colspan="4" class="empty-state">Aucun paiement sur cette période.</td></tr>
@endforelse
</tbody></table></section>
            <section class="book-dashboard__panel"><div class="book-dashboard__panel-header"><h2 class="book-dashboard__panel-title">Stock à surveiller</h2><a href="{{ route('admin.stocks.index') }}" class="book-dashboard__panel-link">Voir les stocks</a></div><div class="book-dashboard__stock-list">
@forelse($lowStockBooks as $book)
<a class="book-dashboard__stock-item" href="{{ route('admin.books.show', $book) }}"><div><div class="book-dashboard__stock-title">{{ $book->title }}</div><div class="book-dashboard__stock-meta">{{ $book->author?->name ?? 'Auteur non renseigné' }} · {{ $book->format }}</div></div><div class="book-dashboard__stock-count">{{ $book->stock_quantity }} restant{{ $book->stock_quantity > 1 ? 's' : '' }}</div></a>
@empty
<div class="empty-state">Aucun stock faible.</div>
@endforelse
</div></section>
        </div>
    </div>
</div>
@endsection