<?php

namespace WizardsGrimoireExt\Core;


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

}