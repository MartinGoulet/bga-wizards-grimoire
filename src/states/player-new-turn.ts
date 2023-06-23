class PlayerNewTurnStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): PlayerNewTurnArgs {
      this.game.playersTables.forEach((table) => {
         table.setPreviousBasicAttack(args.previous_basic_attack);
      });

      if (!this.game.isCurrentPlayerActive()) return;
   }

   onLeavingState(): void {}

   onUpdateActionButtons(args: any): void {}

   restoreGameState() {}
}

interface PlayerNewTurnArgs {
   previous_basic_attack: number;
}
