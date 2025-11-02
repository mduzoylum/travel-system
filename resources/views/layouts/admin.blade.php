<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Travel System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            z-index: 1000;
            position: relative;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand:hover {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }
        
        .nav-item.dropdown:hover .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            background: white;
            backdrop-filter: blur(10px);
            z-index: 9999 !important;
        }
        
        .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: #495057;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            margin-right: 0.75rem;
            width: 16px;
            text-align: center;
        }
        
        .dropdown-divider {
            margin: 0.5rem 1rem;
            border-color: #e9ecef;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }
        
        .main-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            margin: 2rem 0;
            padding: 2rem;
            min-height: calc(100vh - 200px);
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: 3rem;
            border-radius: 20px 20px 0 0;
        }
        
        @media (max-width: 768px) {
            .navbar-nav {
                margin-top: 1rem;
            }
            
            .nav-link {
                margin: 0.25rem 0;
            }
            
            .main-content {
                margin: 1rem 0;
                padding: 1.5rem;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-plane-departure me-2"></i>
                Travel Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(auth()->user()->isAdmin())
                        {{-- Admin Menüsü --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-file-contract me-1"></i>
                                Kontrat Modülü
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.hotels.index') }}"><i class="fas fa-hotel"></i> Oteller</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.contracts.index') }}"><i class="fas fa-handshake"></i> Kontratlar</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.suppliers.index') }}"><i class="fas fa-truck"></i> Tedarikçiler</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.supplier-groups.index') }}"><i class="fas fa-layer-group"></i> Tedarikçi Grupları</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.supplier-payments.index') }}"><i class="fas fa-money-bill-wave"></i> Tedarikçi Ödemeleri</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-credit-card me-1"></i>
                                Kredi Sistemi
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.credits.index') }}"><i class="fas fa-wallet"></i> Kredi Hesapları</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.firms.index') }}"><i class="fas fa-building"></i> Firmalar</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-check-circle me-1"></i>
                                Onay Sistemi
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.approvals.index') }}"><i class="fas fa-cogs"></i> Onay Senaryoları</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.approval-requests.index') }}"><i class="fas fa-clipboard-list"></i> Onay İstekleri</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-chart-line me-1"></i>
                                Kar Sistemi
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.profits.index') }}"><i class="fas fa-coins"></i> Kar Kuralları</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.profits.service-fees') }}"><i class="fas fa-percentage"></i> Servis Ücretleri</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.profits.calculations') }}"><i class="fas fa-calculator"></i> Kar Hesaplamaları</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.profits.reports') }}"><i class="fas fa-chart-bar"></i> Kar Raporları</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users me-1"></i>
                                Kullanıcılar
                            </a>
                        </li>
                    @elseif(auth()->user()->isSupplier())
                        {{-- Tedarikçi Kullanıcı Menüsü --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
                                <i class="fas fa-truck me-1"></i>
                                Tedarikçiler
                            </a>
                        </li>
                    @else
                        {{-- Normal Kullanıcı Menüsü --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
                                <i class="fas fa-truck me-1"></i>
                                Tedarikçiler
                            </a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.users.edit', auth()->user()->id) }}"><i class="fas fa-user-circle"></i> Profil</a></li>
                            @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i> Ayarlar</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Giriş Yap
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="fas fa-plane-departure me-2"></i>
                    Travel System Admin Panel
                </div>
                <div class="col-md-6 text-md-end">
                    &copy; {{ date('Y') }} Travel System. Tüm hakları saklıdır.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
