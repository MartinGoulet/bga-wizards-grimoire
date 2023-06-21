<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class FatalFlaw extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage. Reveal the top mana card on 1 of your opponents spells, dealing additional damage equal to it's power.
    }
}
