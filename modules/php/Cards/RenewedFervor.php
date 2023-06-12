<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;
use WizardsGrimoire\Objects\CardLocation;

class RenewedFervor extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off each of your instant attack spells that costs 2 or less.
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire();

        $cards = array_filter($spells, function ($card) {
            $card_type = Spell::getCardInfo($card);
            $isInstantAttackSpellWithCostTwoOrLess =
                $card_type['activation'] == WG_SPELL_ACTIVATION_INSTANT &&
                $card_type['type'] == WG_SPELL_TYPE_DAMAGE &&
                $card_type['cost'] <= 2;

            return $isInstantAttackSpellWithCostTwoOrLess;
        });

        $top_mana_cards = [];
        foreach ($cards as $card_id => $card) {
            $position = intval($card['location_arg']);
            $top_mana_cards[] = Game::get()->deck_manas->pickCardForLocation(
                CardLocation::PlayerManaCoolDown($player_id, $position),
                CardLocation::Hand(),
                $player_id
            );
        }
        Notifications::moveManaCard($player_id, $cards, $top_mana_cards);
    }
}
