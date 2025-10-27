<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\SpellCard;

class Rewind extends BaseCard {

    public function castSpell($args) {
        // Pick up mana off each of your other instant spells that have more than 1 mana on them

        $spells = SpellCard::getCardsFromRepertoire();
        $manas = [];
        foreach ($spells as $spell) {
            if (intval($spell['id']) == $this->id) {
                continue;
            }
         
            $type = SpellCard::getCardInfo($spell)['activation'];
            if ($type == WG_SPELL_ACTIVATION_INSTANT) {
                $position = SpellCard::getPositionInRepertoire($spell);
                $count = ManaCard::countOnTopOfManaCoolDown($position);
                if ($count > 1) {
                    $manas = array_merge($manas, [ManaCard::getOnTopOnManaCoolDown($position)]);
                }
            }
        }

        if(!empty($manas)) {
            ManaCard::addCardsToHand($manas);
        }
    }
}
