type NewActionType = "actionCastMana";
type TakeActionType = "castSpell";

class ActionManager {
   private actions: string[] = [];
   private actions_args: string[] = [];
   private current_card: SpellCard;
   private take_action: TakeActionType;

   constructor(private game: WizardsGrimoire) {}

   public setup(takeAction: TakeActionType = "castSpell", newAction: NewActionType = "actionCastMana") {
      log("actionmanager.reset");

      this.reset();
      this.take_action = takeAction;
      this.actions.push(newAction);
      return this;
   }

   public reset() {
      log("actionmanager.reset");

      this.current_card = null;
      this.take_action = null;
      this.actions = [];
      this.actions_args = [];
      return this;
   }

   public addAction(card: SpellCard) {
      this.current_card = card;
      const card_type = this.game.getCardType(card);
      log("actionmanager.addAction", card, card_type);

      if (!card_type.js_actions) {
         log("actionmanager.addAction no js_Actions");
      } else if (Array.isArray(card_type.js_actions)) {
         card_type.js_actions.forEach((action) => this.actions.push(action));
      } else if (typeof card_type.js_actions === "string") {
         this.actions.push(card_type.js_actions);
      }

      log("actionmanager.actions values", this.actions);
      return this;
   }

   public addArgument(arg: string) {
      log("actionmanager.addArgument", arg);
      this.actions_args.push(arg);
      return this;
   }

   public activateNextAction() {
      if (this.actions.length > 0) {
         // Invoke action
         const nextAction = this.actions.shift();
         this[nextAction]();
         return;
      }

      const card_type = this.game.getCardType(this.current_card);
      log("actionmanager.activateNextAction", this.current_card, card_type, this.actions_args);

      const handleError = (is_error: boolean) => {
         is_error ? this.game.restoreGameState() : this.game.clearSelection();
      };

      const data = {
         card_id: this.current_card.id,
         args: this.actions_args.join(";"),
      };

      this.game.takeAction(this.take_action, data, null, handleError);
   }

   ////////////////////////////////////////
   // Actions

   private actionCastMana() {
      const { name, cost } = this.game.getCardType(this.current_card);
      let msg = _("${you} must pay ${nbr} mana card");
      msg = msg.replace("${nbr}", cost.toString());

      this.game.setClientState(states.client.castSpellWithMana, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            card: this.current_card,
         },
      });
   }

   private actionSelectManaFrom() {
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());

      const emptyDecks = player_table
         .getManaDeckWithSpellOver()
         .filter((deck) => deck.isEmpty())
         .map((deck) => deck.location);

      const argsSuppl = {
         exclude: emptyDecks,
         ignore: null,
      };

      if (this.actions.length > 0 && this.actions[0] == "actionSelectManaTo") {
         argsSuppl.ignore = () => {
            // Remove the actionSelectManaTo
            this.actions.shift();
            this.activateNextAction();
         };
      }

      const msgFrom = _("${you} must select ${nbr} mana card - source");
      this.selectManaDeck(1, msgFrom, true, argsSuppl);
   }

   private actionSelectManaTo() {
      const manaDeckPosition: number = Number(this.actions_args[this.actions_args.length - 1]);
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      player_table.mana_cooldown[manaDeckPosition].forceSelected();

      const argsSuppl = {
         exclude: [manaDeckPosition],
      };

      const msg = _("${you} must select ${nbr} mana card - destination");
      this.selectManaDeck(1, msg, true, argsSuppl);
   }

   private actionShadowAttack() {
      // const msg = _("${you} must select ${nbr} mana card to discard");
      // this.selectMana(1, msg, true);
      this.actionSelectManaFrom();
   }

   private actionSharedPower() {
      const msg = _("${you} may select ${nbr} mana card to give to your opponent");
      this.selectManaHand(1, msg, false);
   }

   private actionTimeDistortion() {
      const msg = _("${you} may select up to ${nbr} mana card");
      this.selectMana(2, msg, false);
   }

   ////////////////////////////////////////
   // Utilities

   private selectMana(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.current_card);
      msg = msg.replace("${nbr}", count.toString());

      argsSuppl.exclude = argsSuppl.exclude ?? [];
      argsSuppl.exclude.push(Number(this.current_card.location_arg));

      const args = {
         ...argsSuppl,
         player_id: this.game.getPlayerId(),
         card: this.current_card,
         count,
         exact,
      };

      this.game.setClientState(states.client.selectMana, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private selectManaHand(count: number, msg: string, exact: boolean) {
      const { name } = this.game.getCardType(this.current_card);
      msg = msg.replace("${nbr}", count.toString());

      this.game.setClientState(states.client.selectManaHand, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            player_id: this.game.getPlayerId(),
            card: this.current_card,
            count,
            exact,
         },
      });
   }

   private selectManaDeck(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.current_card);
      msg = msg.replace("${nbr}", count.toString());

      argsSuppl.exclude = argsSuppl.exclude ?? [];
      argsSuppl.exclude.push(Number(this.current_card.location_arg));

      const args = {
         ...argsSuppl,
         player_id: this.game.getPlayerId(),
         card: this.current_card,
         count,
         exact,
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }
}
