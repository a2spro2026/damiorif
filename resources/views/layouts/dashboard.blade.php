<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tableau de Bord') — DAMIO-RIF</title>
    <script>
        (function () {
            try {
                var t = localStorage.getItem('damiorif-theme');
                if (t !== 'light' && t !== 'dark') t = 'dark';
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
            try {
                if (localStorage.getItem('damiorif-sidebar') === 'collapsed') {
                    document.documentElement.setAttribute('data-sidebar', 'collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,600;700&display=swap" rel="stylesheet">
    <style>
        :root,
        html[data-theme="dark"] {
            --burgundy: #1B2438;
            --burgundy-mid: #243044;
            --burgundy-deep: #0B1020;
            --gold: #5EC8B3;
            --gold-light: #A8E6D8;
            --gold-glow: rgba(94, 200, 179, 0.35);
            --accent: #5EC8B3;
            --accent-soft: #7DD3C0;
            --accent-deep: #2A9B86;
            --surface: rgba(255, 255, 255, 0.035);
            --bg-body: linear-gradient(165deg, #070B14 0%, #121A2B 42%, #0B1020 100%);
            --bg-body-radials:
                radial-gradient(ellipse 80% 50% at 10% -10%, rgba(94, 200, 179, 0.14), transparent 50%),
                radial-gradient(ellipse 60% 40% at 100% 0%, rgba(56, 189, 248, 0.08), transparent 45%);
            --bg-nav: linear-gradient(90deg, rgba(7, 11, 20, 0.98) 0%, rgba(27, 36, 56, 0.96) 40%, rgba(7, 11, 20, 0.98) 100%);
            --bg-sidebar: linear-gradient(185deg, rgba(27, 36, 56, 0.98) 0%, rgba(15, 22, 38, 0.99) 55%, rgba(7, 11, 20, 1) 100%);
            --bg-panel: linear-gradient(160deg, rgba(255,255,255,0.045) 0%, rgba(255,255,255,0.015) 100%), rgba(15, 22, 38, 0.55);
            --bg-table: rgba(0, 0, 0, 0.22);
            --bg-input: #FFFFFF;
            --bg-modal: linear-gradient(160deg, rgba(27, 36, 56, 0.98), rgba(11, 16, 32, 0.99));
            --bg-row-alt: rgba(27, 36, 56, 0.22);
            --bg-row-hover: rgba(94, 200, 179, 0.12);
            --text: #F8FAFC;
            --text-muted: rgba(248, 250, 252, 0.55);
            --text-soft: rgba(248, 250, 252, 0.78);
            --border: rgba(94, 200, 179, 0.22);
            --border-strong: rgba(94, 200, 179, 0.38);
            --shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
            --th-text: #0B1020;
            --sidebar-width: 270px;
            --navbar-height: 72px;
        }

        html[data-theme="light"] {
            --burgundy: #E8EEF5;
            --burgundy-mid: #D7E0EB;
            --burgundy-deep: #0F172A;
            --gold: #0F766E;
            --gold-light: #0D9488;
            --gold-glow: rgba(15, 118, 110, 0.22);
            --accent: #0F766E;
            --accent-soft: #14B8A6;
            --accent-deep: #0D9488;
            --surface: rgba(15, 23, 42, 0.03);
            --bg-body: linear-gradient(165deg, #F4F7FB 0%, #EEF3F8 50%, #E8EEF5 100%);
            --bg-body-radials:
                radial-gradient(ellipse 70% 45% at 8% -5%, rgba(20, 184, 166, 0.12), transparent 50%),
                radial-gradient(ellipse 50% 35% at 100% 0%, rgba(56, 189, 248, 0.08), transparent 45%);
            --bg-nav: linear-gradient(90deg, #FFFFFF 0%, #F8FAFC 50%, #FFFFFF 100%);
            --bg-sidebar: linear-gradient(185deg, #FFFFFF 0%, #F8FAFC 100%);
            --bg-panel: #FFFFFF;
            --bg-table: #FFFFFF;
            --bg-input: #FFFFFF;
            --bg-modal: #FFFFFF;
            --bg-row-alt: #F1F5F9;
            --bg-row-hover: rgba(15, 118, 110, 0.08);
            --text: #0F172A;
            --text-muted: #64748B;
            --text-soft: #334155;
            --border: rgba(15, 23, 42, 0.1);
            --border-strong: rgba(15, 118, 110, 0.35);
            --shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
            --th-text: #FFFFFF;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            background: var(--bg-body-radials), var(--bg-body);
            color: var(--text);
            letter-spacing: 0.01em;
            transition: background 0.35s ease, color 0.25s ease;
        }

        /* Barres de saisie claires partout (évite les champs noirs illisibles) */
        input:not([type="checkbox"]):not([type="radio"]):not(.statut-select),
        select:not(.statut-select),
        textarea {
            color-scheme: light;
            background-color: #FFFFFF !important;
            color: #0F172A !important;
            caret-color: #0F172A;
        }
        input::placeholder,
        textarea::placeholder {
            color: #94A3B8 !important;
            opacity: 1;
        }
        select:not(.statut-select) option,
        .field select option {
            background: #FFFFFF !important;
            color: #0F172A !important;
        }
        .statut-select {
            color: #FFFFFF !important;
        }
        .statut-select.statut-reporte {
            color: #1a1200 !important;
        }
        .field select.statut-select.statut-en_instance {
            background: linear-gradient(180deg, #9ca3af 0%, #6b7280 48%, #4b5563 100%) !important;
            color: #fff !important;
        }
        .field select.statut-select.statut-en_cours {
            background: linear-gradient(180deg, #60a5fa 0%, #3b82f6 45%, #1d4ed8 100%) !important;
            color: #fff !important;
        }
        .field select.statut-select.statut-paye {
            background: linear-gradient(180deg, #4ade80 0%, #22c55e 45%, #15803d 100%) !important;
            color: #fff !important;
        }
        .field select.statut-select.statut-imp {
            background: linear-gradient(180deg, #f87171 0%, #ef4444 45%, #b91c1c 100%) !important;
            color: #fff !important;
        }
        .field select.statut-select.statut-reporte {
            background: linear-gradient(180deg, #fde047 0%, #eab308 48%, #a16207 100%) !important;
            color: #1a1200 !important;
        }
        .field select.statut-select.statut-devalide {
            background: linear-gradient(180deg, #c084fc 0%, #a855f7 45%, #7e22ce 100%) !important;
            color: #fff !important;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
            margin-bottom: 0.95rem;
        }
        .kpi-card {
            border-radius: 14px;
            padding: 0.85rem 0.95rem;
            background: var(--surface);
            border: 1px solid rgba(94, 200, 179, 0.18);
            box-shadow: inset 0 1px 0 rgba(94, 200, 179, 0.08);
        }
        .kpi-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gold-light);
            font-weight: 700;
            margin-bottom: 0.35rem;
        }
        .kpi-value {
            font-family: 'Fraunces', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }
        .kpi-value span {
            font-size: 0.62rem;
            font-family: 'Manrope', sans-serif;
            color: var(--gold-light);
            margin-left: 0.25rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 600;
        }
        .filter-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.65rem;
            margin-bottom: 0.85rem;
        }
        .filter-bar input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            border: 1px solid rgba(94, 200, 179, 0.3);
            font-family: inherit;
            font-size: 0.85rem;
            outline: none;
        }
        .filter-bar input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(94, 200, 179, 0.12);
        }

        html[data-theme="light"] .kpi-card {
            background: #FFFFFF;
            border-color: rgba(15, 23, 42, 0.1);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }
        html[data-theme="light"] .kpi-label { color: #0F766E; }
        html[data-theme="light"] .kpi-value { color: #0F172A; }
        html[data-theme="light"] .kpi-value span { color: #64748B; }

        .theme-toggle {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--border-strong);
            background: var(--surface);
            color: var(--gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
        }
        .theme-toggle:hover {
            transform: translateY(-1px);
            border-color: var(--gold);
            box-shadow: 0 6px 16px var(--gold-glow);
        }
        .theme-toggle svg { width: 18px; height: 18px; }
        .theme-toggle .icon-sun { display: none; }
        .theme-toggle .icon-moon { display: block; }
        html[data-theme="light"] .theme-toggle .icon-sun { display: block; }
        html[data-theme="light"] .theme-toggle .icon-moon { display: none; }
        html[data-theme="light"] .theme-toggle {
            background: #F1F5F9;
            color: #0F766E;
            border-color: rgba(15, 118, 110, 0.28);
        }

        .sidebar-panel-toggle {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--border-strong);
            background: var(--surface);
            color: var(--gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-panel-toggle:hover {
            transform: translateY(-1px);
            border-color: var(--gold);
            box-shadow: 0 6px 16px var(--gold-glow);
        }
        .sidebar-panel-toggle svg { width: 18px; height: 18px; }
        .sidebar-panel-toggle .icon-show { display: none; }
        .sidebar-panel-toggle .icon-hide { display: block; }
        html[data-sidebar="collapsed"] .sidebar-panel-toggle .icon-show { display: block; }
        html[data-sidebar="collapsed"] .sidebar-panel-toggle .icon-hide { display: none; }
        html[data-theme="light"] .sidebar-panel-toggle {
            background: #F1F5F9;
            color: #0F766E;
            border-color: rgba(15, 118, 110, 0.28);
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
            background: var(--bg-nav);
            border-bottom: 1px solid var(--border-strong);
            box-shadow:
                0 4px 24px rgba(0, 0, 0, 0.12),
                0 1px 0 rgba(255, 255, 255, 0.04);
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
                0 0 0 3px rgba(94, 200, 179, 0.12),
                0 0 24px var(--gold-glow);
            flex-shrink: 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .navbar-logo:hover {
            transform: scale(1.04);
            box-shadow:
                0 0 0 4px rgba(94, 200, 179, 0.2),
                0 0 32px rgba(94, 200, 179, 0.45);
        }

        .navbar-title h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 50%, #2A9B86 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 12px rgba(94, 200, 179, 0.25));
            line-height: 1.2;
        }

        .navbar-title .subtitle {
            font-size: 0.68rem;
            color: var(--text-muted);
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
                linear-gradient(135deg, rgba(94, 200, 179, 0.16) 0%, rgba(0, 0, 0, 0.28) 100%);
            border: 1px solid rgba(94, 200, 179, 0.32);
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
            font-family: 'Fraunces', serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--burgundy-deep);
            background: linear-gradient(145deg, #A8E6D8 0%, #5EC8B3 55%, #2A9B86 100%);
            box-shadow:
                0 0 0 2px rgba(94, 200, 179, 0.25),
                0 3px 10px rgba(94, 200, 179, 0.35);
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
            background: rgba(94, 200, 179, 0.12);
            border: 1px solid rgba(94, 200, 179, 0.38);
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
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-strong);
            box-shadow: 8px 0 28px rgba(0, 0, 0, 0.2);
            padding: 1.05rem 0.85rem 1rem;
            z-index: 90;
            overflow: hidden;
            transition: transform 0.32s ease, opacity 0.25s ease, width 0.32s ease, padding 0.32s ease, border-color 0.32s ease;
        }

        html[data-sidebar="collapsed"] {
            --sidebar-width: 0px;
        }

        html[data-sidebar="collapsed"] .sidebar {
            transform: translateX(-100%);
            opacity: 0;
            pointer-events: none;
            width: 0;
            padding-left: 0;
            padding-right: 0;
            border-right-color: transparent;
            box-shadow: none;
        }

        .sidebar-top {
            flex-shrink: 0;
            position: relative;
            z-index: 2;
            padding-bottom: 0.35rem;
            margin-bottom: 0.15rem;
            background: inherit;
        }

        .sidebar-scroll {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(94, 200, 179, 0.35) transparent;
        }

        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(94, 200, 179, 0.35);
            border-radius: 99px;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 140px;
            background: linear-gradient(180deg, rgba(94, 200, 179, 0.08) 0%, transparent 100%);
            pointer-events: none;
        }

        .sidebar-footer {
            flex-shrink: 0;
            margin-top: 0.75rem;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(94, 200, 179, 0.22);
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
            border: 1px solid rgba(94, 200, 179, 0.4);
            border-radius: 12px;
            background: linear-gradient(145deg, rgba(94, 200, 179, 0.12), rgba(0, 0, 0, 0.25));
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
            background: rgba(94, 200, 179, 0.22);
            border-color: var(--gold);
            box-shadow: 0 0 18px rgba(94, 200, 179, 0.22);
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
            margin-bottom: 0.85rem;
            padding: 0.95rem 1rem;
            border: 1px solid rgba(94, 200, 179, 0.55);
            border-radius: 16px;
            text-decoration: none;
            font-family: 'Manrope', sans-serif;
            font-size: 0.9rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--burgundy-deep);
            background: linear-gradient(135deg, #A8E6D8 0%, #7DD3C0 40%, #5EC8B3 70%, #2A9B86 100%);
            box-shadow:
                0 8px 24px rgba(94, 200, 179, 0.35),
                0 0 28px rgba(94, 200, 179, 0.14),
                inset 0 1px 0 rgba(255, 255, 255, 0.45);
            position: relative;
            overflow: hidden;
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            flex-shrink: 0;
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
                0 12px 30px rgba(94, 200, 179, 0.48),
                0 0 40px rgba(94, 200, 179, 0.22),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .btn-dashboard:hover::after {
            transform: translateX(120%);
        }

        .btn-dashboard.active {
            box-shadow:
                0 0 0 2px rgba(94, 200, 179, 0.4),
                0 10px 28px rgba(94, 200, 179, 0.42),
                0 0 42px rgba(94, 200, 179, 0.22);
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
            color: rgba(94, 200, 179, 0.62);
            padding: 0 0.35rem 0.85rem;
            margin-bottom: 0.45rem;
            font-weight: 700;
        }

        .sidebar-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(94, 200, 179, 0.35), transparent);
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
            color:var(--text-soft);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            transition: background 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease, color 0.28s ease, transform 0.2s ease;
            border: 1px solid rgba(94, 200, 179, 0.12);
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
            background: linear-gradient(145deg, rgba(94, 200, 179, 0.16), rgba(0, 0, 0, 0.28));
            border: 1px solid rgba(94, 200, 179, 0.22);
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
            background: linear-gradient(100deg, rgba(94, 200, 179, 0.18) 0%, rgba(94, 200, 179, 0.05) 100%);
            border-color: rgba(94, 200, 179, 0.32);
            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.22),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transform: translateX(2px);
        }

        .sidebar-link:hover::before {
            height: 58%;
        }

        .sidebar-link:hover .icon-wrap {
            background: linear-gradient(145deg, rgba(94, 200, 179, 0.35), rgba(94, 200, 179, 0.1));
            border-color: rgba(94, 200, 179, 0.5);
            box-shadow: 0 0 16px rgba(94, 200, 179, 0.22);
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
            background: linear-gradient(100deg, rgba(94, 200, 179, 0.32) 0%, rgba(94, 200, 179, 0.1) 65%, transparent 100%);
            border-color: rgba(94, 200, 179, 0.45);
            box-shadow:
                0 10px 24px rgba(0, 0, 0, 0.28),
                0 0 20px rgba(94, 200, 179, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .sidebar-link.active::before {
            height: 72%;
        }

        .sidebar-link.active .icon-wrap {
            background: linear-gradient(135deg, rgba(94, 200, 179, 0.45), rgba(94, 200, 179, 0.14));
            border-color: var(--gold);
            box-shadow: 0 0 18px rgba(94, 200, 179, 0.3);
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
            border-left: 1px solid rgba(94, 200, 179, 0.28);
            background: linear-gradient(180deg, rgba(94,200,179,0.05), transparent 30%);
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
            color:var(--text-soft);
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
            background: rgba(94, 200, 179, 0.35);
            box-shadow: 0 0 0 2px rgba(94, 200, 179, 0.08);
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
            background: linear-gradient(90deg, rgba(94, 200, 179, 0.14), rgba(94, 200, 179, 0.04));
            border-color: rgba(94, 200, 179, 0.2);
            transform: translateX(3px);
        }

        .submenu-link:hover::before {
            background: var(--gold);
            box-shadow: 0 0 10px rgba(94, 200, 179, 0.55);
        }

        .submenu-link:hover .sub-icon {
            opacity: 1;
            color: var(--gold-light);
            transform: scale(1.08);
        }

        .submenu-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(94, 200, 179, 0.24), rgba(94, 200, 179, 0.08));
            border-color: rgba(94, 200, 179, 0.35);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
        }

        .submenu-link.active::before {
            background: var(--gold-light);
            box-shadow: 0 0 12px rgba(94, 200, 179, 0.7);
        }

        .submenu-link.active .sub-icon {
            opacity: 1;
            color: var(--gold-light);
        }

        .menu-badge {
            margin-left: auto;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 .35rem;
            border-radius: 999px;
            background: #3b82f6;
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .submenu-group-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: rgba(94, 200, 179, 0.55);
            padding: 0.65rem 0.75rem 0.3rem;
            font-weight: 700;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 1rem 1.5rem 1.5rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.32s ease;
        }

        .content-panel {
            background: var(--bg-panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.25rem;
            min-height: 400px;
            box-shadow: var(--shadow);
            color: var(--text);
            backdrop-filter: blur(10px);
        }

        /* ——— Tableaux : en-têtes stylés + contenu centré (projet entier) ——— */
        .table-wrap {
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: var(--bg-table);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
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
            color: var(--th-text) !important;
            white-space: nowrap;
            background: linear-gradient(180deg, var(--accent-soft) 0%, var(--accent) 48%, var(--accent-deep) 100%) !important;
            border-bottom: 2px solid rgba(15, 23, 42, 0.2) !important;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.35),
                0 4px 14px rgba(0, 0, 0, 0.12);
            position: relative;
        }

        .data-table thead th:not(:last-child),
        .lines-table thead th:not(:last-child),
        .content-panel table thead th:not(:last-child) {
            border-right: 1px solid rgba(15, 23, 42, 0.12);
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
            color: var(--text-soft);
            border-bottom: 1px solid var(--border);
        }

        .data-table tbody tr:nth-child(even) td,
        .content-panel table tbody tr:nth-child(even) td {
            background: var(--bg-row-alt);
        }

        .data-table tbody tr:hover td,
        .content-panel table tbody tr:hover td {
            background: var(--bg-row-hover) !important;
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
            html[data-sidebar="collapsed"] .sidebar {
                display: none;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                border-right: none;
                border-bottom: 1px solid rgba(94, 200, 179, 0.2);
                overflow: visible;
                transform: none;
                opacity: 1;
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

        /* ——— Mode clair : contrastes lisibles ——— */
        html[data-theme="light"] .navbar-title h1 {
            background: none;
            -webkit-text-fill-color: #0F766E;
            color: #0F766E;
            filter: none;
        }
        html[data-theme="light"] .navbar-user {
            background: #F1F5F9;
            border-color: rgba(15, 118, 110, 0.22);
            box-shadow: none;
        }
        html[data-theme="light"] .navbar-user-name { color: #0F172A; }
        html[data-theme="light"] .navbar-user-statut {
            color: #0F766E;
            background: rgba(15, 118, 110, 0.08);
            border-color: rgba(15, 118, 110, 0.22);
        }
        html[data-theme="light"] .navbar-logo {
            background: #fff;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }
        html[data-theme="light"] .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.06);
        }
        html[data-theme="light"] .sidebar-label { color: #64748B; }
        html[data-theme="light"] .sidebar-label::after {
            background: linear-gradient(90deg, rgba(15, 118, 110, 0.25), transparent);
        }
        html[data-theme="light"] .sidebar-link {
            color: #334155;
            background: #F8FAFC;
            border-color: rgba(15, 23, 42, 0.08);
            box-shadow: none;
        }
        html[data-theme="light"] .sidebar-link:hover,
        html[data-theme="light"] .sidebar-link.active {
            color: #0F766E;
            background: rgba(15, 118, 110, 0.1);
            border-color: rgba(15, 118, 110, 0.28);
            box-shadow: none;
        }
        html[data-theme="light"] .sidebar-link .icon,
        html[data-theme="light"] .sidebar-link .link-arrow,
        html[data-theme="light"] .submenu-link .sub-icon { color: #0F766E; }
        html[data-theme="light"] .sidebar-link .icon-wrap {
            background: rgba(15, 118, 110, 0.08);
            border-color: rgba(15, 118, 110, 0.18);
            box-shadow: none;
        }
        html[data-theme="light"] .submenu {
            border-left-color: rgba(15, 118, 110, 0.2);
            background: transparent;
        }
        html[data-theme="light"] .submenu-link {
            color: #475569;
        }
        html[data-theme="light"] .submenu-link:hover,
        html[data-theme="light"] .submenu-link.active {
            color: #0F766E;
            background: rgba(15, 118, 110, 0.08);
            border-color: rgba(15, 118, 110, 0.2);
            box-shadow: none;
        }
        html[data-theme="light"] .submenu-group-label { color: #94A3B8; }
        html[data-theme="light"] .btn-logout {
            color: #0F766E;
            background: #F1F5F9;
            border-color: rgba(15, 118, 110, 0.28);
        }
        html[data-theme="light"] .btn-logout:hover {
            background: rgba(15, 118, 110, 0.12);
            color: #0F172A;
            box-shadow: none;
        }
        html[data-theme="light"] .btn-dashboard {
            color: #fff;
            background: linear-gradient(135deg, #14B8A6 0%, #0F766E 55%, #0D9488 100%);
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(15, 118, 110, 0.25);
        }
        html[data-theme="light"] .page-toolbar h2,
        html[data-theme="light"] .modal-header h3 { color: #0F766E !important; }
        html[data-theme="light"] .page-meta,
        html[data-theme="light"] .page-note,
        html[data-theme="light"] .empty-row td { color: #64748B !important; }
        html[data-theme="light"] .btn-ghost {
            color: #0F766E !important;
            background: #F1F5F9 !important;
            border-color: rgba(15, 118, 110, 0.28) !important;
        }
        html[data-theme="light"] .btn-gold {
            color: #fff !important;
            background: linear-gradient(135deg, #14B8A6, #0F766E 55%, #0D9488) !important;
        }
        html[data-theme="light"] .field label { color: #0F766E !important; }
        html[data-theme="light"] .field input,
        html[data-theme="light"] .field select,
        html[data-theme="light"] .field textarea,
        html[data-theme="light"] .filter select,
        html[data-theme="light"] .lines-table tbody td input {
            background: #FFFFFF !important;
            color: #0F172A !important;
            border-color: #CBD5E1 !important;
        }
        html[data-theme="light"] .icon-btn {
            color: #0F766E !important;
            background: #F1F5F9 !important;
            border-color: #CBD5E1 !important;
        }
        html[data-theme="light"] .modal-backdrop { background: rgba(15, 23, 42, 0.45) !important; }
        html[data-theme="light"] .modal-sheet {
            background: #FFFFFF !important;
            border-color: rgba(15, 118, 110, 0.22) !important;
            color: #0F172A !important;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18) !important;
        }
        html[data-theme="light"] .chip {
            color: #0F766E !important;
            background: rgba(15, 118, 110, 0.08) !important;
            border-color: rgba(15, 118, 110, 0.22) !important;
        }
        html[data-theme="light"] .totals-bar { color: #0F766E !important; border-color: rgba(15, 23, 42, 0.1) !important; }
        html[data-theme="light"] .dash-card,
        html[data-theme="light"] .dash-mini-table-wrap {
            background: #FFFFFF !important;
            border-color: rgba(15, 23, 42, 0.1) !important;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05) !important;
        }
        html[data-theme="light"] .dash-mini-table thead th {
            color: #FFFFFF !important;
            background: linear-gradient(180deg, #14B8A6 0%, #0F766E 55%, #0D9488 100%) !important;
            border-bottom-color: rgba(15, 23, 42, 0.15) !important;
        }
        html[data-theme="light"] .dash-mini-table tbody td {
            color: #334155 !important;
            border-bottom-color: rgba(15, 23, 42, 0.08) !important;
        }
        html[data-theme="light"] .dash-mini-table tbody tr:nth-child(even) td { background: #F8FAFC !important; }
        html[data-theme="light"] .dash-mini-table tbody tr:hover td { background: rgba(15, 118, 110, 0.08) !important; }
        html[data-theme="light"] .alert-error {
            background: #FEF2F2 !important;
            border-color: #FECACA !important;
            color: #B91C1C !important;
        }
        html[data-theme="light"] .alert-success {
            background: #ECFDF5 !important;
            border-color: #A7F3D0 !important;
            color: #047857 !important;
        }

        /* Contenu page : textes encore en blanc / pastels trop clairs */
        html[data-theme="light"] .user-name,
        html[data-theme="light"] .card-value,
        html[data-theme="light"] .card-split-value,
        html[data-theme="light"] .users-table tbody td,
        html[data-theme="light"] .content-panel td,
        html[data-theme="light"] .content-panel .data-table tbody td {
            color: #0F172A !important;
        }
        html[data-theme="light"] .user-sub,
        html[data-theme="light"] .hint,
        html[data-theme="light"] .field .hint,
        html[data-theme="light"] .bons-hint,
        html[data-theme="light"] .qte-zero,
        html[data-theme="light"] .auth-empty,
        html[data-theme="light"] .empty-mini td,
        html[data-theme="light"] .pwd-mask,
        html[data-theme="light"] .card-title,
        html[data-theme="light"] .card-split-label,
        html[data-theme="light"] .card-value span,
        html[data-theme="light"] .card-split-value span {
            color: #64748B !important;
        }
        html[data-theme="light"] .card-icon,
        html[data-theme="light"] .login-pill,
        html[data-theme="light"] .auth-chip {
            color: #0F766E !important;
            background: rgba(15, 118, 110, 0.08) !important;
            border-color: rgba(15, 118, 110, 0.22) !important;
        }
        html[data-theme="light"] .card-select,
        html[data-theme="light"] .period-toggle button,
        html[data-theme="light"] .bons-table input,
        html[data-theme="light"] input:not([type="checkbox"]):not([type="radio"]):not(.statut-select),
        html[data-theme="light"] select:not(.statut-select),
        html[data-theme="light"] textarea {
            color: #0F172A !important;
            background: #FFFFFF !important;
            border-color: #CBD5E1 !important;
        }
        html[data-theme="light"] .card-select option {
            background: #FFFFFF !important;
            color: #0F172A !important;
        }
        html[data-theme="light"] .period-toggle {
            background: #F1F5F9 !important;
            border-color: #CBD5E1 !important;
        }
        html[data-theme="light"] .period-toggle button.active {
            background: rgba(15, 118, 110, 0.14) !important;
            color: #0F766E !important;
        }
        html[data-theme="light"] .card-split-item,
        html[data-theme="light"] .chart-wrap,
        html[data-theme="light"] .table-wrap {
            background: #F8FAFC !important;
            border-color: rgba(15, 23, 42, 0.1) !important;
        }
        html[data-theme="light"] .users-table tbody tr:nth-child(even) td,
        html[data-theme="light"] .data-table tbody tr:nth-child(even) td {
            background: #F1F5F9 !important;
        }
        html[data-theme="light"] .solde-pos,
        html[data-theme="light"] .qte-neg,
        html[data-theme="light"] .badge-solde.non { color: #B91C1C !important; }
        html[data-theme="light"] .solde-zero,
        html[data-theme="light"] .qte-pos,
        html[data-theme="light"] .badge-solde.oui { color: #047857 !important; }
        html[data-theme="light"] .badge-solde.oui {
            background: rgba(16, 185, 129, 0.12) !important;
            border-color: rgba(16, 185, 129, 0.35) !important;
        }
        html[data-theme="light"] .badge-solde.non {
            background: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }
        html[data-theme="light"] .users-table .badge-statut {
            color: #0F766E !important;
            background: rgba(15, 118, 110, 0.1) !important;
            border-color: rgba(15, 118, 110, 0.28) !important;
        }
        html[data-theme="light"] .users-table .badge-statut.directeur {
            color: #0F766E !important;
            background: rgba(15, 118, 110, 0.16) !important;
        }
        html[data-theme="light"] .users-table .badge-statut.gerant {
            color: #1D4ED8 !important;
            background: rgba(37, 99, 235, 0.1) !important;
            border-color: rgba(37, 99, 235, 0.28) !important;
        }
        html[data-theme="light"] .users-table .badge-statut.facturation {
            color: #047857 !important;
            background: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.28) !important;
        }
        html[data-theme="light"] .users-table .badge-statut.magasinier {
            color: #A16207 !important;
            background: rgba(234, 179, 8, 0.12) !important;
            border-color: rgba(202, 138, 4, 0.35) !important;
        }
        html[data-theme="light"] .users-table .badge-statut.depot_tanger,
        html[data-theme="light"] .users-table .badge-statut.depot_nador,
        html[data-theme="light"] .users-table .badge-statut.depot_tetouan,
        html[data-theme="light"] .users-table .badge-statut.depot_houcima,
        html[data-theme="light"] .users-table .badge-statut.depot_belkciri {
            color: #7E22CE !important;
            background: rgba(168, 85, 247, 0.1) !important;
            border-color: rgba(147, 51, 234, 0.28) !important;
        }
        html[data-theme="light"] .btn-ghost {
            color: #0F766E !important;
            background: #F1F5F9 !important;
            border-color: rgba(15, 118, 110, 0.28) !important;
        }
        html[data-theme="light"] .modal-sheet label,
        html[data-theme="light"] .modal-sheet p,
        html[data-theme="light"] .modal-sheet .hint,
        html[data-theme="light"] .modal-body {
            color: #334155 !important;
        }
        html[data-theme="light"] .sidebar-link.active,
        html[data-theme="light"] .submenu-link.active {
            color: #0F766E !important;
        }
        html[data-theme="dark"] .data-table thead th,
        html[data-theme="dark"] .lines-table thead th,
        html[data-theme="dark"] .content-panel table thead th {
            color: #0B1020 !important;
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
            <button type="button" class="sidebar-panel-toggle" id="sidebarPanelToggle" title="Masquer le menu" aria-label="Masquer le menu latéral" aria-pressed="false">
                <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M9 3v18"/>
                    <path d="M14 9l3 3-3 3"/>
                </svg>
                <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M9 3v18"/>
                    <path d="M13 9l-3 3 3 3"/>
                </svg>
            </button>
            <button type="button" class="theme-toggle" id="themeToggle" title="Mode clair / sombre" aria-label="Basculer mode clair ou sombre">
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M21 14.5A8.5 8.5 0 1 1 9.5 3 7 7 0 0 0 21 14.5z"/>
                </svg>
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
            </button>
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
            <div class="sidebar-top">
                <a href="{{ route('dashboard') }}" class="btn-dashboard {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M3 10.5L12 3l9 7.5"/>
                        <path d="M5 10v9a1 1 0 0 0 1 1h4v-5h4v5h4a1 1 0 0 0 1-1v-9"/>
                    </svg>
                    Tableau de Bord
                </a>
            </div>
            <div class="sidebar-scroll">
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
                                            class="submenu-link {{ request()->routeIs($child['route']) || ($child['key'] === 'stock.depot' && request()->routeIs('stock.depot_*')) ? 'active' : '' }}"
                                        >
                                            @include('partials.menu-icon', ['icon' => $child['icon'] ?? 'file', 'class' => 'sub-icon'])
                                            <span>{{ $child['label'] }}</span>
                                            @if (($child['key'] ?? '') === 'stock.commande_depot' && \App\Support\StockNotifications::pendingCommandes(auth()->user()) > 0)
                                                <span class="menu-badge">{{ \App\Support\StockNotifications::pendingCommandes(auth()->user()) }}</span>
                                            @endif
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
            var THEME_KEY = 'damiorif-theme';
            var toggle = document.getElementById('themeToggle');

            function currentTheme() {
                return document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
            }

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                try { localStorage.setItem(THEME_KEY, theme); } catch (e) {}
                if (toggle) {
                    toggle.setAttribute('aria-label', theme === 'light' ? 'Passer en mode sombre' : 'Passer en mode clair');
                    toggle.title = theme === 'light' ? 'Mode sombre' : 'Mode clair';
                }
                try {
                    window.dispatchEvent(new CustomEvent('damiorif-theme-change', { detail: { theme: theme } }));
                } catch (e) {}
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    applyTheme(currentTheme() === 'light' ? 'dark' : 'light');
                });
                applyTheme(currentTheme());
            }

            var SIDEBAR_KEY = 'damiorif-sidebar';
            var sidebarBtn = document.getElementById('sidebarPanelToggle');

            function sidebarCollapsed() {
                return document.documentElement.getAttribute('data-sidebar') === 'collapsed';
            }

            function applySidebar(collapsed) {
                if (collapsed) {
                    document.documentElement.setAttribute('data-sidebar', 'collapsed');
                } else {
                    document.documentElement.removeAttribute('data-sidebar');
                }
                try {
                    localStorage.setItem(SIDEBAR_KEY, collapsed ? 'collapsed' : 'open');
                } catch (e) {}
                if (sidebarBtn) {
                    sidebarBtn.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
                    sidebarBtn.title = collapsed ? 'Afficher le menu' : 'Masquer le menu';
                    sidebarBtn.setAttribute('aria-label', collapsed ? 'Afficher le menu latéral' : 'Masquer le menu latéral');
                }
            }

            if (sidebarBtn) {
                sidebarBtn.addEventListener('click', function () {
                    applySidebar(!sidebarCollapsed());
                });
                applySidebar(sidebarCollapsed());
            }

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

        window.damioBindTableFilters = function (tableId, options) {
            var table = document.getElementById(tableId);
            if (!table) return;
            var tbody = table.querySelector('tbody');
            if (!tbody) return;
            var inputs = document.querySelectorAll((options && options.filterRoot) || '.filter-bar [data-filter]');
            var emptyRow = tbody.querySelector('.js-filter-empty') || tbody.querySelector('.empty-row');

            function apply() {
                var filters = {};
                inputs.forEach(function (input) {
                    filters[input.getAttribute('data-filter')] = (input.value || '').trim().toLowerCase();
                });
                var visible = 0;
                var sums = {};
                tbody.querySelectorAll('tr[data-row]').forEach(function (tr) {
                    var ok = true;
                    Object.keys(filters).forEach(function (key) {
                        if (!filters[key]) return;
                        var hay = (tr.getAttribute('data-' + key) || '').toLowerCase();
                        if (hay.indexOf(filters[key]) === -1) ok = false;
                    });
                    tr.style.display = ok ? '' : 'none';
                    if (!ok) return;
                    visible++;
                    var sumKeys = (tr.getAttribute('data-sum') || '').split(',').filter(Boolean);
                    sumKeys.forEach(function (pair) {
                        var parts = pair.split(':');
                        var name = parts[0];
                        var val = parseFloat(parts[1]) || 0;
                        sums[name] = (sums[name] || 0) + val;
                    });
                });
                var dataCount = tbody.querySelectorAll('tr[data-row]').length;
                if (emptyRow && emptyRow.classList.contains('js-filter-empty')) {
                    emptyRow.style.display = (dataCount && !visible) ? '' : 'none';
                }
                if (options && typeof options.onChange === 'function') {
                    options.onChange(visible, sums);
                }
            }

            inputs.forEach(function (input) {
                input.addEventListener('input', apply);
            });
            apply();
        };

        window.damioFormatMoney = function (n) {
            return (Math.round((n || 0) * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };
    </script>
</body>
</html>
