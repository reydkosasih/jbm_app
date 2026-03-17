<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Kesalahan Server</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 24px;
        }

        .error-card {
            max-width: 500px;
            width: 100%;
        }

        .error-icon {
            font-size: 80px;
            color: #ffc107;
            margin-bottom: 24px;
        }

        .error-code {
            font-size: 96px;
            font-weight: 800;
            color: #ffc107;
            line-height: 1;
            margin-bottom: 16px;
            text-shadow: 0 0 30px rgba(255, 193, 7, .4);
        }

        .error-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .error-desc {
            font-size: 15px;
            color: #a1a5b7;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .btn-home {
            display: inline-block;
            background: #009ef7;
            color: white;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
        }

        .btn-home:hover {
            background: #0086d3;
            color: white;
        }

        .btn-reload {
            display: inline-block;
            color: #a1a5b7;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 14px;
            margin-left: 8px;
            cursor: pointer;
            background: none;
            border: none;
        }

        .btn-reload:hover {
            color: white;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="error-code">500</div>
        <div class="error-title">Terjadi Kesalahan Server</div>
        <div class="error-desc">
            Server mengalami masalah dan tidak dapat memproses permintaan Anda saat ini.<br>
            Tim kami sedang menangani masalah ini. Silakan coba beberapa saat lagi.
        </div>
        <div>
            <a href="<?= base_url() ?>" class="btn-home"><i class="fa-solid fa-house me-2"></i>Ke Beranda</a>
            <button onclick="location.reload()" class="btn-reload"><i class="fa-solid fa-rotate-right me-1"></i>Coba Lagi</button>
        </div>
    </div>
</body>

</html>