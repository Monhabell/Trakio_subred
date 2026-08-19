import { createAnimations } from "./animations.js"
import { monedas } from "./monedas.js"

let score = 0; // Variable global para la puntuación
let vidas = 3;
const config = {
    type: Phaser.AUTO,
    width: 650,
    height: 380,
    backgroundColor: '#049cd8',
    parent: 'game',
    physics: {
        default: 'arcade',
        arcade: {
            gravity: { y: 300 },
            debug: false
        }
    },
    scene: {
        preload, // se ejecuta para precargar recursos
        create, // se ejecuta cuando el juego comienza
        update // se ejecuta en cada frame
    }
}

new Phaser.Game(config)

function preload() {

    this.load.image('background', '../../game/assets/fondo.png');
    this.load.image('cloud1', '../../game/assets/scenery/overworld/cloud1.png');
    //this.load.spritesheet('mascotaGesi', 'assets/mascotaGesi1.png', { frameWidth: 41.6, frameHeight: 56 });
    this.load.spritesheet('mascotaGesi', '../../game/assets/mascotaGesiFinal.png', { frameWidth: 128, frameHeight: 128 });
    this.load.spritesheet('mascotaGesiload', '../../game/assets/mascotaload.png', { frameWidth: 128, frameHeight: 128 });
    this.load.spritesheet('mascotaGesimuerto', '../../game/assets/Dead_mascota.png', { frameWidth: 128, frameHeight: 128 });

    this.load.spritesheet('saltar', '../../game/assets/saltar.png', { frameWidth: 128, frameHeight: 128 });
    this.load.spritesheet('arbol', '../../game/assets/scenery/arbol1.png', { frameWidth: 208, frameHeight: 191 });
    this.load.spritesheet('arbol2', '../../game/assets/scenery/arbol2.png', { frameWidth: 208, frameHeight: 191 });

    this.load.spritesheet('indicacion', '../../game/assets/scenery/1.png', { frameWidth: 208, frameHeight: 191 });

    this.load.spritesheet('arbusto', '../../game/assets/scenery/arbusto.png', { frameWidth: 208, frameHeight: 191 });

    this.load.spritesheet('lava_falling', '../../game/assets/scenery/lava_callendo.png', {
        frameWidth: 126,  // Ajusta según el ancho de cada frame en la hoja de sprites
        frameHeight: 129  // Ajusta según la altura de cada frame en la hoja de sprites
    });


    this.load.spritesheet('portal', '../../game/assets/scenery/door.png', { frameWidth: 128, frameHeight: 100 });
    this.load.spritesheet('rueda', '../../game/assets/trampa_circular.png', { frameWidth: 313, frameHeight: 316 });


    this.load.image('suelo', '../../game/assets/scenery/piso1.png');
    this.load.image('sueloflotante', '../../game/assets/scenery/piso_flotante.png');
    this.load.image('columna', '../../game/assets/scenery/tile5.png');
    this.load.image('sueloflota', '../../game/assets/scenery/tile34.png');



    this.load.image('suelo2', '../../game/assets/scenery/trap2.png');
    this.load.image('suelo3', '../../game/assets/scenery/piso.png');
    this.load.image('door', '../../game/assets/scenery/door.png');



    this.load.spritesheet('lava', '../../game/assets/scenery/lava1.png', { frameWidth: 64, frameHeight: 64 });

    this.load.audio('gameover', '../../game/assets/sound/music/gameover.mp3');
    // cargar enemigos
    this.load.spritesheet('malo', '../../game/assets/entities/underground/Run.png', { frameWidth: 128, frameHeight: 128 });
    this.load.spritesheet('maloDead', '../../game/assets/Dead.png', { frameWidth: 128, frameHeight: 128 });
    // cargar monedas
    this.load.spritesheet('coins', '../../game/assets/collectibles/coin.png', { frameWidth: 16, frameHeight: 16 });

    // sonido de matar
    this.load.audio('matar', '../../game/assets/sound/effects/matar.wav');
    this.load.audio('moneda', '../../game/assets/sound/effects/coin.mp3');


}

