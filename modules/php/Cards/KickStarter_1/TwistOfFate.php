<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class TwistOfFate extends BaseCard {

    public function castSpell($args) {
        // Deal 2 damage. You may discard and replace 1 of your other spells with a new spell. Move all mana on it onto the new spell
        $this->dealDamage(2);

        if ($args == null || $args == "") {
            return;
        }

        if (sizeof($args) != 2) {
            throw new BgaSystemException("Wrong number of arguments");
        }

        $new_spell_id = intval(array_shift($args));
        $old_spell_pos = intval(array_shift($args));

        $old_spell = SpellCard::getFromRepertoire($old_spell_pos);
        $new_spell = SpellCard::get($new_spell_id);

        SpellCard::replaceSpell($old_spell, $new_spell);

        if (ManaCard::countOnTopOfManaCoolDown($old_spell_pos) > 0) {
            $this->checkOngoingSpell($old_spell, false);
            $this->checkOngoingSpell($new_spell, true);
        }
    }

    private function checkOngoingSpell($spell, bool $is_active) {
        $card_type = SpellCard::getCardInfo($spell);
        if ($card_type['activation'] == WG_SPELL_ACTIVATION_ONGOING) {
            $instance = SpellCard::getInstanceOfCard($spell);
            $instance->isOngoingSpellActive($is_active, Players::getPlayerId());
        }
    }
}
