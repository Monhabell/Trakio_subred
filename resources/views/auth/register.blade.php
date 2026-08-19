<!doctype html>
<html lang="es">

<head>
  <title>Trakio - Registro</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="{{ asset('img/logo_temp.png') }}" />

  @vite([
    'resources/css/auth/register.css'
  ])
  <!-- Bootstrap CSS v5.2.0-beta1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2756377313199849"
    crossorigin="anonymous"></script>
</head>

<body class="center-items relative h-100">
  <div class="hero">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
      </div>
    @endif
    <div class="wrapper">
      <div class="logo">
        <img src="{{ asset('img/LogoTrakioRounded.png') }}" alt="">
      </div>
      <div class="text-center mt-4 name">
        Registro Trakio
      </div>

      <form method="POST" id="form-reg" class="relative w-100" action="{{ route('register.store') }}">
        @csrf

        <div id="container-fills" class="inset">
          <div id="section-1">
            <div class="align-items-center">
              <label for="subnet">Subred<span class="req"> *</span></label>
              <select id="subnet" name="subnet" class="form-field">
                <option value="">Seleccione</option>
                <option value="1">Norte</option>
                <option value="2">Centro oriente</option>
                <option value="3">Sur occidente</option>
                <option value="4">Sur</option>
              </select>
            </div>

            <div class="align-items-center">
              <label for="environment">Entorno / Proceso<span class="req"> *</span></label>
              <select id="environment" name="environment" value="{{ old('environment') }}" class="form-field">
                @foreach ($environments as $environment)
                    <option value="{{ $environment->id }}">{{ $environment->entorno }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div id="section-2" hidden>
            <div class="align-items-center">
              <label for="position">Perfil <small>(especificado en el contrato)</small><span class="req"> *</span></label>
              <select id="role_id" name="role_id" value="{{ old('position') }}" class="form-field">
                <option value="">Seleccione</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="email">Email<span class="req"> *</span></label>
              <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="off" class="form-field">
            </div>
          </div>

          <div id="section-3" class="relative" hidden>
            <div class="flex-row flex-between">
              <div class="box-name" id="box-name">
                <label for="name">Nombre(s) completo(s)<span class="req"> *</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="off" class="form-field">
              </div>
            </div>

            <div class="mt-3">
              <label for="last_name">Apellidos completos<span class="req"> *</span></label>
              <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" autocomplete="off" class="form-field">
            </div>
          </div>

          <div id="section-4" hidden>
            <div>
              <label for="password">Contraseña <small style="font-size: 10px;">(Mínimo 8
                  caracteres)</small></label>
              <input type="password" name="password" id="password" autocomplete="off" class="form-field">
              <span id="res-pass1"></span>
            </div>

            <div class="mt-3">
              <label for="password_confirmation">Confirme la contraseña</label>
              <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="off"
                class="form-field">
              <span id="res-pass2" class="text-warning"></span>
            </div>

            <div>
              <div class="form-field mb-3 d-flex align-items-center">
        
                {!! NoCaptcha::renderJs() !!}
                {!! NoCaptcha::display() !!}

                @if ($errors->has('g-recaptcha-response'))
                    <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                @endif

              </div>
            </div>
          </div>
        </div>

        <div class="containerBtn flex-row flex-between">
          <div class="relative box-icon-back">
            <button name="btn_next" id="btn_back" class="btn btn-back btn-secondary border-0" hidden><svg
                xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="18"
                height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M15 6l-6 6l6 6"></path>
              </svg>Atrás</button>
          </div>

          <div class="relative box-icon-next">
            <button name="btn_next" id="btn_next" class="btn btn-next btn-primary border-0">Siguiente<svg
                xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="18"
                height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 6l6 6l-6 6"></path>
              </svg></button>
              
            <button type="submit" id="register_btn" name="register_btn" class="btn btn-success"
              hidden>Finalizar</button>
          </div>
        </div>
      </form>
      <div class="text-center fs-6">
        <a class="text-danger" href="{{ route('login.form') }}">Ya tengo una cuenta</a>
      </div>
      <div class="cube"></div>
      <div class="cube"></div>
      <div class="cube"></div>
      <div class="cube"></div>
      <div class="cube"></div>
      <div class="cube"></div>
    </div>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
      integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
      integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
      integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('js/noResend.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/register.js') }}?0.2"></script>
</body>

</html>