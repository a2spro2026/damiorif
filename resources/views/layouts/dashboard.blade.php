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
            --burgundy: #1a3d2c;
            --burgundy-mid: #245239;
            --burgundy-deep: #0c2418;
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
            background: linear-gradient(160deg, var(--burgundy-deep) 0%, #06140e 50%, var(--burgundy-deep) 100%);
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
            background: linear-gradient(90deg, rgba(12, 36, 24, 0.98) 0%, rgba(26, 61, 44, 0.96) 35%, rgba(12, 36, 24, 0.98) 100%);
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
            gap: 0.85rem;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.35rem 0.75rem 0.35rem 0.4rem;
            border-radius: 999px;
            background:
                linear-gradient(135deg, rgba(201, 164, 92, 0.16) 0%, rgba(0, 0, 0, 0.28) 100%);
            border: 1px solid rgba(201, 164, 92, 0.32);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.06),
                0 4px 16px rgba(0, 0, 0, 0.25);
        }

        .navbar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--burgundy-deep);
            background: linear-gradient(145deg, #e8d5a8 0%, #c9a45c 55%, #a8863f 100%);
            box-shadow:
                0 0 0 2px rgba(201, 164, 92, 0.25),
                0 3px 10px rgba(201, 164, 92, 0.35);
        }

        .navbar-user-meta {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            padding-right: 0.25rem;
        }

        .navbar-user-name {
            font-size: 0.88rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.02em;
            line-height: 1.15;
            white-space: nowrap;
        }

        .navbar-user-statut {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            align-self: flex-start;
            padding: 0.12rem 0.5rem 0.12rem 0.3rem;
            border-radius: 999px;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gold-light);
            background: rgba(201, 164, 92, 0.12);
            border: 1px solid rgba(201, 164, 92, 0.38);
            white-space: nowrap;
        }

        .navbar-user-statut::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
            background: #4ade80;
            box-shadow: 0 0 8px rgba(74, 222, 128, 0.7);
        }

        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            display: flex;
            flex-direction: column;
            background:
                radial-gradient(ellipse 120% 40% at 0% 0%, rgba(201, 164, 92, 0.12), transparent 55%),
                linear-gradient(185deg, rgba(26, 61, 44, 0.96) 0%, rgba(10, 30, 20, 0.98) 55%, rgba(6, 20, 14, 0.99) 100%);
            border-right: 1px solid rgba(201, 164, 92, 0.28);
            box-shadow:
                8px 0 32px rgba(0, 0, 0, 0.45),
                inset -1px 0 0 rgba(201, 164, 92, 0.08);
            padding: 1.05rem 0.85rem 1rem;
            z-index: 90;
            overflow: hidden;
        }

        .sidebar-scroll {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(201, 164, 92, 0.35) transparent;
        }

        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(201, 164, 92, 0.35);
            border-radius: 99px;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 140px;
            background: linear-gradient(180deg, rgba(201, 164, 92, 0.08) 0%, transparent 100%);
            pointer-events: none;
        }

        .sidebar-footer {
            flex-shrink: 0;
            margin-top: 0.75rem;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(201, 164, 92, 0.22);
            position: relative;
            z-index: 1;
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.7rem 1rem;
            border: 1px solid rgba(201, 164, 92, 0.4);
            border-radius: 12px;
            background: linear-gradient(145deg, rgba(201, 164, 92, 0.12), rgba(0, 0, 0, 0.25));
            color: var(--gold-light);
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-logout svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .btn-logout:hover {
            background: rgba(201, 164, 92, 0.22);
            border-color: var(--gold);
            box-shadow: 0 0 18px rgba(201, 164, 92, 0.22);
            color: #fff;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
            padding-top: var(--navbar-height);
        }

        .btn-dashboard {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            width: 100%;
            margin-bottom: 1.15rem;
            padding: 0.95rem 1rem;
            border: 1px solid rgba(201, 164, 92, 0.55);
            border-radius: 16px;
            text-decoration: none;
            font-family: 'Cairo', sans-serif;
            font-size: 0.9rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--burgundy-deep);
            background: linear-gradient(135deg, #e8d5a8 0%, #d4af37 40%, #c9a45c 70%, #a8863f 100%);
            box-shadow:
                0 8px 24px rgba(201, 164, 92, 0.35),
                0 0 28px rgba(201, 164, 92, 0.14),
                inset 0 1px 0 rgba(255, 255, 255, 0.45);
            position: relative;
            overflow: hidden;
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .btn-dashboard::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 30%, rgba(255,255,255,0.35) 50%, transparent 70%);
            transform: translateX(-120%);
            transition: transform 0.55s ease;
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow:
                0 12px 30px rgba(201, 164, 92, 0.48),
                0 0 40px rgba(201, 164, 92, 0.22),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .btn-dashboard:hover::after {
            transform: translateX(120%);
        }

        .btn-dashboard.active {
            box-shadow:
                0 0 0 2px rgba(201, 164, 92, 0.4),
                0 10px 28px rgba(201, 164, 92, 0.42),
                0 0 42px rgba(201, 164, 92, 0.22);
        }

        .btn-dashboard svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sidebar-label {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            color: rgba(201, 164, 92, 0.62);
            padding: 0 0.35rem 0.85rem;
            margin-bottom: 0.45rem;
            font-weight: 700;
        }

        .sidebar-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(201, 164, 92, 0.35), transparent);
        }

        .sidebar-nav {
            list-style: none;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .sidebar-nav > .sidebar-group {
            margin-bottom: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            width: 100%;
            padding: 0.72rem 0.8rem;
            border-radius: 14px;
            color: rgba(255, 255, 255, 0.78);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            transition: background 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease, color 0.28s ease, transform 0.2s ease;
            border: 1px solid rgba(201, 164, 92, 0.12);
            background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(0,0,0,0.18));
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
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
            box-shadow: 0 0 12px var(--gold-glow);
        }

        .sidebar-link .icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(201, 164, 92, 0.16), rgba(0, 0, 0, 0.28));
            border: 1px solid rgba(201, 164, 92, 0.22);
            flex-shrink: 0;
            transition: all 0.28s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.08);
        }

        .sidebar-link .icon {
            width: 18px;
            height: 18px;
            color: var(--gold);
            opacity: 0.9;
            transition: all 0.28s ease;
        }

        .sidebar-link .link-text {
            flex: 1;
            line-height: 1.2;
        }

        .sidebar-link .link-arrow {
            width: 15px;
            height: 15px;
            color: var(--gold);
            opacity: 0.7;
            transition: transform 0.28s ease, color 0.2s ease, opacity 0.2s ease;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            color: var(--gold-light);
            background: linear-gradient(100deg, rgba(201, 164, 92, 0.18) 0%, rgba(201, 164, 92, 0.05) 100%);
            border-color: rgba(201, 164, 92, 0.32);
            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.22),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transform: translateX(2px);
        }

        .sidebar-link:hover::before {
            height: 58%;
        }

        .sidebar-link:hover .icon-wrap {
            background: linear-gradient(145deg, rgba(201, 164, 92, 0.35), rgba(201, 164, 92, 0.1));
            border-color: rgba(201, 164, 92, 0.5);
            box-shadow: 0 0 16px rgba(201, 164, 92, 0.22);
            transform: scale(1.04);
        }

        .sidebar-link:hover .icon {
            opacity: 1;
            color: var(--gold-light);
        }

        .sidebar-link:hover .link-arrow {
            color: var(--gold);
            opacity: 1;
        }

        .sidebar-link.active {
            color: #fff;
            background: linear-gradient(100deg, rgba(201, 164, 92, 0.32) 0%, rgba(201, 164, 92, 0.1) 65%, transparent 100%);
            border-color: rgba(201, 164, 92, 0.45);
            box-shadow:
                0 10px 24px rgba(0, 0, 0, 0.28),
                0 0 20px rgba(201, 164, 92, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .sidebar-link.active::before {
            height: 72%;
        }

        .sidebar-link.active .icon-wrap {
            background: linear-gradient(135deg, rgba(201, 164, 92, 0.45), rgba(201, 164, 92, 0.14));
            border-color: var(--gold);
            box-shadow: 0 0 18px rgba(201, 164, 92, 0.3);
        }

        .sidebar-link.active .icon {
            opacity: 1;
            color: var(--gold-light);
        }

        .sidebar-group.open > .sidebar-toggle .link-arrow {
            transform: rotate(90deg);
            color: var(--gold-light);
            opacity: 1;
        }

        .sidebar-toggle {
            width: 100%;
            cursor: pointer;
            background: transparent;
            font: inherit;
            text-align: left;
        }

        .submenu {
            list-style: none;
            margin: 0;
            padding: 0;
            border-left: 0 solid transparent;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: max-height 0.35s ease, opacity 0.22s ease, padding 0.22s ease, margin 0.22s ease, visibility 0.22s;
        }

        .sidebar-group.open .submenu {
            max-height: 900px;
            opacity: 1;
            visibility: visible;
            margin: 0.35rem 0 0.2rem 0.7rem;
            padding: 0.4rem 0.2rem 0.45rem 0.7rem;
            border-left: 1px solid rgba(201, 164, 92, 0.28);
            background: linear-gradient(180deg, rgba(201,164,92,0.05), transparent 30%);
            border-radius: 0 12px 12px 0;
        }

        .submenu > li {
            margin: 0;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.58rem 0.8rem;
            margin-bottom: 0.28rem;
            border-radius: 11px;
            color: rgba(255, 255, 255, 0.68);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: all 0.22s ease;
            border: 1px solid transparent;
            position: relative;
        }

        .submenu-link::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(201, 164, 92, 0.35);
            box-shadow: 0 0 0 2px rgba(201, 164, 92, 0.08);
            flex-shrink: 0;
            transition: all 0.22s ease;
        }

        .submenu-link .sub-icon {
            width: 15px;
            height: 15px;
            color: var(--gold);
            opacity: 0.8;
            flex-shrink: 0;
            transition: opacity 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .submenu-link:hover {
            color: var(--gold-light);
            background: linear-gradient(90deg, rgba(201, 164, 92, 0.14), rgba(201, 164, 92, 0.04));
            border-color: rgba(201, 164, 92, 0.2);
            transform: translateX(3px);
        }

        .submenu-link:hover::before {
            background: var(--gold);
            box-shadow: 0 0 10px rgba(201, 164, 92, 0.55);
        }

        .submenu-link:hover .sub-icon {
            opacity: 1;
            color: var(--gold-light);
            transform: scale(1.08);
        }

        .submenu-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(201, 164, 92, 0.24), rgba(201, 164, 92, 0.08));
            border-color: rgba(201, 164, 92, 0.35);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
        }

        .submenu-link.active::before {
            background: var(--gold-light);
            box-shadow: 0 0 12px rgba(201, 164, 92, 0.7);
        }

        .submenu-link.active .sub-icon {
            opacity: 1;
            color: var(--gold-light);
        }

        .submenu-group-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: rgba(201, 164, 92, 0.55);
            padding: 0.65rem 0.75rem 0.3rem;
            font-weight: 700;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 1rem 1.5rem 1.5rem;
            min-height: calc(100vh - var(--navbar-height));
        }

        .content-panel {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(201, 164, 92, 0.12);
            border-radius: 16px;
            padding: 1.25rem;
            min-height: 400px;
        }

        /* ——— Tableaux : en-têtes stylés + contenu centré (projet entier) ——— */
        .table-wrap {
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid rgba(201, 164, 92, 0.22);
            background: rgba(0, 0, 0, 0.22);
            box-shadow: inset 0 1px 0 rgba(201, 164, 92, 0.08);
        }

        .data-table,
        .lines-table,
        .content-panel table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table thead th,
        .lines-table thead th,
        .content-panel table thead th {
            text-align: center !important;
            vertical-align: middle !important;
            padding: 0.85rem 0.65rem !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            color: #0c2418 !important;
            white-space: nowrap;
            background: linear-gradient(180deg, #e8d5a8 0%, #c9a45c 48%, #a8863f 100%) !important;
            border-bottom: 2px solid rgba(26, 61, 44, 0.45) !important;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.45),
                inset 0 -1px 0 rgba(26, 61, 44, 0.12),
                0 4px 14px rgba(0, 0, 0, 0.18);
            position: relative;
        }

        .data-table thead th:not(:last-child),
        .lines-table thead th:not(:last-child),
        .content-panel table thead th:not(:last-child) {
            border-right: 1px solid rgba(26, 61, 44, 0.12);
        }

        .data-table thead th:first-child,
        .lines-table thead th:first-child,
        .content-panel table thead th:first-child {
            border-top-left-radius: 12px;
        }

        .data-table thead th:last-child,
        .lines-table thead th:last-child,
        .content-panel table thead th:last-child {
            border-top-right-radius: 12px;
        }

        .data-table tbody td,
        .lines-table tbody td,
        .content-panel table tbody td {
            text-align: center !important;
            vertical-align: middle !important;
            padding: 0.75rem 0.65rem;
            font-size: 0.86rem;
            color: rgba(255, 255, 255, 0.88);
            border-bottom: 1px solid rgba(201, 164, 92, 0.1);
        }

        .data-table tbody tr:nth-child(even) td,
        .content-panel table tbody tr:nth-child(even) td {
            background: rgba(26, 61, 44, 0.18);
        }

        .data-table tbody tr:hover td,
        .content-panel table tbody tr:hover td {
            background: rgba(201, 164, 92, 0.1) !important;
        }

        .data-table .action-btns,
        .content-panel .action-btns {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.35rem;
        }

        .data-table .auth-chips {
            justify-content: center;
            margin-inline: auto;
        }

        .lines-table thead th {
            font-size: 0.65rem !important;
            padding: 0.55rem 0.4rem !important;
            border-radius: 0 !important;
        }

        .lines-table tbody td {
            padding: 0.35rem;
        }

        .lines-table tbody td input {
            text-align: center;
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
                overflow: visible;
            }

            .sidebar-scroll {
                overflow: visible;
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
                flex-direction: row;
                text-align: left;
                gap: 0.4rem;
                padding: 0.65rem 0.5rem;
                font-size: 0.8rem;
            }

            .navbar-title .subtitle { display: none; }

            .top-navbar {
                justify-content: flex-start;
            }
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
        @php
            $authUser = auth()->user();
            $userInitial = mb_strtoupper(mb_substr($authUser->name ?? 'U', 0, 1));
            $userStatut = \App\Support\AppMenus::statutLabel($authUser->statut);
        @endphp
        <div class="navbar-actions">
            <div class="navbar-user">
                <div class="navbar-user-avatar" aria-hidden="true">{{ $userInitial }}</div>
                <div class="navbar-user-meta">
                    <div class="navbar-user-name">{{ $authUser->name }}</div>
                    <span class="navbar-user-statut">{{ $userStatut }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-scroll">
                <a href="{{ route('dashboard') }}" class="btn-dashboard {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M3 10.5L12 3l9 7.5"/>
                        <path d="M5 10v9a1 1 0 0 0 1 1h4v-5h4v5h4a1 1 0 0 0 1-1v-9"/>
                    </svg>
                    Tableau de Bord
                </a>
                <p class="sidebar-label">Menu principal</p>
                <ul class="sidebar-nav" id="sidebarNav">
                    @foreach (\App\Support\UserAccess::navigationFor(auth()->user()) as $moduleKey => $section)
                        <li class="sidebar-group" data-menu="{{ $moduleKey }}">
                            <button
                                type="button"
                                class="sidebar-link sidebar-toggle"
                                aria-expanded="false"
                            >
                                <span class="icon-wrap">
                                    @include('partials.menu-icon', ['icon' => $section['icon']])
                                </span>
                                <span class="link-text">{{ $section['label'] }}</span>
                                <svg class="link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                            </button>
                            <ul class="submenu">
                                @php $lastGroup = null; @endphp
                                @foreach ($section['children'] as $child)
                                    @if (!empty($child['group']) && $child['group'] !== $lastGroup)
                                        <li class="submenu-group-label">{{ $child['group'] }}</li>
                                        @php $lastGroup = $child['group']; @endphp
                                    @endif
                                    <li>
                                        <a
                                            href="{{ route($child['route']) }}"
                                            class="submenu-link {{ request()->routeIs($child['route']) ? 'active' : '' }}"
                                        >
                                            @include('partials.menu-icon', ['icon' => $child['icon'] ?? 'file', 'class' => 'sub-icon'])
                                            <span>{{ $child['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            var STORAGE_KEY = 'damiorif_sidebar_open';

            function getOpenMenus() {
                try {
                    var raw = sessionStorage.getItem(STORAGE_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (e) {
                    return [];
                }
            }

            function saveOpenMenus(keys) {
                try {
                    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(keys));
                } catch (e) {}
            }

            function setGroupOpen(group, open) {
                var btn = group.querySelector('.sidebar-toggle');
                group.classList.toggle('open', open);
                if (btn) {
                    btn.classList.toggle('active', open);
                    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
            }

            function persistState() {
                var open = [];
                document.querySelectorAll('.sidebar-group.open').forEach(function (group) {
                    var key = group.getAttribute('data-menu');
                    if (key) open.push(key);
                });
                saveOpenMenus(open);
            }

            var saved = getOpenMenus();

            document.querySelectorAll('.sidebar-group').forEach(function (group) {
                var key = group.getAttribute('data-menu');
                var hasActive = !!group.querySelector('.submenu-link.active');
                var shouldOpen = saved.indexOf(key) !== -1 || hasActive;
                setGroupOpen(group, shouldOpen);
            });

            persistState();

            document.querySelectorAll('.sidebar-toggle').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var group = btn.closest('.sidebar-group');
                    setGroupOpen(group, !group.classList.contains('open'));
                    persistState();
                });
            });
        })();
    </script>
</body>
</html>
