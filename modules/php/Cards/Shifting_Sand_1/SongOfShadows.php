<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Objects\CardLocation;

class SongOfShadows extends BaseCard {

    public function castSpell($args) {
        // Gain 5 mana
        $this->drawManaCards(5);

        if (empty($args)) {
            $discard = Game::get()->deck_manas->countCardInLocation(CardLocation::Discard());
            if ($discard > 0) {
                throw new \BgaSystemException('No card chosen for Song of Shadows');
            } else {
                return;
            }
        }

        // Give opponent 1 mana card of your choice from discard pile
        $card_id = intval(array_shift($args));
        $card = ManaCard::get($card_id);
        if ($card['location'] != 'discard') {
            throw new \BgaSystemException('Invalid card chosen for Song of Shadows: ' . $card_id);
        }
        ManaCard::addCardsToHand([$card], Players::getOpponentId());
    }
}
