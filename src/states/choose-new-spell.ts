class ChooseNewSpellStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table = this.game.getPlayerTable(this.game.getPlayerId());

      if (this.player_table.getSpellSlotAvailables().length == 0) {
         this.clearSelectionMode();
      } else if (this.player_table.spell_repertoire.getCards().length < 6) {
         this.onEnteringStateChoose();
      } else {
         this.onEnteringStateReplace();
      }
   }

   onEnteringStateChoose(): void {
      const handleSelection = (selection: SpellCard[], lastChange: SpellCard) => {
         if (selection && selection.length === 1) {
            this.game.enableButton("btn_confirm", "blue");
         } else {
            this.game.disableButton("btn_confirm");
         }
      };

      this.game.tableCenter.spellPool.setSelectionMode("single");
      this.game.tableCenter.spellPool.onSelectionChange = handleSelection;
   }

   onEnteringStateReplace(): void {
      const handleSelection = () => {
         const chooseSpell = this.player_table.spell_repertoire.getSelection();
         const replaceSpell = this.game.tableCenter.spellPool.getSelection();
         this.game.toggleButtonEnable("btn_replace", chooseSpell.length == 1 && replaceSpell.length == 1);
      };

      this.game.tableCenter.spellPool.setSelectionMode("single");
      this.game.tableCenter.spellPool.onSelectionChange = handleSelection;
      this.player_table.spell_repertoire.setSelectionMode("single");
      this.player_table.spell_repertoire.onSelectionChange = handleSelection;

      const positions = this.player_table.getSpellSlotAvailables();
      const selectableCards = this.player_table.spell_repertoire
         .getCards()
         .filter((card) => positions.indexOf(Number(card.location_arg)) >= 0);

      this.player_table.spell_repertoire.setSelectableCards(selectableCards);
      this.game.setGamestateDescription("Replace");
   }

   onLeavingState(): void {
      this.clearSelectionMode();
   }
   onUpdateActionButtons(args: any): void {
      this.player_table = this.game.getPlayerTable(this.game.getPlayerId());

      if (this.player_table.spell_repertoire.getCards().length < 6) {
         this.game.addActionButtonDisabled("btn_confirm", _("Choose"), () => {
            const selectedSpell = this.game.tableCenter.spellPool.getSelection()[0];
            this.game.takeAction("chooseSpell", { id: selectedSpell.id });
         });
      } else {
         const available_slots = this.player_table.getSpellSlotAvailables();
         if (available_slots.length > 0) {
            this.game.addActionButtonDisabled("btn_replace", _("Replace"), () => {
               const selectedSpell = this.game.tableCenter.spellPool.getSelection()[0];
               const replacedSpell = this.player_table.spell_repertoire.getSelection()[0];

               this.game.takeAction("replaceSpell", {
                  new_spell_id: selectedSpell.id,
                  old_spell_id: replacedSpell.id,
               });
            });
         }
      }
      if (this.player_table.spell_repertoire.getCards().length == 6) {
         this.game.addActionButtonPass();
      }
   }

   restoreGameState() {}

   clearSelectionMode(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
      this.player_table.spell_repertoire.setSelectionMode("none");
      this.player_table.spell_repertoire.onSelectionChange = null;
   }
}
