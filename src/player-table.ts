class Hand extends LineStock<ManaCard> {
   constructor(manager: CardManager<ManaCard>, element: HTMLElement, protected current_player: boolean) {
      super(manager, element, {
         center: true,
         wrap: "wrap",
         sort: sortFunction("type", "type_arg"),
      });
   }

   public addCard(
      card: ManaCard,
      animation?: CardAnimation<ManaCard>,
      settings?: AddCardSettings,
   ): Promise<boolean> {
      const copy: ManaCard = { ...card, isHidden: !this.current_player };
      if (!this.current_player) {
         copy.type = null;
      }
      return super.addCard(copy, animation, settings);
   }
}

class PlayerTable {
   public player_id: number;

   public spell_repertoire: SlotStock<SpellCard>;
   public mana_cooldown: { [pos: number]: ManaDeck } = {};
   public hand: LineStock<ManaCard>;

   private current_player: boolean;

   constructor(private game: WizardsGrimoire, player: WizardsGrimoirePlayerData) {
      this.player_id = Number(player.id);
      this.current_player = this.player_id == this.game.getPlayerId();

      const { id: pId, name: pName, color: pColor } = player;
      const pCurrent = this.current_player.toString();

      const html = `
            <div style="--color: #${pColor}" data-color="${pColor}">
               <div id="player-table-${pId}" class="player-table whiteblock" data-discount-next-spell="0" data-discount-next-attack="0">
                  <span class="wg-title">${_("Spell Repertoire")}</span>
                  <div id="player-table-${pId}-spell-repertoire" class="spell-repertoire"></div>
                  <div id="player-table-${pId}-mana-cooldown" class="mana-cooldown">
                     <div id="player_table-${pId}-mana-deck-1" class="mana-deck"></div>
                     <div id="player_table-${pId}-mana-deck-2" class="mana-deck"></div>
                     <div id="player_table-${pId}-mana-deck-3" class="mana-deck"></div>
                     <div id="player_table-${pId}-mana-deck-4" class="mana-deck"></div>
                     <div id="player_table-${pId}-mana-deck-5" class="mana-deck"></div>
                     <div id="player_table-${pId}-mana-deck-6" class="mana-deck"></div>
                  </div>
               </div>
               <div class="player-table whiteblock player-hand">
                  <span class="wg-title">${_("Hand")}</span>
                  <div id="player-table-${pId}-hand-cards" class="hand cards" data-player-id="${pId}" data-current-player="${pCurrent}" data-my-hand="${pCurrent}"></div>
               </div>
            </div>`;

      dojo.place(html, "tables");

      this.spell_repertoire = new SlotStock(
         game.spellsManager,
         document.getElementById(`player-table-${this.player_id}-spell-repertoire`),
         {
            slotsIds: [1, 2, 3, 4, 5, 6],
            slotClasses: ["wg-spell-slot"],
            mapCardToSlot: (card) => card.location_arg,
         },
      );

      for (let index = 1; index <= 6; index++) {
         const divDeck = document.getElementById(`player_table-${pId}-mana-deck-${index}`);
         this.mana_cooldown[index] = new ManaDeck(game.manasManager, divDeck, index);
      }

      const board: PlayerBoardInfo = game.gamedatas.player_board[pId];
      this.spell_repertoire.addCards(board.spells);

      Object.keys(board.manas).forEach((pos: string) => {
         const deck = this.mana_cooldown[Number(pos)];
         board.manas[pos].forEach((card) => {
            deck.addCard(card, null, { index: 0 });
         });
      });

      this.hand = new Hand(
         game.manasManager,
         document.getElementById(`player-table-${pId}-hand-cards`),
         this.current_player,
      );
      this.hand.addCards(board.hand ?? []);
   }

   public canCast(card: SpellCard): boolean {
      const { cost } = this.game.getCardType(card);
      return this.hand.getCards().length >= cost;
   }

   public getSpellSlotAvailables(): number[] {
      if (this.spell_repertoire.getCards().length < 6) {
         return arrayRange(this.spell_repertoire.getCards().length + 1, 6);
      }

      const slots: number[] = [];
      Object.keys(this.mana_cooldown).forEach((index) => {
         const deck: ManaDeck = this.mana_cooldown[index];
         if (deck.getCards().length == 0) {
            slots.push(Number(index));
         }
      });
      return slots;
   }

   public getManaDecks(exclude: number[] = []) {
      const positions = [];
      for (let index = 1; index <= 6; index++) {
         if (exclude.indexOf(index) < 0) {
            positions.push(index);
         }
      }
      return positions.map((position: number) => this.mana_cooldown[position]);
   }

   public getManaDeckWithSpellOver(exclude: number[] = []) {
      const spellsPosition: number[] = this.spell_repertoire
         .getCards()
         .map((card) => Number(card.location_arg));

      const positions = [];
      for (let index = 1; index <= 6; index++) {
         if (exclude.indexOf(index) < 0 && spellsPosition.indexOf(index) >= 0) {
            positions.push(index);
         }
      }
      return positions.map((position: number) => this.mana_cooldown[position]);
   }

   public onChooseSpell(card: SpellCard) {
      this.spell_repertoire.addCard(card, {
         fromStock: this.game.tableCenter.spellPool,
      });
   }

   public async onDrawManaCard(cards: ManaCard[]) {
      for (let index = 0; index < cards.length; index++) {
         const card = cards[index];
         const topHiddenCard: ManaCard = { ...card, isHidden: true };

         const { manaDeck } = this.game.tableCenter;
         manaDeck.setCardNumber(manaDeck.getCardNumber(), topHiddenCard);
         await this.hand.addCard(card);
      }
   }

   public async onMoveManaCard(before: ManaCard, after: ManaCard) {
      const stockBeforeManager = this.game.manasManager.getCardStock(before);
      const stockBefore = this.getStock(before);
      const stockAfter = this.getStock(after);

      if (stockBefore !== stockBeforeManager) {
         // The card already move (client state)
         return;
      }

      if (!stockAfter.contains(after)) {
         const newCard: ManaCard = { ...after, isHidden: this.isStockHidden(stockAfter) };
         await stockAfter.addCard(newCard);
      }
   }

   private isStockHidden(stock: CardStock<ManaCard>) {
      return (this.hand == stock && !this.current_player) || this.game.tableCenter.manaDeck == stock;
   }

   private getStock(card: ManaCard): CardStock<ManaCard> {
      if (card.location == "hand") {
         if (card.location_arg == this.player_id) {
            return this.hand;
         } else {
            return this.game.getPlayerTable(Number(card.location_arg)).hand;
         }
      }

      if (card.location == "deck") {
         return this.game.tableCenter.manaDeck;
      }

      if (card.location == "discard") {
         return this.game.tableCenter.manaDiscard;
      }

      const index = Number(card.location.substring(card.location.length - 1));
      return this.mana_cooldown[index];
   }

   getDiscountNextAttack() {
      return Number(this.getPlayerTableDiv().dataset.discountNextAttack);
   }

   setDiscountNextAttack(amount: number) {
      this.getPlayerTableDiv().dataset.discountNextAttack = amount.toString();
   }

   getDiscountNextSpell() {
      return Number(this.getPlayerTableDiv().dataset.discountNextSpell);
   }

   setDiscountNextSpell(amount: number) {
      this.getPlayerTableDiv().dataset.discountNextSpell = amount.toString();
   }

   private getPlayerTableDiv() {
      return document.getElementById(`player-table-${this.player_id}`);
   }
}
