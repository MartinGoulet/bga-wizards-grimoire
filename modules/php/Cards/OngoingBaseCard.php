<?php

namespace WizardsGrimoireExt\Cards;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\SpellCard;

abstract class OngoingBaseCard extends BaseCard {

    abstract public function isActive(): bool;

    protected function isActiveAtLeastOneMana(): bool {
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $player_id = SpellCard::getPlayerId($spell);
        $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
        return $count >= 1;
    }

    protected function isActiveExactMana(int $nbr): bool {
        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);
        $player_id = SpellCard::getPlayerId($spell);
        $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
        return $count == $nbr;
    }

}