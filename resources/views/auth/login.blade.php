@extends('index')

@section('main')
    <form class="p-3 mt-3" method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Campo de correo electrónico --}}
        <small class="text-danger d-block">
            Ingresa el mismo correo que usaste al registrarte.
        </small>
        <div class="form-field d-flex align-items-center mb-3">
            <span class="icon fa fa-user"></span>
            <input 
                type="email" 
                name="email" 
                id="email" 
                placeholder="Escribe tu correo completo (ejemplo: usuario@correo.com)" 
                required
                autofocus
            >
        </div>

        {{-- Campo de contraseña --}}
        <div class="form-field d-flex align-items-center mb-3">
            <span class="icon fa fa-key"></span>
            <input 
                id="inputPass" 
                type="password" 
                name="password" 
                placeholder="Escribe tu contraseña" 
                autocomplete="off" 
                required
            >
            <span id="viewPassIcon" class="fa fa-solid fa-eye"></span>
        </div>
        

        {{-- reCAPTCHA --}}
        <div class="my-3">
            {!! NoCaptcha::renderJs() !!}
            {!! NoCaptcha::display() !!}
        </div>

        {{-- Mensajes de error --}}
        @error('message')
            <div class="red-alert mb-3 px-3">
                <h3 class="text-alert my-1" role="alert">{{ $message }}</h3>
            </div>
        @enderror

        {{-- Botón de envío --}}
        <div class="w-100">
            <button type="submit" name="ingresar" id="ingresar"
                class="btn btn-primary w-100 rounded-pill btn-loader">
                Iniciar sesión
            </button>
        </div>
    </form>

    {{-- Enlaces inferiores --}}
    <div class="text-center fs-6 d-flex justify-content-between px-4 mt-3">
        <a class="text-danger" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    </div>

    <div class="bg-register-section mt-3 mt-md-5 rounded py-2 py-md-3 px-2 px-md-4 mx-3">
        <small>¿No tienes una cuenta?</small>
        <a class="ms-2 ms-0" href="{{ route('register') }}">Registrarse ahora</a>
    </div>
@endsection
