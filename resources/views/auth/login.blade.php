<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Damio Rif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Thème pro DamioRif — navy graphite + menthe */
            --forest: #1B2438;
            --forest-deep: #0B1020;
            --burgundy: #1B2438;
            --burgundy-deep: #0B1020;
            --gold: #5EC8B3;
            --gold-light: #A8E6D8;
            --gold-glow: rgba(94, 200, 179, 0.45);
            --glass: rgba(11, 16, 32, 0.82);
            --glass-border: rgba(94, 200, 179, 0.38);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: #E8F5F2;
            overflow-x: hidden;
        }

        .bg-layer {
            position: fixed;
            inset: 0;
            background-color: #070B14;
            background-image: url('{{ asset('images/damiorif-background.png') }}');
            background-repeat: no-repeat;
            background-position: left center;
            /* Évite un zoom trop fort sur PC portable (cover agrandit trop une image 1024×682) */
            background-size: auto 100%;
            z-index: 0;
        }

        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(
                105deg,
                rgba(7, 11, 20, 0.12) 0%,
                rgba(11, 16, 32, 0.1) 38%,
                rgba(11, 16, 32, 0.52) 70%,
                rgba(7, 11, 20, 0.74) 100%
            );
            z-index: 1;
        }

        .particles {
            position: fixed;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--gold);
            border-radius: 50%;
            opacity: 0;
            animation: float 8s infinite ease-in-out;
            box-shadow: 0 0 8px var(--gold-glow);
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 25%; animation-delay: 1.5s; }
        .particle:nth-child(3) { left: 40%; animation-delay: 3s; }
        .particle:nth-child(4) { left: 60%; animation-delay: 0.8s; }
        .particle:nth-child(5) { left: 75%; animation-delay: 2.2s; }
        .particle:nth-child(6) { left: 88%; animation-delay: 4s; }
        .particle:nth-child(7) { left: 50%; animation-delay: 1s; }
        .particle:nth-child(8) { left: 92%; animation-delay: 3.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.7; }
            90% { opacity: 0.4; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        .page {
            position: relative;
            z-index: 4;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: clamp(0.85rem, 2.2vh, 1.5rem) clamp(1rem, 3vw, 2rem);
        }

        .login-card {
            width: 100%;
            max-width: min(440px, 36vw);
            min-height: 0;
            padding: clamp(1.6rem, 3.2vh, 2.35rem) clamp(1.5rem, 2.4vw, 2.15rem);
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--glass);
            backdrop-filter: blur(18px) saturate(1.4);
            -webkit-backdrop-filter: blur(18px) saturate(1.4);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow:
                0 0 0 1px rgba(94, 200, 179, 0.08),
                0 8px 32px rgba(0, 0, 0, 0.5),
                0 0 60px rgba(94, 200, 179, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            animation: cardEnter 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards,
                       glowPulse 4s ease-in-out infinite 1s;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--gold), transparent 40%, transparent 60%, var(--gold));
            opacity: 0.25;
            z-index: -1;
            filter: blur(1px);
        }

        @keyframes cardEnter {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 0 1px rgba(94,200,179,0.08), 0 8px 32px rgba(0,0,0,0.5), 0 0 40px rgba(94,200,179,0.12), inset 0 1px 0 rgba(255,255,255,0.06); }
            50% { box-shadow: 0 0 0 1px rgba(94,200,179,0.2), 0 12px 40px rgba(0,0,0,0.55), 0 0 80px rgba(94,200,179,0.28), inset 0 1px 0 rgba(255,255,255,0.08); }
        }

        .card-header {
            text-align: center;
            margin-bottom: clamp(1.1rem, 2.4vh, 1.75rem);
        }

        .card-header .brand {
            font-family: 'Fraunces', serif;
            font-size: clamp(1.45rem, 2.2vw, 1.75rem);
            font-weight: 700;
            color: var(--gold);
            letter-spacing: 0.04em;
            text-shadow: 0 0 20px var(--gold-glow);
        }

        .card-header .subtitle {
            font-size: clamp(0.78rem, 1.1vw, 0.88rem);
            color: rgba(255, 255, 255, 0.65);
            margin-top: 0.35rem;
        }

        .card-header .divider {
            width: 72px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 0.85rem auto 0;
        }

        .form-group {
            margin-bottom: clamp(0.85rem, 1.8vh, 1.2rem);
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 0.4rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            color: var(--gold);
            opacity: 0.7;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 0.78rem 1rem 0.78rem 2.75rem;
            background: rgba(7, 11, 20, 0.75);
            border: 1px solid rgba(94, 200, 179, 0.35);
            border-radius: 12px;
            color: #E8F5F2;
            font-family: inherit;
            font-size: 0.92rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            outline: none;
            caret-color: #A8E6D8;
            color-scheme: dark;
        }

        /* Chrome/Edge autofill : garde le fond sombre + texte clair */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus,
        .form-control:-webkit-autofill:active {
            -webkit-text-fill-color: #E8F5F2 !important;
            caret-color: #A8E6D8;
            box-shadow: 0 0 0 1000px #0B1020 inset !important;
            transition: background-color 99999s ease-out;
            border-color: rgba(94, 200, 179, 0.55) !important;
        }

        select.form-control {
            padding-left: 2.75rem;
            cursor: pointer;
            appearance: none;
            background-color: rgba(7, 11, 20, 0.75);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23A8E6D8' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }

        select.form-control option {
            background: #0B1020;
            color: #E8F5F2;
        }

        .form-control::placeholder { color: rgba(168, 230, 216, 0.45); }

        .form-control:focus {
            border-color: var(--gold);
            background: rgba(7, 11, 20, 0.92);
            box-shadow:
                0 0 0 3px rgba(94, 200, 179, 0.15),
                0 0 20px rgba(94, 200, 179, 0.2);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: clamp(1rem, 2vh, 1.35rem);
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .remember-row input[type="checkbox"] {
            accent-color: var(--gold);
            width: 16px;
            height: 16px;
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem 1rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #A8E6D8 0%, #5EC8B3 45%, #2A9B86 100%);
            color: #0B1020;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow:
                0 10px 28px rgba(94, 200, 179, 0.35),
                0 0 0 1px rgba(255,255,255,0.12) inset;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 30px rgba(94, 200, 179, 0.5),
                0 0 50px rgba(94, 200, 179, 0.25);
        }

        .btn-login:hover::after { transform: translateX(100%); }

        .btn-login:active { transform: translateY(0); }

        .alert-error {
            background: rgba(180, 20, 30, 0.25);
            border: 1px solid rgba(255, 80, 80, 0.4);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            color: #ffb4b4;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        .footer-note {
            text-align: center;
            margin-top: clamp(1rem, 2.2vh, 1.5rem);
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.4);
        }

        /* Grands écrans : image un peu plus large, panneau stable */
        @media (min-width: 1600px) {
            .bg-layer {
                background-size: cover;
                background-position: left center;
            }
            .login-card {
                max-width: min(460px, 28vw);
            }
        }

        /* PC portable / écran moyen : image moins zoomée, panneau compact */
        @media (max-width: 1440px) {
            .bg-layer {
                background-size: auto 100%;
                background-position: left center;
            }
            .login-card {
                max-width: min(420px, 40vw);
            }
        }

        @media (max-width: 1100px) {
            .bg-layer {
                background-size: cover;
                background-position: 18% center;
            }
            .login-card {
                max-width: min(400px, 46vw);
            }
        }

        /* Tablette / petit laptop */
        @media (max-width: 900px) {
            .page {
                justify-content: center;
                padding: 1rem;
            }
            .bg-layer {
                background-size: cover;
                background-position: center center;
            }
            .bg-overlay {
                background: linear-gradient(
                    180deg,
                    rgba(7, 11, 20, 0.35) 0%,
                    rgba(11, 16, 32, 0.55) 45%,
                    rgba(7, 11, 20, 0.78) 100%
                );
            }
            .login-card {
                max-width: min(420px, 92vw);
            }
        }

        @media (max-width: 768px) {
            .page {
                justify-content: center;
                padding: 0.85rem;
            }
            .login-card {
                padding: 1.5rem 1.25rem;
                max-width: 94vw;
                border-radius: 16px;
            }
            .login-card::before { border-radius: 18px; }
        }

        /* Hauteur basse (laptops 768–900px) : densifier le panneau */
        @media (max-height: 820px) and (min-width: 769px) {
            .login-card {
                padding: 1.35rem 1.5rem;
            }
            .card-header { margin-bottom: 0.9rem; }
            .form-group { margin-bottom: 0.75rem; }
            .footer-note { margin-top: 0.85rem; }
        }

        @media (max-height: 700px) {
            .particles { display: none; }
            .login-card {
                padding: 1.1rem 1.25rem;
            }
            .card-header .brand { font-size: 1.35rem; }
            .form-control { padding: 0.65rem 0.9rem 0.65rem 2.55rem; font-size: 0.88rem; }
            .btn-login { padding: 0.7rem; font-size: 0.85rem; }
        }
    </style>
</head>
<body>
    <div class="bg-layer"></div>
    <div class="bg-overlay"></div>

    <div class="particles">
        @for ($i = 1; $i <= 8; $i++)
            <span class="particle"></span>
        @endfor
    </div>

    <main class="page">
        <div class="login-card">
            <div class="card-header">
                <div class="brand">Damio Rif</div>
                <div class="subtitle">Système de Gestion</div>
                <div class="divider"></div>
            </div>

            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="on">
                @csrf

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <select name="statut" id="statut" class="form-control" required autocomplete="off">
                            <option value="" disabled {{ old('statut', 'directeur') ? '' : 'selected' }}>— Sélectionner —</option>
                            <option value="directeur" {{ old('statut', 'directeur') === 'directeur' ? 'selected' : '' }}>Directeur</option>
                            <option value="gerant" {{ old('statut', 'directeur') === 'gerant' ? 'selected' : '' }}>Gérant</option>
                            <option value="facturation" {{ old('statut', 'directeur') === 'facturation' ? 'selected' : '' }}>Facturation</option>
                            <option value="magasinier" {{ old('statut', 'directeur') === 'magasinier' ? 'selected' : '' }}>Magasinier</option>
                            <option value="depot_tanger" {{ old('statut', 'directeur') === 'depot_tanger' ? 'selected' : '' }}>Depot Tanger</option>
                            <option value="depot_nador" {{ old('statut', 'directeur') === 'depot_nador' ? 'selected' : '' }}>Depot Nador</option>
                            <option value="depot_tetouan" {{ old('statut', 'directeur') === 'depot_tetouan' ? 'selected' : '' }}>Depot Tetouan</option>
                            <option value="depot_houcima" {{ old('statut', 'directeur') === 'depot_houcima' ? 'selected' : '' }}>Depot Houcima</option>
                            <option value="depot_belkciri" {{ old('statut', 'directeur') === 'depot_belkciri' ? 'selected' : '' }}>Depot Belkciri</option>
                            <option value="depot_damiorif" {{ old('statut', 'directeur') === 'depot_damiorif' ? 'selected' : '' }}>Dépôt DamioRif</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="login">Login</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input
                            type="text"
                            name="login"
                            id="login"
                            class="form-control"
                            placeholder="Votre identifiant"
                            value="{{ old('login') }}"
                            required
                            autofocus
                            autocomplete="username"
                            spellcheck="false"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember" value="1">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">Connexion</button>
            </form>

            <p class="footer-note">© {{ date('Y') }} A2s------Tous Droits Réservés</p>
        </div>
    </main>
</body>
</html>
