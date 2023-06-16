<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;
use WizardsGrimoire\Objects\CardLocation;

class BadFortune extends BaseCard {

    public function castSpell($args)
    {
        // Deal 4 damage. Reveal 3 cards from the mana deck. 
        // Place any revealed 1 power mana on this spell. Return the rest in any order
        $this->dealDamage(4);

        $reveled_cards = ManaCard::revealFromDeck(3);
        $spell = SpellCard::get(Globals::getSpellPlayed());
        $position = SpellCard::getPositionInRepertoire($spell);

        $this->moveManaCardEqualOne($reveled_cards, $position);
        $this->moveManaCardOverOne($reveled_cards);
    }

    public function moveManaCardEqualOne($reveled_cards, $position) {
        $power_1_cards = array_filter($reveled_cards, function($card) {
            return intval($card['type']) == 1;
        });

        $cards_after = [];
        foreach ($power_1_cards as $card_id => $card) {
            ManaCard::addOnTopOfManaCoolDown($card_id, $position);
            $cards_after[] = ManaCard::get($card_id);
        }

        Notifications::moveManaCard(Players::getPlayerId(), $power_1_cards, $cards_after);
    }

    public function moveManaCardOverOne($reveled_cards) {
        $power_others  = array_filter($reveled_cards, function($card) {
            return intval($card['type']) > 1;
        });

        $cards_after = [];
        foreach ($power_others as $card_id => $card) {
            ManaCard::addOnTopOfDeck($card_id);
            $cards_after[] = ManaCard::get($card_id);
        }

        Notifications::moveManaCard(Players::getPlayerId(), $reveled_cards, $cards_after);
    }

}
