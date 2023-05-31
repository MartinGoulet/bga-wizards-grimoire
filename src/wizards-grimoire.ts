const isDebug =
   window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
const log = isDebug ? console.log.bind(window.console) : function () {};

interface WizardsGrimoire
   extends ebg.core.gamegui,
      BgaGame<WizardsGrimoirePlayerData, WizardsGrimoireGamedatas> {
   dontPreloadImage(image_file_name: string): void;
   ensureSpecificGameImageLoading(image_file_names_array: string[]);
   displayScoring(
      anchor_id: string,
      color: string,
      score: number,
      duration: number,
      offset_x?: number,
      offset_y?: number
   ): void;
   fadeOutAndDestroy(id: string, duration?: number, delay?: number): void;
   showMessage(msg: string, type: "info" | "error" | "only_to_log"): void;
}

class WizardsGrimoire
   implements ebg.core.gamegui, BgaGame<WizardsGrimoirePlayerData, WizardsGrimoireGamedatas>
{
   public readonly gamedatas: WizardsGrimoireGamedatas;
   public notifManager: NotificationManager = new NotificationManager(this);
   public spellsManager: SpellCardManager = new SpellCardManager(this);
   public manasManager: ManaCardManager = new ManaCardManager(this);
   public tableCenter: TableCenter;

   constructor() {}

   /*
        setup:
        
        This method must set up the game user interface according to current game situation specified
        in parameters.
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
        
        "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
    */
   public setup(gamedatas: WizardsGrimoireGamedatas) {
      log(gamedatas);

      this.notifManager = new NotificationManager(this);
      this.spellsManager = new SpellCardManager(this);
      this.manasManager = new ManaCardManager(this);
      this.tableCenter = new TableCenter(this);

      // Setting up player boards
      for (let player_id in gamedatas.players) {
      }

      this.tableCenter.spellPool.addCards(gamedatas.slot_cards);

      const hand = document.getElementById("current-player-table");

      const stock = new LineStock(this.manasManager, hand, { center: true });

      this.setupNotifications();
   }

   ///////////////////////////////////////////////////
   //// Game & client states

   // onEnteringState: this method is called each time we are entering into a new game state.
   //                  You can use this method to perform some user interface changes at this moment.
   //
   public onEnteringState(stateName: string, args: any) {}

   // onLeavingState: this method is called each time we are leaving a game state.
   //                 You can use this method to perform some user interface changes at this moment.
   //
   public onLeavingState(stateName: string) {}

   // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
   //                        action status bar (ie: the HTML links in the status bar).
   //
   public onUpdateActionButtons(stateName: string, args: any) {}

   ///////////////////////////////////////////////////
   //// Utilities

   public takeAction(action: string, data?: any) {
      data = data || {};
      data.lock = true;
      (this as any).ajaxcall(`/wizardsgrimoire/wizardsgrimoire/${action}.html`, data, this, () => {});
   }

   public addActionButtonRed(id: string, label: string, action: () => void) {
      this.addActionButton(id, label, action, null, null, "red");
   }

   ///////////////////////////////////////////////////
   //// Reaction to cometD notifications

   /*
        setupNotifications:
        
        In this method, you associate each of your game notifications with your local method to handle it.
        
        Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                your karmaka.game.php file.
     
    */
   setupNotifications() {
      log("notifications subscriptions setup");
      this.notifManager.setup();
   }

   ///////////////////////////////////////////////////
   //// Logs

   /* @Override */
   format_string_recursive(log: string, args: any) {
      try {
         if (log && args && !args.processed) {
            args.processed = true;

            if (args.card_name !== undefined) {
               args.card_name = "<b>" + _(args.card_name) + "</b>";
            }
         }
      } catch (e) {
         console.error(log, args, "Exception thrown", e.stack);
      }
      return this.inherited(arguments);
   }
}

let cardId = 200000;
function getCardId() {
   return cardId++;
}
