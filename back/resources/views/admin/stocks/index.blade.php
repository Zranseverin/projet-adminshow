@extends('layouts.app')

@section('title', 'Stocks')
@section('page-title', 'Gestion des stocks')

@php
    $totalStock = (int) $books->sum('stock_quantity');
    $movementCount = $movements->total();
    $orderedUnits = 0;
@endphp

@push('styles')
    <style>
        .stock-kpi-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px; margin-bottom: 22px; }
        .stock-kpi { position: relative; overflow: hidden; padding: 18px; border: 1px solid var(--border); border-radius: var(--radius-lg); background: var(--white); box-shadow: var(--shadow); }
        .stock-kpi::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 5px; background: var(--blue); }
        .stock-kpi.green::before { background: var(--green); }
        .stock-kpi.amber::before { background: var(--amber); }
        .stock-kpi.purple::before { background: var(--purple); }
        .stock-kpi.red::before { background: var(--red); }
        .stock-kpi-value { font-family: var(--font-serif); font-size: 25px; font-weight: 600; line-height: 1; color: var(--navy); }
        .stock-kpi-label { margin-top: 5px; font-size: 12.5px; color: var(--muted); }
        .stock-kpi-delta { margin-top: 8px; font-size: 11.5px; color: var(--muted); }
        .stock-kpi-delta.up { color: var(--green); }
        .stock-kpi-delta.down { color: var(--red); }
        .stock-layout { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 20px; align-items: start; }
        .stock-table-wrap { overflow-x: auto; }
        .stock-table td { vertical-align: middle; }
        .stock-level { min-width: 150px; }
        .stock-level-top { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 6px; font-size: 12px; color: var(--muted); }
        .stock-track { height: 8px; overflow: hidden; border-radius: 999px; background: var(--sand); }
        .stock-fill { height: 100%; border-radius: inherit; background: var(--green); }
        .stock-actions { display: flex; gap: 6px; align-items: center; }
        .stock-actions form { margin: 0; }
        .stock-square-btn { width: 30px; height: 30px; border: 1.5px solid var(--border); border-radius: 8px; background: var(--white); color: var(--navy); cursor: pointer; font-weight: 700; }
        .stock-square-btn:hover { border-color: var(--amber); color: var(--amber); }
        .stock-side { display: flex; flex-direction: column; gap: 16px; }
        .stock-side-card { overflow: hidden; border: 1px solid var(--border); border-radius: var(--radius-lg); background: var(--white); box-shadow: var(--shadow); }
        .stock-side-head { display: flex; justify-content: space-between; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--border); color: var(--navy); font-weight: 700; font-size: 13px; }
        .stock-side-body { padding: 0 16px; }
        .stock-alert-item, .stock-history-item { display: flex; justify-content: space-between; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .stock-alert-item:last-child, .stock-history-item:last-child { border-bottom: 0; }
        .stock-alert-title, .stock-history-title { color: var(--navy); font-size: 13px; font-weight: 700; }
        .stock-alert-sub, .stock-history-sub { margin-top: 2px; color: var(--muted); font-size: 12px; }
        .quick-stock-form { display: grid; gap: 12px; padding: 16px; }
        .btn-green { background: var(--green); color: var(--white); }
        .btn-green:hover { filter: brightness(.94); }
        .stock-muted { color: var(--muted); font-size: 12px; }
        .stock-empty { padding: 18px 0; color: var(--green); text-align: center; font-size: 13px; }
        @media (max-width: 1200px) {
            .stock-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stock-layout { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    @if (session('success')) <div class="flash">{{ session('success') }}</div> @endif

    <section class="stock-kpi-grid">
        <article class="stock-kpi">
            <div class="stock-kpi-value">{{ $availableBooksCount }}</div>
            <div class="stock-kpi-label">Livres disponibles sur le site</div>
            <div class="stock-kpi-delta">{{ $books->count() }} livres suivis</div>
        </article>
        <article class="stock-kpi green">
            <div class="stock-kpi-value">{{ number_format($totalStock, 0, ',', ' ') }}</div>
            <div class="stock-kpi-label">Unites en stock total</div>
            <div class="stock-kpi-delta up">Inventaire actuel</div>
        </article>
        <article class="stock-kpi amber">
            <div class="stock-kpi-value">{{ number_format($totalSoldMonth, 0, ',', ' ') }}</div>
            <div class="stock-kpi-label">Livres vendus ce mois</div>
            <div class="stock-kpi-delta up">Depuis les commandes front</div>
        </article>
        <article class="stock-kpi purple">
            <div class="stock-kpi-value">{{ $orderedUnits }}</div>
            <div class="stock-kpi-label">Unites commandees</div>
            <div class="stock-kpi-delta">En attente de reception</div>
        </article>
        <article class="stock-kpi red">
            <div class="stock-kpi-value">{{ $lowStockBooks->count() }}</div>
            <div class="stock-kpi-label">Alertes stock bas (&lt; 10)</div>
            <div class="stock-kpi-delta down">{{ $lowStockBooks->isNotEmpty() ? 'Reapprovisionnement requis' : 'Aucune alerte' }}</div>
        </article>
    </section>

    <div class="stock-layout">
        <div class="card">
            <div class="card-header">
                <div class="toolbar" style="flex: 1">
                    <div class="search-field">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="stockSearch" placeholder="Filtrer les livres..." oninput="filterStockRows()">
                    </div>
                    <select class="select-filter" id="stockFilter" style="max-width: 190px" onchange="filterStockRows()">
                        <option value="">Tous les stocks</option>
                        <option value="low">Stock bas (&lt; 10)</option>
                        <option value="ok">Stock OK (&ge; 10)</option>
                        <option value="rupture">Rupture (= 0)</option>
                    </select>
                </div>
                <a class="btn btn-green btn-sm" href="{{ route('admin.stocks.create') }}">Approvisionner</a>
            </div>

            <div class="stock-table-wrap">
                <table class="stock-table" id="stockTable">
                    <thead>
                        <tr>
                            <th>Livre</th>
                            <th>Dispo. site</th>
                            <th>Stock restant</th>
                            <th>Vendus</th>
                            <th>Commandes</th>
                            <th>Niveau</th>
                            <th>Ajuster</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            @php
                                $stock = (int) $book->stock_quantity;
                                $sold = (int) ($book->sold_units ?? 0);
                                $level = $stock === 0 ? 'rupture' : ($stock < 10 ? 'low' : 'ok');
                                $barPercent = min(100, max(3, $stock * 4));
                                $barColor = $stock === 0 ? 'var(--red)' : ($stock < 10 ? 'var(--amber)' : 'var(--green)');
                                $coverUrl = $book->cover_image && \Illuminate\Support\Str::startsWith($book->cover_image, ['http://', 'https://'])
                                    ? $book->cover_image
                                    : ($book->cover_image ? asset('storage/'.$book->cover_image) : null);
                            @endphp
                            <tr data-stock-row data-search="{{ strtolower($book->title.' '.$book->author?->name.' '.$book->format) }}" data-level="{{ $level }}">
                                <td>
                                    <div class="book-cell">
                                        <span class="cover-thumb" style="background: {{ $book->cover_background ?: 'linear-gradient(145deg,#2A5F8E,#1a3a5c)' }}">
                                            @if ($coverUrl)
                                                <img src="{{ $coverUrl }}" alt="{{ $book->title }}">
                                            @else
                                                {{ $book->cover_emoji ?: 'B' }}
                                            @endif
                                        </span>
                                        <span>
                                            <span class="book-title">{{ $book->title }}</span>
                                            <span class="book-author">{{ $book->author?->name ?? 'Auteur inconnu' }} · {{ $book->format }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td><span class="badge {{ $book->is_active && $stock > 0 ? 'badge-stock' : 'badge-muted' }}">{{ $book->is_active && $stock > 0 ? 'Oui' : 'Non' }}</span></td>
                                <td><strong>{{ $stock }}</strong> unite{{ $stock > 1 ? 's' : '' }}</td>
                                <td>{{ $sold }}</td>
                                <td>0</td>
                                <td>
                                    <div class="stock-level">
                                        <div class="stock-level-top">
                                            <span>{{ $level === 'rupture' ? 'Rupture' : ($level === 'low' ? 'Stock bas' : 'Stock OK') }}</span>
                                            <span>{{ $stock }}</span>
                                        </div>
                                        <div class="stock-track"><div class="stock-fill" style="width: {{ $barPercent }}%; background: {{ $barColor }}"></div></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stock-actions">
                                        <form method="POST" action="{{ route('admin.stocks.store') }}">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <input type="hidden" name="type" value="out">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="source" value="Ajustement manuel">
                                            <button class="stock-square-btn" type="submit" @disabled($stock === 0)>-</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.stocks.store') }}">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <input type="hidden" name="type" value="in">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="source" value="Ajustement manuel">
                                            <button class="stock-square-btn" type="submit">+</button>
                                        </form>
                                        <a class="btn btn-ghost btn-sm" href="{{ route('admin.stocks.create', ['book_id' => $book->id]) }}">Saisir</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><div class="empty-state">Aucun livre a suivre.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap" style="display:flex;justify-content:space-between;align-items:center;gap:12px">
                <div class="stock-muted"><span id="stockVisibleCount">{{ $books->count() }}</span> livres affiches</div>
                <button class="btn btn-ghost btn-sm" type="button" onclick="exportStockCsv()">Exporter stock CSV</button>
            </div>
        </div>

        <aside class="stock-side">
            <div class="stock-side-card">
                <div class="stock-side-head">
                    <span>Alertes stock bas</span>
                    <span class="stock-muted">{{ $lowStockBooks->count() }}</span>
                </div>
                <div class="stock-side-body">
                    @forelse ($lowStockBooks->take(6) as $book)
                        <div class="stock-alert-item">
                            <div>
                                <div class="stock-alert-title">{{ $book->title }}</div>
                                <div class="stock-alert-sub">{{ $book->stock_quantity === 0 ? 'Rupture de stock' : $book->stock_quantity.' unite'.($book->stock_quantity > 1 ? 's' : '').' restante'.($book->stock_quantity > 1 ? 's' : '') }}</div>
                            </div>
                            <a class="btn btn-ghost btn-sm" href="{{ route('admin.stocks.create', ['book_id' => $book->id]) }}">Ajuster</a>
                        </div>
                    @empty
                        <div class="stock-empty">Tous les stocks sont OK</div>
                    @endforelse
                </div>
            </div>

            <div class="stock-side-card">
                <div class="stock-side-head"><span>Approvisionnement rapide</span></div>
                <form class="quick-stock-form" method="POST" action="{{ route('admin.stocks.store') }}">
                    @csrf
                    <div>
                        <label class="form-label" for="quick_book_id">Livre</label>
                        <select class="form-control" id="quick_book_id" name="book_id" required>
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}">{{ $book->title }} - stock: {{ $book->stock_quantity }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="type" value="in">
                    <div>
                        <label class="form-label" for="quick_quantity">Quantite recue</label>
                        <input class="form-control" id="quick_quantity" name="quantity" type="number" min="1" value="50" required>
                    </div>
                    <div>
                        <label class="form-label" for="quick_source">Fournisseur</label>
                        <input class="form-control" id="quick_source" name="source" value="Hachette Distribution">
                    </div>
                    <div>
                        <label class="form-label" for="quick_note">Note</label>
                        <textarea class="form-control" id="quick_note" name="note" placeholder="Reception, facture, lot..."></textarea>
                    </div>
                    <button class="btn btn-green" type="submit">Valider la reception</button>
                </form>
            </div>

            <div class="stock-side-card">
                <div class="stock-side-head">
                    <span>Historique mouvements</span>
                    <a class="stock-muted" href="{{ route('admin.stocks.create') }}">Ajouter</a>
                </div>
                <div class="stock-side-body">
                    @forelse ($recentMovements as $movement)
                        <div class="stock-history-item">
                            <div>
                                <div class="stock-history-title">{{ $movement->book?->title ?? 'Livre supprime' }}</div>
                                <div class="stock-history-sub">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }} · {{ $movement->source ?: $movement->type }}
                                </div>
                            </div>
                            <div class="stock-muted">{{ $movement->created_at->format('d/m H:i') }}</div>
                        </div>
                    @empty
                        <div class="stock-empty">Aucun mouvement.</div>
                    @endforelse
                </div>
            </div>

            <div class="stock-side-card">
                <div class="stock-side-head"><span>Top ventes stock</span></div>
                <div class="stock-side-body">
                    @foreach ($books->sortByDesc('sold_units')->take(5) as $book)
                        <div class="stock-history-item">
                            <div>
                                <div class="stock-history-title">{{ $book->title }}</div>
                                <div class="stock-history-sub">{{ $book->author?->name ?? 'Auteur inconnu' }}</div>
                            </div>
                            <div class="stock-muted">{{ (int) ($book->sold_units ?? 0) }} vendu{{ (int) ($book->sold_units ?? 0) > 1 ? 's' : '' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        function filterStockRows() {
            const search = (document.getElementById('stockSearch')?.value || '').toLowerCase();
            const filter = document.getElementById('stockFilter')?.value || '';
            let count = 0;

            document.querySelectorAll('[data-stock-row]').forEach((row) => {
                const matchesSearch = row.dataset.search.includes(search);
                const matchesLevel = !filter || row.dataset.level === filter;
                const visible = matchesSearch && matchesLevel;
                row.style.display = visible ? '' : 'none';
                if (visible) count++;
            });

            const counter = document.getElementById('stockVisibleCount');
            if (counter) counter.textContent = count;
        }

        function exportStockCsv() {
            const rows = [['titre','auteur_format','stock','niveau']];
            document.querySelectorAll('[data-stock-row]').forEach((row) => {
                if (row.style.display === 'none') return;
                rows.push([
                    row.querySelector('.book-title')?.textContent?.trim() || '',
                    row.querySelector('.book-author')?.textContent?.trim() || '',
                    row.children[2]?.textContent?.trim() || '',
                    row.dataset.level || ''
                ]);
            });

            const csv = rows.map((row) => row.map((cell) => '"' + String(cell).replace(/"/g, '""') + '"').join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'stock_chapitre.csv';
            link.click();
            URL.revokeObjectURL(url);
        }
    </script>
@endpush
