<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\SpellCard;

class Sandstorm extends BaseCard {

    public function castSpell($args) {
        // Deal 4 damage
        $this->dealDamage(4);
    }

    public function getSpellDiscount() {
        
        // This spell costs 1 less for each consecutive regeneration and utility spell cast before this spell. 
        $count = 0;
        $spellIds = Globals::getPlayedSpellsThisTurn();
        
        if(intval(current($spellIds)) == $this->id) {
            return 0;
        }

        foreach ($spellIds as $spellId) {
            $spell = SpellCard::get($spellId);
            $type = SpellCard::getCardInfo($spell)['type'];
            if (in_array($type, [WG_SPELL_TYPE_REGENERATION, WG_SPELL_TYPE_UTILITY])) {
                $count++;
            } else {
                break;
            }
        }

        return $count;
    }

}