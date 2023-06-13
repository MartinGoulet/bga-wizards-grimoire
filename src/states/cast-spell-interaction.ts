class CastSpellInteractionStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: CastSpellInteractionArgs): void {
      this.game.markCardAsSelected(args.spell);
      if (!this.game.isCurrentPlayerActive()) return;
      this.game.actionManager.setup("castSpellInteraction");
      this.game.actionManager.addActionInteraction(args.spell);
      setTimeout(() => {
         this.game.actionManager.activateNextAction();
      }, 10);
   }

   onLeavingState(): void {}

   onUpdateActionButtons(args: CastSpellInteractionArgs): void {}

   restoreGameState() {}
}

interface CastSpellInteractionArgs {
   spell: SpellCard;
}
