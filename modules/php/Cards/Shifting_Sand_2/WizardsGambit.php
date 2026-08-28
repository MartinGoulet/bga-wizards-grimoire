<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

class WizardsGambit extends BaseCard {

    public function castSpell($args) {
        // Deal 2 damage. Choose 1 of your spells. You may swap the mana on this spell with the chosen spell
        $this->dealDamage(2);
        
        if(empty($args)) {
            return;
        }

        $targetPosition = intval(array_shift($args));

        $currentSpell = SpellCard::get($this->id);
        $currentPosition = SpellCard::getPositionInRepertoire($currentSpell);

        ManaCard::exchangeManaCoolDownBetweenPositions($currentPosition, $targetPosition);
    }

}