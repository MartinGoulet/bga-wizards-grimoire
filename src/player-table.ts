class PlayerTable {
   public player_id: number;

   public spell_repertoire: SlotStock<SpellCard>;
   public mana_cooldown: Deck<ManaCard>[] = [];
   public hand: LineStock<ManaCard>;

   private current_player: boolean;

   constructor(private game: WizardsGrimoire, player: WizardsGrimoirePlayerData) {
      this.player_id = Number(player.id);
      this.current_player = this.player_id == this.game.getPlayerId();

      const { id: pId, name: pName, color: pColor } = player;
      const pCurrent = this.current_player.toString();

      const html = `
            <div id="player-table-${pId}" class="player-table whiteblock" data-color="${pColor}" style="--color: #${pColor}">
                <span class="wg-title">${pName}</span>
                <div id="player-table-${pId}-spell-repertoire" class="spell-repertoire"></div>
                <div id="player-table-${pId}-mana-cooldown" class="mana-cooldown">
                    <div id="player_table-${pId}-mana-deck-1" class="mana-deck"></div>
                    <div id="player_table-${pId}-mana-deck-2" class="mana-deck"></div>
                    <div id="player_table-${pId}-mana-deck-3" class="mana-deck"></div>
                    <div id="player_table-${pId}-mana-deck-4" class="mana-deck"></div>
                    <div id="player_table-${pId}-mana-deck-5" class="mana-deck"></div>
                    <div id="player_table-${pId}-mana-deck-6" class="mana-deck"></div>
                </div>
                <div id="player-table-${pId}-hand-cards" class="hand cards" data-player-id="${pId}" data-current-player="${pCurrent}" data-my-hand="${pCurrent}"></div>
            <div>`;

      dojo.place(html, "tables");

      this.spell_repertoire = new SlotStock(
         game.spellsManager,
         document.getElementById(`player-table-${this.player_id}-spell-repertoire`),
         {
            slotsIds: [1, 2, 3, 4, 5, 6],
            slotClasses: ["wg-spell-slot"],
            mapCardToSlot: (card) => card.location_arg
         }
      );

      for (let index = 1; index <= 6; index++) {
         const divDeck = document.getElementById(`player_table-${pId}-mana-deck-${index}`);
         const deck = new Deck(game.manasManager, divDeck, {
            cardNumber: 0,
            counter: {}
         });
         this.mana_cooldown.push(deck);
      }

      const board: PlayerBoardInfo = game.gamedatas.player_board[pId];
      this.spell_repertoire.addCards(board.spells);

      Object.keys(board.manas).forEach((pos: string) => {
         this.mana_cooldown[Number(pos) - 1].setCardNumber(board.manas[pos]);
      });

      if (pCurrent) {
         this.hand = new LineStock(
            game.manasManager,
            document.getElementById(`player-table-${pId}-hand-cards`),
            {
               center: true
            }
         );
         this.hand.addCards(board.hand ?? []);
      }
   }

   public onChooseSpell(card: SpellCard) {
      this.spell_repertoire.addCard(card, {
         fromStock: this.game.tableCenter.spellPool
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
}
