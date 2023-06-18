const isDebug =
   window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
const log = isDebug ? console.log.bind(window.console) : function () {};
const LOCAL_STORAGE_ZOOM_KEY = "wizards-grimoire-zoom";
const LOCAL_STORAGE_JUMP_TO_FOLDED_KEY = "wizards-grimoire-jump-to-folded";
const arrayRange = (start, end) => Array.from(Array(end - start + 1).keys()).map((x) => x + start);

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
      offset_y?: number,
   ): void;
   fadeOutAndDestroy(id: string, duration?: number, delay?: number): void;
   showMessage(msg: string, type: "info" | "error" | "only_to_log"): void;
   updatePlayerOrdering(): void;
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
   public actionManager: ActionManager;

   public gameOptions: GameOptions;
   public tableCenter: TableCenter;
   public playersTables: PlayerTable[] = [];
   public zoomManager: ZoomManager;

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
      this.actionManager = new ActionManager(this);

      new JumpToManager(this, {
         localStorageFoldedKey: LOCAL_STORAGE_JUMP_TO_FOLDED_KEY,
         topEntries: [
            new JumpToEntry(_("Spell Pool"), "spell-pool", { color: "darkblue" }),
            new JumpToEntry(_("Decks"), "table-center", { color: "#224757" }),
         ],
         entryClasses: "triangle-point",
         defaultFolded: true,
      });

      this.gameOptions = new GameOptions(this);
      this.tableCenter = new TableCenter(this);

      // Setting up player boards
      this.createPlayerTables(gamedatas);

      this.zoomManager = new ZoomManager({
         element: document.getElementById("table"),
         smooth: false,
         zoomControls: {
            color: "black",
         },
         localStorageZoomKey: LOCAL_STORAGE_ZOOM_KEY,
         zoomLevels: [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1, 1.25, 1.5, 1.75, 2],
      });

      this.setupNotifications();

      this.setupDebug(gamedatas);
   }

   toggleOngoingSpell(value: OngoingSpell) {
      document.getElementById("table").classList.toggle(`wg-ongoing-spell-${value.name}`, value.active);
   }

   setupDebug(gamedatas: WizardsGrimoireGamedatas) {
      const arrCardType: CardType[] = [];
      Object.keys(gamedatas.card_types).forEach((index) => arrCardType.push(gamedatas.card_types[index]));

      // const level1 = arrCardType
      //    .filter((x) => x.icon == "Base_1")
      //    .sort((a: CardType, b: CardType) => a.debug.localeCompare(b.debug))
      //    .map((x) => `${x.debug} : ${x.name}`);

      // const level2 = arrCardType
      //    .filter((x) => x.icon == "Base_2")
      //    .sort((a: CardType, b: CardType) => a.debug.localeCompare(b.debug))
      //    .map((x) => `${x.debug} : ${x.name}`);

      // const expansion = arrCardType
      //    .filter((x) => x.icon == "KickStarter_1")
      //    .sort((a: CardType, b: CardType) => a.debug.localeCompare(b.debug))
      //    .map((x) => `${x.debug} : ${x.name}`);

      // const text = {
      //    level1,
      //    level2,
      //    expansion,
      // };

      // console.log(text);
      log("----------------");
      arrCardType
         .filter((x) => x.debug !== "lightgreen")
         .sort((a: CardType, b: CardType) => a.name.localeCompare(b.name))
         .sort((a: CardType, b: CardType) => a.icon.localeCompare(b.icon))
         .forEach((card) => {
            log(card.name, ",", card.icon, ",", card.debug);
            // log(card.description);
            // log("----------------");
         });
      log("----------------");
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

   public addActionButtonClientCancel() {
      const handleCancel = (evt: any): void => {
         dojo.stopEvent(evt);
         this.restoreGameState();
      };
      this.addActionButtonGray("btnCancelAction", _("Cancel"), handleCancel);
   }

   public addActionButtonPass() {
      const handlePass = () => {
         this.takeAction("pass");
      };
      this.addActionButtonRed("btn_pass", _("Pass"), handlePass);
   }

   public addActionButtonGray(id: string, label: string, action: (evt: any) => void) {
      this.addActionButton(id, label, action, null, null, "gray");
   }

   public addActionButtonRed(id: string, label: string, action: () => void) {
      this.addActionButton(id, label, action, null, null, "red");
   }

   private createPlayerTables(gamedatas: WizardsGrimoireGamedatas) {
      gamedatas.players_order.forEach((player_id) => {
         const player = gamedatas.players[Number(player_id)];
         const table = new PlayerTable(this, player);
         this.playersTables.push(table);
      });
   }

   public toggleButtonEnable(id: string, enabled: boolean, color: "blue" | "red" | "gray" = "blue"): void {
      if (enabled) {
         this.enableButton(id, color);
      } else {
         this.disableButton(id);
      }
   }

   public disableButton(id: string): void {
      const el = document.getElementById(id);
      if (el) {
         el.classList.remove("bgabutton_blue");
         el.classList.remove("bgabutton_red");
         el.classList.add("bgabutton_disabled");
      }
   }

   public enableButton(id: string, color: "blue" | "red" | "gray" = "blue"): void {
      const el = document.getElementById(id);
      if (el) {
         el.classList.add(`bgabutton_${color}`);
         el.classList.remove("bgabutton_disabled");
      }
   }

   public getCardType(card: SpellCard): CardType {
      return this.gamedatas.card_types[card.type];
   }

   public getOpponentId(): number {
      return Number(this.gamedatas.opponent_id);
   }

   public getPlayerId(): number {
      return Number(this.player_id);
   }

   public getPlayerTable(playerId: number): PlayerTable {
      return this.playersTables.find((playerTable) => playerTable.player_id === playerId);
   }

   public markCardAsSelected(card: SpellCard) {
      const div = this.spellsManager.getCardElement(card);
      div.classList.add("wg-selected");
   }

   restoreGameState() {
      log("restoreGameState");
      this.actionManager.reset();
      this.stateManager.restoreGameState();
      this.clearSelection();
      this.restoreServerGameState();
   }

   clearSelection() {
      log("clearSelection");
      this.tableCenter.spellPool.unselectAll();
      this.playersTables.forEach((table) => {
         table.spell_repertoire.unselectAll();
      });
      document.querySelectorAll(".wg-selected").forEach((node) => {
         node.classList.remove("wg-selected");
      });
      document.querySelectorAll(".wg-selectable").forEach((node) => {
         node.classList.remove("wg-selectable");
      });
      document.querySelectorAll(".wg-deck-was-selected").forEach((node) => {
         node.classList.remove("wg-deck-was-selected");
      });
   }

   public setGamestateDescription(property: string = "") {
      const originalState = this.gamedatas.gamestates[this.gamedatas.gamestate.id];
      this.gamedatas.gamestate.description = `${originalState["description" + property]}`;
      this.gamedatas.gamestate.descriptionmyturn = `${originalState["descriptionmyturn" + property]}`;
      (this as any).updatePageTitle();
   }

   public setTooltip(id: string, html: string) {
      this.addTooltipHtml(id, html, this.TOOLTIP_DELAY);
   }

   public takeAction(
      action: string,
      data?: any,
      onSuccess?: (result: any) => void,
      onComplete?: (is_error: boolean) => void,
   ) {
      data = data || {};
      data.lock = true;
      onSuccess = onSuccess ?? function (result: any) {};
      onComplete = onComplete ?? function (is_error: boolean) {};
      (this as any).ajaxcall(
         `/wizardsgrimoire/wizardsgrimoire/${action}.html`,
         data,
         this,
         onSuccess,
         onComplete,
      );
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

            if (args.damage !== undefined) {
               args.damage = `<div class="wg-icon-log i-dmg_undef"><span>${args.damage}</span></div>`;
            }

            if (args.nbr_mana_card !== undefined) {
               args.nbr_mana_card = `<div class="wg-icon-log i-cards"><span>${args.nbr_mana_card} </span></div>`;
            }

            if (args.mana_values !== undefined) {
               args.mana_values = args.mana_values
                  .map((value) => `<div class="wg-icon-log i-mana-x">${value}</div>`)
                  .join(" ");
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
