<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\SpellCard;

class FrozenGoblet extends BaseCard {

    public function castSpell($args) {
        Globals::setFrozenGobletActive(true);
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }
}
