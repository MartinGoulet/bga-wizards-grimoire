<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use WizardsGrimoireExt\Cards\RelicCard;

class ResurrectionScroll extends RelicCard {

    public function castSpell($args) {

    }

    public function onDestroyRelic()
    {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}