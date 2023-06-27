class BasicAttackBattleVisionStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: BasicAttackBattleVisionArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const { hand } = this.game.getPlayerTable(this.game.getPlayerId());

      const cards = hand.getCards();
      const cardsFiltered = cards.filter((card) => card.type === args.value.toString());

      hand.setSelectionMode("single");
      hand.setSelectableCards(cardsFiltered);

      hand.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_block", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {}

   onUpdateActionButtons(args: BasicAttackBattleVisionArgs): void {
      const handleSelect = () => {
         const { hand } = this.game.getPlayerTable(this.game.getPlayerId());
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
