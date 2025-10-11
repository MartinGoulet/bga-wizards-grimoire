<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Game;
use WizardsGrimoireExt\Core\Globals;

class Growth extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        // During your turn, increase the power of all mana by 1
        Globals::setIsActiveGrowth($value, $player_id);
        
        if ($value == true && Globals::getIsActiveSecretOath()) {
            Game::undoSavepoint();
        }
    }
}
