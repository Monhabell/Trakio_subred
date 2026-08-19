import AttributeInput from "./AttributeInput.js";
import barrios from "../../json_localidades/barrios.js";
import SelectSearch from "./SelectSearch.js";

export default class Listas extends AttributeInput {

    async barrios(inputLocalidadId, barrioInputId) {
        const inputLocalidad = document.getElementById(`valorControl${inputLocalidadId}`);

        inputLocalidad.addEventListener('change', ()=>{
            // selectSearch.set();
            let localidad = inputLocalidad.value;
            localidad = localidad[0] == 0 ? localidad[1] : localidad;
            
            if (this.insertOptionsList(barrios[localidad], true, barrioInputId)) {
                const selectSearch = new SelectSearch();
                selectSearch.insert([barrioInputId]);
            }
        });
    }

    /**
     * 
     * @param {Array} values 
     * @param {boolean} withSearch
     * @param {Array } onInputsIds
     */
    insertOptionsList(values = [], ...onInputsIds) {
        const inputs = onInputsIds.map((id) =>
            document.getElementById(`valorControl${id}`)
        );
    
        inputs.forEach((input) => {
            if (input) {
                const select = document.createElement("select");
                select.id = input.id;
                select.name = input.name;
                select.className = "form-control";
                select.style.width = "100%";
    
                values.forEach((value) => {
                    const option = document.createElement("option");
                    option.value = value;
                    option.textContent = value;
                    select.appendChild(option);
                });
    
                const inputValue = input.value.trim();
                if (values.includes(inputValue)) {
                    select.value = inputValue;
                }
    
                input.parentNode.replaceChild(select, input)
            }
        });

        return true;

    }

