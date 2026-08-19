<x-guest-layout>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4 text-center text-light">
            <h3 class="fw-bold">Restablecer contraseña</h3>
            <small>Por favor, ingrese su nueva contraseña.</small>
        </div>

        <!-- Email Address -->
        <div class="form-field">
            <label for="email" class="form-label text-light mb-0">Email</label>

            <div class="form-field d-flex align-items-center mb-3">
                <span class="icon fa fa-user"></span>
                <input readonly id="email" type="email" value="{{ Request('email') }}" name="email" required autofocus autocomplete="username" />
            </div>
            
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
        </div>

        <!-- Password -->
        <div class="form-field mb-3">
            <label for="password" class="form-label text-light">Nueva contraseña</label>

            <div class="form-field d-flex align-items-center mb-3">
                <input id="password" class="form-control bg-secondary text-light border-0" type="password"
                    name="password" required autocomplete="new-password" />
                <span id="viuPasseyes" class="fa fa-eye" onclick="viewPassword()"></span>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
        </div>

        <!-- Confirm Password -->
        <div class="form-field mb-3">
            <label for="password_confirmation" class="form-label text-light">Confirmar contraseña</label>
            <input id="password_confirmation" class="form-control bg-secondary text-light border-0" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <span id="viuPasseyesConfirmation" class="fa fa-eye" onclick="viewPasswordConfirmation()"></span>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-key fa-sm pe-3"></i>Restablecer contraseña
            </button>
        </div>
    </form>
</x-guest-layout>