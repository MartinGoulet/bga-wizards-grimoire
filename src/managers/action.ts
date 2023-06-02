type NewActionType = "actionCastMana";
type TakeActionType = "castSpell";

class ActionManager {
   private actions: string[] = [];
   private actions_args: string[] = [];
   private current_card: SpellCard;
   private take_action: TakeActionType;

   constructor(private game: WizardsGrimoire) {}

   public setup(
      card: SpellCard,
      takeAction: TakeActionType = "castSpell",
      newAction: NewActionType = "actionCastMana"
   ) {
      log("actionmanager.reset");

      this.reset();
      this.current_card = card;
      this.take_action = takeAction;
      this.actions.push(newAction);
   }

   public reset() {
      log("actionmanager.reset");

      this.current_card = null;
      this.take_action = null;
      this.actions = [];
      this.actions_args = [];
   }

   public addAction(card: SpellCard) {
      log("actionmanager.addAction", card);
      this.current_card = card;

      const { js_actions } = this.game.getCardType(card);
      if (!js_actions) {
         log("actionmanager.addAction no js_Actions");
      } else if (Array.isArray(js_actions)) {
         js_actions.forEach((action) => this.actions.push(action));
      } else if (typeof js_actions === "string") {
         this.actions.push(js_actions);
      }

      log("actionmanager.actions values", this.actions);
   }

   public addArgument(arg: string) {
      log("actionmanager.addArgument", arg);
      this.actions_args.push(arg);
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
         args: this.actions_args.join(";")
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
            card: this.current_card
         }
      });
   }

   private replaceWithArg(msg, args) {
      msg = this.game.format_string_recursive(msg, args);
      return dojo.string.substitute(msg, args);
   }
}
