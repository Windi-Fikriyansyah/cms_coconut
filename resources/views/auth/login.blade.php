<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KelasSatu</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #254768 0%, #1a3651 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            margin: 20px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            background: #f7f7f7;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Logo + teks samping kiri */
        .logo-side {
            flex: 1;
            background: linear-gradient(135deg, #3a6ea5 0%, #5a8dee 100%);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            text-align: center;
            color: white;
            padding: 40px 20px 20px;
        }

        .logo-side img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .logo-side h2 {
            font-size: 28px;
            font-weight: bold;
        }

        .logo-side p {
            margin-top: 10px;
            font-size: 16px;
            opacity: 0.9;
        }

        .header {
            text-align: center;
            padding: 15px 20px 10px 20px;
            /* atas lebih kecil */
            margin-bottom: 0;
        }

        .toast {
            visibility: hidden;
            min-width: 280px;
            background-color: #333;
            color: #fff;
            text-align: left;
            border-radius: 12px;
            padding: 16px 20px;
            position: fixed;
            z-index: 9999;
            top: 20px;
            /* pojok atas */
            right: 20px;
            /* geser ke kanan */
            font-size: 15px;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s, transform 0.5s;
        }

        .toast.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .toast.success {
            background: #28a745;
        }

        .toast.error {
            background: #dc3545;
        }

        .toast.info {
            background: #17a2b8;
        }

        .header .logo-img {
            width: 140px;
            height: auto;
            margin-bottom: 10px;
        }


        .header p {
            color: #666;
            font-size: 16px;
        }

        .form-container {
            padding: 10px 20px 20px 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #254768;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #eb631d;
            box-shadow: 0 0 0 3px rgba(235, 99, 29, 0.1);
            transform: translateY(-2px);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #eb631d, #ff7a35);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 25px;
            box-shadow: 0 6px 20px rgba(235, 99, 29, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(235, 99, 29, 0.4);
            background: linear-gradient(135deg, #d55419, #eb631d);
        }

        .forgot-password {
            text-align: center;
            margin-top: 5px;
        }

        .forgot-password a {
            color: #eb631d;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #d55419;
            text-decoration: underline;
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 15px 0;
            position: relative;
            color: #666;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
            z-index: 1;
        }

        .divider span {
            background: #f7f7f7;
            padding: 0 20px;
            position: relative;
            z-index: 2;
            font-size: 14px;
        }

        /* Tombol daftar */
        .register-section {
            text-align: center;
            margin-top: 20px;
        }

        .register-section a {
            display: inline-block;
            padding: 14px 20px;
            border-radius: 12px;
            background: white;
            border: 2px solid #254768;
            font-size: 16px;
            font-weight: 600;
            color: #254768;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .register-section a:hover {
            background: #254768;
            color: white;
        }


        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
            }

            .logo-side {
                min-height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Sisi Form Login -->
        <div class="login-container">
            <div class="header">

                <p>Silakan login untuk melanjutkan</p>
            </div>

            <div class="form-container">
                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" required placeholder="Masukkan email">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password">
                    </div>

                    <button type="submit" class="login-btn">Masuk</button>
                </form>





            </div>
        </div>

        <!-- Sisi Logo + Judul -->
        <div class="logo-side">
            <!-- Lottie container -->
            <div id="lottie-logo" style="width:280px; height:280px; margin-bottom:20px;"></div>

            <h2>CMS COCONUT</h2>
        </div>
    </div>

    <div id="toast" class="toast"></div>
    <script>
        // Lottie animation
        lottie.loadAnimation({
            container: document.getElementById('lottie-logo'), // container div
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://assets9.lottiefiles.com/packages/lf20_qp1q7mct.json' // animasi e-course
        });

        function showToast(message, type = "info") {
            const toast = document.getElementById("toast");
            toast.className = "toast show " + type;
            toast.innerText = message;

            setTimeout(() => {
                toast.className = toast.className.replace("show", "");
            }, 4000);
        }
    </script>

    <script>
        @if (session('success'))
            showToast("{{ session('success') }}", "success");
        @endif

        @if ($errors->any())
            showToast("Email atau password salah. Silakan coba lagi.", "error");
        @endif
    </script>


</body>

</html>
