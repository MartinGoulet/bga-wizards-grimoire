class BasicAttackBattleVisionStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: BasicAttackBattleVisionArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const { hand } = this.game.getCurrentPlayerTable();

      const cards = hand.getCards();
      const cardsFiltered = cards.filter((card) => card.type === args.value.toString());

      hand.setSelectionMode("single");
      hand.setSelectableCards(cardsFiltered);

      hand.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_block", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {
      const { hand } = this.game.getCurrentPlayerTable();
      hand.setSelectionMode("none");
      hand.onSelectionChange = null;
   }

   onUpdateActionButtons(args: BasicAttackBattleVisionArgs): void {
      const handleSelect = () => {
         const { hand } = this.game.getCurrentPlayerTable();
         const selectedMana: ManaCard = hand.getSelection()[0];
         if (selectedMana) {
            this.game.takeAction("blockBasicAttack", { id: selectedMana.id });
         }
      };

      this.game.addActionButtonDisabled("btn_block", _("Block"), handleSelect);
      this.game.addActionButtonPass();
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface BasicAttackBattleVisionArgs {
   value: number;
}
