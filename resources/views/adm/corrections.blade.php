@extends('layouts.adm.navigation')

@section('main')

<div class="d-none align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Correcciones</h1>
</div>
<div class="card shadow mb-4 pt-0 border-0">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between bg-dark">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link nav-link-corrections" data-filter-status="">Todas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-corrections" data-filter-status="null">Pendientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-link-corrections" data-filter-status="1">Realizadas</a>
            </li>
        </ul>

        <form id="form-search-corrections">
            <div class="input-group">
                <input type="text" class="form-control small" id="search_file" name="search_file"
                    placeholder="Buscar ficha" aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-primary" id="btn-search-correction">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- Card Body -->
    <div class="card-body bg-secondary">
        <div class="table-responsive">
            <div class="row row-cols-md-2 row-cols-lg-3 g-2 g-lg-3 scrollable-container box-corrections" id="filter-corrections-box">
            </div>
        </div>
    </div>
</div>

@endsection