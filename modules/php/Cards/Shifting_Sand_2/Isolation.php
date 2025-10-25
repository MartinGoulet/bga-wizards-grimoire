<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class Isolation extends BaseCard {

    public function castSpell($args) {
        // Deal 4 damage, minus 2 for each of your regeneration and utility spells that have mana on them
        $damage = 4;
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire($player_id);
        foreach ($spells as $spell) {
            $spell_info = SpellCard::getCardInfo($spell);
            if (in_array($spell_info['type'], [WG_SPELL_TYPE_REGENERATION, WG_SPELL_TYPE_UTILITY])) {
                $mana_cards = ManaCard::getCardsOnManaCoolDown(SpellCard::getPositionInRepertoire($spell), $player_id);
                if (count($mana_cards) > 0) {
                    $damage -= 2;
                }
            }
        }

        $damage = max(0, $damage);
        $this->dealDamage($damage);
    }

}