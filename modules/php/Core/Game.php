<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire;

/*
 * Game: a wrapper over table object to allow more generic modules
 */

class Game {
    public static function get() {
        return WizardsGrimoire::get();
    }
}