function create() {
    createAnimations(this);
    let cantidad_fondo = 10;

    // Define la posición inicial de la primera lava
    let inical = 190;//1350

    // El ancho de cada bloque de lava (ajusta según tu imagen)
    let anchofondo = 750; // Por ejemplo, si cada imagen de lava mide 64 píxeles de ancho

    for (let i = 0; i < cantidad_fondo; i++) {
        // Crear cada bloque de lava en una posición consecutiva
        this.lava = this.add.image(inical + (i * anchofondo), 200, 'background')
            .setScale(0.9)
        //.setSize(64, 64).setOffset(35, 15)
        //.refreshBody();
    }
    let door = this.add.sprite(1150, 155, 'portal');
    door.setFrame(0);
    door.setScale(1);
    door.anims.play('portalopen', true)
    door.setFlipX(false);


    this.scoreText = this.add.text(180, 20, `Puntaje: ${score}`, { fontSize: '20px', fill: '#fff' });
    this.scoreText.setScrollFactor(0); // Mantener el texto fijo en la pantalla

    this.vidasText = this.add.text(50, 20, `Vidas: ${vidas}`, { fontSize: '20px', fill: '#fff' });
    this.vidasText.setScrollFactor(0); // Mantener el texto fijo en la pantalla


    this.floor = this.physics.add.staticGroup();

    // Crear las piezas del piso numero 1


    let cantPiso = 4;
    let positionInicial = 0;
    let anchoPiso = 65;

    for (let i = 0; i < cantPiso; i++) {
        // Crear cada bloque de lava en una posición consecutiva
        this.floor.create(positionInicial + (i * anchoPiso), config.height - 30, 'suelo').setOrigin(0, 0.5)
            .setScale(1)
            .refreshBody()
            .refreshBody()
            .setSize(65, 50).setOffset(1, 15); // mprimer piso
    }

    this.piso2 = this.floor.create(250, config.height - 30, 'suelo').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15); // mprimer piso

    this.piso3 = this.floor.create(350, config.height - 160, 'sueloflotante').setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15); // mprimer piso
    this.piso3.setVisible(false);

    let cantPiso2 = 9;
    let positionInicial2 = 500;
    let anchoPiso2 = 65;

    for (let i = 0; i < cantPiso2; i++) {

        this.floor.create(positionInicial2 + (i * anchoPiso2), config.height - 30, 'suelo').setOrigin(0, 0.5)
            .setScale(1)
            .refreshBody()
            .refreshBody()
            .setSize(65, 50).setOffset(1, 15); // mprimer piso
    }

    this.floor.create(850, config.height - 160, 'sueloflotante').setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15); // mprimer piso

    // this.floor.create(990, config.height - 260, 'sueloflotante').setScale(1)
    //     .refreshBody()
    //     .refreshBody()
    //     .setSize(65, 50).setOffset(1, 15); // mprimer piso



    this.floor.create(1200, config.height - 30, 'columna').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 0); // mprimer piso

    this.floor.create(1200, config.height - 90, 'columna').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 0); // mprimer piso

    this.floor.create(1200, config.height - 150, 'columna').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 0); // mprimer piso

    this.floor.create(1200, config.height - 210, 'suelo').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15);

    this.floor.create(1260, config.height - 210, 'sueloflota').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15);



    this.floor.create(1150, config.height - 155, 'sueloflotante').setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15); // mprimer piso



    this.piso7 = this.floor.create(1450, config.height - 155, 'suelo2').setOrigin(0, 0.5).setScale(1).refreshBody().setSize(80, 60).setOffset(25, 35);
    this.piso8 = this.floor.create(1640, config.height - 155, 'suelo2').setOrigin(0, 0.5).setScale(1).refreshBody().setSize(80, 60).setOffset(25, 35);
    this.piso9 = this.floor.create(1780, config.height - 155, 'suelo2').setOrigin(0, 0.5).setScale(1).refreshBody().setSize(80, 60).setOffset(25, 35);

    this.lavaes = this.physics.add.group();
    let cantidadLava = 7;
    let posicionXInicial = 1268;//1350
    let anchoLava = 94; // Por ejemplo, si cada imagen de lava mide 64 píxeles de ancho

    for (let i = 0; i < cantidadLava; i++) {
        this.lavaes.create(posicionXInicial + (i * anchoLava), config.height - 200, 'lava').anims.play('lava_quema', true)
            .setOrigin(0, 0.5)
            .setScale(1.5)
            .setSize(60, 40).setOffset(1, 22)
    }

    this.lavaesgota = this.physics.add.group();


    this.lavacaer = this.floor.create(1450, config.height - 370, 'lava_falling').setOrigin(0, 0.5).anims.play('lavacaer', true)
        .setScale(0.5).refreshBody()
        .setSize(10, 5).setOffset(18, 1);


    this.lavacaer = this.floor.create(1650, config.height - 370, 'lava_falling').setOrigin(0, 0.5).anims.play('lavacaer', true)
        .setScale(0.5).refreshBody()
        .setSize(10, 5).setOffset(18, 1);

    this.lavacaer = this.floor.create(1780, config.height - 370, 'lava_falling').setOrigin(0, 0.5).anims.play('lavacaer', true)
        .setScale(0.5).refreshBody()
        .setSize(10, 5).setOffset(18, 1);

    this.time.addEvent({
        delay: 1400,    // 2000 ms = 2 segundos
        callback: crearGotaDeLava3,
        callbackScope: this,
        loop: true
    });

    this.time.addEvent({
        delay: 1300,    // 2000 ms = 2 segundos
        callback: crearGotaDeLava,
        callbackScope: this,
        loop: true
    });

    this.time.addEvent({
        delay: 1750,    // 2000 ms = 2 segundos
        callback: crearGotaDeLava2,
        callbackScope: this,
        loop: true
    });


    this.time.addEvent({
        delay: 2700,    // 2000 ms = 2 segundos
        callback: crearzombis,
        callbackScope: this,
        loop: true
    });


    // piso para lava
    let cantidadpiso3 = 10;
    let posicionInicialPiso3 = 1260;
    let anchopiso3 = 64;

    for (let i = 0; i < cantidadpiso3; i++) {
        this.pisolava = this.floor.create(posicionInicialPiso3 + (i * anchopiso3), config.height - 10, 'suelo3').setOrigin(0, 0.5).setScale(1).refreshBody()
            .setSize(64, 40).setOffset(0, 18);
    }


    this.floor.create(1930, config.height - 30, 'columna').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 0); // mprimer piso

    this.floor.create(1930, config.height - 90, 'columna').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 0); // mprimer piso

    this.floor.create(1930, config.height - 150, 'columna').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 0); // mprimer piso

    this.floor.create(1930, config.height - 210, 'suelo').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15);

    this.floor.create(1930, config.height - 210, 'sueloflota').setOrigin(0, 0.5)
        .setScale(1)
        .refreshBody()
        .refreshBody()
        .setSize(65, 50).setOffset(1, 15);


    let cantidadpisotrampa = 5;
    let posicionInicialPisotrampa = 2015;
    let anchopisotrampa = 64;

    for (let i = 0; i < cantidadpisotrampa; i++) {
        this.pisotrampa = this.floor.create(posicionInicialPisotrampa + (i * anchopisotrampa), config.height - 80, 'suelo3').setOrigin(0, 0.5).setScale(1).refreshBody()
            .setSize(64, 40).setOffset(0, 18);
    }

    this.pisotrampa = this.floor.create(2335, config.height - 8, 'suelo3').setOrigin(0, 0.5).setScale(1).refreshBody()
        .setSize(64, 40).setOffset(0, 18);

    let cantidadpisotrampa2 = 15;
    let posicionInicialPisotrampa2 = 2399;
    let anchopisotrampa2 = 64;

    for (let i = 0; i < cantidadpisotrampa2; i++) {
        this.pisotrampa = this.floor.create(posicionInicialPisotrampa2 + (i * anchopisotrampa2), config.height - 80, 'suelo3').setOrigin(0, 0.5).setScale(1).refreshBody()
            .setSize(64, 40).setOffset(0, 18);
    }



    // Crear un grupo de enemigos
    this.enemies = this.physics.add.group();

    // Número de enemigos que quieres crear
    const numEnemies = 3; // Cambia este valor según lo que necesites

    for (let i = 0; i < numEnemies; i++) {
        // Crear un enemigo
        let enemy = this.enemies.create(690 + i * 80, config.height - 250, 'malo').anims.play('enemy-walk', true)
            .setOrigin(0, 1)
            .setGravityY(300)
            .setVelocityX(-50)
            .setScale(1);
        enemy.flipX = true;
        enemy.body.setSize(28, 65).setOffset(42, 62); // recirde de secmenbto de colicion
    }

    this.rueda = this.physics.add.sprite(3231, 10, 'rueda');
    const radio = Math.min(313, 313) / 2; // Elegimos el menor de ancho o alto para un radio circular.
    this.rueda.body.setCircle(radio);
    this.rueda.setScale(0.5)
    this.rueda.anims.play('rueda_corre', true)
    //this.rueda.setVelocityX(-50)// ajustar velocidad de rueda


    this.mascotaGesi = this.physics.add.sprite(50, 100, 'mascotaGesi')
        .setScale(1)
        .setCollideWorldBounds(true)// asegura que no salga de los limites del juego
        .setGravityY(480); // aplicar garvedad vertical

    // Recortar la imagen desde la parte superior
    this.mascotaGesi.setCrop(0, 57, this.mascotaGesi.width, this.mascotaGesi.height - 50);

    this.mascotaGesi.body.setSize(20, 69).setOffset(55, 57); // recirde de secmenbto de colicion

    // Hacer que todos los enemigos colisionen con el suelo
    this.physics.add.collider(this.enemies, this.floor);
    this.physics.add.collider(this.rueda, this.floor);


    this.physics.add.collider(this.enemies, this.enemies, (enemy1, enemy2) => {
        // Cambiar la dirección de ambos enemigos invirtiendo su velocidad
        enemy1.setVelocityX(-enemy1.body.velocity.x);
        enemy2.setVelocityX(-enemy2.body.velocity.x);

        // Cambiar la dirección visual (flip) dependiendo de la nueva dirección de la velocidad
        enemy1.flipX = enemy1.body.velocity.x < 0;  // Flip si se mueve hacia la izquierda
        enemy2.flipX = enemy2.body.velocity.x < 0;  // Flip si se mueve hacia la izquierda
    });



    this.physics.add.collider(this.mascotaGesi, this.floor)
    this.physics.add.collider(this.lavaes, this.floor)

    // Configurar la colisión entre el jugador y los enemigos
    this.physics.add.collider(this.mascotaGesi, this.enemies, onHitEnemy, null, this);// muerte con enemigo

    this.physics.add.collider(this.mascotaGesi, this.rueda, ruedahit, null, this);// muerte con rueda


    this.physics.add.collider(this.mascotaGesi, this.lavaes, onlava, null, this);// muere con lava

    this.physics.add.collider(this.lavaesgota, this.lavaes, onlavagotas, null, this);// muere con lava

    this.physics.add.collider(this.mascotaGesi, this.lavaesgota, onlavagotasgesi, null, this);// muere con lava

    // monedas
    monedas(this);
    this.physics.add.overlap(this.mascotaGesi, this.coins, collectCoin, null, this);
    this.physics.add.world.setBounds(0, 0, 4000, 380);

    // camara
    this.cameras.main.setBounds(0, 0, 4000, 380); // cambiar tamaño de mundo
    this.cameras.main.startFollow(this.mascotaGesi);

    this.keys = this.input.keyboard.createCursorKeys();
}

