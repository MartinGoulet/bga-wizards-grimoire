class CastSpellStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const repertoire = player_table.spell_repertoire;

      repertoire.setSelectionMode("single");
      repertoire.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         if (selection && selection.length === 1 && player_table.canCast(selection[0])) {
            this.game.enableButton("btn_cast", "blue");
         } else {
            this.game.disableButton("btn_cast");
         }
      };
   }

   onLeavingState(): void {
      const repertoire = this.game.getPlayerTable(this.game.getPlayerId()).spell_repertoire;
      repertoire.setSelectionMode("none");
      repertoire.onSelectionChange = null;
   }

   onUpdateActionButtons(args: any): void {
      const handleCastSpell = () => {
         const repertoire = this.game.getPlayerTable(this.game.getPlayerId()).spell_repertoire;
         const selectedSpell: SpellCard = repertoire.getSelection()[0];

         this.game.markCardAsSelected(selectedSpell);
         this.game.actionManager.setup(selectedSpell);
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButtonDisabled("btn_cast", _("Cast spell"), handleCastSpell);
      this.game.addActionButtonPass();
   }
}
