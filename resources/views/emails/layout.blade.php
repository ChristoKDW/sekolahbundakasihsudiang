<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notifikasi Pembayaran' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
        }
        .info-value {
            font-weight: 600;
            color: #333;
        }
        .amount {
            font-size: 24px;
            color: #4F46E5;
            font-weight: 700;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .footer a {
            color: #4F46E5;
            text-decoration: none;
        }
        .status-success {
            color: #10B981;
        }
        .status-pending {
            color: #F59E0B;
        }
        .status-danger {
            color: #EF4444;
        }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <div class="container">
            <div class="header">
                <h1>SMKS Bunda Kasih Sudiang</h1>
                <p>Sistem Pembayaran Digital</p>
            </div>
            
            <div class="content">
                {{ $slot }}
            </div>
            
            <div class="footer">
                <p>Email ini dikirim secara otomatis oleh sistem.</p>
                <p>Jika Anda memiliki pertanyaan, silakan hubungi kami di <a href="mailto:info@smksbundakasih.sch.id">info@smksbundakasih.sch.id</a></p>
                <p>&copy; {{ date('Y') }} SMKS Bunda Kasih Sudiang. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
