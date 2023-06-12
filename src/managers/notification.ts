const ANIMATION_MS = 1500;

class NotificationManager {
   constructor(private game: WizardsGrimoire) {}

   setup() {
      this.subscribeEvent("onChooseSpell", 500);
      this.subscribeEvent("onRefillSpell", 500);
      this.subscribeEvent("onDrawManaCards", 1000, true);
      this.subscribeEvent("onMoveManaCards", 1000, true);
      this.subscribeEvent("onManaDeckShuffle", 2500);
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
   private notif_onRefillSpell(notif: INotification<NotifRefillSpellArgs>) {
      const { card } = notif.args;
      log("onRefillSpell", card);
      this.game.tableCenter.onRefillSpell(card);
   }

   private notif_onDrawManaCards(notif: INotification<NotifDrawManaCardsArgs>) {
      const { player_id, cards } = notif.args;
      log("onDrawManaCards", cards);
      this.game.getPlayerTable(player_id).onDrawManaCard(cards);
   }

   private notif_onManaDeckShuffle(notif: INotification<NotifManaDeckShuffleArgs>) {
      log("onManaDeckShuffle");
      this.game.tableCenter.shuffleManaDeck(notif.args.cards);
   }

   private notif_onMoveManaCards(notif: INotification<NotifMoveManaCardsArgs>) {
      const { player_id, cards_before, cards_after, nbr } = notif.args;
      log("onMoveManaCards", cards_before, cards_after);
      for (let index = 0; index < nbr; index++) {
         const before = cards_before[index];
         const after = cards_after.find((x) => x.id == before.id);
         this.game.getPlayerTable(player_id).onMoveManaCard(before, after);
      }
   }

   // private notif_onSpellCoolDown(notif: INotification<NotifSpellCoolDownArgs>) {
   //    const { player_id, mana_cards_discard } = notif.args;
   //    log("notif_onSpellCoolDown", mana_cards_discard);
   //    const player_table = this.game.getPlayerTable(Number(player_id));
   //    mana_cards_discard.forEach((card_id) => {
   //       player_table.onMoveManaCard(this.game.manasManager.getCardById(card_id), card);
   //    });
   // }

   private notif_onHealthChanged(notif: INotification<NotifHealthChangedArgs>) {
      log("notif_onHealthChanged", notif.args);
      const { player_id, life_remaining, damage } = notif.args;
      this.game.scoreCtrl[player_id].toValue(life_remaining);
      const color = damage > 0 ? "ff0000" : "008000";
      this.game.displayScoring(`player-table-${player_id}`, color, -damage, 1000);
   }
}
