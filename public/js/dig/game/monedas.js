export const monedas = (game) => {

    game.coins = game.physics.add.staticGroup();
    game.coins.create(280, 280, 'coins').anims.play('coins-giro', true).setScale(2);

    game.coins.create(2799, 250, 'coins').anims.play('coins-giro', true).setScale(1.5);



}
