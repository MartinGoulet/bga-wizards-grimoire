<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\SpellCard;

class CursedMind extends BaseCard {

    public function castSpell($args) {
        Globals::setCursedMindIncreaseCost(1);
    }

    public function onAfterCastSpell() {
        $lastSpellId = Globals::getSpellPlayed();
        if ($lastSpellId != $this->id && Globals::getCursedMindIncreaseCost() > 0) {
            $spell = SpellCard::get($lastSpellId);
            $instance = SpellCard::getInstanceOfCard($spell);
            $instance->dealDamage(4);
            Globals::setCursedMindIncreaseCost(0);
        }
    }

}