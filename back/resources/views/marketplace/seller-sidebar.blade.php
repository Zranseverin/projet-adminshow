@php
    $navigationSeller = $seller ?? auth()->user()->seller;
    $navigationAdmin = $adminPreview ?? false;
    $navPrefix = $navigationAdmin ? 'marketplace' : 'seller';
    $navScope = $navigationAdmin ? ['seller_id' => $navigationSeller->id] : [];
    $navDashboardUrl = $navigationAdmin ? route('marketplace.sellers.dashboard', $navigationSeller) : route('seller.dashboard');
    $navigationLinks = [
        ['label'=>'Accueil', 'icon'=>'grid', 'url'=>$navDashboardUrl, 'active'=>request()->routeIs('seller.dashboard', 'marketplace.sellers.dashboard')],
        ['label'=>'Produits', 'icon'=>'box', 'url'=>route($navPrefix.'.products.index', $navScope), 'active'=>request()->routeIs($navPrefix.'.products.*')],
        ['label'=>'Commandes', 'icon'=>'bag', 'url'=>route($navPrefix.'.orders', $navScope), 'active'=>request()->routeIs($navPrefix.'.orders*')],
        ['label'=>'Stocks', 'icon'=>'stock', 'url'=>route($navPrefix.'.inventory', $navScope), 'active'=>request()->routeIs($navPrefix.'.inventory*')],
        ['label'=>'Boutique', 'icon'=>'shop', 'url'=>$navigationAdmin ? route('marketplace.sellers') : route('seller.shop'), 'active'=>request()->routeIs('seller.shop*')],
    ];
    if (! $navigationAdmin) {
        if (auth()->user()->ownsSeller()) {
            $navigationLinks[] = ['label'=>'E-mails', 'icon'=>'mail', 'url'=>route('seller.mail-settings.edit'), 'active'=>request()->routeIs('seller.mail-settings.*')];
            $navigationLinks[] = ['label'=>'Membres', 'icon'=>'members', 'url'=>route('seller.members.index'), 'active'=>request()->routeIs('seller.members.*')];
        } else {
            $navigationLinks = array_values(array_filter($navigationLinks, fn ($link) => $link['icon'] !== 'shop'));
        }
    }
@endphp
<svg class="icon-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><symbol id="mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></symbol></svg>
<svg class="icon-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><symbol id="members" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M16 4a4 4 0 0 1 0 8M19 21v-2a7 7 0 0 0-2-5"/></symbol></svg>
<svg class="icon-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><symbol id="grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol><symbol id="box" viewBox="0 0 24 24"><path d="m3 7 9-4 9 4v10l-9 4-9-4V7Zm0 0 9 4 9-4M12 11v10"/></symbol><symbol id="bag" viewBox="0 0 24 24"><path d="M4 7h16v14H4V7Zm4 0V5a4 4 0 0 1 8 0v2"/></symbol><symbol id="stock" viewBox="0 0 24 24"><path d="M3 21V11h4v10M10 21V4h4v17M17 21V8h4v13"/></symbol><symbol id="shop" viewBox="0 0 24 24"><path d="M4 10v11h16V10M2 10l3-7h14l3 7M2 10c0 4 5 4 5 0 0 4 5 4 5 0 0 4 5 4 5 0 0 4 5 4 5 0M9 21v-7h6v7"/></symbol></svg>
<aside class="dash-sidebar"><a class="dash-brand" href="{{ $navDashboardUrl }}" aria-label="Zran Market"><img src="{{ asset('zran-market.svg') }}" alt="" width="34" height="34"></a><nav aria-label="Gestion de la boutique" style="--seller-nav-count:{{ count($navigationLinks) }}">@foreach($navigationLinks as $link)<a href="{{ $link['url'] }}" @class(['active'=>$link['active']]) @if($link['active']) aria-current="page" @endif><svg aria-hidden="true"><use href="#{{ $link['icon'] }}"/></svg><span>{{ $link['label'] }}</span></a>@endforeach</nav><a class="store-link" href="{{ config('app.storefront_url') }}" title="Voir la boutique publique"><svg aria-hidden="true"><use href="#shop"/></svg><span>Voir le site</span></a></aside>
