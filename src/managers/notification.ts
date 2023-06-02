const ANIMATION_MS = 1500;

class NotificationManager {
   constructor(private game: WizardsGrimoire) {}

   setup() {
      this.subscribeEvent("onChooseSpell", 500);
      this.subscribeEvent("onRefillSpell", 500);
      this.subscribeEvent("onDrawManaCards", 1000);
      this.subscribeEvent("onSpellCoolDown", 1000);

      this.game.notifqueue.setIgnoreNotificationCheck(
         "message",
         (notif) => notif.args.excluded_player_id && notif.args.excluded_player_id == this.game.player_id
      );
   }

   private subscribeEvent(eventName: string, time?: number) {
      try {
         dojo.subscribe(eventName, this, "notif_" + eventName);
         if (time) {
            this.game.notifqueue.setSynchronous(eventName, time);
         }
      } catch {
         console.error("NotificationManager::subscribeEvent", eventName);
      }
   }

   private notif_onChooseSpell(args: INotification<NotifChooseSpellArgs>) {
      const { player_id, card } = args.args;
      log("onChooseSpell", card);
      this.game.getPlayerTable(player_id).onChooseSpell(card);
   }
   private notif_onRefillSpell(args: INotification<NotifRefillSpellArgs>) {
      const { card } = args.args;
      log("onRefillSpell", card);
      this.game.tableCenter.onRefillSpell(card);
   }

   private notif_onDrawManaCards(args: INotification<NotifDrawManaCardsArgs>) {
      const { player_id, cards } = args.args;
      log("onDrawManaCards", cards);
      this.game.getPlayerTable(player_id).onDrawManaCard(cards);
   }

   private notif_onSpellCoolDown(args: INotification<NotifSpellCoolDownArgs>) {}
}
