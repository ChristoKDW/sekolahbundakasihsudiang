<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sekolah Bunda Kasih</title>
    
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

        .forgot-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .forgot-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Header */
        .forgot-header {
            background: linear-gradient(135deg, var(--dark) 0%, #374151 100%);
            padding: 50px 40px;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .forgot-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.3) 0%, transparent 70%);
        }

        .forgot-header-content {
            position: relative;
            z-index: 1;
        }

        .forgot-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
            animation: pulse-icon 2s infinite ease-in-out;
        }

        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .forgot-icon i {
            font-size: 2.5rem;
            color: #fff;
        }

        .forgot-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .forgot-header p {
            font-size: 0.95rem;
            opacity: 0.85;
            line-height: 1.6;
        }

        /* Body */
        .forgot-body {
            padding: 40px;
        }

        .alert-custom {
            border-radius: 14px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
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

        .alert-info-custom {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 25px;
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

        .form-floating-custom input:focus + .form-icon {
            color: var(--primary);
        }

        .form-floating-custom input::placeholder {
            color: #9CA3AF;
        }

        .btn-reset {
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

        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        }

        .btn-reset:active {
            transform: translateY(-1px);
        }

        .btn-reset:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-dark);
            gap: 12px;
        }

        .back-link i {
            transition: transform 0.3s ease;
        }

        .back-link:hover i {
            transform: translateX(-5px);
        }

        /* Steps Indicator */
        .steps-info {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .step-item {
            flex: 1;
            text-align: center;
            padding: 15px;
            background: #F9FAFB;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .step-item.active {
            background: #EEF2FF;
            border: 2px solid var(--primary);
        }

        .step-number {
            width: 35px;
            height: 35px;
            background: #E5E7EB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0 auto 10px;
            color: #6B7280;
        }

        .step-item.active .step-number {
            background: var(--primary);
            color: #fff;
        }

        .step-item p {
            margin: 0;
            font-size: 0.8rem;
            color: #6B7280;
        }

        .step-item.active p {
            color: var(--primary);
            font-weight: 500;
        }

        /* Footer */
        .forgot-footer {
            text-align: center;
            padding: 20px 40px 30px;
            background: #F9FAFB;
            border-top: 1px solid #E5E7EB;
        }

        .forgot-footer p {
            color: #9CA3AF;
            font-size: 0.85rem;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 575.98px) {
            body {
                padding: 15px;
            }

            .forgot-card {
                border-radius: 20px;
            }

            .forgot-header {
                padding: 40px 25px;
            }

            .forgot-body {
                padding: 30px 25px;
            }

            .forgot-header h1 {
                font-size: 1.25rem;
            }

            .steps-info {
                flex-direction: column;
                gap: 10px;
            }

            .step-item {
                display: flex;
                align-items: center;
                gap: 15px;
                text-align: left;
            }

            .step-number {
                margin: 0;
            }
        }

        /* Loading state */
        .btn-reset.loading .btn-text {
            display: none;
        }

        .btn-reset .spinner {
            display: none;
        }

        .btn-reset.loading .spinner {
            display: inline-block;
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

    <div class="forgot-wrapper">
        <div class="forgot-card">
            <!-- Header -->
            <div class="forgot-header">
                <div class="forgot-header-content">
                    <div class="forgot-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h1>Lupa Password?</h1>
                    <p>Jangan khawatir! Masukkan email yang terdaftar dan kami akan mengirimkan link untuk reset password Anda.</p>
                </div>
            </div>

            <!-- Body -->
            <div class="forgot-body">
                <!-- Steps Info -->
                <div class="steps-info">
                    <div class="step-item active">
                        <div class="step-number">1</div>
                        <p>Masukkan Email</p>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <p>Cek Email</p>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <p>Reset Password</p>
                    </div>
                </div>

                @if(session('status'))
                <div class="alert-custom alert-success-custom">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <div>
                        <strong>Email Terkirim!</strong><br>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                    <div>
                        <strong>Terjadi Kesalahan</strong><br>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                    @csrf
                    
                    <div class="form-floating-custom">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               placeholder="Masukkan alamat email Anda"
                               value="{{ old('email') }}"
                               required 
                               autofocus>
                        <i class="fas fa-envelope form-icon"></i>
                    </div>

                    <button type="submit" class="btn-reset" id="submitBtn">
                        <span class="btn-text">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Link Reset Password
                        </span>
                        <span class="spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            Mengirim...
                        </span>
                    </button>
                </form>

                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke halaman Login
                </a>
            </div>

            <!-- Footer -->
            <div class="forgot-footer">
                <p>&copy; {{ date('Y') }} Sekolah Bunda Kasih Sudiang<br>
                <small>TK • SD • SMP • SMA</small></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission loading state
        document.getElementById('forgotForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</body>
</html>
