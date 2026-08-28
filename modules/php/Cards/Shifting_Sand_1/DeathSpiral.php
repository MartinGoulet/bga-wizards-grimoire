<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class DeathSpiral extends BaseCard {

    public function castSpell($args) {
    }

    public function onAfterDiscardManaFromSpell(int $mana_id) {
        // Each time you discard off this spell, deal 1 damage for each mana remaining on it
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $ownerId = SpellCard::getPlayerId($spell);

        $count = ManaCard::countOnTopOfManaCoolDown($position, $ownerId);
        $this->dealDamage($count, Players::getOpponentIdOf($ownerId));
        
    }

}