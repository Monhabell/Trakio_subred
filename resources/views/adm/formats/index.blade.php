@extends('layouts.adm.navigation')

@section('main')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Bases y formatos</h1>

    <a type="button" href="{{ route('formats.create') }}" class="btn btn-sm btn-primary" >
        <i class="fa-solid fa-sm fa-plus text-white-50"></i> Añadir base
    </a>
</div>

{{-- Cantidades de bases por entorno --}}
<div class="row mb-3 gap-3">
    @foreach ($bases->groupBy('environment_id') as $envId => $groupedBases)
    
        <div class="col card-count-bases mb-2 card border-left-primary py-2">
            <div class="card-body row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        {{ $groupedBases->first()->entorno->entorno }}
                    </div>
                    <div class="h2 mb-0 font-weight-bold text-gray-300">
                        {{ $groupedBases->where('is_active', true)->count() }} <small class="text-gray-600 h6">bases activas</small>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fa-solid fa-database fa-2x text-gray-800"></i>
                </div>
            </div>
        </div>
    
    @endforeach
</div>

<div class="col-12 px-0 scrollable-container table-bases">
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Entorno</th>
                <th>Nombre</th>
                <th>Tiempo encabezado</th>
                <th>Tiempo ind / seg</th>
                <th>Estado</th>
                <th>Editar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bases as $base)
            <tr>
                <td>{{ $base->id }}</td>
                <td>{{ $base->entorno->entorno }}</td>
                <td>{{ $base->name }}</td>
                <td>{{ $base->header_time }}</td>
                <td>{{ $base->body_time }}</td>
                <td>
                    @if ($base->is_active)    
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Inactivar base</button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('formats.deactivate', $base->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-danger">Confirmar inactivación</button>
                            </form>
                        </li>
                    </ul>
                    @else
                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Activar base</button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('formats.activate', $base->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-success">Confirmar activación</button>
                            </form>
                        </li>
                    </ul>
                    @endif
                </td>
                <td class="text-primary fw-bold text-left">
                    <a type="button" href="{{ route('formats.edit', $base->id) }}" class="btn btn-info btn-sm fw-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    @if ($base->is_dad)
                        <button type="button" title="Enlazar formato" class="btn btn-primary btn-sm btn-add-son" data-bs-toggle="modal" data-bs-target="#modalAddSon" data-base="{{ $base }}">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalAddSon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="titleModalAddSon">Enlazar formato</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAddSon" action="{{ route('formats.associate.son') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <input type="number" id="format_id_dad" name="format_id_dad" hidden>
                <label for="format_id_son" class="form-label">Selecciona formato a enlazar:</label>
                <select class="form-select" id="format_id_son" name="format_id_son" required>
                </select>
            </div>

            <div id="list-aditional-formats">

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
            </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
    const bases = @json($bases);

    const btnsAddSon = document.querySelectorAll('.btn-add-son');

    btnsAddSon.forEach(btn => {
        btn.addEventListener('click', function() {
            const base = JSON.parse(this.getAttribute('data-base'));
            const form = document.getElementById('formAddSon');
            document.getElementById('format_id_dad').value = base.id;
            
            document.getElementById('titleModalAddSon').innerText = `Enlazar formato a ${base.name}`;
            
            const select = document.getElementById('format_id_son');
            select.innerHTML = '';

            const basesAssignments = listBasesAssignments(base);
            const basesHijas = listaDeBasesHijas(base, basesAssignments);
            
            if (basesHijas.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.text = 'No hay formatos disponibles para enlazar';
                select.appendChild(option);
                select.disabled = true;
            } else {
                select.disabled = false;
                
                basesHijas.forEach(baseHija => {
                    const option = document.createElement('option');
                    option.value = baseHija.id;
                    option.text = baseHija.name;
                    select.appendChild(option);
                });
            }

            insertBasesAssignments(basesAssignments);
        });
    });

    function listaDeBasesHijas(format, basesAssignments) {        
        return bases.filter(base => (!base.is_dad || base.is_free) && base.environment_id === format.environment_id && !basesAssignments.includes(base) && base.is_active && base.id !== format.id);
    }

    function listBasesAssignments(format) {
        const basesAsignadas = bases.filter(base => format.adicionales.map(format => format.id_format_adicional).includes(base.id));
        return basesAsignadas;
    }

    function insertBasesAssignments(basesAssignments) {
        const container = document.getElementById('list-aditional-formats');
        container.innerHTML = '';

        if (basesAssignments.length) {
            
            const title = document.createElement('h5');
            title.textContent = 'Formatos enlazados:';
            container.appendChild(title);
    
            basesAssignments.forEach(base => {
                const div = document.createElement('div');
                div.classList.add('mb-2', 'p-2', 'border', 'rounded');
    
                div.innerHTML = `
                    <span>${base.name}</span>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-assignment d-none" data-base-id="${base.id}">Eliminar</button>
                `;
                container.appendChild(div);
            });
        }
    }
</script>
@endsection