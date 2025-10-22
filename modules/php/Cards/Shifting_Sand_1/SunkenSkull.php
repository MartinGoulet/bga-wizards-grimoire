<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\RelicCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class SunkenSkull extends RelicCard {

    public function castSpell($args) {
        Globals::setSunkenSkullActivePlayer(Players::getOpponentId());
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}