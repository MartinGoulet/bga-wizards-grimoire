define([
   "dojo",
   "dojo/_base/declare",
   "ebg/core/gamegui",
   "ebg/counter",
   "ebg/stock",
   g_gamethemeurl + "modules/js/core_patch_tooltip_position.js",
], function (dojo, declare) {
   return declare(
      "bgagame.wizardsgrimoireext",
      [ebg.core.gamegui, wg.core_patch_tooltip_position],
      new Game(),
   );
});
