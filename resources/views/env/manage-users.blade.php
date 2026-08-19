@extends('layouts.env.navigation')

@section('main')

@if ($errors->any())
<div class="alert alert-danger mb-4">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="env-page-header">
    <div>
        <span class="env-page-tag">Gestión de equipo</span>
        <h1 class="env-page-title">Usuarios</h1>
    </div>
    <span class="env-badge env-badge-red">
        <i class="fa-solid fa-user-tie"></i> Usuarios encontrados: {{ count($usersInfo) ?? 0 }}
    </span>
</div>

<div id="editUsers">
    <div class="env-filter-bar">
        <div class="dropdown">
            <button class="env-btn env-btn-primary dropdown-toggle" type="button" id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-filter"></i> Filtros de búsqueda
            </button>
            <ul class="dropdown-menu p-3" aria-labelledby="filtersDropdown" onclick="event.stopPropagation()" style="min-width: 280px;">
                <form action="{{ route('env.users') }}" method="GET" id="filtersForm">
                    <div class="env-filter-group mb-3" style="width: 100%;">
                        <label for="search-user" class="env-filter-label">Buscar por nombre de usuario</label>
                        <input type="text" class="form-control env-filter-input" id="search-user" name="search_user" placeholder="Nombre de usuario" value="{{ request('search_user') }}" style="width: 100%;">
                    </div>

                    <div class="env-filter-group mb-3" style="width: 100%;">
                        <label for="search-role" class="env-filter-label">Filtrar por perfil</label>
                        <select class="form-select env-filter-input" id="search-role" name="search_role" style="width: 100%;">
                            <option value="">Todos los perfiles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ request('search_role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="env-filter-group mb-3" style="width: 100%;">
                        <label for="search-month" class="env-filter-label">Con horas adicionales en</label>
                        <select class="form-select env-filter-input" id="search-month" name="search_month" style="width: 100%;">
                            <option value="" selected>Todos los meses</option>
                            {{!! getListMonthsOptions('search_month') !!}}
                        </select>
                    </div>

                    <div class="d-flex flex-row gap-2">
                        <button type="submit" class="env-btn env-btn-primary btn-sm w-100 btn-loader" title="Aplicar filtros"><i class="fa-solid fa-check"></i></button>
                        <a type="submit" href="{{ route('env.users') }}" class="env-btn env-btn-ghost btn-sm w-100 btn-loader" title="Borrar filtros"><i class="fa-solid fa-x"></i></a>
                    </div>
                </form>
            </ul>
        </div>
    </div>

    @if (count($usersInfo) > 0)
    <div class="row row-cols-1 row-cols-lg-2 g-3">
        @foreach ($usersInfo as $userInfo)
        <div class="col">
            <div class="user-card-horizontal h-100">
                <div class="d-flex flex-row justify-content-between">
                    <section class="user-card-avatar me-3 text-center px-3">
                        @if ($userInfo->data_user && $userInfo->data_user->url_img)
                        <img class="img-profile rounded-circle" src="{{ asset('img/img_perfil/'.$userInfo->data_user->id_user.'/'.$userInfo->userInfdata_user->url_img) }}"/>
                        @else
                        <i class="fa-solid fa-{{ getIconRole($userInfo->role_id) }} fa-2xl"></i>
                        @endif
                    </section>

                    <div class="d-flex justify-content-between w-100">
                        <!-- Información principal -->
                        <section class="user-card-info d-flex flex-column">
                            <h4 class="user-card-name mb-1">{{ properNouns($userInfo->name, $userInfo->last_name) }}</h4>
                            <p class="user-card-role mb-0">{{ $userInfo->role->name }}</p>
                            <p class="user-card-email mb-0">{{ $userInfo->email }}</p>

                            @if ($userInfo->dataUser)
                            <p class="user-card-contract mb-0">{{ $userInfo->dataUser->contract }}</p>
                            @endif
                        </section>

                        <!-- Acciones -->
                        <section class="user-card-actions d-flex flex-column align-items-end">
                            @if (count($userInfo->userHours) > 0 && $userInfo->userHours[0]->total_over_times > 0)
                                <small class="border-0 bg-transparent mb-2 font-italic text-muted">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ number_format($userInfo->userHours[0]->total_over_times, 1) }} hrs adicionales para {{ getCurrentTextMonth(DateTime::createFromFormat('!m', $userInfo->userHours[0]->number_month)->format('F')) }}
                                </small>
                            @endif

                            @if ($userInfo->id != $user->id)
                            <form action="{{ route('user.update', $userInfo->id) }}" method="POST" class="mb-2">
                                @method('PUT')
                                @csrf
                                @php $isActive = $userInfo->is_active; @endphp
                                <button type="submit" class="btn {{ $isActive ? 'btn-danger' : 'btn-primary' }} btn-sm"
                                    name="user_new_status" title="{{ $isActive ? 'Desactivar usuario' : 'Activar usuario' }}"
                                    onclick="return confirm('{{ $isActive ? '¿Desactivar usuario?' : '¿Activar usuario?' }}')"
                                    value="{{ $isActive ? '0' : '1' }}">
                                    {{ $isActive ? 'Desactivar' : 'Activar' }} usuario
                                </button>
                            </form>
                            @endif

                            @if ($userInfo->is_active && $userInfo->dataUser)
                            <div class="dropdown">
                                <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-business-time me-1"></i>Gestionar horas
                                </a>

                                <ul class="dropdown-menu p-2" onclick="event.stopPropagation()">
                                    <form class="mt-1" action="{{ route('hours.store') }}" method="POST" id="overtimes-{{$userInfo->id}}">
                                        @csrf
                                        <div class="form-title-hours">Gestionar horas</div>
                                        <input type="number" name="user_id" value="{{ $userInfo->id }}" required hidden>
                                        <select class="form-select form-select-sm mb-2" name="number_month" required>
                                            {{!! getListMonthsOptions() !!}}
                                        </select>
                                        <input type="number" class="form-control form-control-sm mb-2" name="year" placeholder="Ej: 2024" value="{{ getCurrentYear() }}" required>
                                        <input type="number" class="form-control form-control-sm mb-2" name="overtime_hours" placeholder="Horas adicionales" step="0.1">
                                        <input type="number" class="form-control form-control-sm mb-2" name="month_hours" placeholder="Horas mes" step="0.1" value="{{count($userInfo->userHours) > 0 ? $userInfo->userHours[0]->hours_per_month : ''}}">
                                        <input type="submit" class="btn btn-success btn-sm w-100 border-0" value="Guardar">
                                    </form>
                                </ul>
                            </div>
                            @endif
                        </section>
                    </div>
                </div>

                @if ($userInfo->dataUser)
                <div class="user-card-extra mt-2 small">
                    <span class="border-0 bg-transparent"><i class="fas fa-briefcase-medical me-2"></i>EPS: {{ $userInfo->dataUser->eps }}</span>
                    <span class="border-0 bg-transparent"><i class="fas fa-piggy-bank me-2"></i>AFP: {{ $userInfo->dataUser->afp }}</span>
                    <span class="border-0 bg-transparent"><i class="fas fa-shield-alt"></i>ARL: {{ $userInfo->dataUser->arl }}</span>
                    <span class="border-0 bg-transparent"><i class="fas fa-people-arrows"></i>CAJA: {{ $userInfo->dataUser->caja }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="no-results-screen">
        <div class="no-results-content text-center">
            <i class="fa-solid fa-users-slash fa-3x mb-4"></i>
            <h2 class="no-results-title">Aquí aparecerán los usuarios registrados</h2>
            <p class="no-results-message">No hay registros de usuarios en este momento. Cuando se añadan usuarios, podrá verlos aquí.</p>
            <button onclick="location.reload();" class="btn btn-primary mt-3">Recargar</button>
        </div>
    </div>
    @endif
</div>
@endsection