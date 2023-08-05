class SelectSpellStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: SelectSpellArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table = this.game.getPlayerTable(args.player_id);

      this.player_table.spell_repertoire.setSelectionMode("single");

      if (args.selection) {
         this.player_table.spell_repertoire.setSelectableCards(args.selection);
      }

      this.player_table.spell_repertoire.onSelectionChange = (selection: SpellCard[]) => {
         this.game.toggleButtonEnable("btn_confirm", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {
      this.player_table.spell_repertoire.setSelectionMode("none");
      this.player_table.spell_repertoire.onSelectionChange = null;
   }

   onUpdateActionButtons(args: SelectSpellArgs): void {
      const handleConfirm = () => {
         if (this.player_table.spell_repertoire.getSelection().length == 0) return;

         const spell = this.player_table.spell_repertoire.getSelection()[0];
         this.game.actionManager.addArgument(spell.location_arg.toString());
         this.game.actionManager.activateNextAction();
      };

      const handleIgnore = () => {
         const text = _("Are-you sure you want to ignore this effect?");
         this.game.confirmationDialog(text, args.ignore);
      };

      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      if (args.cancel) {
         this.game.addActionButtonClientCancel();
      }
      if (args.pass) {
         this.game.addActionButtonPass();
      }
      if (args.ignore) {
         this.game.addActionButtonRed("btn_ignore", _("Ignore"), handleIgnore);
      }
      this.game.disableButton("btn_confirm");
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface SelectSpellArgs {
   player_id: number;
   selection?: SpellCard[];
   cancel: boolean;
   pass?: boolean;
   ignore?: () => void;
}
