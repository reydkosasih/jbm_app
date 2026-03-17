<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak</title>
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
            color: #f1416c;
            margin-bottom: 24px;
        }

        .error-code {
            font-size: 96px;
            font-weight: 800;
            color: #f1416c;
            line-height: 1;
            margin-bottom: 16px;
            text-shadow: 0 0 30px rgba(241, 65, 108, .4);
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

        .btn-back {
            display: inline-block;
            color: #a1a5b7;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 14px;
            margin-left: 8px;
        }

        .btn-back:hover {
            color: white;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-icon"><i class="fa-solid fa-lock"></i></div>
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <div class="error-desc">
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Silakan login dengan akun yang sesuai atau hubungi administrator.
        </div>
        <div>
            <a href="<?= base_url('login') ?>" class="btn-home"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</a>
            <a href="javascript:history.back()" class="btn-back"><i class="fa-solid fa-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>
</body>

</html>