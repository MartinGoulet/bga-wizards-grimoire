<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

abstract class OngoingBaseCard extends BaseCard {

    abstract public function isActive(): bool;

    /** @return {name: string, isActive: bool} */
    public function getArguments(): array {
        $card_type = SpellCard::getCardInfo(SpellCard::get($this->id));

        return [
            'name' => strtolower($card_type['class']),
            'active' => $this->isActive()
        ];
    }

    protected function isActiveAtLeastOneMana(): bool {
        $spell = SpellCard::get($this->id);
        if(!SpellCard::isSpellInRepertoire($spell)) {
            return false;
        }
        $position = SpellCard::getPositionInRepertoire($spell);
        $player_id = SpellCard::getPlayerId($spell);
        $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
        return $count >= 1;
    }

    protected function isActiveExactMana(int $nbr): bool {
        $spell = SpellCard::get($this->id);
        if(!SpellCard::isSpellInRepertoire($spell)) {
            return false;
        }
        $position = SpellCard::getPositionInRepertoire($spell);
        $player_id = SpellCard::getPlayerId($spell);
        $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
        return $count == $nbr;
    }

}