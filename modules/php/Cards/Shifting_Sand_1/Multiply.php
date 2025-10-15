<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class Multiply extends BaseCard {


    public function onModifyBasicAttackDamage(int $damage): int {
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $count = ManaCard::countOnTopOfManaCoolDown($position, Players::getPlayerId());
        if ($count == 3) {
            // If there is 3 mana on this spell, double the damage instead
            return $damage * 2;
        }
        return $damage;
    }

}