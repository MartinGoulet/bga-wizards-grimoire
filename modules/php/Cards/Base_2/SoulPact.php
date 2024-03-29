<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\SpellCard;

class SoulPact extends BaseCard {

    public function castSpell($args) {
        // If the previous spell you cast this turn was an instant attack spell, gain mana equal to the damage dealt by that spell
        $spell = SpellCard::get(Globals::getPreviousSpellPlayed());
        if ($spell == null) {
            Notifications::spellNoEffect();
            return;
        }

        $card_info = SpellCard::getCardInfo($spell);
        if ($card_info['type'] == WG_SPELL_TYPE_ATTACK && $card_info['activation'] == WG_SPELL_ACTIVATION_INSTANT) {
            $this->drawManaCards(Globals::getPreviousSpellDamage());
        } else {
            Notifications::spellNoEffect();
        }
    }
}
