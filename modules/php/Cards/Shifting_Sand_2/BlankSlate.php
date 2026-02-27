<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

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
