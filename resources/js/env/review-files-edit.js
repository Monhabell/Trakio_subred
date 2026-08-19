function updateFileNumber() {
    const inputLocalidad = document.querySelectorAll(".input-edit-localidad");

    inputLocalidad.forEach((input) => {
        input.addEventListener("change", async (e) => {
            const spanFileNumber = document.getElementById(`file-number-${e.target.dataset.fileId}`);
            const inputFileNumber = document.getElementById(`file-number-input-${e.target.dataset.fileId}`);
            const localidadName = e.target.options[e.target.selectedIndex].text;
            const newFileNumber = `1${getLocalidadName(localidadName)}${spanFileNumber.textContent.slice(3,spanFileNumber.textContent.length)}`;
            inputFileNumber.value = newFileNumber;
            spanFileNumber.textContent = newFileNumber;
            spanFileNumber.classList.add("data-changed");
        });
        
    });
}

function getLocalidadName(localidadName) {
    const localidades = {
        'usaquen': '01',
        'chapinero': '02',
        'santa fe': '03',
        'san cristóbal': '04',
        'usme': '05',
        'tunjuelito': '06',
        'bosa': '07',
        'kennedy': '08',
        'fontibón': '09',
        'engativa': '10',
        'suba': '11',
        'barrios unidos': '12',
        'teusaquillo': '13',
        'los mártires': '14',
        'antonio nariño': '15',
        'puente aranda': '16',
        'la candelaria': '17',
        'rafael uribe uribe': '18',
        'ciudad bolivar': '19',
        'sumapaz': '20',
    };

    return localidades[localidadName.toLowerCase()] ?? 'Desconocido';
}

updateFileNumber();
