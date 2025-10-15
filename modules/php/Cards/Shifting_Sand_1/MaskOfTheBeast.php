<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class MaskOfTheBeast extends BaseCard {

    public function castSpell($args) {
        $hand = ManaCard::getHand(Players::getOpponentId());
        foreach ($hand as $mana) {
            ManaCard::addOnTopOfDiscard($mana['id']);
        }
        Notifications::discardManaCards(Players::getOpponentId(), $hand);
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}