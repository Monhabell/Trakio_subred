@extends('layouts/env/navigation')

@section('main')

{{-- Page Header --}}
<div class="env-page-header pd-page-header mb-2">
    <div class="pd-header-main">
        <div class="pd-header-icon">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="page-header-body">
            <span class="env-page-tag">Consecutivos</span>
            <h1 class="env-page-title pd-page-title">Preparar entrega</h1>
            <p class="env-page-subtitle">Busca fichas, agrégalas a la cola y define el orden de entrega</p>
        </div>
    </div>
    <div class="shortcuts-hint d-none d-lg-flex">
        <span class="shortcut-tag"><kbd>Doble clic</kbd> Agregar</span>
        <span class="shortcut-tag"><kbd>Ctrl</kbd>+clic Selección múltiple</span>
        <span class="shortcut-tag"><kbd>Shift</kbd>+clic Rango</span>
        <span class="shortcut-tag"><kbd>Enter</kbd> Mover selección</span>
    </div>
</div>

{{-- Tab Switcher --}}
<div class="tab-switcher">
    <button type="button" class="btn-section-toggle active" data-target="section-consecutivos">
        <span class="tab-dot"></span>
        <i class="fas fa-list-ol"></i> Consecutivos
    </button>
    <button type="button" class="btn-section-toggle" data-target="section-sisco">
        <span class="tab-dot"></span>
        <i class="fas fa-exchange-alt"></i> SISCO
    </button>
</div>

{{-- SECCIÓN CONSECUTIVOS --}}
<section id="section-consecutivos" class="content-section env-card pd-section">
    <div class="pd-split">

        {{-- Panel: Búsqueda --}}
        <div class="panel-col">
            <div class="panel-header">
                <div class="panel-header-left">
                    <div class="panel-icon search-icon"><i class="fas fa-search"></i></div>
                    <div>
                        <p class="panel-title">Buscar fichas</p>
                        <p class="panel-subtitle">Escribe el nombre, número o usuario</p>
                    </div>
                </div>
            </div>
            <div class="search-wrapper">
                <input type="text" class="search-input form-control"
                    placeholder="Buscar por nombre, consecutivo, usuario…">
                <button class="btn search-btn" type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="search-results">
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-search"></i></div>
                    <p>Realice una búsqueda para comenzar</p>
                </div>
            </div>
        </div>

        {{-- Flecha divisora --}}
        <div class="panel-divider">
            <div class="panel-divider-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>

        {{-- Panel: Cola de entrega --}}
        <div class="panel-col">
            <form class="delivery-form d-flex flex-column h-100" method="POST"
                action="{{ route('reviewconsecutives.update-status-gesi') }}">
                @csrf
                <input type="hidden" name="selected_ids" class="final-order">
                <input type="hidden" name="status" value="3">

                <div class="panel-header">
                    <div class="panel-header-left">
                        <div class="panel-icon queue-icon"><i class="fas fa-layer-group"></i></div>
                        <div>
                            <p class="panel-title">
                                Cola de entrega
                                <span class="badge rounded-pill bg-danger ms-1 queue-count">0</span>
                            </p>
                            <p class="panel-subtitle">Arrastra para reordenar</p>
                        </div>
                    </div>
                </div>

                <div class="queue-panel delivery-queue-area flex-grow-1 d-flex flex-column">
                    <div class="delivery-queue flex-grow-1">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                            <p>La cola está vacía</p>
                            <p class="pd-empty-hint">Arrastra fichas aquí o usa doble clic</p>
                        </div>
                    </div>
                    <div class="deliver-footer">
                        <button type="button" class="btn btn-clear-queue clear-queue-btn" disabled>
                            <i class="fas fa-trash-alt me-1"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-red deliver-button" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Entregar a Gesi
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</section>

{{-- SECCIÓN SISCO --}}
<section id="section-sisco" class="content-section env-card pd-section d-none">
    <div class="pd-split">

        {{-- Panel: Búsqueda --}}
        <div class="panel-col">
            <div class="panel-header">
                <div class="panel-header-left">
                    <div class="panel-icon search-icon"><i class="fas fa-search"></i></div>
                    <div>
                        <p class="panel-title">Buscar fichas SISCO</p>
                        <p class="panel-subtitle">Escribe el nombre, número o usuario</p>
                    </div>
                </div>
            </div>
            <div class="search-wrapper">
                <input type="text" class="search-input form-control"
                    placeholder="Buscar por nombre, consecutivo, usuario…">
                <button class="btn search-btn" type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="search-results">
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-search"></i></div>
                    <p>Realice una búsqueda para comenzar</p>
                </div>
            </div>
        </div>

        {{-- Flecha divisora --}}
        <div class="panel-divider">
            <div class="panel-divider-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>

        {{-- Panel: Cola de entrega --}}
        <div class="panel-col">
            <form class="delivery-form d-flex flex-column h-100" method="POST"
                action="{{ route('reviewconsecutives.update-status-sisco') }}">
                @csrf
                <input type="hidden" name="selected_ids" class="final-order">

                <div class="panel-header">
                    <div class="panel-header-left">
                        <div class="panel-icon queue-icon"><i class="fas fa-layer-group"></i></div>
                        <div>
                            <p class="panel-title">
                                Cola SISCO
                                <span class="badge rounded-pill bg-danger ms-1 queue-count">0</span>
                            </p>
                            <p class="panel-subtitle">Arrastra para reordenar</p>
                        </div>
                    </div>
                </div>

                <div class="queue-panel delivery-queue-area flex-grow-1 d-flex flex-column">
                    <div class="delivery-queue flex-grow-1">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                            <p>La cola está vacía</p>
                            <p class="pd-empty-hint">Arrastra fichas aquí o usa doble clic</p>
                        </div>
                    </div>
                    <div class="deliver-footer">
                        <button type="button" class="btn btn-clear-queue clear-queue-btn" disabled>
                            <i class="fas fa-trash-alt me-1"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-red deliver-button" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Entregar SISCO
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</section>

