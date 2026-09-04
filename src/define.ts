var BgaZoom;

define([
   "dojo",
   "dojo/_base/declare",
   getLibUrl('bga-zoom', '1.x'),
   "ebg/core/gamegui",
   "ebg/counter",
   "ebg/stock",
   g_gamethemeurl + "modules/js/core_patch_tooltip_position.js",
], function (dojo, declare, BgaZoom1) {
   BgaZoom = BgaZoom1;
   return declare(
      "bgagame.wizardsgrimoire",
      [ebg.core.gamegui, wg.core_patch_tooltip_position],
      new Game(),
   );
});
