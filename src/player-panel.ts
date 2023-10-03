class PlayerPanel {
   public player_id: number;
   public hand_counter: ebg.counter;
   public turn_counter: ebg.counter;

   public last_attack_power: ebg.counter;
   public last_attack_damage: ebg.counter;

   constructor(public game: WizardsGrimoire, player: WizardsGrimoirePlayerData, public isFirst: boolean) {
      this.player_id = Number(player.id);

      let smallBoard = document.getElementById(`player_small_board_${player.id}`);
      if (smallBoard) {
         smallBoard.parentElement.removeChild(smallBoard);
      }

      const player_turn_text =
         this.player_id == game.gamedatas.first_player ? _("First choice") : _("First attacker");

      const player_turn_class = this.player_id == game.gamedatas.first_player ? "choice" : "attacker";

      const htmlGameInfo =
         isFirst == false
            ? ""
            : `<div class="break"></div>
            <div class="game-info">
               ${_("Last basic attack")}
               <div id="last_attack_power" class="wg-icon-log i-mana-x"></div>
               <div class="wg-icon-log i-dmg_undef"><span id="last_attack_damage"></span></div>
            </div>
      </div>`;

      const smallBoardHtml = `<div id="player_small_board_${player.id}" class="player_small_board">
            <div id="hand-icon-wrapper-${player.id}" class="icon-wrapper">
                <div>
                <div id="player_small_board_${player.id}_hand_value" class="text"></div>
                <div id="player_small_board_${player.id}_hand_icon" class="icon hand"></div>
                </div>
                <div>
                    <div class="text">${_("Turn")} </div>  
                    <div id="player_small_board_${player.id}_turn_value" class="text"></div>
                </div>
                <div class="break"></div>
                <div class="${player_turn_class}">
                    <div class="text">${player_turn_text}</div> 
                </div>
                ${htmlGameInfo}
            </div>
         </div>`;

      document.getElementById(`player_board_${player.id}`).insertAdjacentHTML("beforeend", smallBoardHtml);

      this.hand_counter = this.createCounter(`player_small_board_${player.id}_hand_value`, 0);
      this.turn_counter = this.createCounter(`player_small_board_${player.id}_turn_value`, player.turn);
      if (isFirst) {
         this.last_attack_power = this.createCounter(
            "last_attack_power",
            game.gamedatas.globals.previous_basic_attack,
         );
         this.last_attack_damage = this.createCounter(
            "last_attack_damage",
            game.gamedatas.globals.last_basic_attack_damage,
         );
         this.game.setTooltip(
            "last_attack_power",
            _("Mana power used by the player for the last basic attack"),
         );
         this.game.setTooltip(
            "last_attack_damage",
            _("Damage received by the player during the last basic attack"),
         );
      }
   }

   private createCounter(target: string, value: number) {
      var counter = new ebg.counter();
      counter.create(target);
      counter.setValue(value);
      return counter;
   }
}
