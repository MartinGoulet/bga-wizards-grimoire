<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class InfiniteFlame extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana();
    }

    public function getArguments(): array {
        return [
            'name' => 'infiniteflame',
            'active' => $this->isActive()
        ];
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
