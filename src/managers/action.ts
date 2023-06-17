type TakeActionType = "castSpell" | "castSpellInteraction";

class ActionManager {
   private actions: string[] = [];
   private actions_args: string[] = [];
   private current_card: SpellCard;
   private take_action: TakeActionType;

   constructor(private game: WizardsGrimoire) { }

   public setup(takeAction: TakeActionType = "castSpell", newAction?: string) {
      log("actionmanager.reset");

      this.reset();
      this.take_action = takeAction;
      if (newAction) {
         this.actions.push(newAction);
      }
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
      this.addActionPriv(card_type.js_actions);
      log("actionmanager.actions values", this.actions);
      return this;
   }

   public addActionInteraction(card: SpellCard) {
      this.current_card = card;
      const card_type = this.game.getCardType(card);
      log("actionmanager.addActionInteraction", card, card_type);
      this.addActionPriv(card_type.js_actions_interaction);
      return this;
   }

   private addActionPriv(actions?: string[] | string) {
      if (!actions) {
         log("actionmanager.addActionPriv no actions");
      } else if (Array.isArray(actions)) {
         actions.forEach((action) => this.actions.push(action));
      } else if (typeof actions === "string") {
         this.actions.push(actions);
      }

      log("actionmanager.addActionPriv values", this.actions);
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

   /////////////////////////////////////////////////////////////
   //     _____              _       ____                   __ 
   //    / ____|            | |     |  _ \                 /_ |
   //   | |     __ _ _ __ __| |___  | |_) | __ _ ___  ___   | |
   //   | |    / _` | '__/ _` / __| |  _ < / _` / __|/ _ \  | |
   //   | |___| (_| | | | (_| \__ \ | |_) | (_| \__ \  __/  | |
   //    \_____\__,_|_|  \__,_|___/ |____/ \__,_|___/\___|  |_|
   //                                                          
   /////////////////////////////////////////////////////////////

   private actionArcaneTactics() {
      const msg = _("${you} may select ${nbr} mana card from your hand");
      this.returnManaCardToDeck(msg, 4, false);
   }

   private actionFreeze() {
      this.question({
         cancel: true,
         options: [
            {
               label: _("Draw 4 cards"),
               action: () => {
                  this.activateNextAction();
               },
            },
            {
               label: _("Place a mana card from the mana deck on one of your opponent's spells"),
               action: () => {
                  this.selectManaDeck(1, _("Select an opponent's spell deck"), true, {
                     player_id: this.game.getOpponentId(),
                  });
               },
            },
         ],
      });
   }

   private actionFriendlyTruce() {
      const msg = _("${you} may give ${nbr} cards from your hand or pass");
      this.selectManaHand(3, msg, true, { canCancel: false, skip: { label: "Pass" } });
   }

   private actionGuiltyBond() {
      const msg = _("${you} may select ${nbr} mana card from your hand");
      this.selectManaHand(1, msg, true);
   }

   private actionMistOfPain() {
      const msg = _("${you} may discard up to ${nbr} mana card from your hand");
      this.selectManaHand(4, msg, false, { canCancel: false });
   }

   private actionRejuvenation() {
      this.question({
         cancel: true,
         options: [
            {
               label: _("Gain 4 mana cards"),
               action: () => {
                  this.activateNextAction();
               },
            },
            {
               label: _("Take 2 mana cards of any power from the discard pile"),
               action: () => {
                  this.actions.push("actionSelectTwoManaCardFromDiscard");
                  this.activateNextAction();
               },
            },
         ],
      });
   }

   private actionShackledMotion() {
      this.question({
         cancel: true,
         options: [
            {
               label: _("Draw 4 cards"),
               action: () => {
                  this.addArgument("1");
                  this.activateNextAction();
               },
            },
            {
               label: _("Your opponent must discard their hand"),
               action: () => {
                  this.addArgument("2");
                  this.activateNextAction();
               },
            },
         ],
      });
   }

   private actionSneakyDeal() {
      this.question({
         cancel: true,
         options: [
            {
               label: _("Deal 1 damage"),
               action: () => {
                  this.activateNextAction();
               },
            },
            {
               label: _("Discard a mana card off 1 of your other spells"),
               action: () => {
                  this.actions.push("actionSelectManaFrom");
                  this.activateNextAction();
               },
            },
         ],
      });
   }

   private actionTimeDistortion() {
      const msg = _("${you} may select up to ${nbr} mana card");
      this.selectMana(2, msg, false);
   }

   private actionTrapAttack() {
      this.actionSelectManaFrom();
   }

   private actionWrath() {
      const msg = _("${you} may discard ${nbr} mana card from your hand");
      this.selectManaHand(2, msg, true, {
         canCancel: false,
         skip: {
            label: _("Pass"),
            message: _("Are you sure that you didn't want to discard mana cards?"),
         },
      });
   }

   /////////////////////////////////////////////////////////////
   //     _____              _       ____                   ___  
   //    / ____|            | |     |  _ \                 |__ \ 
   //   | |     __ _ _ __ __| |___  | |_) | __ _ ___  ___     ) |
   //   | |    / _` | '__/ _` / __| |  _ < / _` / __|/ _ \   / / 
   //   | |___| (_| | | | (_| \__ \ | |_) | (_| \__ \  __/  / /_ 
   //    \_____\__,_|_|  \__,_|___/ |____/ \__,_|___/\___| |____|
   //                                                            
   /////////////////////////////////////////////////////////////



   ///////////////////////////////////////////////////////////////////////////////////
   //     _____                      _                         _   _                 
   //    / ____|                    (_)              /\       | | (_)                
   //   | |  __  ___ _ __   ___ _ __ _  ___ ___     /  \   ___| |_ _  ___  _ __  ___ 
   //   | | |_ |/ _ \ '_ \ / _ \ '__| |/ __/ __|   / /\ \ / __| __| |/ _ \| '_ \/ __|
   //   | |__| |  __/ | | |  __/ |  | | (__\__ \  / ____ \ (__| |_| | (_) | | | \__ \
   //    \_____|\___|_| |_|\___|_|  |_|\___|___/ /_/    \_\___|\__|_|\___/|_| |_|___/
   //                                                                                
   ///////////////////////////////////////////////////////////////////////////////////


   private actionCastMana() {
      const { name, cost, type } = this.game.getCardType(this.current_card);
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());

      let modifiedCost = Math.max(cost - player_table.getDiscountNextSpell(), 0);
      if (type == "red") {
         modifiedCost = Math.max(modifiedCost - player_table.getDiscountNextAttack(), 0);
      }

      const msg = _("${you} must pay ${nbr} mana card").replace("${nbr}", modifiedCost.toString());

      this.game.setClientState(states.client.castSpellWithMana, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            card: this.current_card,
            cost: modifiedCost,
         },
      });
   }

   private actionGiveManaFromHandToOpponent() {
      const msg = _("${you} may select ${nbr} mana card to give to your opponent");
      this.selectManaHand(1, msg, false, {
         skip: {
            label: _("Pass"),
            message: _("Are you sure that you didn't want to give a mana to your opponent?"),
         },
      });
   }

   private actionSelectManaCoolDownPlayer() {
      const msg = _("Select a mana card under one of your spell")
      const { name } = this.game.getCardType(this.current_card);

      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const exclude: number[] = [];
      for (let index = 1; index <= 6; index++) {
         if (player_table.mana_cooldown[index].getCards().length == 0 || index == Number(this.current_card.location_arg)) {
            exclude.push(index);
         }
      }

      const args: SelectManaDeckArgs = {
         player_id: this.game.getPlayerId(),
         card: this.current_card,
         count: 1,
         exact: true,
         exclude,
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private actionSelectManaCoolDownOpponent() {
      const msg = _("Select a mana card under one of your opponent's spell")
      const { name } = this.game.getCardType(this.current_card);

      const player_table = this.game.getPlayerTable(this.game.getOpponentId());
      const exclude: number[] = [];
      for (let index = 1; index <= 6; index++) {
         if (player_table.mana_cooldown[index].getCards().length == 0) {
            exclude.push(index);
         }
      }

      const args = {
         player_id: this.game.getOpponentId(),
         card: this.current_card,
         count: 1,
         exact: true,
         exclude
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
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

      const msgFrom =
         this.actions.length > 0
            ? _("${you} must select ${nbr} mana card - source")
            : _("${you} must select ${nbr} mana card");
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

   private actionSelectTwoManaCardFromDiscard() {
      const msg = _("${you} may select ${nbr} mana card from the discard").replace("${nbr}", "2");
      const { name } = this.game.getCardType(this.current_card);
      this.game.setClientState(states.client.selectManaDiscard, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            player_id: this.game.getPlayerId(),
            count: 2,
            exact: true,
         } as SelectManaDiscardArgs,
      });
   }

   /////////////////////////////////////////////////////////////
   //    _    _  _    _  _  _  _    _            
   //   | |  | || |  (_)| |(_)| |  (_)           
   //   | |  | || |_  _ | | _ | |_  _   ___  ___ 
   //   | |  | || __|| || || || __|| | / _ \/ __|
   //   | |__| || |_ | || || || |_ | ||  __/\__ \
   //    \____/  \__||_||_||_| \__||_| \___||___/
   //                                            
   /////////////////////////////////////////////////////////////





   private question(args: QuestionArgs) {
      const { name } = this.game.getCardType(this.current_card);
      this.game.setClientState(states.client.question, {
         descriptionmyturn: _(name),
         args,
      });
   }

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

   private selectManaHand(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.current_card);
      msg = msg.replace("${nbr}", count.toString());

      const args = {
         ...argsSuppl,
         player_id: this.game.getPlayerId(),
         card: this.current_card,
         count,
         exact,
      };

      this.game.setClientState(states.client.selectManaHand, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private selectManaDeck(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.current_card);
      msg = msg.replace("${nbr}", count.toString());

      argsSuppl.exclude = argsSuppl.exclude ?? [];
      if (!argsSuppl.player_id || argsSuppl.player_id == this.game.getPlayerId()) {
         argsSuppl.exclude.push(Number(this.current_card.location_arg));
      }

      const args = {
         player_id: this.game.getPlayerId(),
         card: this.current_card,
         count,
         exact,
         ...argsSuppl,
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private returnManaCardToDeck(msg: string, count: number, canCancel: boolean) {
      msg = msg.replace("${nbr}", count.toString());

      const args = { count, canCancel, exact: true };

      const { name } = this.game.getCardType(this.current_card);
      this.game.setClientState(states.client.selectManaReturnDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }
}
