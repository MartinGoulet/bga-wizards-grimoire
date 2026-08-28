<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

class RaiseTheDead extends BaseCard {

    public function castSpell($args) {

        $this->dealDamage(6);

        $spellPosition = intval(array_shift($args));
        $spell = SpellCard::getFromRepertoire($spellPosition);
        $excluded_spell_id = $spell['id'];

        foreach (range(1, 6) as $position) {
            $spell = SpellCard::getFromRepertoire($position);
            if (!empty($spell) && !in_array($spell['id'], [$this->id, $excluded_spell_id])) {
                ManaCard::dealFromDeckToManaCoolDown($position);
            }
        }
    }

}