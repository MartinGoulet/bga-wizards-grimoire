<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

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