<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pembayaran Digital SMKS Bunda Kasih Sudiang">
    <title>SMKS Bunda Kasih Sudiang - Sistem Pembayaran Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --primary-color: #4F46E5; --secondary-color: #7C3AED; --accent-color: #10B981; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: transparent; padding: 20px 0; transition: all 0.3s; position: fixed; width: 100%; z-index: 1000; }
        .navbar.scrolled { background: white; box-shadow: 0 2px 20px rgba(0,0,0,0.1); padding: 10px 0; }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: white !important; }
        .navbar.scrolled .navbar-brand { color: var(--primary-color) !important; }
        .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; }
        .navbar.scrolled .nav-link { color: #333 !important; }
        .btn-login { background: white; color: var(--primary-color); padding: 10px 25px; border-radius: 50px; font-weight: 600; }
        .hero { min-height: 100vh; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); display: flex; align-items: center; padding-top: 80px; }
        .hero h1 { font-size: 3rem; font-weight: 700; color: white; margin-bottom: 1.5rem; line-height: 1.2; }
        .hero p { font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 2rem; }
        .btn-cta { background: white; color: var(--primary-color); padding: 15px 35px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn-cta:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); color: var(--primary-color); }
        .features { padding: 100px 0; background: #f8f9fa; }
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-size: 2.5rem; font-weight: 700; color: #1F2937; }
        .section-title p { color: #6B7280; }
        .feature-card { background: white; border-radius: 20px; padding: 40px 30px; text-align: center; transition: all 0.3s; height: 100%; border: 1px solid #E5E7EB; }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .feature-icon { width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; font-size: 2rem; color: white; }
        .feature-icon.primary { background: linear-gradient(135deg, #4F46E5, #7C3AED); }
        .feature-icon.success { background: linear-gradient(135deg, #10B981, #059669); }
        .feature-icon.warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .feature-icon.info { background: linear-gradient(135deg, #3B82F6, #2563EB); }
        .how-it-works { padding: 100px 0; }
        .step { text-align: center; }
        .step-number { width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 700; margin: 0 auto 20px; }
        .stats { padding: 80px 0; background: #1F2937; color: white; }
        .stat-value { font-size: 3rem; font-weight: 700; color: var(--accent-color); }
        .stat-label { opacity: 0.8; }
        .cta { padding: 100px 0; text-align: center; }
        footer { background: #111827; color: white; padding: 40px 0 20px; }
        footer a { color: rgba(255,255,255,0.7); text-decoration: none; }
        footer a:hover { color: white; }
        @media (max-width: 768px) { .hero h1 { font-size: 2rem; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap me-2"></i>SMKS Bunda Kasih</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-login" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1>Pembayaran Sekolah Kini Lebih Mudah & Cepat</h1>
                    <p>Sistem pembayaran digital yang memudahkan orang tua membayar SPP dan biaya sekolah lainnya secara online, kapan saja dan di mana saja dengan berbagai metode pembayaran.</p>
                    <a href="{{ route('login') }}" class="btn-cta"><i class="fas fa-rocket me-2"></i>Mulai Sekarang</a>
                    <div class="mt-5 d-flex gap-4">
                        <div><h3 class="text-white mb-0">1000+</h3><small class="text-white-50">Siswa Aktif</small></div>
                        <div><h3 class="text-white mb-0">95%</h3><small class="text-white-50">Kepuasan</small></div>
                        <div><h3 class="text-white mb-0">24/7</h3><small class="text-white-50">Akses</small></div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <i class="fas fa-mobile-alt" style="font-size: 15rem; color: rgba(255,255,255,0.2);"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Fitur Unggulan</h2>
                <p>Sistem pembayaran yang dirancang untuk kemudahan dan keamanan</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon primary"><i class="fas fa-credit-card"></i></div>
                        <h4>Multi Payment</h4>
                        <p>Transfer Bank, E-Wallet, Virtual Account, dan metode lainnya</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon success"><i class="fas fa-shield-alt"></i></div>
                        <h4>Aman & Terpercaya</h4>
                        <p>Transaksi dilindungi enkripsi dan powered by Midtrans</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon warning"><i class="fas fa-bell"></i></div>
                        <h4>Notifikasi Real-time</h4>
                        <p>Pemberitahuan tagihan dan konfirmasi pembayaran instan</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon info"><i class="fas fa-chart-line"></i></div>
                        <h4>Laporan Lengkap</h4>
                        <p>Pantau riwayat pembayaran dan tagihan dengan mudah</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>Cara Kerja</h2>
                <p>Tiga langkah mudah untuk melakukan pembayaran</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h5>Login ke Sistem</h5>
                        <p class="text-muted">Masuk menggunakan akun yang telah didaftarkan oleh sekolah</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="step">
                        <div class="step-number">2</div>
                        <h5>Pilih Tagihan</h5>
                        <p class="text-muted">Pilih tagihan yang ingin dibayar dari daftar tagihan aktif</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="step">
                        <div class="step-number">3</div>
                        <h5>Bayar Online</h5>
                        <p class="text-muted">Pilih metode pembayaran dan selesaikan transaksi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-value">1000+</div>
                    <div class="stat-label">Siswa Terdaftar</div>
                </div>
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-value">50K+</div>
                    <div class="stat-label">Transaksi Sukses</div>
                </div>
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-value">99%</div>
                    <div class="stat-label">Uptime System</div>
                </div>
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-value">5 Detik</div>
                    <div class="stat-label">Konfirmasi Rata-rata</div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Siap Memulai?</h2>
            <p class="text-muted mb-4">Login sekarang dan nikmati kemudahan pembayaran digital</p>
            <a href="{{ route('login') }}" class="btn-cta" style="background: var(--primary-color); color: white;">
                <i class="fas fa-sign-in-alt me-2"></i>Login ke Sistem
            </a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-graduation-cap me-2"></i>SMKS Bunda Kasih</h5>
                    <p class="text-muted small">Sistem Pembayaran Digital untuk kemudahan transaksi pembayaran sekolah.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Kontak</h5>
                    <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i>Jl. Sudiang Raya, Makassar</p>
                    <p class="small text-muted mb-1"><i class="fas fa-phone me-2"></i>(0411) 123-4567</p>
                    <p class="small text-muted"><i class="fas fa-envelope me-2"></i>info@smksbundakasih.sch.id</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Jam Operasional</h5>
                    <p class="small text-muted mb-1">Senin - Jumat: 07:00 - 16:00</p>
                    <p class="small text-muted mb-1">Sabtu: 07:00 - 12:00</p>
                    <p class="small text-muted">Pembayaran Online: 24/7</p>
                </div>
            </div>
            <hr class="border-secondary">
            <p class="text-center text-muted small mb-0">&copy; {{ date('Y') }} SMKS Bunda Kasih Sudiang. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
