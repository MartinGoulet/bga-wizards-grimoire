<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class LivingFire extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage minus 1 for each of your other spells that have mana on them
        $numOtherSpellsWithMana = $this->countOtherSpellsWithMana();
        $damage = max(0, 6 - $numOtherSpellsWithMana);
        $this->dealDamage($damage);
    }

    private function countOtherSpellsWithMana() {
        $playerId = Players::getPlayerId();
        $spellsInPlay = SpellCard::getCardsFromRepertoire($playerId);

        $count = 0;
        foreach ($spellsInPlay as $spell) {
            if ($spell['id'] != $this->id && $this->spellHasMana($spell)) {
                $count++;
            }
        }
        return $count;
    }

    private function spellHasMana(array $spell) {
        $position = SpellCard::getPositionInRepertoire($spell);
        $manaCards = ManaCard::getCardsOnManaCoolDown($position);
        return !empty($manaCards);
    }
}