function onlavagotasgesi(mascotaGesi, lavaesgota) {
    if (mascotaGesi.body.touching.down && lavaesgota.body.touching.up) {
        killgesi(this);
    } else {
        killgesi(this);
    }
}


function onlavagotas(lavaesgotas, lavaes) {
    if (lavaesgotas.body.touching.down && lavaes.body.touching.up) {

        lavaesgotas.anims.play('lavacaergotaSplash', true)
        lavaesgotas.setScale(0.5)
        setTimeout(() => {
            lavaesgotas.destroy();
        }, 500);
    }
}


function crearGotaDeLava() {
    this.lavaesgota.create(1650, config.height - 342, 'lava_falling')
        .setOrigin(0, 0.5)
        .anims.play('lavacaergota', true)
        .setScale(0.5)
        .refreshBody()
        .setSize(10, 20)
        .setOffset(62, 55);
}


function crearGotaDeLava2() {
    this.lavaesgota.create(1780, config.height - 342, 'lava_falling')
        .setOrigin(0, 0.5)
        .anims.play('lavacaergota', true)
        .setScale(0.5)
        .refreshBody()
        .setSize(10, 20)
        .setOffset(62, 55);
}


function crearGotaDeLava3() {
    this.lavaesgota.create(1450, config.height - 342, 'lava_falling')
        .setOrigin(0, 0.5)
        .anims.play('lavacaergota', true)
        .setScale(0.5)
        .refreshBody()
        .setSize(10, 20)
        .setOffset(62, 55);
}


