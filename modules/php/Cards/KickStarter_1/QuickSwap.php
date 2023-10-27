<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class QuickSwap extends BaseCard {

    public function castSpell($args) {
        // Each time you discard off this spell, choose 1: deal 1 damage, or discard this spell and replace it with a new spell

        if($args == null || $args == "") {
            $this->dealDamage(1);
        } else {
            $old_spell = $this->getCard();
            $old_spell_pos = SpellCard::getPositionInRepertoire($old_spell);

            $new_spell_id = intval(array_shift($args));
            $new_spell = SpellCard::isInPool($new_spell_id);
            Notifications::discardSpellCard($this->getCardName());
            SpellCard::replaceSpell($old_spell, $new_spell);

            if (ManaCard::countOnTopOfManaCoolDown($old_spell_pos) > 0) {
                $this->checkOngoingSpell($new_spell, true);
            }
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
