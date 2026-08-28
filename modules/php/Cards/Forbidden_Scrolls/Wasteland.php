<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

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