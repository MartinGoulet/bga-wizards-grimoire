<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class QuickSwap extends BaseCard {

    public function castSpell($args) {
        // Each time you discard off this spell, choose 1: deal 1 damage, or discard this spell and replace it with a new spell

        if($args == null || $args == "") {
            $this->dealDamage(1);
        } else {
            // TODO
        }
    }
}
