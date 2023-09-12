class SelectManaReturnDeckStates implements StateHandler {
   private mana_cards: ManaCard[];
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: SelectManaReturnDeckStatesArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      this.mana_cards = [];

      const { count } = args;

      const handleHandCardClick = (card: ManaCard) => {
         if (args.exact && this.mana_cards.length >= args.count) return;

         this.mana_cards.push(card);
         this.game.tableCenter.manaDeck.addCard({ ...card, type: null, isHidden: true });
         this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === count);
         this.game.toggleButtonEnable("btnCancelAction", this.mana_cards.length > 0, "gray");
         this.game.toggleButtonEnable("btn_pass", this.mana_cards.length == 0, "red");
      };

      const handleDeckCardClick = (card: ManaCard) => {
         if (this.mana_cards.length > 0) {
            this.moveCardFromManaDeckToHand();
            this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === count);
            this.game.toggleButtonEnable("btnCancelAction", this.mana_cards.length > 0, "gray");
            this.game.toggleButtonEnable("btn_pass", this.mana_cards.length == 0, "red");
         }
      };

      this.player_table = this.game.getCurrentPlayerTable();
      this.player_table.hand.onCardClick = handleHandCardClick;
      this.game.tableCenter.manaDeck.onCardClick = handleDeckCardClick;
   }

   onLeavingState(): void {
      this.player_table.hand.onCardClick = null;
      this.game.tableCenter.manaDeck.onCardClick = null;
   }

   onUpdateActionButtons(args: SelectManaReturnDeckStatesArgs): void {
      const handleConfirm = () => {
         this.game.actionManager.addArgument(this.mana_cards.map((x) => x.id).join(","));
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButtonDisabled("btnConfirm", _("Confirm"), handleConfirm);

      if (args.canPass) {
         const handlePass = () => {
            this.game.actionManager.activateNextAction();
         };
         this.game.addActionButtonRed("btn_pass", _("Pass"), handlePass);
      }

      if (args.canCancel) {
         this.game.addActionButtonClientCancel();
      } else {
         const handleCancel = (evt: any): void => {
            this.restoreGameState();
         };
         this.game.addActionButtonDisabled("btnCancelAction", _("Cancel"), handleCancel);
      }
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
         await this.moveCardFromManaDeckToHand();
      }
   }

   private moveCardFromManaDeckToHand() {
      return new Promise((resolve) => {
         const card = this.mana_cards.pop();
         console.log("Mana pop", { ...this.mana_cards });
         this.player_table.hand.addCard(card).then(() => {
            resolve(true);
         });
      });
   }
}

interface SelectManaReturnDeckStatesArgs {
   count: number;
   exact: boolean;
   canCancel: boolean;
   canPass: boolean;
}
