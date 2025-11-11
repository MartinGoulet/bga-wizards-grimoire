<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\RelicCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class MaskOfTheBeast extends RelicCard {

    public function castSpell($args) {
        $hand = ManaCard::getHand(Players::getOpponentId());
        foreach ($hand as $mana) {
            ManaCard::addOnTopOfDiscard($mana['id']);
        }
        Notifications::discardManaCards(Players::getOpponentId(), $hand);
    }
    
    public function onDestroyRelic()
    {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}