function crearzombis() {

    let enemy = this.enemies.create(1090, 195, 'malo').anims.play('enemy-walk', true)
        .setOrigin(0, 1)
        .setGravityY(300)
        .setVelocityX(-50)
        .setScale(1);
    enemy.flipX = true;
    enemy.body.setSize(28, 65).setOffset(42, 62); // recirde de secmenbto de colicion


}



function collectCoin(mascotaGesi, coin) {
    coin.disableBody(true, true);
    this.sound.play('moneda');
    addToScore(100, coin, this);
}

function addToScore(scoreToAdd, origin, game) {

    score += scoreToAdd; // Actualiza la puntuación
    game.scoreText.setText(`Puntaje: ${score}`);

    const scoreText = game.add.text(
        origin.x,
        origin.y,
        scoreToAdd, {
        fontSize: config.width / 40
    }
    );

    game.tweens.add({
        targets: scoreText,
        durations: 500,
        y: scoreText.y - 40,
        onComplete: () => {
            game.tweens.add({
                targets: scoreText,
                durations: 100,
                alpha: 0,
                onClomplete: () => {
                    scoreText.destroy();
                }
            });
        }
    });
}

function onHitEnemy(mascotaGesi, enemy) {
    // Verificar si el enemigo ya está muerto
    if (enemy.isDead) return;

    if (mascotaGesi.body.touching.down && enemy.body.touching.up) {
        // Marcar al enemigo como muerto
        enemy.isDead = true;

        enemy.anims.play('enemy-muerte', true);
        enemy.setVelocityX(5);
        this.sound.play('matar');
        addToScore(150, enemy, this);

        enemy.body.checkCollision.none = false;
        enemy.setVelocityX(0);
        enemy.body.setSize(20, 0.5).setOffset(50, 128);

        setTimeout(() => {
            enemy.destroy();
        }, 5000);
    } else {
        killgesi(this);
    }
}



