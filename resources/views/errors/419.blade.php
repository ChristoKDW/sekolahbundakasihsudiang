<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired | SMKS Bunda Kasih Sudiang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366F1;
            --secondary-color: #4F46E5;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            text-align: center;
            color: white;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 10rem;
            font-weight: 700;
            text-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .clock-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: countdown 1s ease-in-out infinite;
        }
        
        @keyframes countdown {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .btn-home {
            background: white;
            color: var(--primary-color);
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="clock-icon"><i class="fas fa-clock"></i></div>
        <div class="error-code">419</div>
        <p class="error-message">
            <i class="fas fa-hourglass-end me-2"></i>
            Sesi Anda Telah Berakhir
        </p>
        <p class="mb-4 opacity-75">
            Untuk keamanan, sesi Anda telah berakhir karena tidak ada aktivitas.<br>
            Silakan refresh halaman atau login kembali.
        </p>
        <a href="{{ url('/login') }}" class="btn-home">
            <i class="fas fa-sign-in-alt"></i>
            Login Kembali
        </a>
    </div>
</body>
</html>
