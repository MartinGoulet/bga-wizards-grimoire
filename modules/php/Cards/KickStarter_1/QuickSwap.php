<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\SpellCard;

class QuickSwap extends BaseCard {

    public function castSpell($args) {
        // Each time you discard off this spell, choose 1: deal 1 damage, or discard this spell and replace it with a new spell

        if($args == null || $args == "") {
            $this->dealDamage(1);
        } else {
            $new_spell_id = intval(array_shift($args));
            $old_spell = $this->getCard();
            $new_spell = SpellCard::isInPool($new_spell_id);
            Notifications::discardSpellCard($this->getCardName());
            SpellCard::replaceSpell($old_spell, $new_spell);
        }
    }
}
