<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>TRAKIO - Recuperar contraseña</title>
    <!-- Required meta tags -->
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta name="keywords" lang="es" content="gesi, gesiapp, digitacion">
    <meta name="description"
        content="Trakio.pro es una plataforma de gestión diseñada para estandarizar y mejorar los procesos internos dentro de las subredes, con el objetivo de proporcionar herramientas que faciliten el control del talento humano y la automatización de la gestión de la información.">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset('img/logo_temp.png') }}">
    @vite([
        'resources/css/auth/inx_new.css'
    ])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2756377313199849"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="d-flex justify-content-center align-items-center">
        <div class="hero">
            <div class="wrapper">
                <div class="logo">
                    <img src="{{ asset('img/LogoTrakioRounded.png') }}" alt="">
                </div>
                <div class="text-center mt-4 name">
                    Trakio
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>

    <div class="cube"></div>
    <div class="cube"></div>
    <div class="cube"></div>
    <div class="cube"></div>
    <div class="cube"></div>
    <div class="cube"></div>
</body>

<script>
function viewPassword() {
    const pass = document.getElementById('password');
    const viueyes = document.getElementById('viuPasseyes');
    if (pass.type == "password") {
        pass.type = "text";
        viueyes.classList.remove('fa-eye')
        viueyes.classList.add('fa-eye-slash')
        viueyes.classList.add('active')
    } else {
        pass.type = "password";
        viueyes.classList.remove('active')
        viueyes.classList.remove('fa-eye-slash')
        viueyes.classList.add('fa-eye')
    }
}
</script>

<script>
function viewPasswordConfirmation() {
    const pass = document.getElementById('password_confirmation');
    const viueyes = document.getElementById('viuPasseyesConfirmation');
    if (pass.type == "password") {
        pass.type = "text";
        viueyes.classList.remove('fa-eye')
        viueyes.classList.add('fa-eye-slash')
        viueyes.classList.add('active')
    } else {
        pass.type = "password";
        viueyes.classList.remove('active')
        viueyes.classList.remove('fa-eye-slash')
        viueyes.classList.add('fa-eye')
    }
}
</script>



</html>
