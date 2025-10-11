<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class RenewedFervor extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off each of your instant attack spells that costs 2 or less.
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire();

        $spells = array_filter($spells, function ($card) {
            $card_type = SpellCard::getCardInfo($card);
            $isInstantAttackSpellWithCostTwoOrLess =
                $card_type['activation'] == WG_SPELL_ACTIVATION_INSTANT &&
                $card_type['type'] == WG_SPELL_TYPE_ATTACK &&
                $card_type['cost'] <= 2;

            return $isInstantAttackSpellWithCostTwoOrLess;
        });

        $cards = [];
        foreach ($spells as $card_id => $spell) {
            $position = intval($spell['location_arg']);
            if(ManaCard::countOnTopOfManaCoolDown($position) > 0) {
                $cards[] = ManaCard::drawFromManaCoolDown($position);
                Events::onManaPickedUpUnderSpell($position);
            }
        }
        if(sizeof($cards) > 0) {
            Notifications::moveManaCard($player_id, $cards);
        } else {
            Notifications::spellNoEffect();
        }
    }
}
