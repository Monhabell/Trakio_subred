@if (isset($mostrarModal) && $mostrarModal)
<!-- Modal datauser-->
<div class="modal fade" id="modal_user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-dark border-0 rounded-0">
                <h4 class="modal-title fw-bold text-light" id="staticBackdropLabel">Información del contratista</h4>
            </div>
            @if ($errors->any())
            <div class="alert alert-danger mb-0">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form class="modal-body bg-dark container-fluid mx-0 rounded-0" action="@if($user->environment_id == 0)
                        {{ route('datauser.store.gesi') }} 
                        @else {{ route('datauser.store.env') }}
                        @endif" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    @if ($user->environment_id == 0)
                    <div class="col-12 col-lg-3">
                        <div class="m-3" style="position: relative;">
                            <div class="w-100 d-flex justify-content-center">
                                <label class="text-light" for="foto">Foto de perfil</label>
                            </div>

                            <div id="photo-container">
                                <img id="photo-preview" src="{{ asset('img/icons/noImage.jpg') }}" alt="Foto de perfil">
                            </div>

                            <div class="btn_img_perfil">
                                <label for="foto" class="camera-icon d-flex justify-content-center"></label>
                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(event)"
                                    style="display: none;" required>
                                <button id="btn_img_foto" onclick="document.getElementById('foto').click();"
                                    style="background: none; border: none; cursor: pointer;">
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col d-flex flex-column">
                        <div class="row">
                            <p class="fst-italic">*Por favor proporcione información verídica, ya que esta será
                                utilizada para generar el informe de actividades</p>
                        </div>
                        <div class="row row-cols-lg-2 g-2 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="document" name="document"
                                    placeholder="10032464546" value="{{ old('document') }}" required>
                                <label class="text-light">Documento</label>
                            </div>

                            <div class="form-floating">
                                <input type="date" class="form-control bg-transparent" id="birthdate" name="birthdate"
                                    placeholder="10032464546" value="{{ old('birthdate') }}" required>
                                <label class="text-light">Fecha de nacimiento</label>
                            </div>

                            <div class="form-floating">
                                <input type="number" class="form-control bg-transparent" id="phone" name="phone"
                                    placeholder="322221654" value="{{ old('phone') }}" required>
                                <label class="text-light">Teléfono</label>
                            </div>

                            <div class="form-floating">
                                <div class="input-group input-group-lg">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-transparent" id="contract_number"
                                            name="contract_number" placeholder="10032464546"
                                            value="{{ old('contract_number') }}" required>
                                        <label class="text-light">No. contrato</label>
                                    </div>
                                    <span class="input-group-text">-</span>
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-transparent" id="contract_vig"
                                            name="contract_vig" placeholder="10032464546"
                                            value="{{ old('contract_vig') }}" required>
                                        <label class="text-light">Vigencia</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-floating">
                                <select class="form-select" id="sex" name="sex"
                                    aria-label="Floating label select example" required>
                                    <option value="">...</option>
                                    <option value="Masculino" {{ old('sex')=="Masculino" ? 'selected' : '' }}>Masculino
                                    </option>
                                    <option value="Femenino" {{ old('sex')=="Femenino" ? 'selected' : '' }}>Femenino
                                    </option>
                                    <option value="no-binario" {{ old('sex')=="no-binario" ? 'selected' : '' }}>Prefiero
                                        no decir</option>
                                </select>
                                <label for="floatingSelect" class="text-light">Género</label>
                            </div>

                            @if ($user->environment_id == 0)
                            <div class="form-floating">
                                <select class="form-select" id="rh" name="rh" aria-label="Floating label select example"
                                    required>
                                    <option value="">...</option>
                                    <option value="A+"  {{ old('rh')=="A+" ? 'selected' : '' }}>(A+)</option>
                                    <option value="A-"  {{ old('rh')=="A-" ? 'selected' : '' }}>(A-)</option>
                                    <option value="B+"  {{ old('rh')=="B+" ? 'selected' : '' }}>(B+)</option>
                                    <option value="B-"  {{ old('rh')=="B-" ? 'selected' : '' }}>(B-)</option>
                                    <option value="AB+" {{ old('rh')=="AB+" ? 'selected' : '' }}>(AB+)</option>
                                    <option value="AB-" {{ old('rh')=="AB-" ? 'selected' : '' }}>(AB-)</option>
                                    <option value="O+"  {{ old('rh')=="O+" ? 'selected' : '' }}>(O+)</option>
                                    <option value="O-"  {{ old('rh')=="O-" ? 'selected' : '' }}>(O-)</option>
                                </select>
                                <label for="floatingSelect" class="text-light">RH</label>
                            </div>

                            <div class="form-floating">
                                <input type="text" class="form-control bg-transparent" id="address" name="address"
                                    value="{{ old('address') }}" placeholder="Cll 3" required>
                                <label class="text-light">Dirección</label>
                            </div>

                            <div class="form-floating">
                                <select class="form-select" id="ethnicity" name="ethnicity"
                                    aria-label="Floating label select example" required>
                                    <option value="">...</option>
                                    <option value="mestizo"        {{ old('ethnicity')=="mestizo" ? 'selected' : '' }}>Mestizo</option>
                                    <option value="indigena"       {{ old('ethnicity')=="indigena" ? 'selected' : '' }}>Indígena</option>
                                    <option value="afrocolombiano" {{ old('ethnicity')=="afrocolombiano" ? 'selected' : '' }}>Afrocolombiano</option>
                                    <option value="gitano"         {{ old('ethnicity')=="gitano" ? 'selected' : '' }}>Gitano</option>
                                    <option value="raizal"         {{ old('ethnicity')=="raizal" ? 'selected' : '' }}>Raizal</option>
                                    <option value="palenquero"     {{ old('ethnicity')=="palenquero" ? 'selected' : '' }}>Palenquero</option>
                                    <option value="ninguno"        {{ old('ethnicity')=="ninguno" ? 'selected' : '' }}>Ninguno
                                    </option>
                                </select>
                                <label for="floatingSelect" class="text-light">Etnia</label>
                            </div>
                            @endif

                            <div class="form-floating">
                                <select class="form-select" id="eps" name="eps"
                                    aria-label="Floating label select example" required>
                                    <option value="">...</option>
                                    <option value="COMPENSAR E.P.S."                           {{ old('eps')=="COMPENSAR E.P.S." ? 'selected' : ''}}>COMPENSAR E.P.S.</option>
                                    <option value="CAPITAL SALUD E.P.S."                       {{ old('eps')=="CAPITAL SALUD E.P.S." ? 'selected' : '' }}>CAPITAL SALUD E.P.S.</option>
                                    <option value="E.P.S. FAMISANAR LTDA."                     {{ old('eps')=="E.P.S. FAMISANAR LTDA." ? 'selected' : '' }}>E.P.S. FAMISANAR LTDA.</option>
                                    <option value="E.P.S. SANITAS S.A."                        {{ old('eps')=="E.P.S. SANITAS S.A." ? 'selected' : '' }}>E.P.S. SANITAS S.A.</option>
                                    <option value="EPS SERVICIO OCCIDENTAL DE SALUD S.A."      {{ old('eps')=="EPS SERVICIO OCCIDENTAL DE SALUD S.A." ? 'selected' : '' }}>EPS SERVICIO OCCIDENTAL DESALUD S.A.</option>
                                    <option value="EPS Y MEDICINA PREPAGADA SURAMERICANA S.A." {{ old('eps')=="EPS Y MEDICINA PREPAGADA SURAMERICANA S.A." ? 'selected' : '' }}>EPS Y MEDICINA PREPAGADA SURAMERICANA S.A.</option>
                                    <option value="NUEVA EPS S.A."                             {{ old('eps')=="NUEVA EPS S.A." ? 'selected' : '' }}>NUEVA EPS S.A.</option>
                                    <option value="SALUD TOTAL S.A. E.P.S."                    {{ old('eps')=="SALUD TOTAL S.A. E.P.S." ? 'selected' : '' }}>SALUD TOTAL S.A. E.P.S.</option>
                                    <option value="SALUDVIDA S.A. E.P.S."                      {{ old('eps')=="SALUDVIDA S.A. E.P.S." ? 'selected' : '' }}>SALUDVIDA S.A. E.P.S.</option>
                                    <option value="ALIANSALUD E.P.S."                          {{ old('eps')=="ALIANSALUD E.P.S." ? 'selected' : '' }}>ALIANSALUD E.P.S.</option>
                                    <option value="SURA E.P.S."                                {{ old('eps')=="SURA E.P.S." ? 'selected' : '' }}>SURA E.P.S.</option>
                                </select>
                                <label for="floatingSelect" class="text-light">EPS</label>
                            </div>

                            <div class="form-floating">
                                <select class="form-select" id="afp" name="afp"
                                    aria-label="Floating label select example" required>
                                    <option value="">...</option>
                                    <option value="PROTECCION"   {{ old('afp')=="PROTECCION" ? 'selected' : '' }}>PROTECCION</option>
                                    <option value="PORVENIR"     {{ old('afp')=="PORVENIR" ? 'selected' : '' }}>PORVENIR</option>
                                    <option value="COLFONDOS"    {{ old('afp')=="COLFONDOS" ? 'selected' : '' }}>COLFONDOS</option>
                                    <option value="PROTECCION"   {{ old('afp')=="PROTECCION" ? 'selected' : '' }}>PROTECCION</option>
                                    <option value="OLD MUTUAL"   {{ old('afp')=="OLD MUTUAL" ? 'selected' : '' }}>OLD MUTUAL</option>
                                    <option value="COLPENSIONES" {{ old('afp')=="COLPENSIONES" ? 'selected' : '' }}>COLPENSIONES</option>
                                </select>
                                <label for="floatingSelect" class="text-light">AFP</label>
                            </div>

                            <div class="form-floating">
                                <select class="form-select" id="arl" name="arl"
                                    aria-label="Floating label select example" required>
                                    <option value="">...</option>
                                    <option value="SURA"       {{ old('arl')=="SURA" ? 'selected' : '' }}>SURA</option>
                                    <option value="POSITIVA"   {{ old('arl')=="POSITIVA" ? 'selected' : '' }}>POSITIVA</option>
                                    <option value="BOLIVAR"    {{ old('arl')=="BOLIVAR" ? 'selected' : '' }}>BOLIVAR</option>
                                    <option value="COLSANITAS" {{ old('arl')=="COLSANITAS" ? 'selected' : '' }}>COLSANITAS</option>
                                    <option value="COLMENA"    {{ old('arl')=="COLMENA" ? 'selected' : '' }}>COLMENA</option>
                                </select>
                                <label for="floatingSelect" class="text-light">ARL</label>
                            </div>

                            <div class="form-floating">
                                <select class="form-select" id="caja" name="caja"
                                    aria-label="Floating label select example" required>
                                    <option value="">...</option>
                                    <option value="COLSUBSIDIO" {{ old('caja')=="COLSUBSIDIO" ? 'selected' : '' }}>COLSUBSIDIO</option>
                                    <option value="CAFAM"       {{ old('caja')=="CAFAM" ? 'selected' : '' }}>CAFAM</option>
                                    <option value="COMPENSAR"   {{ old('caja')=="COMPENSAR" ? 'selected' : '' }}>COMPENSAR</option>
                                    <option value="NINGUNA"     {{ old('caja')=="NINGUNA" ? 'selected' : '' }}>NINGUNA</option>
                                </select>
                                <label for="floatingSelect" class="text-light">Caja de compensación</label>
                            </div>
                        </div>

                        <div class="row w-100">
                            <input type="submit" class="btn btn-primary w-100" value="Guardar">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para mostrar el modal -->
<script>
    setTimeout(function() {
            $('#modal_user').modal('show');
        }, 500);

        function previewImage(event) {
            var preview = document.getElementById('photo-preview');
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function() {
                preview.src = reader.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }
</script>
@endif