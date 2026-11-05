@php
    $navigationSeller = $seller ?? auth()->user()->seller;
    $navigationAdmin = $adminPreview ?? false;
@endphp
<header class="dash-topbar"><div class="workspace-name"><span class="status-dot"></span> Zran Market <span class="divider">/</span> <strong>{{ $navigationSeller->name }}</strong></div><div class="topbar-right"><span class="today">{{ now()->format('d/m/Y') }}</span><details class="account"><summary><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span>{{ auth()->user()->name }}</span></summary><div class="account-menu">@if($navigationAdmin)<a href="{{ route('marketplace.sellers') }}">Retour aux vendeurs</a>@else<a href="{{ route('seller.shop') }}">Ma boutique</a>@endif<form method="post" action="{{ route('logout') }}">@csrf<button>Déconnexion</button></form></div></details></div></header>
