<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class BadFortune extends BaseCard {

    public function castSpell($args) {
        // Deal 4 damage. Reveal 3 cards from the mana deck. 
        // Place any revealed 1 power mana on this spell. Return the rest in any order
        $this->dealDamage(4);

        ManaCard::revealFromDeck(3);
    }

    public function castSpellInteraction($args) {
        $card_ids = explode(",", array_shift($args));
        $spell = SpellCard::get(Globals::getSpellPlayed());
        $position = SpellCard::getPositionInRepertoire($spell);

        $cards = ManaCard::getCards($card_ids);
        foreach ($card_ids as $card_id) {
            $card = $cards[$card_id];
            if (ManaCard::getPower($card) == 1) {
                ManaCard::addOnTopOfManaCoolDown($card_id, $position);
            } else {
                ManaCard::addOnTopOfDeck($card_id);
            }
        }

        $cards_after = ManaCard::getCards($card_ids);
        Notifications::moveManaCard(Players::getPlayerId(), $cards, $cards_after);
    }

    // public function moveManaCardEqualOne($reveled_cards, $position) {
    //     $power_1_cards = array_filter($reveled_cards, function($card) {
    //         return ManaCard::getPower($card) == 1;
    //     });

    //     $cards_after = [];
    //     foreach ($power_1_cards as $card_id => $card) {
    //         ManaCard::addOnTopOfManaCoolDown($card_id, $position);
    //         $cards_after[] = ManaCard::get($card_id);
    //     }

    //     Notifications::moveManaCard(Players::getPlayerId(), $power_1_cards, $cards_after);
    // }

    // public function moveManaCardOverOne($reveled_cards) {
    //     $power_others  = array_filter($reveled_cards, function($card) {
    //         return ManaCard::getPower($card) > 1;
    //     });

    //     $cards_after = [];
    //     foreach ($power_others as $card_id => $card) {
    //         ManaCard::addOnTopOfDeck($card_id);
    //         $cards_after[] = ManaCard::get($card_id);
    //     }

    //     Notifications::moveManaCard(Players::getPlayerId(), $reveled_cards, $cards_after);
    // }

}
