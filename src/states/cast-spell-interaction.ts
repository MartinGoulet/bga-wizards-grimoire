class CastSpellInteractionStates implements StateHandler {
   public args: CastSpellInteractionArgs
   constructor(private game: Game) {}

   onEnteringState(args: CastSpellInteractionArgs): void {
      this.args = args;
      this.game.markCardAsSelected(args.spell);
      if (!this.game.isCurrentPlayerActive()) return;

      this.game.actionManager.setup("actCastSpellInteraction");
      this.game.actionManager.addActionInteraction(args.spell);
      if ([SpellType.Sand1.Echo, SpellType.ForbiddenScrolls.Echo].includes(args.spell.type)) {
         // Echo
         this.game.actionManager.addArgument(args.previous_spell_played.toString());
      }
      setTimeout(() => {
         this.game.actionManager.activateNextAction();
      }, 10);
   }

   onLeavingState(): void {
      this.args = undefined;
   }

   onUpdateActionButtons(args: CastSpellInteractionArgs): void {}

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface CastSpellInteractionArgs {
   spell: SpellCard;
   previous_spell_played: number;
}
