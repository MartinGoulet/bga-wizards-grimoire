<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\RelicCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\SpellCard;

class FrozenGoblet extends RelicCard {

    public function castSpell($args) {
        Globals::setFrozenGobletActive(true);
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }
}
