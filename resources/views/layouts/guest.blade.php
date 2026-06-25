<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APMS | Authentication</title>

    <!-- Favicon & PWA -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-512x512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        :root {
            --primary-color: #FF6B35;
            --primary-dark: #E55A2B;
            --secondary-color: #2D3047;
            --accent-color: #FFB394;
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e1e2c 0%, #2a2a3c 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Elements */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c61 100%);
            z-index: -1;
            filter: blur(70px);
            opacity: 0.2;
            animation: move 15s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(80px, 100px) scale(1.1); }
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            z-index: 10;
        }

        .auth-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.15);
            overflow: hidden;
        }
        
        .auth-header {
            padding: 25px 30px 10px;
            text-align: center;
        }

        .auth-logo {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            letter-spacing: -0.5px;
        }

        .auth-subtitle {
            color: #555;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .auth-body {
            padding: 8px 30px 25px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .input-group-apms {
            position: relative;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #d0d0d0;
            transition: all 0.2s ease;
        }

        .input-group-apms:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.12);
        }

        .input-group-apms input {
            background: transparent;
            border: none;
            padding: 10px 12px 10px 38px;
            width: 100%;
            outline: none;
            font-size: 0.85rem;
            color: #222;
        }

        .input-group-apms i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }

        .input-group-apms:focus-within i.fa-envelope,
        .input-group-apms:focus-within i.fa-lock {
            color: var(--primary-color);
        }

        .btn-auth {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 16px;
            width: 100%;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 3px 8px rgba(255, 107, 53, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-auth:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.35);
            color: white;
        }

        .auth-link {
            color: #555;
            font-size: 0.8rem;
            text-decoration: none;
            transition: color 0.2s ease;
            font-weight: 500;
        }

        .auth-link:hover {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        /* Divider */
        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0 20px;
        }
        
        .divider-container::before,
        .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .divider-container span {
            padding: 0 15px;
            color: #888;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Social Buttons */
        .btn-group-social {
            display: flex;
            gap: 12px;
        }
        
        .btn-outline-social {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #e0e0e0;
            color: #444;
            padding: 10px 15px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.01);
        }
        
        .btn-outline-social:hover {
            background: #f8f9fa;
            border-color: #c0c0c0;
            transform: translateY(-1px);
            color: #222;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .auth-footer {
            text-align: center;
            margin-top: 15px;
        }

        /* Success/Error Alerts */
        .alert {
            border-radius: 4px;
            font-size: 0.82rem;
            border: none;
            padding: 8px 12px;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #1b5e20;
        }

        .alert-danger {
            background-color: #fce4ec;
            color: #b71c1c;
        }

        @media (max-width: 480px) {
            .auth-body {
                padding: 10px 25px 35px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-circle" style="top: -100px; left: -100px; width: 400px; height: 400px;"></div>
    <div class="bg-circle" style="bottom: -150px; right: -100px; width: 500px; height: 500px; animation-delay: -5s;"></div>

    <div class="auth-container">
        {{ $slot }}
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Double-submit prevention & loading state
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status"></span> Memproses...';
                }
            });
        });

        // Password visibility toggle
        document.querySelectorAll('.input-group-apms input[type="password"]').forEach(function(input) {
            var wrapper = input.closest('.input-group-apms');
            var toggle = document.createElement('i');
            toggle.className = 'fas fa-eye toggle-password';
            toggle.style.cssText = 'right: 20px; left: auto; cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); color: #adb5bd; z-index: 2;';
            wrapper.appendChild(toggle);
            toggle.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.className = 'fas fa-eye-slash toggle-password';
                    this.style.color = 'var(--primary-color)';
                } else {
                    input.type = 'password';
                    this.className = 'fas fa-eye toggle-password';
                    this.style.color = '#adb5bd';
                }
            });
        });
    </script>
</body>
</html>
