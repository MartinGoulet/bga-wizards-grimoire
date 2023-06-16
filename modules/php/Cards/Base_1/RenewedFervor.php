<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;
use WizardsGrimoire\Objects\CardLocation;

class RenewedFervor extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off each of your instant attack spells that costs 2 or less.
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire();

        $spells = array_filter($spells, function ($card) {
            $card_type = SpellCard::getCardInfo($card);
            $isInstantAttackSpellWithCostTwoOrLess =
                $card_type['activation'] == WG_SPELL_ACTIVATION_INSTANT &&
                $card_type['type'] == WG_SPELL_TYPE_DAMAGE &&
                $card_type['cost'] <= 2;

            return $isInstantAttackSpellWithCostTwoOrLess;
        });

        $cards_before = [];
        $cards_after = [];
        foreach ($spells as $card_id => $card) {
            $position = intval($card['location_arg']);
            $card = ManaCard::getOnTopOnManaCoolDown($position);
            $cards_before[] = $card;
            Game::get()->deck_manas->pickCardForLocation(
                CardLocation::PlayerManaCoolDown($player_id, $position),
                CardLocation::Hand(),
                $player_id
            );
            $cards_after[] = ManaCard::get($card['id']);
        }
        Notifications::moveManaCard($player_id, $cards_before, $cards_after);
    }
}
