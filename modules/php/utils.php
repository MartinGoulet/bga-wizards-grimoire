<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Cards\BaseCard;

trait UtilsTrait {

    public function triggerOnAddSpellToRepertoire(array $spellCard) {
        $cards = SpellCard::getOngoingActiveSpells(Players::getPlayerId());
        foreach ($cards as $card_id => $card) {
            $instance = SpellCard::getInstanceOfCard($card);
            if (method_exists($instance, 'onAddSpellToRepertoire')) {
                $instance->onAddSpellToRepertoire($spellCard);
            }
        }
    }

    public function triggerOnAfterDiscardManaFromSpell(BaseCard $instance, int $mana_id) {
        if (method_exists($instance, 'onAfterDiscardManaFromSpell')) {
            $instance->onAfterDiscardManaFromSpell($mana_id);
        }
    }
}
