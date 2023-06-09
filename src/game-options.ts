class GameOptions {
   constructor(private game: WizardsGrimoire) {
      const playerBoards = document.getElementById("");

      const { phase1, phase2, phase3, phase4, phase5 } = {
         phase1: _("Choose a New Spell"),
         phase2: _("Spell Cool Down"),
         phase3: _("Gain 3 Mana"),
         phase4: _("Cast Spells"),
         phase5: _("Basic Attack"),
      };

      const html = `
            <div class="player-board">
                <div class="player-board-inner">
                    <div id="wg-phase-selector" data-phase="1"></div>
                    <ul id="wg-phases">
                        <li><div class="wg-icon"></div><div class="wg-phase-name">1. ${phase1}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">2. ${phase2}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">3. ${phase3}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">4. ${phase4}</div></li>
                        <li><div class="wg-icon"></div><div class="wg-phase-name">5. ${phase5}</div></li>
                    </ul>
                </div>
            </div>`;

      dojo.place(html, "player_boards");

      this.game.updatePlayerOrdering();
   }

   public setPhase(phase: number) {
      document.getElementById("wg-phase-selector").dataset.phase = phase.toString();
      document.getElementById("table").dataset.phase = phase.toString();
   }
}
