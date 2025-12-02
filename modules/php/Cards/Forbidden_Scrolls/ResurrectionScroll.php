<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use BgaUserException;
use WizardsGrimoireExt\Cards\RelicCard;
use WizardsGrimoireExt\Core\SpellCard;

class ResurrectionScroll extends RelicCard {

    public function castSpell($args) {
        throw new BgaUserException("Resurrection Scroll cannot be cast as a spell.");
    }

    public function onDestroyRelic()
    {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}