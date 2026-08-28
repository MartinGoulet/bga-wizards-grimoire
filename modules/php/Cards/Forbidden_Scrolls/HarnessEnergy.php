<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\SpellCard;

class HarnessEnergy extends BaseCard {

    public function castSpell($args) {
        $prevSpell = Globals::getPreviousSpellPlayed();
        if ($prevSpell == 0) {
            Notifications::spellNoEffect();
            return;
        }

        $spell = SpellCard::get($prevSpell);
        $card_type = SpellCard::getCardInfo($spell);

        if ($card_type['activation'] != WG_SPELL_ACTIVATION_INSTANT || $card_type['type'] != WG_SPELL_TYPE_REGENERATION) {
            Notifications::spellNoEffect();
        }
        
        $info = Globals::getNumberOfCardDrawByCardEffectThisTurn();
        $count = $info[$prevSpell] ?? 0;
        $this->dealDamage($count);
    }
}
