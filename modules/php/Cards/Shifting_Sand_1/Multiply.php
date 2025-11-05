<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\OngoingBaseCard;

class Multiply extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveExactMana(3);
    }

    public function getPriority(): int {
        return 2;
    }

    public function onModifyBasicAttackDamage(int $damage): int {

        $isActive = $this->isActive();

        if ($isActive) {
            $message = clienttranslate('${card_name} multiplies basic attack damage by 2');
            Game::get()->notify->all("message", $message, [
                'card_name' => $this->getCardName(),
            ]);
        }

        return $isActive ? $damage * 2 : $damage;
    }
}
