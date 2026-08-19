
<div class="container-form-formats">
    <div>
        <label for="environment" class="col-form-label">Entorno</label>
        <select class="form-select form-select-sm" aria-label="Default select example" name="environment_id" id="environment_id">
            <option value="" selected>Seleccionar entorno</option>
            @foreach ($environments as $environment)
                @if ($environment->id != 0 && $environment->id != 1)
                <option 
                    value="{{ $environment->id }}" 
                    {{ $format->environment_id == $environment->id || old('environment_id') ? 'selected' : '' }}
                    >{{ $environment->entorno }}
                </option>
                @endif
            @endforeach
        </select>
    </div>

    <div>
        <label for="baseName" class="col-form-label">Nombre base</label>
        <input type="text" class="form-control form-control-sm text-uppercase" value ="{{ old('name') ?? $format->name }}" name="name">
    </div>

    <div>
        <label for="editTimeHeader" class="col-form-label">Tiempo encabezado</label>
        <input type="number" class="form-control form-control-sm" step="0.01" lang="en" name="header_time" value="{{ old('header_time') ?? $format->header_time }}">
    </div>

    <div>
        <label for="editTimeUserSeg" class="col-form-label">Tiempo usuario/seguimiento</label>
        <input type="number" class="form-control form-control-sm" step="0.01" lang="en" value="{{ old('body_time') ?? $format->body_time }}" name="body_time">
    </div>
</div>

<div class="border border-primary my-5 p-3 rounded-3">
    <h4>
        Base(s) correspondiente(s) aplicativo secretaría
        <button type="button" class="btn btn-primary btn-sm ms-3" id="add_new_base_btn" title="Agregar nueva base de secretaría"><i class="fa-solid fa-plus"></i></button>
    </h4>
    
    <div class="d-flex gap-3 flex-wrap" id="container-formats-sds">
        @if(isset($format->format_sds) && count($format->format_sds) > 0)
        @foreach($format->format_sds as $key => $sds)
        <div>
            <label for="format_id_sds_{{ $sds->id }}" class="col-form-label">Base aplicativo SDS {{ $key + 1 }}</label>
            <select class="form-select form-select-sm" aria-label="Default select example" name="format_id_sds[]" id="format_id_sds_{{ $sds->id }}">
                <option selected>Seleccionar</option>
                @foreach ($formatsSds as $formatSds)
                    
                    <option value="{{ $formatSds->id }}"
                        {{ $sds->id == $formatSds->id || old('format_id_sds') ? 'selected' : '' }}
                        >{{ strtoupper($formatSds->name_sds) }}
                    </option>
                    
                @endforeach
            </select>
        </div>
        @endforeach
        @endif
    </div>
</div>

<div class="border border-primary my-5 p-3 rounded-3">
    
</div>

<div class="d-flex justify-content-between mt-3">
    <a type="button" href="{{ route('formats.index') }}" class="btn ps-0"><i class="fa-solid fa-arrow-left me-2"></i> Volver</a>
    <button type="submit" class="btn btn-primary btn-sm btn-loader">Guardar</button>
</div>

<script type="module">
    const buttonAddNewBase = document.getElementById('add_new_base_btn');
    const containerBases = document.getElementById('container-formats-sds');
    let bases = @json($formatsSds);
    const enviroment_input = document.getElementById('environment_id');


    enviroment_input.addEventListener('change', function() {
        bases_sds(enviroment_input.value).then(function(fetchedBases) {
            bases = fetchedBases;            
            insertBases(bases);        
        }).catch(function(error) {
                console.error('Error al cargar las bases SDS:', error);
                alert('No se pudieron cargar las bases SDS. Por favor, intente nuevamente más tarde.');
        });
    });
  
    buttonAddNewBase.addEventListener('click', function(event) {
        event.preventDefault();
        if (enviroment_input.value == "") {
            alert('Por favor seleccione un entorno antes de agregar una nueva base.');
            return;
        }
        const baseIndex = containerBases ? containerBases.children.length : 0;
        const div = document.createElement('div');
        const label = document.createElement('label');
        label.setAttribute('for', `format_id_sds_new_${baseIndex}`);
        label.classList.add('col-form-label');
        label.textContent = `Base aplicativo SDS ${baseIndex + 1}`;

        const select = document.createElement('select');
        select.classList.add('form-select', 'form-select-sm');
        select.setAttribute('name', 'format_id_sds[]');
        select.setAttribute('id', `format_id_sds_new_${baseIndex}`);
        select.classList.add('select-base-sds');

        const defaultOption = document.createElement('option');
        defaultOption.setAttribute('selected', '');
        defaultOption.textContent = 'Seleccionar';
        select.appendChild(defaultOption);
    
        div.appendChild(label);
        div.appendChild(select);
        containerBases.appendChild(div);

        if (bases.length > 0) {
            insertBases(bases);
        } else {
            const option = document.createElement('option');
            option.setAttribute('value', '');
            option.textContent = 'No hay bases disponibles';
            select.appendChild(option);
        } 
    });
    
    function insertBases(bases){
        const selects = document.querySelectorAll('.select-base-sds');
        selects.forEach(select => { 
            select.innerHTML = '';
            bases.forEach((base, index) => {
                const option = document.createElement('option');
                option.setAttribute('value', base.id);
                option.textContent = base.name_sds;
                select.appendChild(option);
            });
        }); 
    }


    async function bases_sds(env){
    
        const url = route('formats.bases_sds', {environment: env});
        const response = await fetch(url);
        const data = await response.json();
        return data.formatsSds;
           
    }



</script>
