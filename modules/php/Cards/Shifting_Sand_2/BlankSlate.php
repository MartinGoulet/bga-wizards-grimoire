<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class BlankSlate extends BaseCard {

    public function castSpell($args) {
    }

    public function onAfterDiscardManaFromSpell(int $mana_id) {
        $this->drawManaCards(3);

        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $manas = ManaCard::getCardsOnManaCoolDown($position);

        if (!empty($manas)) {
            ManaCard::addCardsToHand($manas);
            Notifications::moveManaCard(Players::getPlayerId(), $manas, false);
        }
    }
}
