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
   private TOOLTIP_DELAY = document.body.classList.contains("touch-device") ? 1500 : undefined;

   public readonly gamedatas: WizardsGrimoireGamedatas;
   public notifManager: NotificationManager;
   public spellsManager: SpellCardManager;
   public manasManager: ManaCardManager;
   public stateManager: StateManager;

   public tableCenter: TableCenter;
   public playersTables: PlayerTable[] = [];

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
      this.stateManager = new StateManager(this);

      this.tableCenter = new TableCenter(this);

      // Setting up player boards
      for (let player_id in gamedatas.players) {
      }

      this.createPlayerTables(gamedatas);

      this.setupNotifications();
   }

   ///////////////////////////////////////////////////
   //// Game & client states

   // onEnteringState: this method is called each time we are entering into a new game state.
   //                  You can use this method to perform some user interface changes at this moment.
   //
   public onEnteringState(stateName: string, args: any) {
      this.stateManager.onEnteringState(stateName, args);
   }

   // onLeavingState: this method is called each time we are leaving a game state.
   //                 You can use this method to perform some user interface changes at this moment.
   //
   public onLeavingState(stateName: string) {
      this.stateManager.onLeavingState(stateName);
   }

   // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
   //                        action status bar (ie: the HTML links in the status bar).
   //
   public onUpdateActionButtons(stateName: string, args: any) {
      this.stateManager.onUpdateActionButtons(stateName, args);
   }

   ///////////////////////////////////////////////////
   //// Utilities

   public addActionButtonDisabled(id: string, label: string, action?: (evt: any) => void) {
      this.addActionButton(id, label, action);
      this.disableButton(id);
   }

   public addActionButtonRed(id: string, label: string, action: () => void) {
      this.addActionButton(id, label, action, null, null, "red");
   }

   private createPlayerTables(gamedatas: WizardsGrimoireGamedatas) {
      gamedatas.playerorder.forEach((player_id) => {
         const player = gamedatas.players[Number(player_id)];
         const table = new PlayerTable(this, player);
         this.playersTables.push(table);
      });
   }

   public disableButton(id: string): void {
      const el = document.getElementById(id);
      if (el) {
         el.classList.remove("bgabutton_blue");
         el.classList.remove("bgabutton_red");
         el.classList.add("bgabutton_disabled");
      }
   }

   public enableButton(id: string, color: "blue" | "red" = "blue"): void {
      const el = document.getElementById(id);
      if (el) {
         el.classList.add(`bgabutton_${color}`);
         el.classList.remove("bgabutton_disabled");
      }
   }

   public getCardType(card: SpellCard): CardType {
      return this.gamedatas.card_types[card.type];
   }

   public getPlayerId(): number {
      return Number(this.player_id);
   }

   public getPlayerTable(playerId: number): PlayerTable {
      return this.playersTables.find((playerTable) => playerTable.player_id === playerId);
   }

   public setTooltip(id: string, html: string) {
      this.addTooltipHtml(id, html, this.TOOLTIP_DELAY);
   }

   public takeAction(action: string, data?: any) {
      data = data || {};
      data.lock = true;
      (this as any).ajaxcall(`/wizardsgrimoire/wizardsgrimoire/${action}.html`, data, this, () => {});
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

   formatGametext(rawText: string) {
      if (!rawText) return "";
      let value = rawText.replace(",", ",<br />").replace(":", ":<br />");
      return "<p>" + value.split(".").join(".</p><p>") + "</p>";
   }
}

let cardId = 200000;
function getCardId() {
   return cardId++;
}
