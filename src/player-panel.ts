class PlayerPanel {
   public player_id: number;
   public hand_counter: ebg.counter;
   public turn_counter: ebg.counter;

   constructor(public game: WizardsGrimoire, player: WizardsGrimoirePlayerData) {
      this.player_id = Number(player.id);

      let smallBoard = document.getElementById(`player_small_board_${player.id}`);
      if (smallBoard) {
         smallBoard.parentElement.removeChild(smallBoard);
      }

      const player_turn_text =
         this.player_id == game.gamedatas.first_player ? _("First choice") : _("First attacker");

      const player_turn_class = this.player_id == game.gamedatas.first_player ? "choice" : "attacker";

      const smallBoardHtml = `<div id="player_small_board_${player.id}" class="player_small_board">
            <div id="hand-icon-wrapper-${player.id}" class="icon-wrapper">
                <div>
                    <div id="player_small_board_${player.id}_hand_icon" class="icon"></div>
                    <div id="player_small_board_${player.id}_hand_value" class="text"></div>
                </div>
                <div>
                    <div class="text">${_("Turn")} </div>  
                    <div id="player_small_board_${player.id}_turn_value" class="text"></div>
                </div>
                <div class="break"></div>
                <div class="${player_turn_class}">
                    <div class="text">${player_turn_text}</div> 
                </div>
            </div>
         </div>`;

      document.getElementById(`player_board_${player.id}`).insertAdjacentHTML("beforeend", smallBoardHtml);

      this.hand_counter = this.createCounter(`player_small_board_${player.id}_hand_value`, 0);
      this.turn_counter = this.createCounter(`player_small_board_${player.id}_turn_value`, player.turn);
   }

   private createCounter(target: string, value: number) {
      var counter = new ebg.counter();
      counter.create(target);
      counter.setValue(value);
      return counter;
   }
}
