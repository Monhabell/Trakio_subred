export default class Bola {

    constructor(x, y, velocidad){
        this.x = x;
        this.y = y;
        // Tamaño aleatorio (1 - 2.8): da sensación de profundidad/parallax
        this.radio = 1 + Math.random() * 1.8;
        this.dx = (Math.random() * 2) - 1;
        this.dy = (Math.random() * 2) - 1;
        // Las partículas más grandes (cercanas) se mueven un poco más lento
        this.velocidad = velocidad * (1.3 - this.radio / 4);
        this.alpha = 0.45 + Math.random() * 0.45;
    }

    dibujar(ctx){
        ctx.beginPath();
        ctx.fillStyle = `rgba(255, 90, 95, ${this.alpha})`;
        ctx.arc(this.x, this.y, this.radio, 0, Math.PI*2);
        ctx.fill();
        ctx.closePath();
    }

    /**
     * Efecto "planetas" (asistencia gravitacional): la fuerza es sobre todo
     * TANGENCIAL (perpendicular a la línea que las une), así que al pasar
     * cerca de otra partícula la trayectoria se curva/gira en vez de ser
     * arrastrada directamente hacia ella. Eso evita que terminen
     * agrupándose, que es lo que pasa con una atracción puramente radial.
     * Solo hay un pequeño empujón radial (atracción leve) para que se
     * note como "gravedad" y no solo como un giro artificial.
     */
    gravitar(otra) {
        const dx = otra.x - this.x;
        const dy = otra.y - this.y;
        const distancia = Math.sqrt(dx * dx + dy * dy) || 1;
        const radioInfluencia = 110;
        const radioMinimo = 16;
        const angle = Math.atan2(dy, dx);

        if (distancia <= radioMinimo) {
            // Choque casi directo: empuja hacia afuera para que no se superpongan
            const fuerza = ((radioMinimo - distancia) / radioMinimo) * 0.1;
            this.dx -= Math.cos(angle) * fuerza;
            this.dy -= Math.sin(angle) * fuerza;
            return;
        }

        if (distancia < radioInfluencia) {
            const intensidad = (radioInfluencia - distancia) / radioInfluencia;
            const fuerzaRadial = intensidad * 0.008;
            const fuerzaTangencial = intensidad * 0.05;

            // Vector tangencial = perpendicular al vector radial (lo que produce el giro)
            const tangX = -Math.sin(angle);
            const tangY = Math.cos(angle);

            this.dx += Math.cos(angle) * fuerzaRadial + tangX * fuerzaTangencial;
            this.dy += Math.sin(angle) * fuerzaRadial + tangY * fuerzaTangencial;
        }
    }

    mover(bounds, mouse) {
        // Frena ligeramente la velocidad acumulada por gravitar() y la limita
        // a un máximo, para que las trayectorias se curven sin volverse
        // caóticas tras varios encuentros cercanos con otras partículas.
        this.dx *= 0.999;
        this.dy *= 0.999;
        const velocidadActual = Math.sqrt(this.dx ** 2 + this.dy ** 2);
        const velocidadMaxima = 1.8;
        if (velocidadActual > velocidadMaxima) {
            this.dx = (this.dx / velocidadActual) * velocidadMaxima;
            this.dy = (this.dy / velocidadActual) * velocidadMaxima;
        }

        // Calcular la distancia entre la bola y el mouse
        const distanciaX = this.x - mouse.x;
        const distanciaY = this.y - mouse.y;
        const distancia = Math.sqrt(distanciaX ** 2 + distanciaY ** 2);
    
        // Limitar el rango de influencia del mouse
        const rangoInfluencia = 150; // Distancia máxima para el efecto del mouse
        if (distancia < rangoInfluencia) {
            // Calcular la fuerza de alejamiento inversamente proporcional a la distancia
            const fuerza = (rangoInfluencia - distancia) / 4 ;
    
            // Calcular el ángulo hacia el mouse
            const angle = Math.atan2(distanciaY, distanciaX);
    
            // Crear un vector de alejamiento proporcional a la fuerza
            const mouseDx = Math.cos(angle) * fuerza;
            const mouseDy = Math.sin(angle) * fuerza;
    
            // Actualizar la posición con el desplazamiento original y el alejamiento del mouse
            this.x += (this.dx + mouseDx) * this.velocidad;
            this.y += (this.dy + mouseDy) * this.velocidad;
        } else {
            // Movimiento normal si está fuera del rango de influencia del mouse
            this.x += this.dx * this.velocidad;
            this.y += this.dy * this.velocidad;
        }
    
        // Evitar que las bolas salgan del área visible
        if (this.x + this.radio > bounds.width) {
            this.x = bounds.width - this.radio; // Asegurar que permanezca dentro
            this.dx *= -1; // Rebote horizontal
        }
        if (this.x - this.radio < 0) {
            this.x = this.radio; // Asegurar que permanezca dentro
            this.dx *= -1; // Rebote horizontal
        }
        if (this.y + this.radio > bounds.height) {
            this.y = bounds.height - this.radio; // Asegurar que permanezca dentro
            this.dy *= -1; // Rebote vertical
        }
        if (this.y - this.radio < 0) {
            this.y = this.radio; // Asegurar que permanezca dentro
            this.dy *= -1; // Rebote vertical
        }
    }
}