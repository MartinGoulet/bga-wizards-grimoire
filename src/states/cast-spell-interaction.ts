class CastSpellInteractionStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: CastSpellInteractionArgs): void {
      this.game.markCardAsSelected(args.spell);
      if (!this.game.isCurrentPlayerActive()) return;
      this.game.actionManager.setup("castSpellInteraction");
      this.game.actionManager.addActionInteraction(args.spell);
      if (args.spell.type === SpellType.Echo) {
         // Echo
         this.game.actionManager.addArgument(args.previous_spell_played.toString());
      }
      setTimeout(() => {
         this.game.actionManager.activateNextAction();
      }, 10);
   }

   onLeavingState(): void {}

   onUpdateActionButtons(args: CastSpellInteractionArgs): void {}

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface CastSpellInteractionArgs {
   spell: SpellCard;
   previous_spell_played: number;
}
