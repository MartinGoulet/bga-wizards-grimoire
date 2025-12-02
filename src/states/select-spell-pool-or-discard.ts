class SelectSpellPoolOrDiscardState implements StateHandler {
   private discardedSpells: SpellCard[] = [];

   constructor(private game: Game) {}

   onEnteringState(args: SelectSpellPoolOrDiscardArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.game.tableCenter.spellPool.setSelectionMode("single");
      this.game.tableCenter.spellPool.onSelectionChange = (selection: SpellCard[]) => {
         this.game.toggleButtonEnable("btn_confirm", selection && selection.length === 1);
      };

      this.discardedSpells = [...this.game.tableCenter.spellDiscard.getCards()];

      const displaySpellRevealed = async () => {
         const spellRevealed = this.game.tableCenter.spellRevealed;
         const spells = this.discardedSpells.map((card: SpellCard) => {
            const newCard: SpellCard = { ...card };
            newCard.id = card.id + 100000;
            return newCard;
         })
         await spellRevealed.addCards(spells);
         spellRevealed.setSelectionMode("single");
         spellRevealed.onSelectionChange = (selection: SpellCard[]) => {
            this.game.toggleButtonEnable("btn_confirm", selection && selection.length === 1);
         };
      };

      displaySpellRevealed();
   }

   onLeavingState(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
   }

   onUpdateActionButtons(args: SelectSpellPoolOrDiscardArgs): void {
      const handleConfirm = () => {
         const spell = this.game.tableCenter.spellPool.getSelection()[0];
         this.game.actionManager.addArgument(spell.id.toString());
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButtonDisabled("btn_confirm", _("Confirm"), handleConfirm);
      if (args.skip) {
         this.game.addActionButtonRed("btn_skip", _(args.skip.label), args.skip.action);
      }
      if (args.cancel !== false) {
         this.game.addActionButtonClientCancel();
      }
   }

   restoreGameState(): Promise<boolean> {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface SelectSpellPoolOrDiscardArgs {
   skip?: {
      label: string;
      action: () => void;
   };
   cancel?: boolean;
}
