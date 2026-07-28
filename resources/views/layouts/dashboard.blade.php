<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tableau de Bord') — DAMIO-RIF</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --burgundy: #54000b;
            --burgundy-mid: #6b0010;
            --burgundy-deep: #2d0006;
            --gold: #c9a45c;
            --gold-light: #e8d5a8;
            --gold-glow: rgba(201, 164, 92, 0.35);
            --sidebar-width: 270px;
            --navbar-height: 72px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(160deg, var(--burgundy-deep) 0%, #1a0004 50%, var(--burgundy-deep) 100%);
            color: #fff;
        }

        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--navbar-height);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            background: linear-gradient(90deg, rgba(45, 0, 6, 0.98) 0%, rgba(84, 0, 11, 0.96) 35%, rgba(45, 0, 6, 0.98) 100%);
            border-bottom: 1px solid rgba(201, 164, 92, 0.35);
            box-shadow:
                0 4px 30px rgba(0, 0, 0, 0.45),
                0 1px 0 rgba(201, 164, 92, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        .navbar-brand-block {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-logo {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gold);
            background: rgba(0, 0, 0, 0.3);
            box-shadow:
                0 0 0 3px rgba(201, 164, 92, 0.12),
                0 0 24px var(--gold-glow);
            flex-shrink: 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .navbar-logo:hover {
            transform: scale(1.04);
            box-shadow:
                0 0 0 4px rgba(201, 164, 92, 0.2),
                0 0 32px rgba(201, 164, 92, 0.45);
        }

        .navbar-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, #a8863f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 12px rgba(201, 164, 92, 0.25));
            line-height: 1.2;
        }

        .navbar-title .subtitle {
            font-size: 0.68rem;
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 0.22em;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.75);
        }

        .badge-statut {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            background: rgba(201, 164, 92, 0.12);
            border: 1px solid rgba(201, 164, 92, 0.35);
            color: var(--gold-light);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .btn-logout {
            padding: 0.5rem 1.1rem;
            border: 1px solid rgba(201, 164, 92, 0.45);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.25);
            color: var(--gold-light);
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-logout:hover {
            background: rgba(201, 164, 92, 0.15);
            box-shadow: 0 0 20px rgba(201, 164, 92, 0.2);
            border-color: var(--gold);
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
            padding-top: var(--navbar-height);
        }

        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            background:
                linear-gradient(180deg, rgba(84, 0, 11, 0.94) 0%, rgba(45, 0, 6, 0.97) 100%);
            border-right: 1px solid rgba(201, 164, 92, 0.22);
            box-shadow: 4px 0 28px rgba(0, 0, 0, 0.4);
            padding: 1.25rem 0.75rem 1.5rem;
            z-index: 90;
            overflow-y: auto;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(180deg, rgba(201, 164, 92, 0.06) 0%, transparent 100%);
            pointer-events: none;
        }

        .sidebar-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: rgba(201, 164, 92, 0.55);
            padding: 0 0.85rem 0.85rem;
            margin-bottom: 0.15rem;
            border-bottom: 1px solid rgba(201, 164, 92, 0.12);
        }

        .sidebar-nav {
            list-style: none;
            position: relative;
        }

        .sidebar-nav li {
            margin-bottom: 0.45rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.75rem 0.85rem;
            border-radius: 14px;
            color: rgba(255, 255, 255, 0.72);
            text-decoration: none;
            font-size: 0.93rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 0;
            background: linear-gradient(180deg, var(--gold-light), var(--gold));
            border-radius: 0 4px 4px 0;
            transition: height 0.28s ease;
            box-shadow: 0 0 10px var(--gold-glow);
        }

        .sidebar-link .icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.28);
            border: 1px solid rgba(201, 164, 92, 0.18);
            flex-shrink: 0;
            transition: all 0.28s ease;
        }

        .sidebar-link .icon {
            width: 18px;
            height: 18px;
            color: var(--gold);
            opacity: 0.85;
            transition: all 0.28s ease;
        }

        .sidebar-link .link-text {
            flex: 1;
        }

        .sidebar-link .link-arrow {
            width: 16px;
            height: 16px;
            color: rgba(201, 164, 92, 0);
            transform: translateX(-6px);
            transition: all 0.28s ease;
        }

        .sidebar-link:hover {
            color: var(--gold-light);
            background: linear-gradient(90deg, rgba(201, 164, 92, 0.14) 0%, rgba(201, 164, 92, 0.04) 100%);
            border-color: rgba(201, 164, 92, 0.22);
            transform: translateX(4px);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.2);
        }

        .sidebar-link:hover::before {
            height: 55%;
        }

        .sidebar-link:hover .icon-wrap {
            background: rgba(201, 164, 92, 0.15);
            border-color: rgba(201, 164, 92, 0.4);
            box-shadow: 0 0 14px rgba(201, 164, 92, 0.15);
        }

        .sidebar-link:hover .icon {
            opacity: 1;
            color: var(--gold-light);
        }

        .sidebar-link:hover .link-arrow {
            color: var(--gold);
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(201, 164, 92, 0.28) 0%, rgba(201, 164, 92, 0.08) 70%, transparent 100%);
            border-color: rgba(201, 164, 92, 0.38);
            box-shadow:
                0 6px 20px rgba(0, 0, 0, 0.28),
                inset 0 1px 0 rgba(255, 255, 255, 0.07);
        }

        .sidebar-link.active::before {
            height: 70%;
        }

        .sidebar-link.active .icon-wrap {
            background: linear-gradient(135deg, rgba(201, 164, 92, 0.35), rgba(201, 164, 92, 0.12));
            border-color: var(--gold);
            box-shadow: 0 0 16px rgba(201, 164, 92, 0.25);
        }

        .sidebar-link.active .icon {
            opacity: 1;
            color: var(--gold-light);
        }

        .sidebar-link.active .link-arrow {
            color: var(--gold-light);
            opacity: 1;
            transform: translateX(0);
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
        }

        .content-panel {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(201, 164, 92, 0.12);
            border-radius: 16px;
            padding: 2rem;
            min-height: 400px;
        }

        @media (max-width: 900px) {
            :root { --sidebar-width: 240px; }

            .navbar-title h1 {
                font-size: 1.05rem;
                letter-spacing: 0.07em;
            }

            .navbar-logo {
                width: 46px;
                height: 46px;
            }
        }

        @media (max-width: 640px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                border-right: none;
                border-bottom: 1px solid rgba(201, 164, 92, 0.2);
            }

            .app-shell {
                flex-direction: column;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-nav {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.35rem;
            }

            .sidebar-link {
                flex-direction: column;
                text-align: center;
                gap: 0.4rem;
                padding: 0.65rem 0.5rem;
                font-size: 0.8rem;
            }

            .sidebar-link .link-arrow { display: none; }

            .navbar-title .subtitle { display: none; }
        }
    </style>
