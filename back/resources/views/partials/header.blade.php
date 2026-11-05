<header class="topbar">
    <div class="topbar-title">@yield('page-title', 'Tableau de bord')</div>
    <div class="topbar-actions">
        <a class="btn btn-ghost btn-sm" href="#" aria-label="Voir la boutique">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Voir la boutique
        </a>
        <details class="account-menu">
            <summary aria-label="Ouvrir le menu du compte">
                <span class="account-avatar">{{ strtoupper(mb_substr(auth()->user()->name_complet ?: auth()->user()->name, 0, 2)) }}</span>
                <span class="account-copy"><strong>{{ auth()->user()->name_complet ?: auth()->user()->name }}</strong><small>{{ auth()->user()->role?->libelle ?? 'Administrateur' }}</small></span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </summary>
            <div class="account-dropdown">
                <div class="account-dropdown-head"><strong>{{ auth()->user()->name_complet ?: auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div>
                <a href="{{ route('profile.show') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>Mon profil</a>
                <a href="{{ route('profile.edit') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Modifier mes informations</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>Se déconnecter</button></form>
            </div>
        </details>
    </div>
</header>