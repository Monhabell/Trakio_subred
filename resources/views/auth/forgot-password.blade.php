@extends('index')

@section('main')
        <div class="text-center mt-5 name mb-4">
            Recuperar contraseña
        </div>

        <x-auth-session-status class="mb-4 text-center text-light" :status="session('status')" />

        @if (!session('status'))
            <div class="text-center text-light">
                <p>Ingrese su correo electrónico y recibirá un enlace para cambiar la contraseña</p>
            </div>

            <form class="p-3 mt-3" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-field d-flex align-items-center">
                    {{-- <span class="fa fa-user"></span> --}}
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus>
                </div>

                
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-center text-light" />
                <div class="form-group text-center mt-3">
                    <button type="submit" class="btn btn-primary w-100 btn-loader">
                        Enviar
                    </button>
                </div>
            </form>
        @endif      
        @endsection