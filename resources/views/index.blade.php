<!DOCTYPE html>
<html lang="en">

<head>
    <title>Trakio</title>
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
    <!-- Bootstrap CSS v5.2.0-beta1 -->
    @vite([
        'resources/css/auth/inx_new.css' 
    ])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2756377313199849"
        crossorigin="anonymous"></script>
</head>

<body>   
    <div class="hero">
        <div class="wrapper position-absolute z-2">
            <div class="d-flex justify-content-between">
                <div class="logo w-50">
                    <img src="{{ asset('img/LogoTrakioRounded.png') }}" alt="">
                </div>
                <div class="text-center name">
                    <h1>Trakio</h1>
                </div>
            </div>
            <p class="auth-subtitle">Gestión inteligente de productividad</p>
            @yield('main')
        </div>
        <canvas class= "position-absolute top-0 z-1 " id="canvas-bg"></canvas>
        @if (session('success'))
            <div class="alert-success w-25">
                {{ session('success') }}
            </div>
            @endif
            @if (session('status'))
            <div class="alert-success w-25">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('g-recaptcha-response'))
            <div class="alert-capcha">
                <h3 class="fs-6" role="alert">{{ $errors->first('g-recaptcha-response') }}</h3>
            </div>
        @endif
    </div>

    @vite([
        'resources/js/index/main-index.js'
    ])
</body>

</html>