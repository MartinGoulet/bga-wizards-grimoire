<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class Windstorm extends BaseCard {

    public function castSpell($args) {
        // Deal 2 damage, then reveal a mana card from the mana deck. 
        // If it's a 1 power mana card, place it on this spell

        $this->dealDamage(2);

        $cards = ManaCard::revealFromDeck(1);
        $topCard = array_shift($cards);

        if (ManaCard::getPower($topCard) == 1) {
            $spell = SpellCard::get(Globals::getSpellPlayed());
            $position = SpellCard::getPositionInRepertoire($spell);
            ManaCard::addOnTopOfManaCoolDown($topCard['id'], $position);
        } else {
            ManaCard::addOnTopOfDeck($topCard['id']);
        }

        Notifications::moveManaCard(Players::getPlayerId(), [ManaCard::get($topCard['id'])]);
    }
}
