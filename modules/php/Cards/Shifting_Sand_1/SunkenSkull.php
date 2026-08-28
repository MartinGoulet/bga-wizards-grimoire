<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class SunkenSkull extends RelicCard {

    public function castSpell($args) {
        Globals::setSunkenSkullActivePlayer(Players::getOpponentId());
    }
    
    public function onDestroyRelic()
    {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}