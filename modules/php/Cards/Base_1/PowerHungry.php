<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Objects\CardLocation;

class PowerHungry extends BaseCard {

    public function onAfterBasicAttack($mana_id)
    {
        // Move card to player hand who own this card
        $deck =  Game::get()->deck_manas;

        $card_before = $deck->getCard($mana_id);
        $card_after = $deck->moveCard($mana_id, CardLocation::Hand(), $this->owner);
        
        $msg = clienttranslate('${player_name} move the mana card from the basic attack to his hand.');
        Notifications::moveManaCard($this->owner, [$card_before], [$card_after], $msg, false);
    }
}
