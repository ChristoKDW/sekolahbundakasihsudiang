<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pembayaran Digital') - Bunda Kasih Sudiang</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-dark: #4338CA;
            --secondary-color: #10B981;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --info-color: #3B82F6;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F3F4F6;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
            color: #fff;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand img {
            width: 45px;
            height: 45px;
            border-radius: 12px;
        }

        .sidebar-brand-text {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .sidebar-brand-text small {
            font-size: 0.7rem;
            font-weight: 400;
            opacity: 0.7;
        }

        .sidebar.collapsed .sidebar-brand-text {
            display: none;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-label {
            padding: 0.75rem 1.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 600;
        }

        .sidebar.collapsed .menu-label {
            display: none;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.2s ease;
            gap: 0.875rem;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .menu-item.active {
            background: rgba(79, 70, 229, 0.2);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .menu-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        .menu-item span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .sidebar.collapsed .menu-item span {
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Navbar */
        .top-navbar {
            background: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--dark-color);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .toggle-sidebar:hover {
            background: var(--light-color);
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #6B7280;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .notification-btn:hover {
            background: var(--light-color);
            color: var(--dark-color);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--danger-color);
            color: #fff;
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .user-dropdown:hover {
            background: var(--light-color);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .user-role {
            font-size: 0.75rem;
            color: #6B7280;
        }

        /* Page Content */
        .page-content {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .page-subtitle {
            color: #6B7280;
            font-size: 0.9rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stat Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.primary {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .stat-icon.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
        }

        .stat-icon.warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .stat-icon.danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .stat-icon.info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            line-height: 1.2;
        }

        .stat-label {
            color: #6B7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-success {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--light-color);
            border-bottom: 2px solid #E5E7EB;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6B7280;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: var(--light-color);
        }

        /* Forms */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #D1D5DB;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        /* Badges */
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            font-size: 0.75rem;
            border-radius: 6px;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
        }

        /* Modal */
        .modal-content {
            border: none;
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid #E5E7EB;
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #E5E7EB;
            padding: 1rem 1.5rem;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.collapsed ~ .main-content {
                margin-left: 0;
            }

            .page-content {
                padding: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .user-info {
                display: none;
            }

            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6B7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: linear-gradient(135deg, #4F46E5, #10B981); border-radius: 12px;">
                <i class="fas fa-school text-white"></i>
            </div>
            <div class="sidebar-brand-text">
                Bunda Kasih<br>
                <small>Sistem Pembayaran Digital</small>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            @if(auth()->user()->isAdmin())
                <div class="menu-label">Administrasi</div>
                <a href="{{ route('admin.students.index') }}" class="menu-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Data Siswa</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Kelola Role</span>
                </a>
            @endif

            @if(auth()->user()->isTreasurer() || auth()->user()->isAdmin())
                <div class="menu-label">Keuangan</div>
                <a href="{{ route('treasurer.bills.index') }}" class="menu-item {{ request()->routeIs('treasurer.bills.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Kelola Tagihan</span>
                </a>
                <a href="{{ route('treasurer.bill-types.index') }}" class="menu-item {{ request()->routeIs('treasurer.bill-types.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Jenis Tagihan</span>
                </a>
                <a href="{{ route('treasurer.reports.index') }}" class="menu-item {{ request()->routeIs('treasurer.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('treasurer.reconciliation.index') }}" class="menu-item {{ request()->routeIs('treasurer.reconciliation.*') ? 'active' : '' }}">
                    <i class="fas fa-balance-scale"></i>
                    <span>Rekonsiliasi</span>
                </a>
            @endif

            @if(auth()->user()->isParent())
                <div class="menu-label">Pembayaran</div>
                <a href="{{ route('parent.payments.index') }}" class="menu-item {{ request()->routeIs('parent.payments.index') || request()->routeIs('parent.payments.show') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>Tagihan Saya</span>
                </a>
                <a href="{{ route('parent.payments.history') }}" class="menu-item {{ request()->routeIs('parent.payments.history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Pembayaran</span>
                </a>
                <a href="{{ route('parent.students.index') }}" class="menu-item {{ request()->routeIs('parent.students.*') ? 'active' : '' }}">
                    <i class="fas fa-child"></i>
                    <span>Data Anak</span>
                </a>
                <a href="{{ route('parent.profile.index') }}" class="menu-item {{ request()->routeIs('parent.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Profil Saya</span>
                </a>
            @endif

            @if(auth()->user()->isPrincipal() || auth()->user()->isAdmin())
                <div class="menu-label">Laporan</div>
                <a href="{{ route('principal.reports.index') }}" class="menu-item {{ request()->routeIs('principal.reports.index') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Ringkasan</span>
                </a>
                <a href="{{ route('principal.reports.income') }}" class="menu-item {{ request()->routeIs('principal.reports.income') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Pendapatan</span>
                </a>
                <a href="{{ route('principal.reports.collection') }}" class="menu-item {{ request()->routeIs('principal.reports.collection') ? 'active' : '' }}">
                    <i class="fas fa-percentage"></i>
                    <span>Tingkat Koleksi</span>
                </a>
                <a href="{{ route('principal.reports.outstanding') }}" class="menu-item {{ request()->routeIs('principal.reports.outstanding') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Piutang</span>
                </a>
            @endif
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>

            <div class="navbar-right">
                <button class="notification-btn" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="notification-badge">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                    <h6 class="dropdown-header">Notifikasi</h6>
                    @forelse(auth()->user()->unreadNotifications()->take(5)->get() as $notification)
                        <a href="{{ route('notifications.read', $notification) }}" class="dropdown-item py-2">
                            <i class="{{ $notification->type_icon }} me-2"></i>
                            <div>
                                <strong>{{ $notification->title }}</strong>
                                <small class="d-block text-muted">{{ $notification->message }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p class="mb-0">Tidak ada notifikasi baru</p>
                        </div>
                    @endforelse
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('notifications.index') }}" class="dropdown-item text-center">
                        Lihat semua notifikasi
                    </a>
                </div>

                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ auth()->user()->primary_role?->display_name ?? '-' }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user me-2"></i> Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            
            if (window.innerWidth <= 991.98) {
                sidebar.classList.toggle('show');
            }
        });

        // Close sidebar on mobile when clicking overlay
        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
        });

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Confirm delete
        function confirmDelete(form) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        // Format currency
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }
    </script>

    @stack('scripts')
</body>
</html>
