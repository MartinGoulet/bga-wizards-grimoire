class NotificationManager {
   constructor(private game: Game) {}

   setup() {
      this.game.bgaSetupPromiseNotifications({
         handlers: [this],
         onStart: (notifName, msg, args) => {
            console.log(`Notification started: ${notifName}`, args);
         },
      });

      const getNotifs = (): string[] => {
         return Object.getOwnPropertyNames(Object.getPrototypeOf(this))
            .filter((prop) => prop.startsWith("notif_") && typeof this[prop] === "function")
            .map((prop) => prop.slice(6));
      };

      ["message", ...getNotifs()].forEach((eventName) => {
         this.game.notifqueue.setIgnoreNotificationCheck(eventName, (notif: { args: any }) => {
            const skip =
               notif.args.excluded_player_id && Number(notif.args.excluded_player_id) == this.game.getPlayerId();
            return skip;
         });
      });
   }

   private async notif_onChooseSpell(args: NotifChooseSpellArgs) {
      const { player_id, card } = args;
      this.game.getPlayerTable(player_id).onChooseSpell(card);
      await this.game.wait(500);
   }

   private async notif_onDiscardSpell(args: NotifDiscardSpellArgs) {
      const { player_id, card } = args;
      await this.game.tableCenter.spellDiscard.addCard(card);
      await this.game.wait(500);
   }

   private async notif_onDestroySpell(args: { player_id: number; card: SpellCard; destination: string }) {
      const { player_id, card, destination } = args;
      debugger;
      if (destination === "discard") {
         await this.game.tableCenter.spellDiscard.addCard(card);
      } else {
         const stock = this.game.spellsManager.getCardStock(card);
         if (stock) stock.removeCard(card);
      }
      await this.game.wait(500);
   }

   private async notif_onRefillSpell(args: NotifRefillSpellArgs) {
      const { card } = args;
      await this.game.tableCenter.onRefillSpell(card);
      await this.game.wait(500);
   }

   private async notif_onDrawManaCards(args: NotifDrawManaCardsArgs) {
      const { player_id, cards } = args;
      await this.game.getPlayerTable(player_id).hand.addCards(cards);
      await this.game.wait(500);
   }

   private async notif_onManaDeckShuffle(args: NotifManaDeckShuffleArgs) {
      await this.game.tableCenter.shuffleManaDeck(args.cards);
      await this.game.wait(500);
   }

   private async notif_onMoveManaCards(args: NotifMoveManaCardsArgs) {
      const { player_id, cards_after: cards } = args;
      const promises = [];
      for (const card of cards) {
         promises.push(this.game.getPlayerTable(player_id).onMoveManaCard(card));
      }
      await Promise.all(promises);
      await this.game.wait(500);
   }

   private async notif_onRevealManaCardCooldown(args: NotifRevealManaCardCooldown) {
      const { card } = args;
      const [prefix, player_id, position] = card.location.split("_");
      if (Number(player_id) == this.game.getOpponentId()) {
         const manaCooldown = this.game.getPlayerTable(Number(player_id)).mana_cooldown[Number(position)];
         manaCooldown.setCardVisible(card, true);
         await this.game.wait(4000);
         manaCooldown.setCardVisible(card, false);
         await this.game.wait(500);
      }
   }

   private async notif_onHealthChanged(args: NotifHealthChangedArgs) {
      const { player_id, life_remaining, nbr_damage } = args;
      this.game.scoreCtrl[player_id].toValue(life_remaining);
      this.game.getPlayerTable(player_id).health.toValue(life_remaining);
      if (nbr_damage > 0) {
         this.game.displayScoring(`player-table-${player_id}-health`, "ff0000", -nbr_damage, 1000);
      }
      await this.game.wait(250);
   }

   private async notif_onCrystalShard(args: { player_id: number; spell: SpellCard; mana: ManaCard }) {
      const { player_id, spell, mana } = args;

      const playerTable = this.game.getPlayerTable(player_id);
      const fromElement = this.game.spellsManager.getCardElement(spell).parentElement;

      playerTable.spell_repertoire.removeCard(spell);
      await playerTable.hand.addCard(mana, { fromElement });

      await this.game.wait(500);
   }

   private async notif_onCrystalShardDiscard(args: { spell: SpellCard; mana: ManaCard }) {
      const { spell, mana } = args;

      const fromElement = this.game.manasManager.getCardElement(mana).parentElement;

      const manaStock = this.game.manasManager.getCardStock(mana);
      manaStock.removeCard(mana);

      await this.game.tableCenter.spellDiscard.addCard(spell, { fromElement });

      await this.game.wait(500);
   }
}
