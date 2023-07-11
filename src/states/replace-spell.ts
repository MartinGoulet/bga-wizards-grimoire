class ReplaceSpellStates implements StateHandler {
   private player_table: PlayerTable;
   constructor(private game: WizardsGrimoire) {}

   onEnteringState({ exclude }: ReplaceSpellArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table = this.game.getCurrentPlayerTable();

      const handleSelection = () => {
         const chooseSpell = this.player_table.spell_repertoire.getSelection();
         const replaceSpell = this.game.tableCenter.spellPool.getSelection();
         this.game.toggleButtonEnable("btn_replace", chooseSpell.length == 1 && replaceSpell.length == 1);
      };

      this.game.tableCenter.spellPool.setSelectionMode("single");
      this.game.tableCenter.spellPool.onSelectionChange = handleSelection;
      this.player_table.spell_repertoire.setSelectionMode("single");
      this.player_table.spell_repertoire.onSelectionChange = handleSelection;

      const selectableCards = this.player_table.spell_repertoire
         .getCards()
         .filter((card) => exclude.indexOf(Number(card.location_arg)) < 0);

      this.player_table.spell_repertoire.setSelectableCards(selectableCards);
   }

   onLeavingState(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
      this.player_table.spell_repertoire.setSelectionMode("none");
      this.player_table.spell_repertoire.onSelectionChange = null;
   }

   onUpdateActionButtons(args: any): void {
      const handleReplace = () => {
         const newSpell = this.game.tableCenter.spellPool.getSelection()[0];
         const oldSpell = this.player_table.spell_repertoire.getSelection()[0];
         if (!newSpell || !oldSpell) return;

         this.game.actionManager.addArgument(oldSpell.location_arg.toString());
         this.game.actionManager.addArgument(newSpell.id.toString());
         this.game.actionManager.activateNextAction();
      };

      const handleIgnore = () => {
         const text = _("Are-you sure you want to ignore this effect?");
         this.game.confirmationDialog(text, () => this.game.actionManager.activateNextAction());
      };

      this.game.addActionButtonDisabled("btn_replace", _("Replace"), handleReplace);
      this.game.addActionButtonRed("btn_ignore", _("Ignore"), handleIgnore);
      this.game.addActionButtonClientCancel();
   }
   restoreGameState(): Promise<boolean> {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface ReplaceSpellArgs {
   exclude: number[];
}
