<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class ArcaneEye extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off each of your spells that costs 3 or more
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire();

        $cards = [];

        foreach ($spells as $card_id => $spell) {
            $card_type = SpellCard::getCardInfo($spell);
            if (intval($card_type['cost']) >= 3) {
                $pos = intval($spell['location_arg']);
                $card = ManaCard::getOnTopOnManaCoolDown($pos);
                if ($card != null) {
                    $cards[] = $card;
                    ManaCard::addToHand($card['id']);
                    Notifications::pickUpManaCardFromSpell($player_id, $card, $pos);
                    Events::onManaPickedUpUnderSpell($spell['location_arg']);
                }
            }
        }

        if(sizeof($cards) > 0) {
            Notifications::moveManaCard($player_id, $cards);
        } else {
            Notifications::spellNoEffect();
        }
    }
}
