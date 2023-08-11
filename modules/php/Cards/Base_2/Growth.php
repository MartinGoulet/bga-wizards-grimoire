<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;

class Growth extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        // During your turn, increase the power of all mana by 1
        Globals::setIsActiveGrowth($value, $player_id);
        
        if ($value == true && Globals::getIsActiveSecretOath()) {
            Game::undoSavepoint();
        }
    }
}
