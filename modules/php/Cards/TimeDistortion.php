<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class TimeDistortion extends BaseCard {

    public function castSpell($args) {
        $mana_cards_id = array_shift($args);
        
        var_dump(($args));
        throw new BgaSystemException('Test');
    }
}
