<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\SpellCard;

class FrozenGoblet extends RelicCard {

    public function castSpell($args) {
        Globals::setFrozenGobletActive(true);
    }
    
    public function onDestroyRelic()
    {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }
}
