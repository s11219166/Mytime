<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyTime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        :root {
            --primary-color: #FF6B6B;
            --secondary-color: #4ECDC4;
            --accent-color: #45B7D1;
            --gradient: linear-gradient(-45deg, #FF6B6B, #4ECDC4, #45B7D1, #96E6B3);
            --shadow-color: rgba(255, 107, 107, 0.15);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background: #f8f9fa;
            animation: fadeInUp 1s ease-out;
        }

        .split-layout {
            display: flex;
            width: 100%;
        }

        .login-banner {
            flex: 1;
            background: var(--gradient);
            background-size: 400% 400%;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            animation: gradientBG 15s ease infinite;
        }

        .banner-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 500px;
            animation: fadeInUp 1s ease-out 0.5s backwards;
        }

        .banner-content h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shimmer 3s infinite linear;
            background-size: 200% 100%;
        }

        .banner-content p {
            font-size: 1.25rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0;
            animation: fadeInUp 1s ease-out 1s forwards;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        @keyframes particleAnimation {
            0% {
                transform: translate(0, 0) rotate(0deg) scale(1);
                opacity: 0;
            }
            50% {
                transform: translate(var(--tx), var(--ty)) rotate(180deg) scale(1.2);
                opacity: 0.8;
            }
            100% {
                transform: translate(0, 0) rotate(360deg) scale(1);
                opacity: 0;
            }
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0));
            border-radius: 50%;
            backdrop-filter: blur(5px);
            animation: particleAnimation 20s infinite;
            box-shadow: 0 0 20px rgba(255,255,255,0.2);
        }

        .shape::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.3), transparent);
            border-radius: 50%;
            animation: pulse 3s infinite;
        }

        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        @keyframes borderAnimation {
            0% { border-image-source: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); }
            50% { border-image-source: linear-gradient(225deg, var(--secondary-color), var(--accent-color)); }
            100% { border-image-source: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); }
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px var(--shadow-color),
                       0 5px 15px rgba(0,0,0,0.05);
            backdrop-filter: blur(10px);
            border: 3px solid transparent;
            border-image: linear-gradient(45deg, var(--primary-color), var(--secondary-color)) 1;
            animation: borderAnimation 6s infinite, fadeInUp 1s ease-out;
            transform-origin: center;
            transition: var(--transition);
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px var(--shadow-color),
                       0 10px 20px rgba(0,0,0,0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInUp 1s ease-out 0.3s backwards;
        }

        .login-header .logo {
            font-size: 3rem;
            background: var(--gradient);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientBG 8s ease infinite;
            margin-bottom: 1rem;
        }

        .login-header h2 {
            font-size: 1.75rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1rem;
            height: auto;
            font-size: 1rem;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 20px var(--shadow-color);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .form-floating:nth-child(1) .form-control { animation-delay: 0.4s; }
        .form-floating:nth-child(2) .form-control { animation-delay: 0.6s; }

        .form-floating > label {
            padding: 1rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            border-radius: 6px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 12px;
            background: var(--gradient);
            background-size: 300% 300%;
            border: none;
            color: white;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.8s backwards;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0.2) 0%,
                rgba(255, 255, 255, 0.2) 40%,
                rgba(255, 255, 255, 0.6) 50%,
                rgba(255, 255, 255, 0.2) 60%,
                rgba(255, 255, 255, 0.2) 100%
            );
            transform: rotate(45deg);
            transition: 0.8s;
            opacity: 0;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-color);
            animation: gradientBG 3s ease infinite;
        }

        .btn-login:hover::after {
            animation: shine 1.5s ease-out infinite;
        }

        @keyframes shine {
            0% {
                transform: rotate(45deg) translateY(-120%);
                opacity: 0;
            }
            50% {
                transform: rotate(45deg) translateY(120%);
                opacity: 0.3;
            }
            100% {
                transform: rotate(45deg) translateY(120%);
                opacity: 0;
            }
        }

        .role-badges {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .role-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .role-admin {
            background-color: #fee2e2;
            color: #ef4444;
        }

        .role-user {
            background-color: #dcfce7;
            color: #22c55e;
        }

        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
            font-size: 0.875rem;
        }

        @media (max-width: 992px) {
            .login-banner {
                display: none;
            }
        }

        @media (min-width: 992px) {
            .shape:nth-child(1) { top: 20%; left: 20%; width: 60px; height: 60px; }
            .shape:nth-child(2) { top: 60%; left: 70%; width: 100px; height: 100px; }
            .shape:nth-child(3) { top: 40%; left: 40%; width: 80px; height: 80px; }
            .shape:nth-child(4) { top: 80%; left: 30%; width: 40px; height: 40px; }
            .shape:nth-child(5) { top: 10%; left: 60%; width: 70px; height: 70px; }
        }
    </style>
</head>
<body>
        <div class="split-layout">
        <!-- Left side banner -->
        <div class="login-banner">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            <div class="banner-content">
                <h1>MyTime</h1>
                <p>Welcome to MyTime, your comprehensive solution for time tracking and project management. Stay organized, focused, and productive with our intuitive platform.</p>
            </div>
        </div>

        <!-- Right side login form -->
        <div class="login-section">
            <div class="login-container">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h2>Welcome Back</h2>
                    <p class="text-muted">Please sign in to continue</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email address</label>
                    </div>

                    <div class="form-floating">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Keep me signed in</label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        Sign In <i class="fas fa-arrow-right ms-2"></i>
                    </button>

                    @if(Route::has('password.request'))
                        <div class="text-center mt-4">
                            <a href="{{ route('password.request') }}" class="text-decoration-none text-muted">
                                <i class="fas fa-key me-2"></i>Forgot your password?
                            </a>
                        </div>
                    @endif

                    @if(session('intended_role'))
                        <div class="role-badges">
                            <span class="role-badge {{ session('intended_role') === 'admin' ? 'role-admin' : 'role-user' }}">
                                <i class="fas fa-{{ session('intended_role') === 'admin' ? 'shield' : 'user' }}"></i>
                                {{ ucfirst(session('intended_role')) }} Access
                            </span>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
