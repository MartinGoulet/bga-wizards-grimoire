class CastSpellWithManaStates implements StateHandler {
   private mana_cards: ManaCard[];
   private spell: SpellCard;
   private mana_deck: Deck<ManaCard>;
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: CastSpellWithManaArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      this.mana_cards = [];
      this.spell = args.card;

      let { cost, type } = this.game.getCardType(this.spell);

      this.player_table = this.game.getCurrentPlayerTable();
      this.mana_deck = this.player_table.mana_cooldown[this.spell.location_arg];

      cost = Math.max(cost - this.player_table.getDiscountNextSpell(), 0);
      if (type == "red") {
         cost = Math.max(cost - this.player_table.getDiscountNextAttack(), 0);
      }

      const handleHandCardClick = (card: ManaCard) => {
         if (this.mana_cards.length === cost) return;

         this.mana_cards.push(card);
         this.mana_deck.addCard(card);
         this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === cost);
      };

      const handleDeckCardClick = (card: ManaCard) => {
         if (this.mana_cards.length > 0) {
            this.moveCardFromManaDeckToHand();
            this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === cost);
         }
      };

      this.player_table.hand.onCardClick = handleHandCardClick;
      this.mana_deck.onCardClick = handleDeckCardClick;

      log("mana deck", this.mana_deck);
   }

   onLeavingState(): void {
      this.player_table.hand.onCardClick = null;
      this.mana_deck.onCardClick = null;
   }

   onUpdateActionButtons(args: CastSpellWithManaArgs): void {
      const handleConfirm = () => {
         this.game.actionManager.addArgument(this.mana_cards.map((x) => x.id).join(","));
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButton("btnConfirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonClientCancel();

      this.game.toggleButtonEnable("btnConfirm", args.cost == 0);
   }

   restoreGameState() {
      return new Promise<boolean>(async (resolve) => {
         await this.restore();
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
         this.player_table.hand.addCard(card).then(() => {
            resolve(true);
         });
         if (this.mana_cards.length > 0) {
            const topCard = this.mana_cards[this.mana_cards.length - 1];
            this.mana_deck.setCardNumber(this.mana_cards.length, topCard);
         }
      });
   }
}

interface CastSpellWithManaArgs {
   cost: number;
   card: SpellCard;
}
