<?php

namespace WizardsGrimoire\Objects;

class CardLocation {
    static function Deck() {
        return 'deck';
    }

    static function Discard() {
        return 'discard';
    }

    static function Hand() {
        return 'hand';
    }

    static function SpellSlot() {
        return 'slot';
    }

    static function PlayerSpellRepertoire(int $player_id) {
        return "spr_$player_id";
    }

    static function PlayerManaCoolDown(int $player_id, int $position) {
        return "mcd_$player_id" . "_$position";
    }
}
