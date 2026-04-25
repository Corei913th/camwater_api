<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CamwaterPRO') - Dashboard</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #25eb35ff;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --background: #f8fafc;
            --sidebar: #0f172a;
            --sidebar-hover: #1e293b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        aside {
            width: 260px;
            background-color: var(--sidebar);
            color: white;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo span {
            color: var(--primary);
        }

        nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        nav a {
            color: #94a3b8;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }

        nav a:hover, nav a.active {
            background-color: var(--sidebar-hover);
            color: white;
        }

        nav a.active {
            background-color: var(--primary);
            color: white;
        }

        /* Main Content */
        main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid var(--border);
        }

        /* Components */
        .card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-info h3 {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .stat-info p {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th {
            text-align: left;
            padding: 1rem;
            color: var(--text-muted);
            font-weight: 500;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }

        @media (max-width: 768px) {
            aside { width: 80px; padding: 1rem; }
            aside .logo span, aside nav a span { display: none; }
            main { margin-left: 80px; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <aside>
        <div class="logo">
            <i data-lucide="droplets"></i>
            <span>Camwater<span>PRO</span></span>
        </div>
        <nav>
            <a href="{{ route('web.dashboard') }}" class="{{ request()->routeIs('web.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('web.abonnes.index') }}" class="{{ request()->routeIs('web.abonnes.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Abonnés</span>
            </a>
            <a href="{{ route('web.factures.index') }}" class="{{ request()->routeIs('web.factures.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>Factures</span>
            </a>
            <a href="#">
                <i data-lucide="alert-circle"></i>
                <span>Réclamations</span>
            </a>
            <a href="#">
                <i data-lucide="bar-chart-3"></i>
                <span>Statistiques</span>
            </a>
            <div style="margin-top: auto;">
                <a href="#">
                    <i data-lucide="settings"></i>
                    <span>Paramètres</span>
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: var(--danger);">
                    <i data-lucide="log-out"></i>
                    <span>Déconnexion</span>
                </a>
                <form id="logout-form" action="{{ route('web.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </nav>
    </aside>

    <main>
        <header>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 700;">@yield('header_title')</h1>
                <p style="color: var(--text-muted);">@yield('header_subtitle')</p>
            </div>
            <div class="user-profile">
                <div style="text-align: right;">
                    <p style="font-weight: 600; font-size: 0.875rem;">{{ Auth::user()->name }}</p>
                    <p style="color: var(--text-muted); font-size: 0.75rem;">{{ Auth::user()->role }}</p>
                </div>
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                    A
                </div>
            </div>
        </header>

        @if(session('success'))
            <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 0.75rem;">
                <i data-lucide="check-circle" style="width: 20px;"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #fecaca; display: flex; align-items: center; gap: 0.75rem;">
                <i data-lucide="alert-circle" style="width: 20px;"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
