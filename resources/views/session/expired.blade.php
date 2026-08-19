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
        content="Gestión y administración integral del proceso GESI, para la Subred Integrada de Servicios de Salud Norte">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset('img/logo_temp.png') }}">
    <!-- Bootstrap CSS v5.2.0-beta1 -->
    <link rel="stylesheet" href="{{ asset('styles/inx_new.css') }}?0.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2756377313199849"
        crossorigin="anonymous"></script>
</head>

<body>
    <div class="hero">
        <div class="wrapper">
            <div class="logo">
                <img src="{{ asset('img/LogoTrakioRounded.png') }}" alt="">
            </div>
            <div class="text-center mt-4 name">
                GesiApp
            </div>

            <div class="alert alert-danger mt-3" role="alert">
                <span>Sesión cerrada, por favor inicie sesión nuevamente o regístrese</span>
            </div>
           
            <div class="text-center flex-column fs-6">
                <a href="{{ route('index') }}" class="btn btn-primary rounded-pill w-100 mt-2">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-info rounded-pill w-100 mt-4">Registrarse</a>
            </div>
           
        </div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
    </div>

</body>
</html>
