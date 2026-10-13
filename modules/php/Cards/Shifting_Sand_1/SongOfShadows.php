<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use Bga\GameFramework\UserException;
use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class SongOfShadows extends BaseCard {

    public function castSpell($args) {
        if (empty($args)) {
            $discard = Game::get()->deck_manas->countCardInLocation(CardLocation::Discard());
            if ($discard > 0) {
                throw new UserException('No card chosen for Song of Shadows');
            }

            $this->drawManaCards(5);
            return;
        }

        // Give opponent 1 mana card of your choice from discard pile
        $card_id = intval(array_shift($args));
        ManaCard::isInDiscard($card_id);
        Game::get()->deck_manas->moveCard($card_id, 'temp');

        $this->drawManaCards(5);

        $card = ManaCard::get($card_id);
        ManaCard::addCardsToHand([$card], Players::getOpponentId());
    }
}
