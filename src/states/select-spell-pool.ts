class SelectSpellPoolStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.game.tableCenter.spellPool.setSelectionMode("single");
      this.game.tableCenter.spellPool.onSelectionChange = (selection: SpellCard[]) => {
         this.game.toggleButtonEnable("btn_confirm", selection && selection.length === 1);
      };
   }
   onLeavingState(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
   }

   onUpdateActionButtons(args: SelectSpellPoolStatesArgs): void {
      const handleConfirm = () => {
         const spell = this.game.tableCenter.spellPool.getSelection()[0];
         this.game.actionManager.addArgument(spell.id.toString());
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      if (args.skip) {
         this.game.addActionButtonRed("btn_skip", _(args.skip.label), args.skip.action);
      }
      this.game.addActionButtonClientCancel();
      this.game.disableButton("btn_confirm");
   }

   restoreGameState(): Promise<boolean> {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface SelectSpellPoolStatesArgs {
   skip?: {
      label: string;
      action: () => void;
   };
}
