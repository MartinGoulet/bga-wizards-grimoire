class PlayerNewTurnStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: PlayerNewTurnArgs): PlayerNewTurnArgs {
      console.log("Player new turn: ", args);
      this.game.playersTables.forEach((table) => {
         table.setPreviousBasicAttack(args.previous_basic_attack);
      });

      this.game.playersPanels
         .filter((panel) => panel.isFirst)
         .forEach((panel) => {
            panel.last_attack_damage.setValue(args.last_basic_attack_damage);
            panel.last_attack_power.setValue(args.previous_basic_attack);
         });

      if (!this.game.isCurrentPlayerActive()) return;
   }

   onLeavingState(): void {}

   onUpdateActionButtons(args: PlayerNewTurnArgs): void {}

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface PlayerNewTurnArgs {
   previous_basic_attack: number;
   last_basic_attack_damage: number;
}
