class PlayerTable {
   public player_id: number;

   public spell_repertoire: SpellRepertoire;
   public mana_cooldown: { [pos: number]: ManaDeck } = {};
   public hand: LineStock<ManaCard>;

   private current_player: boolean;

   constructor(public game: WizardsGrimoire, player: WizardsGrimoirePlayerData) {
      this.player_id = Number(player.id);
      this.current_player = this.player_id == this.game.getPlayerId();

      const { id: pId, color: pColor } = player;
      const pCurrent = this.current_player.toString();

      const dataset: string[] = [
         `data-color="${pColor}"`,
         `data-current-player="${pCurrent}"`,
         `data-discount-next-spell="0"`,
         `data-discount-next-attack="0"`,
         `data-battle_vision="false"`,
         `data-puppetmaster="false"`,
         `data-secret_oath="false"`,
      ];

      const html = `
            <div id="player-table-${pId}" style="--color: #${pColor}" ${dataset.join(" ")}>
               <div class="player-table whiteblock">
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
                  <div id="player-table-${pId}-hand-cards" class="hand cards" data-player-id="${pId}" data-my-hand="${pCurrent}"></div>
                  <div id="player-table-${pId}-extra-icons" class="player-table-extra-icons"></div>
               </div>
            </div>`;

      dojo.place(html, "tables");

      if (this.current_player) {
         this.setupHaste();
         this.setupSecondStrike();
         this.setupBattleVision();
         this.setupPuppetMaster();
         this.setupSecretOath();
         this.setupGrowth();
         this.setupPowerHungry();
      }

      this.spell_repertoire = new SpellRepertoire(
         game.spellsManager,
         document.getElementById(`player-table-${this.player_id}-spell-repertoire`),
         this,
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
      // TODO check before can be removed
      const stockBeforeManager = this.game.manasManager.getCardStock(after);
      const stockAfter = this.getStock(after);

      if (stockBeforeManager === stockAfter) {
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

      if (card.location == "manarevealed") {
         return this.game.tableCenter.manaRevealed;
      }

      if (card.location == "basicattack") {
         return this.game.tableCenter.basicAttack;
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

   setPreviousBasicAttack(value: number) {
      const id = `puppetmaster_icon_${this.player_id}`;
      const el = document.getElementById(id);
      if (el == null) return;
      el.innerText = value.toString();
   }

   private getPlayerTableDiv() {
      return document.getElementById(`player-table-${this.player_id}`);
   }

   private setupBattleVision() {
      this.setupIcon({
         id: "battlevision",
         title: _("Battle vision"),
         gametext: _(
            "When you basic attacks, your opponent may discard a mana card of the same power from his hand to block the damage",
         ),
      });
   }

   private setupGrowth() {
      this.setupIcon({
         id: "growth",
         title: _("Growth"),
         gametext: _("Increase the power of all mana by 1"),
      });
   }

   private setupHaste() {
      this.setupIcon({
         id: "haste",
         title: _("Haste"),
         gametext: _("The next time you cast a spell this turn, it costs 2 less"),
      });
   }

   private setupPuppetMaster() {
      this.setupIcon({
         id: "puppetmaster",
         title: _("Puppetmaster"),
         gametext: _("You must use a mana of the same power as the previous basic attack phase"),
      });
   }

   private setupPowerHungry() {
      this.setupIcon({
         id: "powerhungry",
         title: _("Power hungry"),
         gametext: _("Your basic attack mana card go to the opponent's hand instead of the discard pile"),
      });
   }

   private setupSecondStrike() {
      this.setupIcon({
         id: "secondstrike",
         title: _("Second strike"),
         gametext: _("The next time you cast an attack spell this turn, it costs 1 less"),
      });
   }

   private setupSecretOath() {
      this.setupIcon({
         id: "secretoath",
         title: _("Secret oath"),
         gametext: _(
            "If you have a 4 power mana in your hand, you must give it to your opponent immediately",
         ),
      });
   }

   private setupIcon({ id, title, gametext }: SetupIconArgs) {
      const cssClass = id;
      id = `${id}_${this.player_id}`;
      dojo.place(
         `<div id="${id}" class="${cssClass}_icon icon"></div>`,
         `player-table-${this.player_id}-extra-icons`,
      );
      this.game.setTooltip(
         id,
         `<div class="player-table-icon-tooltip"><h3>${title}</h3><div>${_(gametext)}</div></div>`,
      );
      document.getElementById(id).addEventListener("click", () => {
         (this.game as any).tooltips[id].open(id);
      });
   }
}

interface SetupIconArgs {
   id: string;
   title: string;
   gametext: string;
}
