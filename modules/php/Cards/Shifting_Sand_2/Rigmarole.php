<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Objects\CardLocation;

class Rigmarole extends BaseCard {

    public function castSpell($args) {

        $this->drawManaCards(1);

        $manas = ManaCard::getHand(Players::getOpponentId());
        if (!empty($manas)) {
            $maxPower = 99;
            $maxCard = [];
            foreach ($manas as $mana) {
                $power = ManaCard::getPower($mana);
                if ($power < $maxPower) {
                    $maxPower = $power;
                    $maxCard = $mana;
                }
            }

            ManaCard::addCardsToHand([$maxCard], Players::getPlayerId());
            Notifications::moveManaCard(Players::getPlayerId(), [$maxCard], false);
        }
    }

    public function castSpellInteraction($args)
    {
        if ($args != null && $args != "") {
            $mana_ids = explode(",", array_shift($args));
            if (sizeof($mana_ids) != 1) {
                throw new \BgaSystemException("Wrong number of card " . sizeof($mana_ids));
            }

            $cards_before = array_map(function ($mana_id) {
                return ManaCard::isInHand($mana_id);
            }, $mana_ids);

            ManaCard::addCardsToHand($cards_before, Players::getOpponentId());
            Notifications::giveManaCards(Players::getPlayerId(), $cards_before);
        }
    }
}
