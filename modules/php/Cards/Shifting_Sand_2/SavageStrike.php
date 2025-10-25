<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\SpellCard;

class SavageStrike extends BaseCard {

    public function castSpell($args) {
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $manaCards = ManaCard::getCardsOnManaCoolDown($position);

        $powers = array_map(function($m) { return ManaCard::getPower($m); }, $manaCards);
        $maxPower = $powers ? max($powers) : 0;

        $this->dealDamage($maxPower);
    }
}
