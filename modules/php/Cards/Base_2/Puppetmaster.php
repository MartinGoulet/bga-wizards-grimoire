<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class Puppetmaster extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana()
            && SpellCard::isInRepertoireBool($this->id, Players::getOpponentId());
    }

    public function getArguments(): array {
        return [
            'name' => 'puppetmaster',
            'active' => $this->isActive(),
        ];
    }

}
