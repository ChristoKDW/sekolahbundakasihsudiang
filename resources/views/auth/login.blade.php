<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pembayaran Sekolah Bunda Kasih</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --secondary: #10B981;
            --dark: #1F2937;
            --light: #F9FAFB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4F46E5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bg-shapes .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 15s infinite ease-in-out;
        }

        .bg-shapes .shape:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-shapes .shape:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
            animation-delay: 2s;
        }

        .bg-shapes .shape:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 50%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
        }

        .login-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        /* Left Side - Brand */
        .login-brand {
            flex: 1;
            background: linear-gradient(135deg, var(--dark) 0%, #374151 100%);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.3) 0%, transparent 70%);
        }

        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        }

        .brand-logo i {
            font-size: 2rem;
            color: #fff;
        }

        .brand-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .brand-description {
            font-size: 0.95rem;
            opacity: 0.7;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(10px);
        }

        .feature-icon {
            width: 45px;
            height: 45px;
            background: rgba(79, 70, 229, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon i {
            font-size: 1.1rem;
        }

        .feature-text strong {
            display: block;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .feature-text small {
            opacity: 0.7;
            font-size: 0.85rem;
        }

        /* Right Side - Form */
        .login-form-side {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .form-header p {
            color: #6B7280;
            font-size: 0.95rem;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating-custom .form-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 1.1rem;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .form-floating-custom input {
            width: 100%;
            padding: 18px 18px 18px 55px;
            border: 2px solid #E5E7EB;
            border-radius: 14px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-floating-custom input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-floating-custom input:focus + .form-icon,
        .form-floating-custom input:not(:placeholder-shown) + .form-icon {
            color: var(--primary);
        }

        .form-floating-custom input::placeholder {
            color: #9CA3AF;
        }

        .form-check-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-check-custom .form-check {
            margin: 0;
        }

        .form-check-custom .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-custom a {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .form-check-custom a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .login-footer {
            margin-top: 30px;
            text-align: center;
        }

        .login-footer p {
            color: #9CA3AF;
            font-size: 0.85rem;
        }

        .alert-custom {
            border-radius: 14px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: none;
        }

        .alert-danger-custom {
            background: #FEE2E2;
            color: #DC2626;
        }

        .alert-success-custom {
            background: #D1FAE5;
            color: #059669;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .login-brand {
                display: none;
            }

            .login-card {
                max-width: 450px;
                margin: 0 auto;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding: 15px;
            }

            .login-card {
                border-radius: 20px;
                min-height: auto;
            }

            .login-form-side {
                padding: 30px 25px;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            z-index: 10;
            padding: 5px;
        }

        .password-toggle:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Left Side - Brand -->
            <div class="login-brand">
                <div class="brand-content">
                    <div class="brand-logo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h1 class="brand-title">Sekolah Bunda Kasih</h1>
                    <p class="brand-subtitle">Sudiang, Makassar</p>
                    <p class="brand-description">
                        Sistem Pembayaran Digital Cerdas untuk kemudahan pembayaran SPP dan biaya sekolah dari jenjang TK hingga SMA.
                    </p>

                    <div class="features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Aman & Terpercaya</strong>
                                <small>Terintegrasi dengan Midtrans</small>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Pembayaran Virtual Account</strong>
                                <small>BCA, BNI, BRI, Mandiri, Permata</small>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Laporan Realtime</strong>
                                <small>Pantau pembayaran dengan mudah</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="login-form-side">
                <div class="form-header">
                    <h2>Selamat Datang! 👋</h2>
                    <p>Silakan masuk ke akun Anda</p>
                </div>

                @if($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                @if(session('status'))
                <div class="alert-custom alert-success-custom">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-floating-custom">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               placeholder="Email Anda"
                               value="{{ old('email') }}"
                               required 
                               autofocus>
                        <i class="fas fa-envelope form-icon"></i>
                    </div>

                    <div class="form-floating-custom">
                        <input type="password" 
                               id="password" 
                               name="password"
                               placeholder="Password"
                               required>
                        <i class="fas fa-lock form-icon"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>

                    <div class="form-check-custom">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </button>
                </form>

                <div class="login-footer">
                    <p>&copy; {{ date('Y') }} Sekolah Bunda Kasih Sudiang<br>
                    <small>TK • SD • SMP • SMA</small></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