{{-- Templates --}}
<script id="ficha-template" type="text/template">
    <div class="ficha-item" data-id="{ID}">
        <div class="d-flex justify-content-between align-items-start">
            <div class="pd-ficha-body">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="pd-ficha-number">#{FILE_NUMBER}</span>
                    <span class="badge {STATUS_CLASS} px-2 pd-ficha-status">{STATUS_TEXT}</span>
                </div>
                <span class="pd-ficha-title">{TITLE}</span>
                <small class="pd-ficha-subtitle">{SUBTITLE}</small>
                <div class="pd-ficha-user-row">
                    <small class="pd-ficha-user">
                        <i class="fas fa-user-circle me-1"></i>{USER_NAME}
                    </small>
                </div>
            </div>
            <div class="d-flex flex-column align-items-end gap-2 ps-2 flex-shrink-0">
                <button type="button" class="btn remove-item-btn" style="display:none;">
                    <i class="fas fa-times fa-sm"></i>
                </button>
                <i class="fas fa-grip-vertical drag-handle fa-sm mt-1"></i>
            </div>
        </div>
    </div>
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    class DeliveryManager {
        constructor(sectionId, apiUrl) {
            this.section = document.getElementById(sectionId);
            this.apiUrl = apiUrl;
            this.queue = [];
            this.lastSelectedIndex = -1;
            this.template = document.getElementById('ficha-template').innerHTML;

            // Elementos
            this.$results = this.section.querySelector('.search-results');
            this.$queueContainer = this.section.querySelector('.delivery-queue');
            this.$searchInput = this.section.querySelector('.search-input');
            this.$searchBtn = this.section.querySelector('.search-btn');
            this.$countBadge = this.section.querySelector('.queue-count');
            this.$btnDeliver = this.section.querySelector('.deliver-button');
            this.$btnClear = this.section.querySelector('.clear-queue-btn');
            this.$finalInput = this.section.querySelector('.final-order');

            this.init();
        }

        init() {
            // 1. Disparar búsqueda al hacer Click
            this.$searchBtn.onclick = () => this.performSearch();

            // 2. Disparar búsqueda al presionar Enter en el input
            this.$searchInput.onkeydown = (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.performSearch();
                }
            };

            // Tecla Enter para mover ítems seleccionados de la búsqueda a la cola
            this.section.addEventListener('keydown', e => {
                if (e.key === 'Enter' && e.target.tagName !== 'INPUT') {
                    const selected = this.$results.querySelectorAll(
                        '.ficha-item.selected:not(.disabled-card)');
                    selected.forEach(el => this.addToQueue(JSON.parse(el.dataset.fullData)));
                }
            });

            // Sortable para reordenar la cola
            new Sortable(this.$queueContainer, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: () => this.syncOrder()
            });

            // Drag & Drop manual
            this.$queueContainer.ondragover = e => {
                e.preventDefault();
                this.$queueContainer.closest('.delivery-queue-area').classList.add('drag-over');
            };
            this.$queueContainer.ondragleave = () => this.$queueContainer.closest('.delivery-queue-area').classList.remove('drag-over');
            this.$queueContainer.ondrop = e => {
                e.preventDefault();
                this.$queueContainer.closest('.delivery-queue-area').classList.remove('drag-over');
                try {
                    const f = JSON.parse(e.dataTransfer.getData('application/json'));
                    this.addToQueue(f);
                } catch (err) {}
            };

            this.$btnClear.onclick = () => {
                if (confirm('¿Limpiar lista?')) {
                    this.queue = [];
                    this.renderQueue();
                }
            };

            const $form = this.section.querySelector('.delivery-form');
            $form.onsubmit = () => {
                this.$btnDeliver.disabled = true;
                this.$btnDeliver.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-2"></span> Procesando...`;
            };

            this.renderQueue();
        }

        performSearch() {
            const q = this.$searchInput.value.trim();
            if (!q) return;
            this.fetchResults(q);
        }

        async fetchResults(q) {
            this.$results.innerHTML =
                '<div class="empty-state"><div class="spinner-border text-danger" style="width:2rem;height:2rem;"></div><p class="mt-2">Consultando servidor…</p></div>';
            try {
                const res = await fetch(`${this.apiUrl}?query=${encodeURIComponent(q)}&_t=${Date.now()}`);
                if (!res.ok) throw new Error("Error en servidor");
                const data = await res.json();
                this.renderResults(data.fichas || []);
            } catch (e) {
                this.$results.innerHTML =
                    '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-exclamation-triangle"></i></div><p class="text-danger">Error de conexión con el controlador</p></div>';
            }
        }

        renderResults(fichas) {
            this.$results.innerHTML = fichas.length ? '' :
                '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-folder-open"></i></div><p>No se encontraron resultados</p></div>';
            this.lastSelectedIndex = -1;

            fichas.forEach((f, index) => {
                const card = this.createCard(f, false);
                card.dataset.index = index;
                card.dataset.fullData = JSON.stringify(f);

                card.onclick = (e) => this.handleSelection(e, card, index);
                card.ondblclick = () => this.addToQueue(f);
                card.setAttribute('draggable', 'true');
                card.ondragstart = e => e.dataTransfer.setData('application/json', JSON.stringify(f));

                this.$results.appendChild(card);
            });
            this.updateVisualMarkers();
        }

        handleSelection(e, card, index) {
            if (card.classList.contains('disabled-card')) return;
            const all = Array.from(this.$results.querySelectorAll('.ficha-item:not(.disabled-card)'));

            if (e.shiftKey && this.lastSelectedIndex !== -1) {
                const start = Math.min(index, this.lastSelectedIndex);
                const end = Math.max(index, this.lastSelectedIndex);
                all.forEach(el => {
                    const i = parseInt(el.dataset.index);
                    el.classList.toggle('selected', i >= start && i <= end);
                });
            } else if (e.ctrlKey || e.metaKey) {
                card.classList.toggle('selected');
            } else {
                all.forEach(el => el.classList.remove('selected'));
                card.classList.add('selected');
            }
            this.lastSelectedIndex = index;
        }

        createCard(f, inQueue) {
            const div = document.createElement('div');
            div.innerHTML = this.template
                .replace(/{ID}/g, f.id).replace(/{FILE_NUMBER}/g, f.file_number)
                .replace(/{TITLE}/g, f.environment_name).replace(/{SUBTITLE}/g, f.base_name)
                .replace(/{USER_NAME}/g, f.user_full_name).replace(/{STATUS_TEXT}/g, f.status_text)
                .replace(/{STATUS_CLASS}/g, f.status_class);

            const el = div.firstElementChild;
            if (inQueue) {
                const btn = el.querySelector('.remove-item-btn');
                btn.style.display = 'block';
                btn.onclick = () => this.removeFromQueue(f.id);
            }
            return el;
        }

        addToQueue(f) {
            if (this.queue.find(x => x.id === f.id)) return;
            this.queue.push(f);
            this.renderQueue();
        }

        removeFromQueue(id) {
            this.queue = this.queue.filter(x => x.id !== id);
            this.renderQueue();
        }

        renderQueue() {
            this.$queueContainer.innerHTML = this.queue.length ? '' :
                '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-inbox"></i></div><p>La cola está vacía</p><p style="font-size:0.7rem;color:#444;">Arrastra fichas aquí o usa doble clic</p></div>';
            this.queue.forEach(f => this.$queueContainer.appendChild(this.createCard(f, true)));

            this.$btnDeliver.disabled = !this.queue.length;
            this.$btnClear.disabled = !this.queue.length;
            this.$countBadge.innerText = this.queue.length;
            this.syncOrder();
            this.updateVisualMarkers();
        }

        syncOrder() {
            const ids = [...this.$queueContainer.querySelectorAll('.ficha-item')].map(el => el.dataset.id);
            this.$finalInput.value = ids.join(',');
        }

        updateVisualMarkers() {
            this.section.querySelectorAll('.search-results .ficha-item').forEach(el => {
                const exists = this.queue.some(f => f.id == el.dataset.id);
                el.classList.toggle('disabled-card', exists);
                if (exists) el.classList.remove('selected');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Instancias
        const consecutivosManager = new DeliveryManager('section-consecutivos',
            "{{ route('api.search-fichas') }}");
        const siscoManager = new DeliveryManager('section-sisco', "{{ route('api.search-fichas-sisco') }}");

        // Gestión de pestañas
        const sectionButtons = document.querySelectorAll('.btn-section-toggle');
        const sections = document.querySelectorAll('.content-section');

        sectionButtons.forEach(btn => {
            btn.onclick = function() {
                const target = this.dataset.target;
                sectionButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                sections.forEach(s => {
                    s.classList.toggle('d-none', s.id !== target);
                    if (s.id === target) {
                        s.style.opacity = '0';
                        setTimeout(() => s.style.opacity = '1', 50);
                    }
                });
            };
        });
    });
</script>
@endsection
