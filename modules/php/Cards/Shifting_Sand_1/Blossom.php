<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class Blossom extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveExactMana(3)
            && SpellCard::isInRepertoireBool($this->id, Players::getPlayerId());
    }

    public function getArguments(): array {
        return [
            'name' => 'blossom',
            'active' => $this->isActive(),
        ];
    }

    public function onModifyManaPower(int $power): int {
        $isActive = $this->isActive();
        return $isActive ? $power + 2 : $power;
    }

}