<?php

namespace WizardsGrimoire\Cards\Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class EchoCard extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. If you cast 1 of your other instant spells immediately before this and it cost 1 or less, this spell gains that spell's effect
        $this->dealDamage(3);

        $prev_spell = Globals::getPreviousSpellPlayed();
        $prev_cost = Globals::getPreviousSpellCost();

        if ($prev_spell == 0 || $prev_cost > 1) {
            Notifications::spellNoEffect();
            return;
        }

        $spell = SpellCard::get($prev_spell);
        $card_info = SpellCard::getCardInfo($spell);
        if($card_info['activation'] !== WG_SPELL_ACTIVATION_INSTANT) {
            Notifications::spellNoEffect();
            return;
        }
        
        Notifications::echoSpell(Players::getPlayerId(), $spell);
        if($card_info['class'] === "Amnesia") {
            $this->dealDamage(Globals::getCardTimesPlayed(102));
        } else {
            Game::get()->activateInstantSpell($spell, $args, _('Echo'));
            return "stop";
        }
    }

    public function castSpellInteraction($args) {
        $prev_spell_id = Globals::getPreviousSpellPlayed();
        $prev_spell = SpellCard::get($prev_spell_id);
        $instance = SpellCard::getInstanceOfCard($prev_spell);
        $instance->castSpellInteraction($args);
    }
}
