<!-- Modal tyc-->
<div class="modal fade" id="disclaimer_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">    
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            @if ($errors->any())
            <div class="alert alert-danger mb-0">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="modal-header ps-4 bg-dark rounded-0 border-0">
                AVISO DE USO Y TRATAMIENTO DE DATOS PERSONALES
            </div>

            <div class="modal-body tyc border-1 text-black p-4">
                <div class="p-4 shadow-inset rounded-2">
                    <h3 class="fw-bold">Términos y Condiciones de Trakio.pro:</h3>
                    <p>Trakio.pro es una plataforma de gestión diseñada para estandarizar y mejorar los procesos internos dentro de las subredes, con el objetivo de proporcionar herramientas que faciliten el control del talento humano y la automatización de la gestión de la información. <b>Es
                            importante aclarar que
                            Trakio.pro NO forma parte de los aplicativos oficiales designados por
                            la Subred
                            Norte</b>. A
                        continuación, se detallan los términos y condiciones de uso:</p>
                    <ol>
                        <li class="fw-bold">Uso Voluntario y Responsabilidad del Usuario</li>
                        <p>El uso de Trakio.pro es completamente voluntario. Al aceptar los términos
                            y condiciones,
                            el
                            usuario asume la responsabilidad de revisar y verificar toda la
                            documentación generada a
                            través de la plataforma antes de presentarla a cualquier entidad
                            externa. Trakio.pro no
                            garantiza que los documentos sean aceptados automáticamente por terceros
                            o por entidades
                            certificadoras, y no se responsabiliza por rechazos, inconsistencias, o
                            inconvenientes
                            que
                            surjan debido al uso de la herramienta.</p>
                        <li class="fw-bold">Fallas Técnicas y Documentación Manual</li>
                        <p>En caso de que Trakio.pro presente fallas técnicas o modificaciones que
                            impidan la
                            generación
                            de la documentación requerida, el contratista será responsable de crear
                            dicha
                            documentación
                            de manera manual.</p>
                        <li class="fw-bold">Protección de Datos Personales (Ley 1581 de 2012)</li>
                        <p>Los datos ingresados a trakio.pro se trataran de acuerdo a la ley de
                            Protección de Datos
                            Personales o Ley 1581 de 2012 que reconoce y protege el derecho que
                            tienen todas las
                            personas a conocer, actualizar y rectificar las informaciones que se
                            hayan recogido
                            sobre
                            ellas en bases de datos o archivos que sean susceptibles de tratamiento
                            por entidades de
                            naturaleza pública o privada.</p>
                    </ol>
                </div>

                <form class="w-100 mt-3 d-flex flex-center" action="{{ route('accept.tyc') }}" method="POST">
                    @csrf
                    <input type="submit" class="btn btn-danger" id="btnTyc" value="ACEPTAR TÉRMINOS Y CONDICIONES">
                </form>
            </div>
        </div>
    </div>
</div>