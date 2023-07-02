<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class ArcaneEye extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card of each of your spells that costs 3 or more
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire();

        $cards_before = [];

        foreach ($spells as $card_id => $spell) {
            $card_type = SpellCard::getCardInfo($spell);
            if (intval($card_type['cost']) >= 3) {
                $pos = intval($spell['location_arg']);
                $card = ManaCard::getOnTopOnManaCoolDown($pos);
                if ($card != null) {
                    $cards_before[] = $card;
                }
            }
        }

        Notifications::moveManaCard($player_id, $cards_before);
    }
}
