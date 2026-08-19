export default class String {
    pattern(campos) {
        const patron = "^[A-ZÁÉÍÓÚÑ\\s]+$";
        for (let campo of campos) { // Recorrer los IDs de los inputs
            const input = document.getElementById(`valorControl${campo}`);
            if (!input) {
                console.warn(`El campo con ID "${campo}" no existe.`);
                continue; // Evita interrumpir el ciclo si un campo no existe
            }
            input.setAttribute("pattern", patron);
        }
        return true;
    }
}