function onlava(mascotaGesi, lava) {

    if (mascotaGesi.body.touching.down && lava.body.touching.up) {
        killgesi(this);
    }

}

function ruedahit(mascotaGesi, rueda) {    
    killgesi(this);
}


function update() {
    if (this.mascotaGesi.isDead) return;

    if (this.keys.left.isDown) {
        // Movimiento hacia la izquierda
        if (this.mascotaGesi.body.touching.down) this.mascotaGesi.anims.play('gesi-walk', true);
        this.mascotaGesi.x -= 2;
        this.mascotaGesi.setVelocityX(-100);
        this.mascotaGesi.flipX = true;
        this.mascotaGesi.body.setSize(20, 69).setOffset(55, 57); // Tamaño y offset normales al caminar

    } else if (this.keys.right.isDown) {
        // Movimiento hacia la derecha
        if (this.mascotaGesi.body.touching.down) this.mascotaGesi.anims.play('gesi-walk', true);
        this.mascotaGesi.x += 2;
        this.mascotaGesi.setVelocityX(100);
        this.mascotaGesi.flipX = false;
        this.mascotaGesi.body.setSize(20, 69).setOffset(55, 57); // Tamaño y offset normales al caminar

    } else if (this.keys.down.isDown) {
        // Acción al presionar la flecha de abajo (agacharse)
        if (this.mascotaGesi.body.touching.down) {
            this.mascotaGesi.anims.play('gesi-agacharse', true); // Animación de agacharse
            this.mascotaGesi.setVelocityX(0); // Detener movimiento horizontal al agacharse
            this.mascotaGesi.body.setSize(20, 60).setOffset(55, 60); // Ajuste del tamaño y offset para agacharse

        }
    } else {
        // Si no se está agachando, restaurar el estado normal (idle o salto)
        if (this.mascotaGesi.body.touching.down) {
            this.mascotaGesi.anims.play('gesi-idle', true); // Animación idle
            this.mascotaGesi.setVelocityX(0);
            this.mascotaGesi.body.setSize(20, 69).setOffset(55, 57); // Restaurar el tamaño y offset normales

        }
    }

    // Salto
    if (this.keys.up.isDown && this.mascotaGesi.body.touching.down) {
        this.mascotaGesi.setVelocityY(-480); // Fuerza del salto
        this.mascotaGesi.anims.play('gesi-salto'); // Animación de salto
        this.mascotaGesi.body.setSize(20, 69).setOffset(55, 57); // Mantener tamaño normal al saltar
    }

    const deathThreshold = 90;

    if (this.mascotaGesi.y >= 390 - deathThreshold) { // linea para matar si cae por fuera de el area de jeugo
        killgesi(this);
    }

    this.enemies.children.each(function (enemy) {
        // Comprueba si el enemigo ha cruzado la coordenada y = 390
        if (enemy.y >= 495 - deathThreshold) {
            enemy.destroy(); // Destruye al enemigo si su posición en y es mayor o igual a 390
        }
    }, this);



    if (vidas === 0) {
        score = 0;
        this.scoreText.setText(`Puntaje: ${score}`);
        vidas = 3;
        this.vidasText.setText(`Vidas: ${vidas}`);
    }

    if (this.mascotaGesi.x >= config.width - 400) { // Ajusta el valor según tu necesidad
        moveFloorPiece(this.piso2, 390);
    }

    let posX = this.mascotaGesi.x;
    let posY = this.mascotaGesi.y;

    // Obtener las dimensiones del área de juego
    let gameWidth = config.width;
    let gameHeight = config.height;

    // Calcular las relaciones
    let relativeX = posX / gameWidth;
    let relativeY = posY / gameHeight;

    // Imprimir las posiciones y relaciones en la consola
    //console.log("x " + this.mascotaGesi.x);
    //console.log("y " + this.mascotaGesi.y);


    if (this.mascotaGesi.x >= 190 && this.mascotaGesi.y <= 210) {
        console.log()
        if (!this.piso3.visible) {
            this.piso3.setVisible(true); // Mostrar el piso
            console.log('piso3 ha aparecido');
        }
    }

    if (this.mascotaGesi.x >= 1610 && this.mascotaGesi.y <= 210) {
        this.piso8.setX(1550);
        this.piso8.setSize(80, 60).setOffset(-62, 35);
    }

    if (this.mascotaGesi.x >= 2777) {
        this.rueda.setVelocityX(-300)
    }

}


function moveFloorPiece(floorPiece, newX) {
    floorPiece.destroy();
    floorPiece.setX(newX);
}

function killgesi(game) {
    const { mascotaGesi, scene, sound } = game;
    if (mascotaGesi.isDead) return;
    mascotaGesi.isDead = true;
    mascotaGesi.anims.play('gesi-muerto');
    mascotaGesi.setCollideWorldBounds(false);
    sound.add('gameover', { volume: 1 }).play();

    vidas -= 1; // Actualiza la puntuación
    game.vidasText.setText(`Vidas: ${vidas}`);

    // Configurar colisiones: solo colisionar con el suelo (down)
    mascotaGesi.body.checkCollision.up = false;
    mascotaGesi.body.checkCollision.left = false;
    mascotaGesi.body.checkCollision.right = false;
    mascotaGesi.body.checkCollision.down = true; // Mantener colisión con el suelo // que quite todas las coliciones menos con el floor
    mascotaGesi.setVelocityX(0);

    setTimeout(() => {
        mascotaGesi.setVelocityY(-2);
    }, 120);

    setTimeout(() => {
        scene.restart();
    }, 3000);
}
