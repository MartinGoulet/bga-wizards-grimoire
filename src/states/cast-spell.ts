class CastSpellStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      this.game.clearSelection();
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const repertoire = player_table.spell_repertoire;

      repertoire.setSelectionMode("single");
      repertoire.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         const canSelect =
            selection &&
            selection.length === 1 &&
            player_table.canCast(selection[0]) &&
            player_table.mana_cooldown[lastChange.location_arg].getCardNumber() == 0;

         this.game.toggleButtonEnable("btn_cast", canSelect);
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
         this.game.actionManager.setup("castSpell", "actionCastMana");
         this.game.actionManager.addAction(selectedSpell);
         this.game.actionManager.activateNextAction();
      };

      const handlePass = () => {
         this.game.takeAction("pass");
      };

      if (this.hasSpellAvailable()) {
         this.game.addActionButtonDisabled("btn_cast", _("Cast spell"), handleCastSpell);
         this.game.addActionButtonPass();
      } else {
         this.game.addActionButton("btn_pass", _("Move to basic attack"), handlePass);
      }
   }

   restoreGameState() {}

   hasSpellAvailable() {
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      return player_table.getManaDeckWithSpellOver().length < player_table.spell_repertoire.getCards().length;
   }
}
