<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class DeathSpiral extends BaseCard {

    public function castSpell($args) {
        // Each time you discard off this spell, deal 1 damage for each mana remaining on it
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);

        $count = ManaCard::countOnTopOfManaCoolDown($position, Players::getPlayerId());
        $this->dealDamage($count);
    }

}