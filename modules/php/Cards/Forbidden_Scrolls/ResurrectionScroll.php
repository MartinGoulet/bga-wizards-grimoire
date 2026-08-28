<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;
use WizardsGrimoire\Objects\CardLocation;

class ResurrectionScroll extends RelicCard {

    public function castSpell($args) {
        $manaId = intval(array_shift($args));
        $spellId  = intval(array_shift($args));

        if ($manaId > 0) {
            ManaCard::addToHand($manaId);
            $card = ManaCard::get($manaId);
            Notifications::moveManaCard(Players::getPlayerId(), [$card]);
        }

        Game::get()->globals->set("resurrection_scroll_spell_id", $spellId);
    }

    public function onDestroyRelic() {
        $newSpellId = Game::get()->globals->get("resurrection_scroll_spell_id");
        $newSpell = SpellCard::get($newSpellId);
        SpellCard::destroyRelic(SpellCard::get($this->id));
        
        if ($newSpell['location'] == CardLocation::SpellSlot()) {
            SpellCard::addNewSpell($newSpell);
        } else {
            SpellCard::addNewSpell($newSpell, false);
        }

        Game::get()->globals->set("resurrection_scroll_spell_id", 0);
    }
}
