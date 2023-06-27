<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Objects\CardLocation;

class PowerHungry extends BaseCard {

    public function isOngoingSpellActive(bool $value) {
        Globals::setIsActivePowerHungry($value);
    }
}
