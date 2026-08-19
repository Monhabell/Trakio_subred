if (document.getElementById("filter-profesional")) {
    $("#filter-profesional").select2({
        theme: "classic",
        placeholder: "Buscar profesional...",
        allowClear: true,
        width: "100%",
    });
}

if (existElement('selected-form')) {
    selectFiles();
};

function selectFiles() {
    const fichas = document.querySelectorAll(".ficha-item.selectable");
    const selectedIdsInput = document.getElementById("selected-ids");
    const countSelectedDisplay = document.getElementById("selected-count");
    const spanCountNumber = document.getElementById("count-number");
    const APPROVE_BUTTON = document.getElementById("approve-button");
    const REJECT_BUTTON = document.getElementById("reject-button");
    const DELIVER_PROFESSIONAL_BUTTON = document.getElementById("deliver-professional-button");
    const DELIVER_BUTTON = document.getElementById("deliver-button");
    const EDIT_BUTTON = document.getElementById("edit-button");
    const selectedIds = new Set();
    let lastSelectedIndex = null;

    /**
     * Actualiza el valor del input oculto, el contador y los botones.
     */
    const manageSelectedIds = (selectedIds) => {
        selectedIdsInput.value = Array.from(selectedIds).join(",");

        if (selectedIds.size > 0) {
            countSelectedDisplay.classList.remove("d-none");
            spanCountNumber.textContent = selectedIds.size;
            if (APPROVE_BUTTON) APPROVE_BUTTON.disabled = false;
            if (REJECT_BUTTON) REJECT_BUTTON.disabled = false;
            if (DELIVER_PROFESSIONAL_BUTTON) DELIVER_PROFESSIONAL_BUTTON.disabled = false;
            if (DELIVER_BUTTON) DELIVER_BUTTON.disabled = false;
            if (EDIT_BUTTON) EDIT_BUTTON.disabled = selectedIds.size > 20;
        } else {
            countSelectedDisplay.classList.add("d-none");
            if (APPROVE_BUTTON) APPROVE_BUTTON.disabled = true;
            if (REJECT_BUTTON) REJECT_BUTTON.disabled = true;
            if (DELIVER_PROFESSIONAL_BUTTON) DELIVER_PROFESSIONAL_BUTTON.disabled = true;
            if (DELIVER_BUTTON) DELIVER_BUTTON.disabled = true;
            if (EDIT_BUTTON) EDIT_BUTTON.disabled = true;
        }
    };

    const updateWatermarks = () => {
        fichas.forEach((ficha) => {
            const wm = ficha.querySelector(".ficha-watermark");
            if (wm) wm.textContent = "";
        });

        Array.from(selectedIds).forEach((id, i) => {
            const ficha = document.querySelector(`.ficha-item[data-id="${id}"]`);
            if (ficha) {
                const wm = ficha.querySelector(".ficha-watermark");
                if (wm) wm.textContent = i + 1;
            }
        });
    };

    fichas.forEach((ficha, index) => {
        ficha.addEventListener("click", (e) => {
            const id = ficha.dataset.id;

            if (e.shiftKey && lastSelectedIndex !== null) {
                // Selección múltiple con Shift
                const start = Math.min(lastSelectedIndex, index);
                const end = Math.max(lastSelectedIndex, index);
                for (let i = start; i <= end; i++) {
                    fichas[i].classList.add("selected-file-review");
                    selectedIds.add(fichas[i].dataset.id);
                }
            } else {
                ficha.classList.toggle("selected-file-review");
                if (ficha.classList.contains("selected-file-review")) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
                lastSelectedIndex = index;
            }

            manageSelectedIds(selectedIds);
            updateWatermarks();
        });
    });

    const submitForm = () => {
        const inputStatus = document.getElementById('input-action');
        const filesForm = document.getElementById('selected-form');

        [APPROVE_BUTTON, REJECT_BUTTON, DELIVER_PROFESSIONAL_BUTTON, DELIVER_BUTTON].forEach(button => {
            if (!button) return;
            button.addEventListener("click", (e) => {
                e.preventDefault();
                inputStatus.value = button.dataset.action;

                const messageConfirm = `¿Estás seguro de ${stringStatus(button.dataset.action)} ${selectedIds.size} ficha(s)?`

                if (!confirm(messageConfirm)) return;
                filesForm.submit();
            });
        });
    }

    const editFiles = () => {
        if(!EDIT_BUTTON) return;
        EDIT_BUTTON.addEventListener("click", async (e) => {
            e.preventDefault();
            const QueryModule = await import('../modules/Queries');
            const Query = new QueryModule.default();
            Query.postData(route('reviewconsecutives.edit'), { ids: Array.from(selectedIds) });
        });
    }

    editFiles();
    submitForm();
}

function stringStatus(status) {
    switch (status) {
        case '2':
            return 'aprobar';
        case '3':
            return 'entregar a GESI';
        case '10':
            return 'rechazar';
        case '6':
            return 'entregar al profesional';
        default:
            return '';
    }
}

function customPagination() {
    const selectPerPage = document.getElementById('per-page');
    if (!selectPerPage) return;

    selectPerPage.addEventListener('change', () => {
        const perPage = selectPerPage.value;
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', perPage);
        params.set('page', 1);
        window.location.search = params.toString();
    });
}

function filterPanel() {
    const panel = document.getElementById('filter-panel');
    if(!panel) return;
    const overlay = document.getElementById('overlay');
    const openBtn = document.querySelectorAll('.open-filters');
    const closeBtn = document.getElementById('close-filters');

    openBtn.forEach(btn => {
        btn.addEventListener('click', () => {
            panel.classList.add('active');
            overlay.classList.add('active');
        });
    });

    closeBtn.addEventListener('click', () => {
        panel.classList.remove('active');
        overlay.classList.remove('active');
    });

    overlay.addEventListener('click', () => {
        panel.classList.remove('active');
        overlay.classList.remove('active');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    customPagination();
    if(existElement('filter-panel')) {
        filterPanel();
    }
});
