@props([
    'name' => true,
    'show' => false,
    'maxWidth' => 'lg',
    'btnClose' => true,
    'icons' => 'alert',
    'btnContent' => 'Aceptar',
    'btnClass' => 'nav-link-files',
    'isBtn' => false,
    'href' => route('documents.index'),
    'function' => [
        'data' =>'data-to-container',
        'value' => 'collapsePlanilla'
    ]
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
][$maxWidth];

$icons = [
    'alert' => 'fa-triangle-exclamation',
    'danger' => 'fa-circle-exclamation'
][$icons]
@endphp

<!-- Modal -->
<div class="modal fade show bg-modal" tabindex="-1" role="dialog" style="display: {{ $show ? 'block' : 'none' }};">
    <div class="modal-dialog modal-dialog-centered {{ $maxWidth }}" role="document">
        <div class="modal-content">
            {{-- <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid {{ $icons }}"></i>
                    {{ $name }}
                </h5>
                @if ($btnClose)
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
                
            </div> --}}
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer divider-line"> 
                @if($isBtn)
                    <button class="btn  {{ $btnClass }} text-light btn-menu" aria-current="page"
                    {{ $function['data'] }}= {{ $function['value'] }} role="button">
                        {{ $btnContent }}
                    </button>
                @else
                    <a class="btn { $btnClass }} text-light btn-menu " href="{{ $href }}">
                        {{ $btnContent }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
