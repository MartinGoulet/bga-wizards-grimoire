class GameOptions {
   constructor(private game: WizardsGrimoire) {
      const playerBoards = document.getElementById("");

      const html = `
            <div class="player-board">
                <div class="player-board-inner">
                    <ul id="dt-phases">
                        <li id="dt-p-unkeep"><div class="dt-icon i-unkeep-phase"></div><div class="dt-phase-name">Choose a New Spell</div></li>
                        <li id="dt-p-unkeep"><div class="dt-icon i-unkeep-phase"></div><div class="dt-phase-name">Spell Cool Down</div></li>
                        <li id="dt-p-main_1"><div class="dt-icon i-cards"></div><div class="dt-phase-name">Gain 3 Mana</div></li>
                        <li id="dt-p-offensive"><div class="dt-icon i-main-phase"></div><div class="dt-phase-name">Cast Spells</div></li>
                        <li id="dt-p-targeting"><div class="dt-icon i-roll-phase"></div><div class="dt-phase-name">Basic Attack</div></li>
                    </ul>
                </div>
            </div>`;

      dojo.place(html, "player_boards");

      this.game.updatePlayerOrdering();
   }
}
