<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

class Lullaby extends OngoingBaseCard {

    public function isActive(): bool {
        $isActive = $this->isActiveAtLeastOneMana();

        $ownerId = $this->getOwnerId();
        if ($isActive) {
            $count = ManaCard::getHandCount($ownerId);
            if ($count == 0) {
                ManaCard::draw(2, $ownerId, "Lullaby");
            }
        }
            
        return $isActive;
    }

    public function getArguments(): array {
        return [
            'name' => 'lullaby',
            'active' => $this->isActive(),
        ];
    }

    public static function check() {

        /** @var Lullaby $card */
        $card = SpellCard::getInstanceOfCardFromClass(Lullaby::class);
        
        if ($card !== null) {
            $card->isActive();
        }

    }
}
