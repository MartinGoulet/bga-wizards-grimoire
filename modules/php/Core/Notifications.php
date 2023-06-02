<?php

namespace WizardsGrimoire\Core;

class Notifications {

    static function chooseSpell($player_id, $card) {
        $msg = clienttranslate('${player_name} chooses ${card_name} from the spell repertoire.');
        self::notifyAll('onChooseSpell', $msg, [
            'player_id' => $player_id,
            'player_name' => self::getPlayerName($player_id),
            'card' => $card,
            'card_name' => self::getSpellCardName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function drawManaCards($player_id, $cards) {
        self::notify($player_id, 'onDrawManaCards', '', [
            'player_id' => $player_id,
            'player_name' => self::getPlayerName($player_id),
            'cards' => array_values($cards),
            'nbr' => sizeof($cards),
        ]);

        $msg = clienttranslate('${player_name} draws ${nbr} mana cards');
        self::message($msg, [
            'player_id' => $player_id,
            'player_name' => self::getPlayerName($player_id),
            'nbr' => sizeof($cards),
        ]);
    }

    static function refillSpell($player_id, $card) {
        $msg = clienttranslate('$${card_name} is added to the spell repertoire.');
        self::notifyAll('onRefillSpell', $msg, [
            'card' => $card,
            'card_name' => self::getSpellCardName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function spellCoolDown($player_id, $mana_cards_discard) {
        self::notify($player_id, 'onSpellCoolDown', '', [
            'player_id' => $player_id,
            'mana_cards_discard' => $mana_cards_discard,
            // 'player_name' => self::getPlayerName($player_id),
        ]);
    }

    /*************************
     **** GENERIC METHODS ****
     *************************/

    protected static function getSpellCardName($card) {
        Game::get()->card_types[$card['type']]['name'];
    }

    protected static function notifyAll($name, $msg, $args = [], $exclude_player_id = null) {
        if ($exclude_player_id != null) {
            $args['excluded_player_id'] = $exclude_player_id;
        }
        Game::get()->notifyAllPlayers($name, $msg, $args);
    }

    protected static function notify($player_id, $name, $msg, $args = []) {
        Game::get()->notifyPlayer($player_id, $name, $msg, $args);
    }

    protected static function message($msg, $args = [], $exclude_player_id = null) {
        self::notifyAll('message', $msg, $args);
    }

    protected static function messageTo($player_id, $msg, $args = []) {
        self::notify($player_id, 'message', $msg, $args);
    }

    protected static function getPlayerName($player_id) {
        return Game::get()->getPlayerNameById($player_id);
    }
}
