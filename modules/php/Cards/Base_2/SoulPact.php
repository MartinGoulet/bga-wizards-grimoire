<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\SpellCard;

class SoulPact extends BaseCard {

    public function castSpell($args) {
        // If the previous spell you cast this turn was an instant attack spell, gain mana equal to the damage dealt by that spell
        $spell = SpellCard::get(Globals::getPreviousSpellPlayed());
        $card_info = SpellCard::getCardInfo($spell)['type'];
        if($card_info == WG_SPELL_TYPE_ATTACK) {
            $this->drawManaCards(Globals::getPreviousSpellDamage());
        }
    }
}
