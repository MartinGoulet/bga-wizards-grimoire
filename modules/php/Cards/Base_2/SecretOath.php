<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class SecretOath extends BaseCard {

    public function isOngoingSpellActive(bool $value)
    {
        // As long as this spell has mana on it, if your opponent has a 4 power mana in their hand, they must give it to you immediately
        Globals::setIsActiveSecretOath($value);
    }
}
