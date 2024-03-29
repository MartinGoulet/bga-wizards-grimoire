const isDebug =
   window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
const log = isDebug ? console.log.bind(window.console) : function () {};
const LOCAL_STORAGE_ZOOM_KEY = "wizards-grimoire-zoom";
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
   addTooltip(
      nodeId: string,
      helpStringTranslated: string,
      actionStringTranslated: string,
      delay?: number,
   ): void;
   addTooltipHtmlToClass(cssClass: string, html: string, delay?: number): void;
}

class WizardsGrimoire
   implements ebg.core.gamegui, BgaGame<WizardsGrimoirePlayerData, WizardsGrimoireGamedatas>
{
   private TOOLTIP_DELAY = document.body.classList.contains("touch-device") ? 1500 : undefined;

   public readonly gamedatas: WizardsGrimoireGamedatas;
   public notifManager: NotificationManager;
   public spellsManager: SpellCardManager;
   public manasManager: ManaCardManager;
   public discardManager: ManaDiscardManager;
   public stateManager: StateManager;
   public actionManager: ActionManager;

   public tooltipManager: TooltipManager;

   public gameOptions: GameOptions;
   public tableCenter: TableCenter;
   public playersPanels: PlayerPanel[];
   public playersTables: PlayerTable[];
   public zoomManager: ZoomManager;
   public modal: Modal;

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
      this.discardManager = new ManaDiscardManager(this);
      this.tooltipManager = new TooltipManager(this);
      this.stateManager = new StateManager(this);
      this.actionManager = new ActionManager(this);

      this.gameOptions = new GameOptions(this);
      this.tableCenter = new TableCenter(this);
      this.modal = new Modal(this);

      const htmlElement = document.getElementsByTagName("html")[0];
      htmlElement.classList.toggle("mobile_version", document.body.classList.contains("mobile_version"));
      htmlElement.classList.toggle("desktop_version", document.body.classList.contains("desktop_version"));

      this.dontPreloadImage("background-mobile.png");
      this.dontPreloadImage("background-desktop.png");
      if (!gamedatas.images.front_2) {
         this.dontPreloadImage("spell-front-2.jpg");
      }

      // Setting up player boards
      this.createPlayerPanels(gamedatas);
      this.createPlayerTables(gamedatas);

      this.zoomManager = new ZoomManager({
         element: document.getElementById("table"),
         smooth: false,
         zoomControls: {
            color: "white",
         },
         localStorageZoomKey: LOCAL_STORAGE_ZOOM_KEY,
         zoomLevels: [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1, 1.25, 1.5, 1.75, 2],
      });

      this.addTooltipHtmlToClass("hand-icon-wrapper", _("Number of cards in hand"), 0);
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

   public addActionButtonClientCancel() {
      const handleCancel = (evt: any): void => {
         evt.stopPropagation();
         evt.preventDefault();
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

   public addActionButtonUndo() {
      const handleUndo = () => {
         if (this.checkAction("undo")) {
            this.takeAction("undo");
         }
      };

      this.addActionButton("btn_undo", _("Undo"), handleUndo, null, null, "gray");
   }

   private createPlayerPanels(gamedatas: WizardsGrimoireGamedatas) {
      this.playersPanels = [];
      let isFirst = true;
      gamedatas.players_order.forEach((player_id) => {
         const player = gamedatas.players[Number(player_id)];
         const panel = new PlayerPanel(this, player, isFirst);
         this.playersPanels.push(panel);
         isFirst = false;
      });
   }

   private createPlayerTables(gamedatas: WizardsGrimoireGamedatas) {
      this.playersTables = [];
      gamedatas.players_order.forEach((player_id) => {
         const player = gamedatas.players[Number(player_id)];
         const table = new PlayerTable(this, player);
         table.setPreviousBasicAttack(gamedatas.globals.previous_basic_attack);
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

   public getSpellCost(spell: SpellCard): number {
      let { cost, type } = this.getCardType(spell);
      const player_table = this.getCurrentPlayerTable();

      cost = cost - player_table.getDiscountNextSpell();
      if (type == "red") {
         cost = cost - player_table.getDiscountNextAttack();
      }

      if (spell.type === SpellType.DeathSpiral) {
         const previous_spell_id = Number(player_table.getPreviousSpellPlayed());
         if (previous_spell_id > 0) {
            const previous_cost = Number(player_table.getPreviousSpellCost());
            if (previous_cost >= 3) {
               cost = 0;
            }
         }
      }

      return Math.max(cost, 0);
   }

   public getPower(card: ManaCard): number {
      let value = Number(card["type"]);
      if (document.getElementById("table").classList.contains("wg-ongoing-spell-growth")) {
         value++;
      }
      return value;
   }

   public getOpponentId(): number {
      return Number(this.gamedatas.opponent_id);
   }

   public getPlayerId(): number {
      return Number(this.player_id);
   }

   public getPlayerPanel(playerId: number): PlayerPanel {
      return this.playersPanels.find((playerPanel) => playerPanel.player_id === playerId);
   }

   public getPlayerTable(playerId: number): PlayerTable {
      return this.playersTables.find((playerTable) => playerTable.player_id === playerId);
   }

   public getCurrentPlayerTable() {
      return this.getPlayerTable(this.getPlayerId());
   }

   public markCardAsSelected(card: SpellCard) {
      const div = this.spellsManager.getCardElement(card);
      div.classList.add("wg-selected");
   }

   async restoreGameState() {
      log("restoreGameState");
      this.actionManager.reset();
      await this.stateManager.restoreGameState();
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

   toggleOngoingSpell(value: OngoingSpell) {
      document.getElementById("table").classList.toggle(`wg-ongoing-spell-${value.name}`, value.active);
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

            if (args.card_name2 !== undefined) {
               args.card_name2 = "<b>" + _(args.card_name2) + "</b>";
            }

            if (args.phase_name !== undefined) {
               args.phase_name = "<b>" + _(args.phase_name) + "</b>";
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
      try {
         return this.inherited(arguments);
      } catch {
         debugger;
      }
   }

   formatGametext(rawText: string) {
      if (!rawText) return "";
      let value = rawText.replace(",", ",<br />").replace(":", ":<br />");
      return "<p>" + value.split(".").join(".</p><p>") + "</p>";
   }
}
