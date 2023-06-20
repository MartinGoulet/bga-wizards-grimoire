<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Objects\CardLocation;

class PowerHungry extends BaseCard {

    public function isOngoingSpellActive(bool $value)
    {
        Globals::setIsActivePowerHungry($value);
    }

    // public function onAfterBasicAttack($mana_id)
    // {
    //     // Both players basic attack mana cards go to your hand instead of the discard pile
    //     $deck =  Game::get()->deck_manas;

    //     $card_before = ManaCard::get($mana_id);
    //     $card_after = $deck->moveCard($mana_id, CardLocation::Hand(), $this->owner);
        
    //     $msg = clienttranslate('${player_name} move the mana card from the basic attack to his hand.');
    //     Notifications::moveManaCard($this->owner, [$card_before], [$card_after], $msg, false);
    // }
}
