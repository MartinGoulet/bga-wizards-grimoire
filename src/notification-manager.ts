const ANIMATION_MS = 1500;

class NotificationManager {
   constructor(private game: WizardsGrimoire) {}

   setup() {
      //   this.subscribeEvent("onDiceLog", 1);
   }

   private subscribeEvent(eventName: string, time?: number) {
      try {
         dojo.subscribe(eventName, this, "notif_" + eventName);
         if (time) {
            this.game.notifqueue.setSynchronous(eventName, time);
         }
      } catch {
         console.error("NotificationManager::subscribeEvent");
      }
   }
}
