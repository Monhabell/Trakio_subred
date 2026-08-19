@extends('layouts.dig.navigation')

@section('main')
    @include('dig.partials.daily-progress-bar')

    <div class="dashboard-wrapper">
        <div class="dig-page-header">
            <div>
                <span class="dig-page-tag">Digitalización · Tiempo real</span>
                <h2 class="dig-page-title">Panel de Operaciones</h2>
                <p class="dig-page-subtitle mb-0">Gestión de asignaciones y productividad en tiempo real</p>
            </div>

            <div class="filter-switch-container">
                <button id="btn-filter-active" class="dig-btn dig-btn-ghost active">
                    <span class="status-dot"></span> PENDIENTES
                </button>
                <button id="btn-filter-finished" class="dig-btn dig-btn-ghost">
                    <span class="status-dot green"></span> COMPLETADOS
                </button>
            </div>
        </div>

        <div class="position-relative carrusel-container mb-4">
            <div id="arrowLeft" class="nav-arrow left-arrow"><i class="fa-solid fa-chevron-left"></i></div>

            <div id="packages-list" class="d-flex gap-3 scroll-container px-3 py-2">
                <div class="loader-placeholder">Cargando paquetes...</div>
            </div>

            <div id="arrowRight" class="nav-arrow right-arrow"><i class="fa-solid fa-chevron-right"></i></div>
        </div>

        <div id="table-selected-package" class="dig-card main-content-card p-0">
            <div class="card-body-glass p-0">
                <div class="dig-table-wrap custom-table-container" id="package-table">
                </div>
            </div>
        </div>

        <div id="game-container" class="text-center py-5" hidden>
            <div class="dig-card glass-panel p-5 mx-auto" style="max-width: 600px;">
                <i class="fa-solid fa-ghost mb-3 game-empty-icon" style="font-size: 3rem;"></i>
                <h1 class="dig-empty-title">¡Sin tareas pendientes!</h1>
                <p class="dig-empty-text mx-auto">No tienes paquetes asignados. Diviértete un poco mientras esperas.</p>
                <div id="game" class="game-box mt-4"></div>
            </div>
        </div>
    </div>
@endsection
