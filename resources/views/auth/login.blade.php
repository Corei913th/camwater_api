<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CamwaterPRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --background: #f8fafc;
            --text-main: #1e293b;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

        body {
            background-color: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-main);
        }

        .logo i { color: var(--primary); }
        .logo span span { color: var(--primary); }

        h1 { font-size: 1.25rem; font-weight: 600; text-align: center; margin-bottom: 0.5rem; }
        p.subtitle { color: #64748b; text-align: center; margin-bottom: 2rem; font-size: 0.875rem; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: #475569; }
        
        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus { border-color: var(--primary); }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.75rem;
            border: none;
            background: var(--primary);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .btn:hover { background: var(--primary-dark); }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <i data-lucide="droplets"></i>
            <span>Camwater<span>PRO</span></span>
        </div>
        
        <h1>Bienvenue</h1>
        <p class="subtitle">Connectez-vous à votre espace gestionnaire</p>

        @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('web.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Adresse Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@camwater.cm" style="{{ $errors->has('email') ? 'border-color: var(--danger);' : '' }}">
                @error('email')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••" style="{{ $errors->has('password') ? 'border-color: var(--danger);' : '' }}">
                @error('password')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
