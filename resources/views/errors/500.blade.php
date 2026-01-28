<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | SMKS Bunda Kasih Sudiang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #DC2626;
            --secondary-color: #991B1B;
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
            animation: shake 2s ease-in-out infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
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
        
        .gear {
            font-size: 5rem;
            animation: spin 4s linear infinite;
            margin-bottom: 1rem;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="gear"><i class="fas fa-cog"></i></div>
        <div class="error-code">500</div>
        <p class="error-message">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Terjadi Kesalahan Server
        </p>
        <p class="mb-4 opacity-75">
            Tim teknis kami sedang bekerja untuk memperbaiki masalah ini.<br>
            Silakan coba beberapa saat lagi.
        </p>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fas fa-home"></i>
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
