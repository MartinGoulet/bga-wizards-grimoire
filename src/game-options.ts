class GameOptions {
   constructor(private game: WizardsGrimoire) {
      const display = document.getElementById("game-phases");
      if (display) {
         display.parentElement.removeChild(display);
      }

      const { phase1, phase2, phase3, phase4, phase5 } = {
         phase1: _("Choose a New Spell"),
         phase2: _("Spell Cool Down"),
         phase3: _("Gain 3 Mana"),
         phase4: _("Cast Spells"),
         phase5: _("Basic Attack"),
      };

      const html = `
            <div class="player-board" id="game-phases">
                <div class="title">${_("Turn order")}</div>
                <div class="player-board-inner">
                    <ul id="wg-phases" data-phase="1">
                        <li><div class="wg-icon"></div><div class="wg-phase-name">1. ${phase1}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">2. ${phase2}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">3. ${phase3}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">4. ${phase4}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">5. ${phase5}</div></li>
                    </ul>
                </div>
            </div>`;

      document.getElementById("player_boards").insertAdjacentHTML("beforeend", html);
      this.game.updatePlayerOrdering();
   }

   public setPhase(phase: number) {
      document.getElementById("wg-phases").dataset.phase = phase.toString();
      document.getElementById("table").dataset.phase = phase.toString();
   }
}
