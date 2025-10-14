<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

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
