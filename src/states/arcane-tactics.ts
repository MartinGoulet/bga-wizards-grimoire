class ArcaneTacticsStates implements StateHandler {
   private mana_cards: ManaCard[];

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;
      this.mana_cards = [];

      const count = 4;

      const handleRevealedCardClick = (card: ManaCard) => {
         if (this.mana_cards.length == count) return;
         this.mana_cards.push(card);
         this.game.tableCenter.manaDeck.addCard({ ...card, type: null, isHidden: true });
         this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === count);
         this.game.toggleButtonEnable("btnCancelAction", this.mana_cards.length > 0, "gray");
         this.game.toggleButtonEnable("btn_pass", this.mana_cards.length == 0, "red");
      };

      const handleDeckCardClick = (card: ManaCard) => {
         if (this.mana_cards.length > 0) {
            this.moveCardFromManaDeckToRevealedMana();
            this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === count);
            this.game.toggleButtonEnable("btnCancelAction", this.mana_cards.length > 0, "gray");
            this.game.toggleButtonEnable("btn_pass", this.mana_cards.length == 0, "red");
         }
      };

      this.game.tableCenter.manaRevealed.onCardClick = handleRevealedCardClick;
      this.game.tableCenter.manaDeck.onCardClick = handleDeckCardClick;
   }

   onLeavingState(): void {
      this.game.tableCenter.manaRevealed.onCardClick = null;
      this.game.tableCenter.manaDeck.onCardClick = null;
   }

   onUpdateActionButtons(args: any): void {
      const handleConfirm = () => {
         if (this.mana_cards.length !== 4) return;
         this.game.actionManager.addArgument(this.mana_cards.map((x) => x.id).join(","));
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButtonDisabled("btnConfirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonClientCancel();
   }

   restoreGameState() {
      return new Promise<boolean>(async (resolve) => {
         await this.restore();
         this.game.disableButton("btnConfirm");
         this.game.disableButton("btnCancelAction");
         resolve(true);
      });
   }

   private async restore() {
      while (this.mana_cards.length > 0) {
         await this.moveCardFromManaDeckToRevealedMana();
      }
   }

   private moveCardFromManaDeckToRevealedMana() {
      return new Promise((resolve) => {
         const card = this.mana_cards.pop();
         console.log("Mana pop", { ...this.mana_cards });
         this.game.tableCenter.manaRevealed.addCard(card).then(() => {
            resolve(true);
         });
      });
   }
}
