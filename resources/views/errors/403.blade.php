<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | SMKS Bunda Kasih Sudiang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #F59E0B;
            --secondary-color: #D97706;
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
        
        .shield-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
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
            margin: 0.5rem;
        }
        
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: var(--secondary-color);
        }
        
        .btn-back {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-back:hover {
            background: white;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="shield-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="error-code">403</div>
        <p class="error-message">
            <i class="fas fa-lock me-2"></i>
            Akses Ditolak
        </p>
        <p class="mb-4 opacity-75">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Silakan hubungi administrator jika Anda memerlukan akses.
        </p>
        <div>
            <a href="javascript:history.back()" class="btn-home btn-back">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <a href="{{ url('/') }}" class="btn-home">
                <i class="fas fa-home"></i>
                Beranda
            </a>
        </div>
    </div>
</body>
</html>
