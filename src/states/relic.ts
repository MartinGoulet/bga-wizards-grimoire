class RelicStates implements StateHandler {
   public args: RelicArgs
   constructor(private game: Game) {}

   onEnteringState(args: RelicArgs): void {
      this.args = args;
      this.game.markCardAsSelected(args.spell);
      if (!this.game.isCurrentPlayerActive()) return;

      this.game.actionManager.setup("actCastSpellInteraction");
      this.game.actionManager.addActionRelic(args.spell);
      setTimeout(() => {
         this.game.actionManager.activateNextAction();
      }, 10);
   }

   onLeavingState(): void {
      this.args = undefined;
   }

   onUpdateActionButtons(args: RelicArgs): void {}

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface RelicArgs {
   spell: SpellCard;
   previous_spell_played: number;
}
