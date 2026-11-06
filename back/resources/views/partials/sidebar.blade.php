<aside class="sidebar">
    <div class="sb-brand">
        <a href="{{ route('dashboard') }}" class="sb-brand-logo" aria-label="Librairie Dominion - Tableau de bord">
            <img src="{{ asset('logo-librairie-dominion.png') }}" alt="Librairie Dominion">
        </a>
    </div>

    <nav class="sb-nav">
        @if(auth()->user()?->account_type === 'admin')
        <a class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Tableau de bord
        </a>
        <div class="sb-section">Marketplace</div>
        @foreach([
            ['route' => 'marketplace.inventory', 'match' => 'marketplace.inventory*', 'label' => 'Stocks', 'icon' => 'M3 7l9-4 9 4-9 4-9-4 M3 7v10l9 4 9-4V7 M12 11v10'],
            ['route' => 'marketplace.advertisements.index', 'match' => 'marketplace.advertisements.*', 'label' => 'Publicités', 'icon' => 'M3 10v4h4l12 5V5L7 10H3 M7 14l2 7h4l-2-5 M21 9v6'],
            ['route' => 'marketplace.products.index', 'match' => 'marketplace.products.*', 'label' => 'Produits', 'icon' => 'M4 4h6v6H4z M14 4h6v6h-6z M4 14h6v6H4z M14 14h6v6h-6z'],
            ['route' => 'marketplace.sellers', 'match' => 'marketplace.sellers*', 'label' => 'Vendeurs', 'icon' => 'M3 21v-2a5 5 0 0 1 10 0v2 M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M16 4a4 4 0 0 1 0 8 M16 15a5 5 0 0 1 5 5v1'],
            ['route' => 'marketplace.orders', 'match' => 'marketplace.orders*', 'label' => 'Commandes', 'icon' => 'M3 6h18v15H3z M3 6l3-4h12l3 4 M8 10a4 4 0 0 0 8 0'],
            ['route' => 'marketplace.settings', 'match' => 'marketplace.settings*', 'label' => 'Paramètres', 'icon' => 'M4 7h16 M4 17h16 M8 4v6 M16 14v6'],
        ] as $item)
        <a class="sb-item {{ request()->routeIs($item['match']) ? 'active' : '' }}" href="{{ route($item['route']) }}" @if(request()->routeIs($item['match'])) aria-current="page" @endif>
            <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['icon'] }}"/></svg>
            {{ $item['label'] }}
        </a>
        @endforeach
        <div class="sb-section">Principal</div>

        <a class="sb-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}" href="{{ route('admin.books.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            Archives livres
            <span class="sb-badge-count">{{ \Illuminate\Support\Facades\Schema::hasTable('books') ? \App\Models\Book::count() : 0 }}</span>
        </a>
        <a class="sb-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            Commandes
            <span class="sb-badge-count">{{ \Illuminate\Support\Facades\Schema::hasTable('orders') ? \App\Models\Order::count() : 0 }}</span>
        </a>
        <a class="sb-item {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}" href="{{ route('admin.stocks.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            </svg>
            Stocks
            <span class="sb-badge-count">{{ \Illuminate\Support\Facades\Schema::hasTable('books') ? \App\Models\Book::where('stock_quantity', '<', 10)->count() : 0 }}</span>
        </a>

        <div class="sb-section">Boutique</div>
        <a class="sb-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 7h-9"/>
                <path d="M14 17H5"/>
                <circle cx="17" cy="17" r="3"/>
                <circle cx="7" cy="7" r="3"/>
            </svg>
            Categories
            <span class="sb-badge-count">{{ \Illuminate\Support\Facades\Schema::hasTable('categories') ? \App\Models\Category::count() : 0 }}</span>
        </a>
        <a class="sb-item {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}" href="{{ route('admin.authors.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Auteurs
        </a>
        <a class="sb-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                <line x1="7" y1="7" x2="7.01" y2="7"/>
            </svg>
            Promotions
        </a>
        <a class="sb-item {{ request()->routeIs('admin.delivery-options.*') ? 'active' : '' }}" href="{{ route('admin.delivery-options.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"/>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            Livraisons
        </a>
        <a class="sb-item {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}" href="{{ route('admin.advertisements.index') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 11h18"/>
                <path d="M5 7h14a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"/>
                <path d="M8 15l2-6 2 6"/>
                <path d="M9 13h2"/>
                <path d="M15 9v6"/>
            </svg>
            Publicites
            <span class="sb-badge-count">{{ \Illuminate\Support\Facades\Schema::hasTable('advertisements') ? \App\Models\Advertisement::where('is_active', true)->count() : 0 }}</span>
        </a>
        <a class="sb-item" href="#">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6V20a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1H4a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6V4a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 .51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.14.31.35.58.6.8H20a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-.51 1z"/>
            </svg>
            Parametres
        </a>
        @else
        <a class="sb-item" href="{{ route('seller.products.index') }}">Mes produits</a>
        <a class="sb-item" href="{{ route('seller.orders') }}">Mes commandes</a>
        <a class="sb-item" href="{{ route('seller.inventory') }}">Mon stock</a>
        <a class="sb-item" href="{{ route('seller.shop') }}">Ma boutique</a>
        @endif
    </nav>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">AD</div>
            <div>
                <div class="sb-name">Admin</div>
                <div class="sb-role">Back office</div>
            </div>
        </div>
    </div>
</aside>
