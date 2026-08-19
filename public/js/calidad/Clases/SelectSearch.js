export default class SelectSearch {
    constructor() {
        this.nuevosDatos = []; // Se cargará desde Laravel
    }

    async insert(campos, active = false) {
        try {
            for (let campo = 0; campo < campos.length; campo++) {
                const campoId = `valorControl${campos[campo]}`;                
                $(`#${campoId}`).select2({
                    placeholder: "Seleccione",
                    allowClear: true,
                    tags: active
                }).on('select2:select', async (e) => {
                    const nuevoDato = e.params.data.text;
                    if (active && !this.nuevosDatos.includes(nuevoDato)) {
                        this.nuevosDatos.push(nuevoDato);
                        await this.guardarDatos();
                    }
                });
                
            }
        } catch (error) {
            console.error("Error en insertBuscador:", error);
        }
    }

    /**
     * 
     * @param {string} selectId 
     */
    set(selectId){
        const select = document.getElementById(`valorControl${selectId}`);
        if (!select) return;

        const newInput = document.createElement('input');
        newInput.placeholder = "Buscar...";
        newInput.type = "text";
        newInput.style.width = "100%";

        select.insertBefore(newInput, option);
        const originalOptions = Array.from(select.options);

        newInput.addEventListener('keyup', () => {
            const search = newInput.value.toLowerCase();
            select.innerHTML = "";
    
            originalOptions.forEach(opt => {
                if (opt.text.toLowerCase().includes(search)) {
                    select.appendChild(opt);
                }
            });
        });
    }

    async dataSave() {
        //guardar datos en una api 
        // Por ejemplo:
        try {
            const url = 'http://127.0.0.1:8000/api/colegios'; // api desde trakio
            await axios.post(url, {nombre: this.nuevosDatos});
        } catch (error) {
            console.error("Error al guardar los datos:", error);
        }
    }
}
