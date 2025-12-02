<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class Wasteland extends BaseCard {
    public function castSpell($args) {

        $damage = 2;
        $playedSpellIds = Globals::getPlayedSpellIdsThisGame(Players::getPlayerId());
        $discardSpellsIds = array_map(fn($c) => intval($c['id']), SpellCard::getDiscard());

        foreach ($playedSpellIds as $spellId) {
            if (in_array($spellId, $discardSpellsIds)) {
                $damage += 1;
            }
        }

        $this->dealDamage($damage);
    }
}