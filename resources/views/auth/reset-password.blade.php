<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sekolah Bunda Kasih</title>
    
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

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        .reset-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .reset-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .reset-header {
            background: linear-gradient(135deg, var(--secondary) 0%, #059669 100%);
            padding: 50px 40px;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .reset-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        }

        .reset-header-content {
            position: relative;
            z-index: 1;
        }

        .reset-icon {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            backdrop-filter: blur(10px);
        }

        .reset-icon i {
            font-size: 2.5rem;
            color: #fff;
        }

        .reset-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .reset-header p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .reset-body {
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

        .form-floating-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating-custom label {
            display: block;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-floating-custom .input-wrapper {
            position: relative;
        }

        .form-floating-custom .form-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 1.1rem;
            z-index: 10;
        }

        .form-floating-custom input {
            width: 100%;
            padding: 16px 18px 16px 55px;
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

        .form-floating-custom input::placeholder {
            color: #9CA3AF;
        }

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

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #E5E7EB;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak { width: 33%; background: #EF4444; }
        .password-strength-bar.medium { width: 66%; background: #F59E0B; }
        .password-strength-bar.strong { width: 100%; background: #10B981; }

        .password-hint {
            font-size: 0.8rem;
            color: #6B7280;
            margin-top: 5px;
        }

        .btn-reset {
            width: 100%;
            padding: 16px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--secondary), #059669);
            border: none;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }

        .reset-footer {
            text-align: center;
            padding: 20px 40px 30px;
            background: #F9FAFB;
            border-top: 1px solid #E5E7EB;
        }

        .reset-footer p {
            color: #9CA3AF;
            font-size: 0.85rem;
            margin: 0;
        }

        @media (max-width: 575.98px) {
            .reset-header {
                padding: 40px 25px;
            }

            .reset-body {
                padding: 30px 25px;
            }

            .reset-header h1 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="reset-wrapper">
        <div class="reset-card">
            <div class="reset-header">
                <div class="reset-header-content">
                    <div class="reset-icon">
                        <i class="fas fa-lock-open"></i>
                    </div>
                    <h1>Reset Password</h1>
                    <p>Buat password baru untuk akun Anda</p>
                </div>
            </div>

            <div class="reset-body">
                @if($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                    <div>
                        @foreach($errors->all() as $error)
                        <span>{{ $error }}</span><br>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-floating-custom">
                        <label>Alamat Email</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   name="email" 
                                   placeholder="Masukkan email Anda"
                                   value="{{ old('email', request('email')) }}"
                                   required>
                            <i class="fas fa-envelope form-icon"></i>
                        </div>
                    </div>

                    <div class="form-floating-custom">
                        <label>Password Baru</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password"
                                   name="password" 
                                   placeholder="Minimal 8 karakter"
                                   required
                                   minlength="8">
                            <i class="fas fa-lock form-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <p class="password-hint">Gunakan kombinasi huruf besar, kecil, angka, dan simbol</p>
                    </div>

                    <div class="form-floating-custom">
                        <label>Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   id="password_confirmation"
                                   name="password_confirmation" 
                                   placeholder="Ulangi password baru"
                                   required>
                            <i class="fas fa-lock form-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-reset">
                        <i class="fas fa-check-circle"></i>
                        Reset Password
                    </button>
                </form>
            </div>

            <div class="reset-footer">
                <p>&copy; {{ date('Y') }} Sekolah Bunda Kasih Sudiang<br>
                <small>TK • SD • SMP • SMA</small></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bar = document.getElementById('strengthBar');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            bar.className = 'password-strength-bar';
            if (strength >= 3) {
                bar.classList.add('strong');
            } else if (strength >= 2) {
                bar.classList.add('medium');
            } else if (strength >= 1) {
                bar.classList.add('weak');
            }
        });
    </script>
</body>
</html>
