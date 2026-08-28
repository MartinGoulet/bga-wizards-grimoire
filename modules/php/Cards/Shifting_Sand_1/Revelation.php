<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class Revelation extends BaseCard {

    public function castSpell($args) {
        // Reveal the top mana on 1 of your spells. Gain a quantity of mana equal
        $player_id = Players::getPlayerId();
        $position = intval(array_shift($args));
        $card = ManaCard::getOnTopOnManaCoolDown($position, $player_id);
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $spellInstance = SpellCard::getInstanceOfCard($spell);
        Notifications::revealManaCardCooldown($player_id, $card, $spellInstance->getCardName());
        $this->drawManaCards(ManaCard::getPower($card));
    }
}
