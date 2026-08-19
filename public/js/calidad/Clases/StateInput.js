export default  class StateInput {
    /**
     * Actualizar el estado visual de un campo en base a si hay un error.
     * @param {HTMLElement} campo - El campo HTML a modificar.
     * @param {boolean} esError - Indica si el campo tiene un error.
     */
    actualizarEstadoCampo(campo, esError) {
        if (!campo) {
            console.error("Campo no encontrado para actualizar estado.");
            return;
        }

        if (esError) {
            campo.style.backgroundColor = "#ff0000";
            campo.style.color = "#FDFEFE";
            this.toggleBotonGuardar(true);
        } else {
            campo.style.backgroundColor = "#36ff00";
            campo.style.color = "#000";
            this.toggleBotonGuardar(false);
        }
    }

    /**
     * Activar o desactivar el botón de guardar.
     * @param {boolean} deshabilitar - Indica si el botón debe estar deshabilitado.
     */
    toggleBotonGuardar(deshabilitar) {
        const boton = document.getElementById('botonActualizarInformacion');
        if (!boton) {
            console.error("Botón 'Actualizar Información' no encontrado.");
            return;
        }
        boton.disabled = deshabilitar;
    }

    /**
     * Habilitar o deshabilitar un conjunto de campos.
    */
    toggleCampos(camposIds, deshabilitar) {
        camposIds.forEach(id => {
            const campo = document.getElementById(`valorControl${id}`);
            if (campo) {
                campo.disabled = deshabilitar;
            } else {
                console.warn(`Campo con ID valorControl${id} no encontrado.`);
            }
        });
    }
}