</head>
<body>
    <header class="top-navbar">
        <div class="navbar-brand-block">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Damio Rif"
                class="navbar-logo"
            >
            <div class="navbar-title">
                <h1>Tableau de Bord DAMIO-RIF</h1>
                <div class="subtitle">Système de gestion</div>
            </div>
        </div>
        <div class="navbar-actions">
            <div class="user-info">
                <span>{{ auth()->user()->name }}</span>
                <span class="badge-statut">{{ ucfirst(auth()->user()->statut) }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Déconnexion</button>
            </form>
        </div>
    </header>

    <div class="app-shell">
        <aside class="sidebar">
            <p class="sidebar-label">Menu principal</p>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('fournisseurs.index') }}" class="sidebar-link {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}">
                        <span class="icon-wrap">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </span>
                        <span class="link-text">Fournisseurs</span>
                        <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('stock.index') }}" class="sidebar-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                        <span class="icon-wrap">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                        </span>
                        <span class="link-text">Stock</span>
                        <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('clients.index') }}" class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <span class="icon-wrap">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </span>
                        <span class="link-text">Client</span>
                        <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('charges.index') }}" class="sidebar-link {{ request()->routeIs('charges.*') ? 'active' : '' }}">
                        <span class="icon-wrap">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </span>
                        <span class="link-text">Charges</span>
                        <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rapports.index') }}" class="sidebar-link {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                        <span class="icon-wrap">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                        </span>
                        <span class="link-text">Rapports</span>
                        <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('configuration.index') }}" class="sidebar-link {{ request()->routeIs('configuration.*') ? 'active' : '' }}">
                        <span class="icon-wrap">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </span>
                        <span class="link-text">Configuration</span>
                        <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
