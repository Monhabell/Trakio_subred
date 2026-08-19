@auth
    @if (!auth()->user()->hasRated())
        <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">¡Queremos saber tu opinión!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>¿Cómo calificarías tu experiencia en nuestra plataforma?</p>

                        <form id="ratingForm">
                            <div class="mb-3 rating-css">
                                <select class="form-select" id="score" required>
                                    <option value="" selected disabled>Selecciona una calificación</option>
                                    <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                                    <option value="4">⭐⭐⭐⭐ Muy buena</option>
                                    <option value="3">⭐⭐⭐ Regular</option>
                                    <option value="2">⭐⭐ Mala</option>
                                    <option value="1">⭐ Muy mala</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" id="comment" placeholder="¿Algún comentario extra? (Opcional)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Enviar Calificación</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Mostrar el modal automáticamente
                var ratingModalEl = document.getElementById('ratingModal');
                // Verificar si el elemento existe para evitar errores de consola si Bootstrap falla
                if (ratingModalEl) {
                    var myModal = new bootstrap.Modal(ratingModalEl);
                    myModal.show();

                    // 2. Manejar el envío
                    document.getElementById('ratingForm').addEventListener('submit', function(e) {
                        e.preventDefault();

                        let rating = document.getElementById('score').value;
                        let comment = document.getElementById('comment').value;
                        let submitBtn = this.querySelector('button[type="submit"]');

                        // Deshabilitar botón para evitar doble envío
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Enviando...';

                        axios.post("{{ route('rating.store') }}", {
                                rating: rating,
                                comment: comment
                            })
                            .then(function(response) {
                                myModal.hide();
                                // Usar SweetAlert si lo tienes, o alert normal
                                alert('¡Gracias por calificar!');
                            })
                            .catch(function(error) {
                                console.error(error);
                                alert('Error al guardar. Intenta de nuevo.');
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Enviar Calificación';
                            });
                    });
                }
            });
        </script>
    @endif
@endauth
