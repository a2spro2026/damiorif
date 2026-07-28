<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Damio Rif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --burgundy: #54000b;
            --burgundy-deep: #2d0006;
            --gold: #c9a45c;
            --gold-light: #e8d5a8;
            --gold-glow: rgba(201, 164, 92, 0.45);
            --glass: rgba(45, 0, 6, 0.72);
            --glass-border: rgba(201, 164, 92, 0.35);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Cairo', sans-serif;
            color: #fff;
            overflow-x: hidden;
        }

        .bg-layer {
            position: fixed;
            inset: 0;
            background: url('{{ asset('images/damiorif-background.png') }}') center / cover no-repeat;
            z-index: 0;
        }

        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(
                105deg,
                rgba(45, 0, 6, 0.55) 0%,
                rgba(45, 0, 6, 0.25) 45%,
                rgba(45, 0, 6, 0.15) 100%
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
            z-index: 3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 1.25rem 2rem 1.25rem 1.25rem;
        }

        .login-card {
            width: 100%;
            max-width: min(640px, 44vw);
            min-height: 78vh;
            padding: 3.75rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--glass);
            backdrop-filter: blur(18px) saturate(1.4);
            -webkit-backdrop-filter: blur(18px) saturate(1.4);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow:
                0 0 0 1px rgba(201, 164, 92, 0.08),
                0 8px 32px rgba(0, 0, 0, 0.5),
                0 0 60px rgba(201, 164, 92, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            animation: cardEnter 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards,
                       glowPulse 4s ease-in-out infinite 1s;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 26px;
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
            0%, 100% { box-shadow: 0 0 0 1px rgba(201,164,92,0.08), 0 8px 32px rgba(0,0,0,0.5), 0 0 40px rgba(201,164,92,0.12), inset 0 1px 0 rgba(255,255,255,0.06); }
            50% { box-shadow: 0 0 0 1px rgba(201,164,92,0.2), 0 12px 40px rgba(0,0,0,0.55), 0 0 80px rgba(201,164,92,0.28), inset 0 1px 0 rgba(255,255,255,0.08); }
        }

        .card-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .card-header .brand {
            font-family: 'Playfair Display', serif;
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: 0.04em;
            text-shadow: 0 0 20px var(--gold-glow);
        }

        .card-header .subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.65);
            margin-top: 0.5rem;
        }

        .card-header .divider {
            width: 90px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 1.25rem auto 0;
        }

        .form-group {
            margin-bottom: 1.65rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-bottom: 0.6rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--gold);
            opacity: 0.7;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 1.05rem 1.15rem 1.05rem 3rem;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(201, 164, 92, 0.3);
            border-radius: 14px;
            color: #fff;
            font-family: inherit;
            font-size: 1.02rem;
            transition: all 0.3s ease;
            outline: none;
        }

        select.form-control {
            padding-left: 3rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23c9a45c' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }

        select.form-control option {
            background: var(--burgundy-deep);
            color: #fff;
        }

        .form-control::placeholder { color: rgba(255, 255, 255, 0.35); }

        .form-control:focus {
            border-color: var(--gold);
            background: rgba(0, 0, 0, 0.5);
            box-shadow:
                0 0 0 3px rgba(201, 164, 92, 0.15),
                0 0 20px rgba(201, 164, 92, 0.2);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.85rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .remember-row input[type="checkbox"] {
            accent-color: var(--gold);
            width: 18px;
            height: 18px;
        }

        .btn-login {
            width: 100%;
            padding: 1.15rem;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #d4af37 0%, #c9a45c 50%, #a8863f 100%);
            color: var(--burgundy-deep);
            font-family: inherit;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow:
                0 4px 20px rgba(201, 164, 92, 0.4),
                0 0 30px rgba(201, 164, 92, 0.15);
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
                0 8px 30px rgba(201, 164, 92, 0.5),
                0 0 50px rgba(201, 164, 92, 0.25);
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
            margin-top: 2.25rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.4);
        }

        @media (max-width: 768px) {
            .page {
                justify-content: center;
                padding: 1rem;
            }
            .login-card {
                padding: 2.5rem 2rem;
                max-width: 94vw;
                min-height: auto;
            }
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

            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label for="statut">Statut</label>
                    <div class="input-wrap">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <select name="statut" id="statut" class="form-control" required>
                            <option value="" disabled {{ old('statut', 'admin') ? '' : 'selected' }}>— Sélectionner —</option>
                            <option value="admin" {{ old('statut', 'admin') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                            <option value="manager" {{ old('statut', 'admin') === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="employe" {{ old('statut', 'admin') === 'employe' ? 'selected' : '' }}>Employé</option>
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
                            value="{{ old('login', 'abdelilah') }}"
                            required
                            autofocus
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
                            value="{{ old('password', 'password') }}"
                            required
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
