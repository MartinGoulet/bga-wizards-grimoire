<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaUserException;
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

        $cards = ManaCard::revealFromDeck(3);

        $mana_value_1 = array_filter($cards, function ($card) {
            return ManaCard::getPower($card) == 1;
        });

        if (sizeof($mana_value_1) > 0) {
            $mana_value_1_ids = array_column($mana_value_1, 'id');
            $this->moveCards($mana_value_1_ids);

            if (sizeof($mana_value_1) == sizeof($cards)) {
                Globals::setSkipInteraction(true);
            }
        }
    }

    public function castSpellInteraction($args) {
        $card_ids = explode(",", array_shift($args));

        foreach ($card_ids as $card_id) {
            ManaCard::isInReveleadMana($card_id);
        }

        $revealed_mana = ManaCard::getRevealedMana();
        if (sizeof($card_ids) != sizeof($revealed_mana)) {
            throw new BgaUserException("You must put on top of the discard all manas from the revealed mana zone");
        }

        $this->moveCards($card_ids);
    }

    private function moveCards($card_ids) {
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

        Notifications::moveManaCard(Players::getPlayerId(), $cards);
    }
}
