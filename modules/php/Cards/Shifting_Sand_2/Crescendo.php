<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\SpellCard;

class Crescendo extends BaseCard {

    public function castSpell($args) {
        Globals::setCrescendoIncreaseCost(1);
    }

    public function onAfterCastSpell() {
        $lastSpellId = Globals::getSpellPlayed();
        if ($lastSpellId != $this->id && Globals::getCrescendoIncreaseCost() > 0) {
            $spell = SpellCard::get($lastSpellId);
            $instance = SpellCard::getInstanceOfCard($spell);
            $instance->drawManaCards(4);
            Globals::setCrescendoIncreaseCost(0);
        }
    }
}
