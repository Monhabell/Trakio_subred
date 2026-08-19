@extends('layouts.dig.navigation')

@section('main')

    <div id="errors-root">
    @if ($fichas->isNotEmpty())
        @php
            $totalPendingFields = $fichas->sum(fn ($f) => $f->pendingFields->count());
        @endphp

        <div class="err-page-header">
            <div>
                <span class="err-page-tag">// Corrección de digitación</span>
                <h1 class="err-page-title">Mis errores pendientes</h1>
            </div>
            <div class="err-summary">
                <div class="err-summary-num">{{ $fichas->count() }}</div>
                <div class="err-summary-label">{{ Str::plural('ficha', $fichas->count()) }} con novedades</div>
            </div>
            <div class="err-summary">
                <div class="err-summary-num">{{ $totalPendingFields }}</div>
                <div class="err-summary-label">{{ Str::plural('campo', $totalPendingFields) }} por corregir</div>
            </div>
        </div>

        <p class="advertesiment">Revise detenidamente cada ficha y marque un campo como corregido solo después de haber realizado el ajuste en el sistema.</p>

        <div class="fichas-grid">
            @foreach ($fichas as $ficha)
                @php
                    $total = $ficha->total_fields_count ?: $ficha->pendingFields->count();
                    $pending = $ficha->pendingFields->count();
                    $corrected = max($total - $pending, 0);
                    $progress = $total > 0 ? round(($corrected / $total) * 100) : 0;
                @endphp
                <article class="ficha-card section-errors" data-total-fields="{{ $total }}" data-pending-fields="{{ $pending }}">
                    <header class="ficha-card-header">
                        <div class="ficha-card-title">
                            <span class="ficha-icon"><i class="fas fa-file-lines"></i></span>
                            <div>
                                <h3>Ficha {{ $ficha->file_number }}</h3>
                                <div class="ficha-tags">
                                    <span class="ficha-tag"><i class="fas fa-layer-group"></i> {{ $ficha->errorImport->format->name ?? 'Sin base' }}</span>
                                    <span class="ficha-tag"><i class="fas fa-folder"></i> {{ $ficha->errorImport->section ?? 'Sin sección' }}</span>
                                    @if ($ficha->location_reference)
                                        <span class="ficha-tag ficha-tag-accent"><i class="fas fa-location-dot"></i> Nº usuario / Pág: {{ $ficha->location_reference }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="pending-count">{{ $pending }} {{ Str::plural('pendiente', $pending) }}</span>
                    </header>

                    @if ($ficha->observations)
                        <div class="ficha-observations">
                            <i class="fas fa-circle-info"></i>
                            <span>{{ $ficha->observations }}</span>
                        </div>
                    @endif

                    <div class="ficha-progress-wrap">
                        <div class="ficha-progress-bar"><div class="ficha-progress-fill" style="width: {{ $progress }}%"></div></div>
                        <span class="ficha-progress-label">{{ $corrected }}/{{ $total }} corregidos</span>
                    </div>

                    <div class="ficha-fields">
                        @foreach ($ficha->pendingFields as $field)
                            @php $isEmpty = trim((string) $field->value) === ''; @endphp
                            <div class="field-row">
                                <div class="field-info">
                                    <span class="field-label">{{ $field->field->label }}</span>
                                    <span class="field-issue {{ $isEmpty ? 'field-issue-empty' : 'field-issue-incorrect' }}">
                                        <i class="fas {{ $isEmpty ? 'fa-circle-exclamation' : 'fa-triangle-exclamation' }}"></i>
                                        {{ $isEmpty ? 'Campo vacío' : 'Dato incorrecto' }}
                                    </span>
                                    @if (trim((string) $field->observacion) !== '')
                                        <span class="field-note">
                                            <i class="fas fa-comment-dots"></i> {{ $field->observacion }}
                                        </span>
                                    @endif
                                </div>
                                <label class="fix-toggle">
                                    <input type="checkbox" class="update-checkbox" data-field-id="{{ $field->id }}">
                                    <span class="fix-toggle-track"><span class="fix-toggle-thumb"><i class="fas fa-check"></i></span></span>
                                    <span class="fix-toggle-label">Corregido</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    @else
    <div class="feed-container">
        <div class="feed-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="feed-text">¡Felicidades! 🎉</div>
        <div class="feed-subtext">En este momento no tienes errores pendientes.</div>
    </div>
    @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const root = document.getElementById("errors-root");

            const showEmptyState = () => {
                root.innerHTML = `
                    <div class="feed-container">
                        <div class="feed-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="feed-text">¡Felicidades! 🎉</div>
                        <div class="feed-subtext">En este momento no tienes errores pendientes.</div>
                    </div>
                `;
            };

            root.querySelectorAll(".update-checkbox").forEach(function(checkbox) {
                checkbox.addEventListener("change", function() {
                    let fieldId = this.dataset.fieldId;
                    let checkboxEl = this;

                    if (!this.checked) {
                        return;
                    }

                    const row = checkboxEl.closest(".field-row");
                    row.classList.add("is-saving");

                    axios.patch(`/error-fields/${fieldId}/resolve`, {
                        resolved: true
                    })
                    .then(response => {
                        if (!response.data.success) {
                            console.error("Error al actualizar el campo");
                            checkboxEl.checked = false;
                            row.classList.remove("is-saving");
                            return;
                        }

                        const card = checkboxEl.closest("article.ficha-card");

                        row.classList.remove("is-saving");
                        row.classList.add("is-resolved");

                        setTimeout(() => {
                            row.remove();

                            const remaining = card.querySelectorAll(".field-row").length;
                            const total = parseInt(card.dataset.totalFields, 10) || 0;
                            const corrected = Math.max(total - remaining, 0);
                            const progressPct = total > 0 ? Math.round((corrected / total) * 100) : 0;

                            const fill = card.querySelector(".ficha-progress-fill");
                            if (fill) fill.style.width = progressPct + "%";

                            const progressLabel = card.querySelector(".ficha-progress-label");
                            if (progressLabel) progressLabel.textContent = `${corrected}/${total} corregidos`;

                            const badge = card.querySelector(".pending-count");
                            if (badge) badge.textContent = remaining + (remaining === 1 ? " pendiente" : " pendientes");

                            if (remaining === 0) {
                                card.classList.add("is-complete");
                                setTimeout(() => {
                                    card.remove();
                                    if (root.querySelectorAll("article.ficha-card").length === 0) {
                                        showEmptyState();
                                    }
                                }, 400);
                            }
                        }, 350);
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        checkboxEl.checked = false;
                        row.classList.remove("is-saving");
                    });
                });
            });
        });
    </script>
@endsection
