<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\SpellCard;

class BlankSlate extends BaseCard {

    public function castSpell($args) {
    }

    public function onAfterDiscardManaFromSpell(int $mana_id) {
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $ownerId = SpellCard::getPlayerId($spell);

        $this->drawManaCards(3, $ownerId);

        $manas = ManaCard::getCardsOnManaCoolDown($position, $ownerId);

        if (!empty($manas)) {
            ManaCard::addCardsToHand($manas, $ownerId);
            Notifications::moveManaCard($ownerId, $manas, false);
        }
    }
}
