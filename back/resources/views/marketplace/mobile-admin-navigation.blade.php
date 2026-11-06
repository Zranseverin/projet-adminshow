<details class="mobile-admin-navigation {{ $legacy ?? false ? 'legacy-mobile-navigation' : '' }}">
    <summary><span aria-hidden="true">☰</span> Menu</summary>
    <nav aria-label="Navigation administrateur mobile">
        @auth
            @if(auth()->user()->account_type === 'admin')
            <a href="{{ route('marketplace.products.index') }}">Produits et validations</a>
            <a href="{{ route('marketplace.sellers') }}">Boutiques et vendeurs</a>
            <a href="{{ route('marketplace.orders') }}">Commandes</a>
            <a href="{{ route('marketplace.inventory') }}">Stocks</a>
            <a href="{{ route('marketplace.advertisements.index') }}">Publicités</a>
            <a href="{{ route('marketplace.settings') }}">Paramètres</a>
            <a href="{{ route('dashboard') }}">Tableau de bord</a>
            @endif
            <a href="{{ route('profile.show') }}">Mon profil</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="secondary" style="width:100%">Déconnexion</button></form>
        @else
            <a href="{{ route('login') }}">Se connecter</a>
        @endauth
    </nav>
</details>
