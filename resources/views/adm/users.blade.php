@extends('layouts.adm.navigation')

@section('main')

<div class="card shadow mb-4 pt-0 border-0">
    <div class="card-header row align-items-center justify-content-between bg-dark gap-2">
        <form class="d-flex flex-row col-12 col-md-8 col-lg-9 px-0" action="{{ route('user.show') }}">
            <div class="navbar-search w-100 w-md-75">
                <div class="input-group pb-0">
                    <input type="search" class="form-control form-control-sm" name="search_user" placeholder="Buscar usuario"
                        aria-label="Search" aria-describedby="basic-addon2" value="{{ request('search_user') }}">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-primary btn-loader" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="navbar-search d-none d-sm-block w-25 ms-md-2">
                <div class="input-group">
                    <select class="form-select form-select-sm" name="search_environment" id="search_environment"
                        aria-label="Search" aria-describedby="basic-addon2" value="{{ request('search_environment') }}">
                        <option value="">Seleccionar entorno...</option>
                        @foreach ($environments as $environment)
                        <option value={{ $environment->id }}
                            {{ request('search_environment') == $environment->id ? 'selected' : '' }}>
                            {{ $environment->entorno }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="col-12 col-md-3 col-lg-2 dropdown d-flex justify-content-end px-0">
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary w-100" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-download fa-sm text-white-50"></i> Generar reporte
            </a>

            <!-- Contenido del popover -->
            <ul class="dropdown-menu p-2 right-0 bg-dark">
                <form action="{{ route('user.export') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <select class="form-select form-select-sm" name="environment_id" id="environment_id"
                            aria-label="Search" aria-describedby="basic-addon2" value="{{ request('search_environment') }}">
                            <option value="">Todos</option>
                            @foreach ($environments as $environment)
                            <option value={{ $environment->id }}>
                                {{ $environment->entorno }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success w-100">Descargar</button>
                </form>
            </ul>
        </div>
    </div>

    <div class="card-body py-0 px-1 bg-dark rounded-0" id="users-container">
        <div class="scrollable-container box-users">
            @foreach ($users as $userData)
            <form action="{{ route('user.update', ['user' => $userData->id]) }}" id="form-update-user-{{ $userData->id }}"
                class="user-card p-0" method="POST">
                @method('PUT')
                @csrf
                <div class="card-inner pb-3" id="card-inner-{{ $userData->id }}">
                    <div class="card-front">
                        <div class="ds-top"></div>
                        <div class="avatar-holder">
                        @if (!empty($userData->dataUser) && file_exists(public_path('img/img_perfil/'.$userData->id.'/'.$userData->dataUser->url_img)))
                            <img src="{{ asset('img/img_perfil/'.$userData->id.'/'.$userData->dataUser->url_img) }}" alt="Foto de perfil">
                        @else
                            <img src="{{ asset('img/undraw_profile_2.svg') }}" alt="Imagen de perfil predeterminada">
                        @endif
                        </div>

                        <div class="name mt-1 mt-md-5 d-flex flex-column">
                            <a {{ $userData->entorno->id == 0 ? 'class=user-details' : '' }}
                                data-user-id = "{{ $userData->id }}">{{ properNouns($userData->name,
                                $userData->last_name) }}</a>
                            <small class="text-secondary">{{ $userData->email }}</small>
                        </div>

                        <div class="ds-info">
                            <div class="ds pens flex-column">
                                <h6>{{ $userData->entorno->entorno }}</h6>
                                <span class="text-secondary">{{$userData->role->name}}</span>
                            </div>
                        </div>

                        <div class="flex-center">
                            @if ($user->id != $userData->id)
                            @if ($userData->is_active)
                            <button type="submit" class="btn btn-sm btn-primary" name="user_new_status"
                                title="Desactivar usuario" onclick="return confirm('¿Desactivar usuario?')"
                                value="0">
                                Activo
                            </button>
                            @else
                            <button type="submit" class="btn btn-sm btn-dark" name="user_new_status"
                                title="Activar usuario" onclick="return confirm('¿Activar usuario?')" value="1">
                                Inactivo
                            </button>
                            @endif
                            @endif
                        </div>
                    </div>

                    @if (!empty($userData->dataUser))
                    <div class="card-back h-100 d-flex justify-content-center">
                        <div class="ds-top d-flex flex-center justify-content-between w-100 px-4">
                            <div>
                                <h5 class="text-white fs-6 fw-bold mb-0">CC {{ $userData->dataUser->document }}</h5>
                                <small class="text-white">{{ $userData->dataUser->contract }}</small>
                            </div>
                            <div class="btn btn-light user-details" data-user-id="{{ $userData->id }}">
                                <a class="badge text-bg-light">
                                    <i class="fa-solid fa-angles-left"></i>
                                </a>
                            </div>
                        </div>

                        <div class="position-absolute position-absolute top-50 start-50 translate-middle">
                            <div class="bg-white rounded-5 position-relative ps-2 flex-column pe-3 mb-2">
                                <div class="circle-icon-card">
                                    <i class="fa-solid fa-phone fa-2xs" style="color: #ffffff;"></i>
                                </div>
                                <span class="ms-4">{{ $userData->dataUser->phone }}</span>
                            </div>

                            <div class="bg-white rounded-5 position-relative ps-2 flex-column pe-3 mb-2">
                                <div class="circle-icon-card">
                                    <i class="fa-solid fa-droplet fa-2xs" style="color: #ffffff;"></i>
                                </div>
                                <span class="ms-4">{{ $userData->dataUser->rh }}</span>
                            </div>

                            <div class="bg-white rounded-5 position-relative ps-2 flex-column pe-3 mb-2">
                                <div class="circle-icon-card">
                                    <i class="fa-solid fa-cake-candles fa-2xs" style="color: #ffffff;"></i>
                                </div>
                                <span class="ms-4">{{
                                    \Carbon\Carbon::parse($userData->dataUser->birthdate)->format('d-m-Y') }}</span>
                            </div>

                            <div class="bg-light rounded-5 position-relative ps-2 flex-column">
                                <div class="circle-icon-card">
                                    <i class="fa-solid fa-tag fa-2xs" style="color: #ffffff;"></i>
                                </div>
                                <select class="ps-3 w-100 rounded-pill env-select-user" name="environment_assign" data-user-id="{{ $userData->id }}">
                                    <option value="">Sin entorno</option>
                                    @foreach ($environments as $environment)
                                        @if ($environment->id != 0)
                                        <option value="{{ $environment->id }}" {{ $environment->id == $userData->dataUser->environment_assignment_id ? 'selected' : '' }}>
                                            {{ $environment->entorno }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-white rounded-5 position-relative ps-2 flex-column pe-3 mb-2 d-flex align-items-center">
                            <div class="circle-icon-card">
                                <i class="fa-solid fa-at fa-2xs" style="color: #ffffff;"></i>
                            </div>
                            <input type="text" class="ms-4 border-0 flex-grow-1 username-input-user" name="username"
                                data-user-id="{{ $userData->id }}" placeholder="usuario (ej: dcgaray)"
                                value="{{ $userData->username }}">
                            <button type="button" class="btn btn-sm btn-link p-0 ms-1 save-username-user"
                                data-user-id="{{ $userData->id }}" title="Guardar usuario">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </div>

                        <div class="badge w-100 card-back-footer text-wrap text-white" style="width: 6rem;">
                            <h5 class="">SGSS</h5>
                            <span class="text-white">{{ $userData->dataUser->eps }}</span>
                            <span class="text-white">{{ $userData->dataUser->afp }}</span>
                            <span class="text-white">{{ $userData->dataUser->arl }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </form>
            @endforeach
        </div>

        @if (count($users) > 0)
            <div class="d-flex flex-column justify-content-center mt-4">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection