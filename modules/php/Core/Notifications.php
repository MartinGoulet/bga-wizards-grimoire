<?php

namespace WizardsGrimoire\Core;

class Notifications {

    static function basicAttackCard(int $player_id, $mana_card) {
        $msg = clienttranslate('${player_name} discard a ${mana_values} for is basic attack');
        self::message($msg, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => [ManaCard::getPower($mana_card)],
        ]);
    }

    static function basicAttack(int $player_id, int $damage, int $life_remaining) {
        $message = clienttranslate('${player_name} received ${damage} from a basic attack');

        self::notifyAll('onHealthChanged', $message, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "life_remaining" => $life_remaining,
            "damage" => $damage,
        ]);
    }

    static function castSpell($player_id, $card_name, $cards_before, $cards_after) {
        $msg = clienttranslate('${player_name} cast ${card_name}');
        self::message($msg, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'card_name' => $card_name,
            'i18n' => ['card_name'],
        ]);

        self::moveManaCard($player_id, $cards_before, $cards_after, '');
    }

    static function chooseSpell($player_id, $card) {
        $msg = clienttranslate('${player_name} chooses ${card_name} from the spell repertoire.');
        self::notifyAll('onChooseSpell', $msg, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'card' => $card,
            'card_name' => SpellCard::getName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function discardSpell($player_id, $card) {
        self::notifyAll('onDiscardSpell', '', [
            'player_id' => intval($player_id),
            'card' => $card,
        ]);
    }

    static function drawManaCards($player_id, $cards) {
        $msg = clienttranslate('${player_name} draws ${mana_values}');
        $args = [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues($cards),
        ];

        $args['cards'] = array_values($cards);
        self::notify($player_id, 'onDrawManaCards', $msg, $args);

        $msg = clienttranslate('${player_name} draws ${nbr_mana_card}');
        $args = [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'nbr_mana_card' => sizeof($cards),
        ];

        $args['cards'] = array_values(Game::anonynizeCards($cards));
        self::notifyAll('onDrawManaCards', $msg, $args, $player_id);
    }

    static function hasNoManaCard(int $player_id, int $value) {
        self::message(clienttranslate('${player_name} does not have a ${value} power mana.'), [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "value" => $value,
        ]);
    }

    static function ongoingSpellActive($variable, $value) {
        self::notifyAll('onOngoingSpellActive', '', [
            "variable" => $variable,
            "value" => $value,
        ]);
    }

    static function manaDeckShuffle($cards) {
        $msg = clienttranslate("Mana deck reshuffle because it's empty");
        self::notifyAll('onManaDeckShuffle', $msg, [
            "cards" => $cards,
        ]);
    }

    static function moveManaCard($player_id, $cards_before, $cards_after, $msg = "@@@", $anonimyze = true) {
        if ($msg == "@@@") {
            $msg = clienttranslate('${player_name} move ${nbr} mana cards');
        }
        $args = [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'nbr' => sizeof($cards_before),
        ];

        $args['cards_before'] = array_values($cards_before);
        $args['cards_after'] = array_values($cards_after);
        self::notify($player_id, 'onMoveManaCards', $msg, $args);

        if ($anonimyze) {
            $args['cards_before'] = array_values(Game::anonynizeCards($cards_before));
            $args['cards_after'] = array_values(Game::anonynizeCards($cards_after));
        }
        self::notifyAll('onMoveManaCards', $msg, $args, $player_id);
    }

    public static function receiveDamageFromCard(string $card_name, int $player_id, int $damage, int $life_remaining) {
        $message = clienttranslate('${player_name} received ${damage} from ${card_name}');

        self::notifyAll('onHealthChanged', $message, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "life_remaining" => $life_remaining,
            "damage" => $damage,
            "card_name" => $card_name,
            "i18n" => ["card_name"],
        ]);
    }

    static function refillSpell($player_id, $card) {
        $msg = clienttranslate('${card_name} is added to the spell repertoire.');
        self::notifyAll('onRefillSpell', $msg, [
            'card' => $card,
            'card_name' => SpellCard::getName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function revealManaCard(int $player_id, array $cards) {
        self::message('${player_name} reveals ${mana_values} from mana deck', [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => self::getPowerValues($cards),
        ]);
    }

    static function revealManaCardHand(int $player_id, array $cards) {
        self::message('${player_name} reveals ${mana_values} from his hand', [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => self::getPowerValues($cards),
        ]);
    }

    static function spellNoEffect() {
        self::message(clienttranslate("The spell has no effect since requirement was not met"));
    }

    /*************************
     **** GENERIC METHODS ****
     *************************/

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

    private static function getPowerValues(array $cards) {
        return array_map(function ($card) {
            return ManaCard::getPower($card);
        }, $cards);
    }
}