    async eps(idNumCampoEps, idNumCampoTipoAfiliacion){
        const tipoAfiliacion = document.getElementById(`valorControl${idNumCampoTipoAfiliacion}`);
        const options = Array.from(tipoAfiliacion.querySelectorAll("option"));
        const epsInput = document.getElementById(`valorControl${idNumCampoEps}`);        
    
        const epsSubsidiado = [
            'ALIANSALUD E.P.S S.A-S',
            'ASMET SALUD E.P.S S.A.S-S',
            'ASOCIACION INDIGENA DEL CAUCA E.P.S.I-S',
            'CAJACOPI E.P.S S.A.S-S',
            'CAPITAL SALUD E.P.S S.A.S-S',
            'CAPRESOCA E.P.S-S',
            'CAJA DE COMPENSACIÓN FAMILIAR DEL CHOCÓ-S',
            'COMFAORIENTE-S',
            'COOSALUD E.P.S S.A-S',
            'COMPENSAR E.P.S-S',
            'DUSAKAWI A.R.S.I-S',
            'EMSSANAR S.A.S-S',
            'E.P.S FAMILIAR DE COLOMBIA S.A.S-S',
            'E.P.S FAMISANAR S.A.S-S',
            'MALLAMAS E.P.S.I',
            'MUTUAL SER E.P.S-S',
            'NUEVA E.P.S S.A-S',
            'PIJAOS SALUD E.P.S.I-S',
            'SALUD TOTAL E.P.S S.A-S',
            'SANITAS S.A.S-S',
            'SAVIA SALUD E.P.S-S',
            'E.P.S SURAMERICANA S.A-S',
            'SERVICIO OCCIDENTAL DE SALUD S.A-S',
            'FUNDACIÓN SALUD MIA-S',
            'SALUD BOLÍVAR E.P.S S.A.S-S',
            'ANAS WAYUU EPSI-S',
        ]
    
        const epsContributivo = [
            'ALIANSALUD E.P.S S.A',
            'ANAS WAYUU E.P.S.I',
            'ASMET SALUD E.P.S S.A.S',
            'ASOCIACION INDIGENA DEL CAUCA E.P.S.I',
            'CAJACOPI E.P.S S.A.S',
            'CAPITAL SALUD E.P.S S.A.S',
            'CAPRESOCA E.P.S',
            'CAJA DE COMPENSACIÓN FAMILIAR DEL CHOCÓ',
            'COMFAORIENTE',
            'COOSALUD E.P.S S.A',
            'COMPENSAR E.P.S',
            'DUSAKAWI A.R.S.I',
            'EMSSANAR S.A.S',
            'E.P.S FAMILIAR DE COLOMBIA S.A.S',
            'E.P.S FAMISANAR S.A.S',
            'MALLAMAS E.P.S.I',
            'MUTUAL SER E.P.S',
            'NUEVA E.P.S S.A',
            'PIJAOS SALUD E.P.S.I',
            'SALUD TOTAL E.P.S S.A',
            'SANITAS S.A.S',
            'E.P.S SURAMERICANA S.A',
            'FUNDACIÓN SALUD MIA',
            'SALUD BOLÍVAR E.P.S S.A.S',
            'EMPRES.A.S PUBLICAS DE MEDELLIN',
            'SERVICIO OCCIDENTAL DE SALUD S.O.S',
            'FONDO PASIVO SOCIAL DE LOS FERROCARRILES NACIONALES',
            'COMFENALCO VALLE DE LA GENTE'
        ];
    
        const epsExcepcion = [
            'ECOPETROL',
            'UNIVERSIDADES PÚBLICAS',
            'FUERZAS MILITARES',
            'MAGISTERIO',
            'POLICÍA NACIONAL',
            'SANIDAD MILITAR',
            'SERVISALUD',
            'FIDUCENTRAL'
        ];
        
        const epsList = {
            [options[1].value]: epsSubsidiado,
            [options[2].value]: epsContributivo,
            [options[3].value]: epsExcepcion,
            [options[5].value]: [
                'NO ASEGURADO',
            ]
        }
    
        const epsListKeys = [...epsSubsidiado, ...epsContributivo, ...epsExcepcion, 'NO ASEGURADO'];
        
        const injectarEps = () => {
            const epsConcuerda = epsListKeys.includes(epsInput.value);

            if (!epsConcuerda) {
                const newSpan = document.createElement("span");
                newSpan.innerHTML = `<p style="color: red">Ingresar <b>${epsInput.value}</b></p>`;
                epsInput.parentElement.appendChild(newSpan);
            };

            this.insertOptionsList([...new Set(epsList[tipoAfiliacion.value])], idNumCampoEps);

            if(!epsConcuerda) this.backgroundError(epsInput.id);
        }
    
        injectarEps();
    
        tipoAfiliacion.addEventListener("change", () =>{
            this.insertOptionsList([...new Set(epsList[tipoAfiliacion.value])], idNumCampoEps);
        });
    }

    /**
     * 
     * @param {Array} inputsId 
     * @returns 
     */

    async digitadores(inputsId){
        if (!Array.isArray(inputsId)) {
            console.error("El argumento `inputsId` debe ser un array.");
            return;
        }

        // try {
        //     const url = 'http://127.0.0.1:8000/api/v2/users';
        //     const response = await fetch(url);
        //     console.log(response);
        //     console.log(JSON.parse(sessionStorage.getItem('loginInfo')));
            
            
        // } catch (error) {
            
        // }

        const digitadores = [
            "",
            "ALBA SABOGAL",
            "ANGIE JIMENEZ",
            "CAMILA YOPASA",
            "CAROLINA GARAY",
            "CRISTHIAN PARRA",
            "DANIEL MANCHEGO",
            "FELIPE ARIAS",
            "GINA GOMEZ",
            "JENNY ROJAS",
            "LAURA VELASCO",
            "LUIS ESGUERRA",
            "MARCELA CAPERA",
            "MARILYN ARANGO",
            "MARTHA RIAÑO",
            "PAOLA GAMBOA",
            "MARIANA OCHOA",
            "DAVID ALVAREZ",
            "NICOLAS ABRIL",
            "JUAN CASTELLANOS",
            "JESISSON ANDREY",
            "SANTIAGO RODRIGUEZ",
            "JUAN GUTIERREZ",
            "ANDRES OROZCO",
            "MIGUEL GALINDO",
            "SANTIAGO ARANZAZU",
            "JUAN CASTELLANOS",
            "NICOLÁS ABRIL",
        ];

        this.insertOptionsList(digitadores, ...inputsId);
    }
}
