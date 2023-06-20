<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\SpellCard;

class ForceOfNature extends BaseCard {

    public function castSpell($args) {
        // Deal 4 damage, minus 1 damage for each of your other attack spells that have mana on them
        $current_spell_id = Globals::getSpellPlayed();
        $attack_spell_with_mana_on_then = 0;

        $spells = SpellCard::getCardsFromRepertoire();
        foreach ($spells as $spell_id => $spell) {
            $spell_type = SpellCard::getCardInfo($spell)['type'];
            if ($spell_id != $current_spell_id && $spell_type == WG_SPELL_TYPE_ATTACK) {
                if (ManaCard::countOnTopOfManaCoolDown(SpellCard::getPositionInRepertoire($spell)) > 0) {
                    $attack_spell_with_mana_on_then++;
                }
            }
        }

        if ($attack_spell_with_mana_on_then >= 4) {
            Notifications::spellNoEffect();
        } else {
            $this->dealDamage(4 - $attack_spell_with_mana_on_then);
        }
    }
}
