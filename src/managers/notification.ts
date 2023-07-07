class NotificationManager {
   constructor(private game: WizardsGrimoire) {}

   setup() {
      this.subscribeEvent("onChooseSpell", 500);
      this.subscribeEvent("onDiscardSpell", 500);
      this.subscribeEvent("onRefillSpell", 500);
      this.subscribeEvent("onDrawManaCards", 650, true);
      this.subscribeEvent("onMoveManaCards", 1000, true);
      this.subscribeEvent("onManaDeckShuffle", 2500);
      this.subscribeEvent("onRevealManaCardCooldown", 500);
      this.subscribeEvent("onHealthChanged", 500);

      this.game.notifqueue.setIgnoreNotificationCheck(
         "message",
         (notif) => notif.args.excluded_player_id && notif.args.excluded_player_id == this.game.player_id,
      );
   }

   private subscribeEvent(eventName: string, time: number, setIgnore: boolean = false) {
      try {
         dojo.subscribe(eventName, this, "notif_" + eventName);
         if (time) {
            this.game.notifqueue.setSynchronous(eventName, time);
         }
         if (setIgnore) {
            this.game.notifqueue.setIgnoreNotificationCheck(
               eventName,
               (notif) =>
                  notif.args.excluded_player_id && notif.args.excluded_player_id == this.game.player_id,
            );
         }
      } catch {
         console.error("NotificationManager::subscribeEvent", eventName);
      }
   }

   private notif_onChooseSpell(notif: INotification<NotifChooseSpellArgs>) {
      const { player_id, card } = notif.args;
      log("onChooseSpell", card);
      this.game.getPlayerTable(player_id).onChooseSpell(card);
   }

   private notif_onDiscardSpell(notif: INotification<NotifDiscardSpellArgs>) {
      const { player_id, card } = notif.args;
      log("onDiscardSpell", card);
      this.game.tableCenter.spellDiscard.addCard(card);
   }

   private notif_onRefillSpell(notif: INotification<NotifRefillSpellArgs>) {
      const { card } = notif.args;
      log("onRefillSpell", card);
      this.game.tableCenter.onRefillSpell(card);
   }

   private notif_onDrawManaCards(notif: INotification<NotifDrawManaCardsArgs>) {
      const { player_id, cards } = notif.args;
      log("onDrawManaCards", cards);
      this.game.getPlayerTable(player_id).hand.addCards(cards);
      // const { hand } = this.game.getPlayerTable(player_id);
      // cards.forEach((card) => hand.addCard(card));
   }

   private notif_onManaDeckShuffle(notif: INotification<NotifManaDeckShuffleArgs>) {
      log("onManaDeckShuffle");
      this.game.tableCenter.shuffleManaDeck(notif.args.cards);
   }

   private notif_onMoveManaCards(notif: INotification<NotifMoveManaCardsArgs>) {
      const { player_id, cards_after: cards } = notif.args;
      log("onMoveManaCards", cards);
      cards.forEach((card) => {
         this.game.getPlayerTable(player_id).onMoveManaCard(undefined, card);
      });
   }

   private notif_onRevealManaCardCooldown(notif: INotification<NotifRevealManaCardCooldown>) {
      log("notif_onRevealManaCardCooldown", notif.args);
      const { card } = notif.args;
      const [prefix, player_id, position] = card.location.split("_");
      if (Number(player_id) == this.game.getOpponentId()) {
         const manaCooldown = this.game.getPlayerTable(Number(player_id)).mana_cooldown[Number(position)];
         manaCooldown.setCardVisible(card, true);
         setTimeout(() => {
            manaCooldown.setCardVisible(card, false);
         }, 4000);
      }
   }

   private notif_onHealthChanged(notif: INotification<NotifHealthChangedArgs>) {
      log("notif_onHealthChanged", notif.args);
      const { player_id, life_remaining, nbr_damage } = notif.args;
      this.game.scoreCtrl[player_id].toValue(life_remaining);
      this.game.getPlayerTable(player_id).health.toValue(life_remaining);
      if (nbr_damage > 0) {
         this.game.displayScoring(`player-table-${player_id}-health`, "ff0000", -nbr_damage, 1000);
      }
   }
}
