@extends(Auth::user()->environment_id != 0 && !Auth::user()->is_admin ? 'layouts.members.nav' :
(Auth::user()->environment_id != 0 && Auth::user()->is_admin ? 'layouts.env.navigation' : (Auth::user()->environment_id
== 0 && Auth::user()->is_admin ? 'layouts.adm.navigation' : 'layouts.dig.navigation')))

@section('main')
<section>
    @if ($errors->any())
        <div class="alert alert-danger mb-0">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form id="profile-form" class="profile-container" method="POST" action="{{ route('profile.update', ['user' => $data_user->id ]) }}" enctype="multipart/form-data">
        @csrf
        <div class="franja">
            <div class="profile-image-container">
                @if (!$data_user->url_img)
                <img src="{{ asset('img/icons/noImageRed.jpg') }}" alt="Foto de perfil" id="profile-pic" class="profile-pic">
                @else
                <img src="{{ asset('img/img_perfil/' . $data_user->id_user . '/' . $data_user->url_img) }}" 
                     alt="Foto de perfil" id="profile-pic" class="profile-pic">
                @endif
            
                <input type="file" name="profile_img" id="profile-pic-input" accept="image/*" style="display: none;">
                <label for="profile-pic-input" title="Cambiar imagen" class="btn-icon-upload">
                    <i class="fas fa-camera"></i>
                </label>
            </div>                     
        </div>

        <div class="container-fluid">
            <div id="profile-details">
                <div class="form-group d-inline-flex position-relative w-100 flex-center">
                    <div class="position-relative">
                        <button type="button" class="edit-button bg-transparent border-0">
                            <i class="fa-solid fa-pencil edit-button text-muted"></i>
                        </button>
                    </div>
                    <div class="position-relative">
                        <span class="fs-1 color-light ms-md-4 mb-0 edit-button">
                            {{ properNouns($user->name, $user->last_name) }}
                        </span>
                        <div class="position-absolute position-absolute top-50 start-50 translate-middle d-flex flex-row w-100">
                            <input type="text" class="form-control opacity" name="name" value="{{ $user->name }}">
                            <input type="text" class="form-control opacity" name="last_name" value="{{ $user->last_name }}">
                        </div>                   
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-lg-2">
                    <div class="p-2">
                        <div class="profile-card w-100 d-flex flex-column p-0 pb-3">
                            <div class="title_card d-flex justify-content-between w-100 py-2 px-3">
                                <h3 class="mb-0">Datos personales</h3>
                                <button type="button" class="edit-button btn btn-outline-light btn-sm">
                                    <i class="fa-solid fa-pencil edit-button"></i>
                                </button>
                            </div>

                            <div class="row gy-4 w-100 data-container py-3 px-2">
                                <div class="mt-0">
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-user pt-1"></i>
                                        <h6 class="ps-3 mb-0">Documento</h6>
                                    </div>
                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <input type="number" class="form-control form-control-sm opacity position-absolute" id="document" name="document" value="{{ $data_user->document }}">
                                        <span class="position-absolute">{{ $data_user->document ?? "Sin datos"}}</span>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-calendar-days pt-1"></i>
                                        <h6 class="ps-3 mb-0">Fecha de nacimiento</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <input type="date" class="form-control form-control-sm opacity position-absolute" id="birthdate"
                                            name="birthdate" value="{{ $data_user->birthdate }}">
                                        <span class="position-absolute">{{ formatDMY($data_user->birthdate) ?? "Sin datos"}}</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-venus-mars fa-sm flex-center"></i>
                                        <h6 class="ps-3 mb-0">Género</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $data_user->sex ?? "Sin datos"}}</span>
                                        <select class="form-select form-select-sm form-select form-select-sm-sm opacity position-absolute" id="gender" name="gender">
                                            <option value="Masculino" {{ $data_user->sex == 'Masculino' ? 'selected'
                                                : '' }}>
                                                Masculino</option>
                                            <option value="Femenino" {{ $data_user->sex == 'Femenino' ? 'selected' :
                                                '' }}>Femenino
                                            </option>
                                            <option value="no-binario" {{ $data_user->sex == 'no-binario' ?
                                                'selected' : '' }}>
                                                Prefiero no decir</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-droplet flex-center"></i>
                                        <h6 class="ps-3 mb-0">RH</h6>
                                    </div>
                                
                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $data_user->rh ?? "Sin datos" }}</span>
                                        <select class="form-select form-select-sm opacity position-absolute" id="rh" name="rh" {{
                                            $user->environment_id == 0 ?? 'required' }}>
                                            <option value=""></option>
                                            <option value="A+" {{ $data_user->rh == 'A+' ? 'selected' : '' }}>
                                                (A+)</option>
                                            <option value="A-" {{ $data_user->rh == 'A-' ? 'selected' : '' }}>
                                                (A-)</option>
                                            <option value="B+" {{ $data_user->rh == 'B+' ? 'selected' : '' }}>
                                                (B+)</option>
                                            <option value="B-" {{ $data_user->rh == 'B-' ? 'selected' : '' }}>
                                                (B-)</option>
                                            <option value="AB+" {{ $data_user->rh == 'AB+' ? 'selected' : '' }}>
                                                (AB+)</option>
                                            <option value="AB-" {{ $data_user->rh == 'AB-' ? 'selected' : '' }}>
                                                (AB-)</option>
                                            <option value="O+" {{ $data_user->rh == 'O+' ? 'selected' : '' }}>
                                                (O+)</option>
                                            <option value="O-" {{ $data_user->rh == 'O-' ? 'selected' : '' }}>
                                                (O-)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-genderless flex-center"></i>
                                        <h6 class="ps-3 mb-0">Etnia</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $data_user->ethnicity ?? "Sin datos"}}</span>
                                        <select class="form-select form-select-sm form-select form-select-sm-sm opacity position-absolute" id="ethnicity" name="ethnicity" {{
                                            $user->environment_id == 0 ?? 'required' }}>
                                            <option value=""></option>
                                            <option value="mestizo" {{ $data_user->ethnicity == 'mestizo' ?
                                                'selected' : '' }}>
                                                Mestizo</option>
                                            <option value="indigena" {{ $data_user->ethnicity == 'indigena' ?
                                                'selected' : '' }}>
                                                Indígena</option>
                                            <option value="afrocolombiano" {{ $data_user->ethnicity ==
                                                'afrocolombiano' ? 'selected' : '' }}>
                                                Afrocolombiano
                                            </option>
                                            <option value="gitano" {{ $data_user->ethnicity == 'gitano' ? 'selected'
                                                : '' }}>
                                                Gitano
                                            </option>
                                            <option value="raizal" {{ $data_user->ethnicity == 'raizal' ? 'selected'
                                                : '' }}>
                                                Raizal
                                            </option>
                                            <option value="palenquero" {{ $data_user->ethnicity == 'palenquero' ?
                                                'selected' : '' }}>
                                                Palenquero</option>
                                            <option value="ninguno" {{ $data_user->ethnicity == 'ninguno' ?
                                                'selected' : '' }}>
                                                Ninguno</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-2">
                        <div class="profile-card w-100 d-flex flex-column p-0 pb-3">
                            <div class="title_card d-flex justify-content-between w-100 py-2 px-3">
                                <h3 class="mb-0">Seguridad social</h3>
                                <button type="button" class="edit-button btn btn-outline-light btn-sm">
                                    <i class="fa-solid fa-pencil edit-button"></i>
                                </button>
                            </div>

                            <div class="row row-cols-1 gy-4 w-100 data-container py-3 px-2">
                                <div class="mt-0">
                                    <div class="text-start d-flex align-items-center w-100 w-md-50">
                                        <i class="fas fa-briefcase-medical flex-center"></i>
                                        <h6 class="pe-3 ps-3 mb-0">EPS</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-100 w-md-50 position-relative">
                                        <span>{{ $data_user->eps ?? "Sin datos"}}</span>
                                        <select class="form-select form-select-sm form-select form-select-sm-sm opacity position-absolute" id="eps" name="eps">
                                            <option value=""></option>
                                            <option value="COMPENSAR E.P.S." {{ $data_user->eps == 'COMPENSAR E.P.S.' ? 'selected' : '' }}>
                                                COMPENSAR E.P.S.
                                            </option>
                                            <option value="CAPITAL SALUD E.P.S." {{ $data_user->eps == 'CAPITAL SALUD E.P.S.' ? 'selected' : '' }}>
                                                CAPITAL SALUD E.P.S.</option>
                                            <option value="E.P.S. FAMISANAR LTDA." {{ $data_user->eps == 'E.P.S. FAMISANAR LTDA.' ? 'selected' : '' }}>
                                                E.P.S. FAMISANAR LTDA.</option>
                                            <option value="E.P.S. SANITAS S.A." {{ $data_user->eps == 'E.P.S. SANITAS S.A.' ? 'selected' : '' }}>
                                                E.P.S. SANITAS S.A.</option>
                                            <option value="EPS SERVICIO OCCIDENTAL DE SALUD S.A." {{ $data_user->eps
                                                == 'EPS SERVICIO OCCIDENTAL DE SALUD S.A.' ? 'selected' : '' }}>
                                                EPS SERVICIO OCCIDENTAL DE SALUD S.A.</option>
                                            <option value="EPS Y MEDICINA PREPAGADA SURAMERICANA S.A." {{
                                                $data_user->eps == 'EPS Y MEDICINA PREPAGADA SURAMERICANA S.A.' ?
                                                'selected' : '' }}>
                                                EPS Y MEDICINA PREPAGADA SURAMERICANA S.A.</option>
                                            <option value="NUEVA EPS S.A." {{ $data_user->eps == 'NUEVA EPS S.A.' ?
                                                'selected' : '' }}>NUEVA EPS S.A.</option>
                                            <option value="SALUD TOTAL S.A. E.P.S." {{ $data_user->eps == 'SALUD TOTAL S.A. E.P.S.' ? 'selected' : '' }}>
                                                SALUD TOTAL S.A. E.P.S.</option>
                                            <option value="SALUDVIDA S.A. E.P.S." {{ $data_user->eps == 'SALUDVIDA S.A. E.P.S.' ? 'selected' : '' }}>
                                                SALUDVIDA S.A. E.P.S.</option>
                                            <option value="ALIANSALUD E.P.S." {{ $data_user->eps == 'ALIANSALUD E.P.S.' ? 'selected' : '' }}>ALIANSALUD E.P.S.</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fas fa-piggy-bank me-2"></i>
                                        <h6 class="pe-3 ps-3 mb-0">AFP</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span>{{ $data_user->afp }}</span>
                                        <select class="form-select form-select-sm form-select form-select-sm-sm opacity position-absolute" id="afp" name="afp">
                                            <option value=""></option>
                                            <option value="PROTECCION" {{ $data_user->afp == 'PROTECCION' ? 'selected' : '' }}>
                                                PROTECCION</option>
                                            <option value="PORVENIR" {{ $data_user->afp == 'PORVENIR' ? 'selected' :
                                                '' }}>
                                                PORVENIR</option>
                                            <option value="COLFONDOS" {{ $data_user->afp == 'COLFONDOS' ? 'selected'
                                                : '' }}>
                                                COLFONDOS</option>
                                            <option value="OLD MUTUAL" {{ $data_user->afp == 'OLD MUTUAL' ? 'selected' 
                                                : '' }}>OLD MUTUAL</option>
                                            <option value="COLPENSIONES" {{ $data_user->afp == 'COLPENSIONES' ?
                                                'selected' : '' }}>
                                                COLPENSIONES</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fas fa-shield-alt"></i>
                                        <h6 class="pe-3 ps-3 mb-0">ARL: </h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative edit-button">
                                        <span class="edit-button">{{ $data_user->arl ?? "Sin datos"}}</span>
                                        <select class="form-select form-select-sm form-select form-select-sm-sm opacity position-absolute" id="arl" name="arl">
                                            <option value=""></option>
                                            <option value="SURA" {{ $data_user->arl == 'SURA' ? 'selected' : ''
                                                }}>SURA</option>
                                            <option value="POSITIVA" {{ $data_user->arl == 'POSITIVA' ? 'selected' :
                                                '' }}>
                                                POSITIVA</option>
                                            <option value="BOLIVAR" {{ $data_user->arl == 'BOLIVAR' ? 'selected' :
                                                '' }}>BOLIVAR
                                            </option>
                                            <option value="COLSANITAS" {{ $data_user->arl == 'COLSANITAS' ?
                                                'selected' : '' }}>
                                                COLSANITAS</option>
                                            <option value="COLMENA" {{ $data_user->arl == 'COLMENA' ? 'selected' :
                                                '' }}>COLMENA
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fas fa-people-arrows"></i>
                                        <h6 class="pe-3 ps-3 mb-0">Caja: </h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span>{{ $data_user->caja }}</span>
                                        <select class="form-select form-select-sm form-select form-select-sm-sm opacity position-absolute" id="caja" name="caja">
                                            <option value=""></option>
                                            <option value="COLSUBSIDIO" {{ $data_user->caja == 'COLSUBSIDIO' ?
                                                'selected' : '' }}>
                                                COLSUBSIDIO</option>
                                            <option value="CAFAM" {{ $data_user->caja == 'CAFAM' ? 'selected' : ''
                                                }}>CAFAM
                                            </option>
                                            <option value="COMPENSAR" {{ $data_user->caja == 'COMPENSAR' ?
                                                'selected' : '' }}>
                                                COMPENSAR</option>
                                            <option value="NINGUNA" {{ $data_user->caja == 'NINGUNA' ? 'selected' :
                                                '' }}>NINGUNA
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="px-2">
                        <div class="profile-card w-100 d-flex flex-column p-0 pb-3">
                            <div class="title_card d-flex justify-content-between w-100 py-2 px-3">
                                <h3 class="mb-0">Contrato</h3>
                                <button type="button" class="edit-button btn btn-outline-light btn-sm">
                                    <i class="fa-solid fa-pencil edit-button"></i>
                                </button>
                            </div>

                            <div class="row row-cols-1 gy-4 w-100 data-container py-3 px-2">
                                <div class="mt-0">
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-file-contract flex-center"></i>
                                        <h6 class="pe-3 ps-3 mb-0">Contrato No. </h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $data_user->contract }}</span>
                                        <div class="position-absolute">
                                            <input type="number" class="form-control form-control-sm opacity me-2" name="contract_number" value="{{ splitByHyphen($data_user->contract)[0] }}">
                                            <input type="number" class="form-control form-control-sm opacity" name="contract_vig" value="{{ splitByHyphen($data_user->contract)[1] }}">
                                        </div>
                                    </div>
                                </div>

                                @if ($user->role_id != 1 && $user->environment_id != 0)
                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-file-contract flex-center"></i>
                                        <h6 class="pe-3 ps-3 mb-0">Perfil</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $user->role->name }}</span>
                                        <select class="form-select form-select-sm opacity position-absolute" name="role_id">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->role->id == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>    
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="px-2">
                        <div class="profile-card w-100 d-flex flex-column p-0 pb-3 shadow">
                            <div class="title_card d-flex justify-content-between align-items-center w-100 py-2 px-4 border-bottom border-danger">
                                <h3 class="mb-0">Información de contacto</h3>
                                <button type="button" class="edit-button btn btn-outline-light btn-sm">
                                    <i class="fa-solid fa-pencil edit-button"></i>
                                </button>
                            </div>
                        
                            <div class="row row-cols-1 gy-4 w-100 data-container py-3 px-2">
                                <div class="mt-0">
                                    <div class="d-flex align-items-center mb-2 w-50 ">
                                        <i class="fa-solid fa-phone-alt me-3"></i>
                                        <h6 class="mb-0">Teléfono</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $data_user->phone }}</span>
                                        <input type="number" 
                                               class="form-control form-control-sm position-relative opacity" 
                                               name="phone" 
                                               placeholder="7845111" 
                                               value="{{ $data_user->phone }}">
                                    </div>
                                </div>
                        
                                <!-- Dirección -->
                                <div>
                                    <div class="text-start d-flex align-items-center w-50">
                                        <i class="fa-solid fa-map-marker-alt me-3"></i>
                                        <h6 class="pe-3 mb-0">Dirección</h6>
                                    </div>

                                    <div class="text-start d-flex align-items-center w-50 position-relative">
                                        <span class="position-absolute">{{ $data_user->address ?? "Sin datos" }}</span>
                                        <input type="text" 
                                               class="form-control form-control-sm position-absolute opacity" name="address" value="{{ $data_user->address }}" 
                                               {{ $user->environment_id == 0 ? 'required' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3 btn-loader">Actualizar datos</button>
            </div>
        </div>
    </form>
</section>

@endsection