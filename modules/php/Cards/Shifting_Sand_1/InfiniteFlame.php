<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class InfiniteFlame extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana();
    }

    public function getPriority(): int {
        return 1;
    }

    public function onModifyBasicAttackDamage(int $damage): int {
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $count = ManaCard::countOnTopOfManaCoolDown($position, Players::getPlayerId());

        $message = clienttranslate('${card_name} increases basic attack damage by ${count}');
        Game::get()->notify->all("message", $message, [
            'card_name' => $this->getCardName(),
            'count' => $count,
        ]);

        return $damage + $count;
    }

}