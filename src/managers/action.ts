type TakeActionType = "castSpell" | "castSpellInteraction" | "activateDelayedSpell" | "replaceSpell";

class ActionManager {
   private actions: string[] = [];
   private actions_args: string[] = [];
   private current_card: SpellCard[];
   private take_action: TakeActionType;

   constructor(private game: WizardsGrimoire) {}

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

      this.current_card = [];
      this.take_action = null;
      this.actions = [];
      this.actions_args = [];
      return this;
   }

   public addAction(card: SpellCard) {
      this.current_card.push(card);
      const card_type = this.game.getCardType(card);
      log("actionmanager.addAction", card, card_type);
      this.addActionPriv(card_type.js_actions);
      log("actionmanager.actions values", this.actions);
      return this;
   }

   public addActionInteraction(card: SpellCard) {
      this.current_card.push(card);
      const card_type = this.game.getCardType(card);
      log("actionmanager.addActionInteraction", card, card_type);
      this.addActionPriv(card_type.js_actions_interaction);
      return this;
   }

   public addActionDelayed(card: SpellCard) {
      this.current_card.push(card);
      const card_type = this.game.getCardType(card);
      log("actionmanager.addActionDelayed", card, card_type);
      this.addActionPriv(card_type.js_actions_delayed);
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
      log("activateNextAction");
      log(this.actions_args);
      if (this.actions.length > 0) {
         // Invoke action
         const nextAction = this.actions.shift();
         this[nextAction]();
         return;
      }

      const card_type = this.game.getCardType(this.current_card[0]);
      log("actionmanager.activateNextAction", this.current_card, card_type, this.actions_args);

      const handleError = (is_error: boolean) => {
         is_error ? this.game.restoreGameState() : this.game.clearSelection();
      };

      const data = {
         card_id: this.current_card[0].id,
         args: this.actions_args.join(";"),
      };

      this.game.takeAction(this.take_action, data, null, handleError);
   }

   public getCurrentCard(): SpellCard {
      if (this.current_card.length > 0) {
         return this.current_card[this.current_card.length - 1];
      } else {
         return null;
      }
   }

   private actionCastSpell_Replace() {
      this.actions.push("actionCastSpell_Pool", "actionCastSpell_Repertoire", "actionCastSpell_Submit");
      this.activateNextAction();
   }

   private actionCastSpell_Pool() {
      // const msg = _("${you} must select a spell in the spell pool");
      const msg = _("${you} may choose to replace a spell or pass");
      const args: SelectSpellPoolStatesArgs = {
         skip: {
            label: "Pass",
            action: () => {
               this.actions.splice(0);
               this.game.takeAction("pass");
            },
         },
         cancel: false,
      };
      this.game.setClientState(states.client.selectSpellPool, {
         descriptionmyturn: msg,
         args,
      });
   }

   private actionCastSpell_Repertoire() {
      const spellPoolCardId = Number(this.actions_args[0]);
      const spellPoolCard = this.game.tableCenter.spellPool
         .getCards()
         .find((spell) => Number(spell.id) == spellPoolCardId);

      this.game.markCardAsSelected(spellPoolCard);

      const player_table = this.game.getCurrentPlayerTable();

      const selectableSpell = player_table.spell_repertoire.getCards().filter((card) => {
         const manacount = player_table.mana_cooldown[Number(card.location_arg)].getCards().length;
         return manacount == 0;
      });

      const msg = _("${you} must select one of your spell");
      this.game.setClientState(states.client.selectSpell, {
         descriptionmyturn: msg,
         args: {
            player_id: this.game.getPlayerId(),
            selection: selectableSpell,
            cancel: true,
            pass: false,
         } as SelectSpellArgs,
      });
   }

   private actionCastSpell_Submit() {
      const new_spell_id = Number(this.actions_args[0]);
      const old_spell_pos = Number(this.actions_args[1]);

      const old_spell_id = this.game
         .getCurrentPlayerTable()
         .spell_repertoire.getCards()
         .find((card) => Number(card.location_arg) == old_spell_pos).id;

      const handleError = (is_error: boolean) => {
         is_error ? this.game.restoreGameState() : this.game.clearSelection();
      };

      this.game.takeAction(
         "replaceSpell",
         {
            new_spell_id,
            old_spell_id,
         },
         null,
         handleError,
      );
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
      const msg = _("${you} must place 4 of the revealed mana on top of the mana deck");
      const { name } = this.game.getCardType(this.getCurrentCard());
      this.game.setClientState(states.client.arcaneTactics, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {},
      });
   }

   private actionBadFortune() {
      const msg = _("${you} must return revealed mana greater than 1 on the top of mana deck in any order");

      const { name } = this.game.getCardType(this.getCurrentCard());
      this.game.setClientState(states.client.badFortune, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            spell: this.getCurrentCard(),
         } as BadFortuneArgs,
      });
   }

   private actionEnergyReserve() {
      this.actionSelectManaFrom();
   }

   private actionFracture() {
      this.actions.push("actionSelectManaFrom", "actionSelectManaTo");
      this.activateNextAction();
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
                  this.actionSelectSpellOpponent();
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
      const msg = _("${you} may select ${nbr} mana card(s) from your hand");
      this.selectManaHand(1, msg, true);
   }

   private actionMistOfPain() {
      const msg = _("${you} may discard up to ${nbr} mana card(s) from your hand");
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

   private actionShadowAttack() {
      this.actionSelectManaFrom();
   }

   private actionShadowPower() {
      this.actionGiveManaFromHandToOpponent();
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
      const msg = _("${you} may select up to ${nbr} mana card(s)");
      this.selectMana(2, msg, false);
   }

   private actionToxicGift() {
      this.actionGiveManaFromHandToOpponent();
   }

   private actionTrapAttack() {
      this.actionSelectManaFrom();
   }

   private actionWrath() {
      const msg = _("${you} may discard ${nbr} mana card(s) from your hand");
      this.selectManaHand(2, msg, true, {
         canCancel: false,
         skip: {
            label: _("Pass"),
            message: _("Are you sure that you don't want to discard mana cards?"),
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

   private actionAfterShock() {
      this.selectManaHand(1, _("Select ${nbr} mana card(s) to return on top of the mana deck"), true, {
         canCancel: false,
      });
   }

   private actionCoerciveAgreement() {
      this.question({
         cancel: true,
         options: [
            {
               label: _("Take up to 3 randomly selected mana from your opponent's hand"),
               action: () => {
                  this.activateNextAction();
               },
            },
            {
               label: _("Discard a mana card off 2 of your other spells"),
               action: () => {
                  this.selectManaDeck(2, _("${you} may select up to ${nbr} mana card(s)"), false);
               },
            },
         ],
      });
   }

   private actionContamination() {
      const msg = _("${you} may select ${nbr} mana card(s) to move from your hand on the mana deck");
      this.returnManaCardToDeck(msg, 2, true, true);
   }

   private actionDanceOfPain() {
      const player_table = this.game.getCurrentPlayerTable();
      if (player_table.hand.getCards().length <= 2) {
         this.activateNextAction();
         return;
      }

      const count = player_table.hand.getCards().length - 2;
      this.selectManaHand(count, _("${you} must select ${nbr} mana card(s) to discard"), true);
   }

   private actionDelusion() {
      this.actionSelectManaCoolDownOpponent(true);
   }

   private actionEnergyShield() {
      this.actions.push("actionSelectManaCoolDownPlayer", "actionSelectSpellOpponent");
      this.activateNextAction();
   }

   private actionMirrorImage() {
      this.game.markCardAsSelected(this.getCurrentCard());
      const player_table = this.game.getCurrentPlayerTable();

      const selectableSpell = player_table.spell_repertoire.getCards().filter((card) => {
         const manacount = player_table.mana_cooldown[Number(card.location_arg)].getCards().length;
         return manacount > 0;
      });

      const { name } = this.game.getCardType(this.getCurrentCard());
      const msg = _("${you} must select one of your spell");
      this.game.setClientState(states.client.selectSpell, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            player_id: this.game.getPlayerId(),
            selection: selectableSpell,
            cancel: true,
            pass: false,
         } as SelectSpellArgs,
      });
   }

   private actionPossessed() {
      this.selectManaHand(1, _("${you} may give ${nbr} mana card(s) from your hand to reduce damage"), true, {
         canCancel: false,
         skip: {
            label: _("Pass"),
            message: _("Are you sure that you don't want to give a mana to your opponent?"),
         },
      });
   }

   private actionSilentSupport() {
      const player_table = this.game.getCurrentPlayerTable();

      const emptyDecks = player_table
         .getManaDeckWithSpellOver()
         .filter((deck) => deck.isEmpty())
         .map((deck) => deck.location);

      const { name } = this.game.getCardType(this.getCurrentCard());
      const msg = _("${you} must select ${nbr} mana card(s)").replace("${nbr}", "1");

      const args: SelectManaDeckArgs = {
         player_id: this.game.getPlayerId(),
         card: this.getCurrentCard(),
         count: 1,
         exact: true,
         exclude: emptyDecks,
         ignore: () => {
            this.addArgument("0");
            this.activateNextAction();
         },
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   /////////////////////////////////////////////////////////////
   //   _  ___      _        _             _              __
   //   | |/ (_)    | |      | |           | |            /_ |
   //   | ' / _  ___| | _____| |_ __ _ _ __| |_ ___ _ __   | |
   //   |  < | |/ __| |/ / __| __/ _` | '__| __/ _ \ '__|  | |
   //   | . \| | (__|   <\__ \ || (_| | |  | ||  __/ |     | |
   //   |_|\_\_|\___|_|\_\___/\__\__,_|_|   \__\___|_|     |_|
   //
   /////////////////////////////////////////////////////////////

   private actionAffliction() {
      this.game.markCardAsSelected(this.getCurrentCard());
      this.question({
         cancel: true,
         options: [
            {
               label: _("Deal 1 damage to yourself to gain 2 extra cards"),
               action: () => {
                  this.addArgument("1");
                  this.activateNextAction();
               },
            },
            {
               label: _("Pass"),
               action: () => {
                  this.addArgument("0");
                  this.activateNextAction();
               },
            },
         ],
      });
   }

   private actionFatalFlaw() {
      const canIgnore =
         this.game.getPlayerTable(this.game.getOpponentId()).getSpellSlotAvailables().length == 6;
      this.actionSelectManaCoolDownOpponent(canIgnore);
   }

   private actionQuickSwap() {
      this.game.markCardAsSelected(this.getCurrentCard());
      this.question({
         cancel: true,
         options: [
            {
               label: _("Deal 1 damage"),
               action: () => this.activateNextAction(),
            },
            {
               label: _("Discard this spell and replace it with a new spell"),
               action: () => {
                  const msg = _("${you} must select a spell in the spell pool");
                  const { name } = this.game.getCardType(this.getCurrentCard());

                  this.game.setClientState(states.client.selectSpellPool, {
                     descriptionmyturn: _(name) + " : " + msg,
                     args: {},
                  });

                  this.game.markCardAsSelected(this.getCurrentCard());
               },
            },
         ],
      });
   }

   private actionTwistOfFate() {
      this.actions.push("actionTwistOfFate_Pool", "actionTwistOfFate_Repertoire");
      this.activateNextAction();
   }

   private actionTwistOfFate_Pool() {
      const msg = _("${you} must select a spell in the spell pool");
      this.game.setClientState(states.client.selectSpellPool, {
         descriptionmyturn: msg,
         args: {
            skip: {
               label: _("Pass"),
               action: () => {
                  this.actions.splice(0);
                  this.activateNextAction();
               },
            },
         },
      });
   }

   private actionTwistOfFate_Repertoire() {
      const spellPoolCardId = Number(this.actions_args[this.actions_args.length - 1]);
      const spellPoolCard = this.game.tableCenter.spellPool
         .getCards()
         .find((spell) => Number(spell.id) == spellPoolCardId);

      this.game.markCardAsSelected(spellPoolCard);

      const player_table = this.game.getCurrentPlayerTable();

      const selectableSpell = player_table.spell_repertoire.getCards().filter((card) => {
         return card.id !== this.getCurrentCard().id;
      });

      const msg = _("${you} must select one of your spell");
      this.game.setClientState(states.client.selectSpell, {
         descriptionmyturn: msg,
         args: {
            player_id: this.game.getPlayerId(),
            selection: selectableSpell,
            cancel: true,
            pass: false,
         } as SelectSpellArgs,
      });
   }

   private actionWildBloom() {
      this.actions.push("actionWildBloom_selectSpell");
      this.actions.push("actionWildBloom_activate");
      this.activateNextAction();
   }

   private actionWildBloom_selectSpell() {
      this.game.markCardAsSelected(this.getCurrentCard());
      const player_table = this.game.getCurrentPlayerTable();

      const instantSpell = player_table.spell_repertoire.getCards().filter((card) => {
         const type = this.game.getCardType(card);
         if (type.activation == "instant") {
            const manacount = player_table.mana_cooldown[Number(card.location_arg)].getCards().length;
            return manacount == 0;
         }
         return false;
      });

      const { name } = this.game.getCardType(this.getCurrentCard());
      const msg = _("${you} may select one of your instant spell");
      this.game.setClientState(states.client.selectSpell, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            player_id: this.game.getPlayerId(),
            selection: instantSpell,
            cancel: false,
            ignore: () => {
               this.actions.splice(0);
               this.addArgument("0");
               this.activateNextAction();
            },
         } as SelectSpellArgs,
      });
   }

   private actionWildBloom_activate() {
      const selectedSpell = this.game.getCurrentPlayerTable().spell_repertoire.getSelection()[0];
      this.addAction(selectedSpell);
      this.activateNextAction();
   }

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
      const { name, cost, type } = this.game.getCardType(this.getCurrentCard());
      const player_table = this.game.getCurrentPlayerTable();

      let modifiedCost = Math.max(cost - player_table.getDiscountNextSpell(), 0);
      if (type == "red") {
         modifiedCost = Math.max(modifiedCost - player_table.getDiscountNextAttack(), 0);
      }

      const msg = _("${you} must pay ${nbr} mana card(s)").replace("${nbr}", modifiedCost.toString());

      this.game.setClientState(states.client.castSpellWithMana, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            card: this.getCurrentCard(),
            cost: modifiedCost,
         },
      });
   }

   private actionGiveManaFromHandToOpponent() {
      const msg = _("${you} may select ${nbr} mana card(s) to give to your opponent");
      this.selectManaHand(1, msg, false, {
         skip: {
            label: _("Pass"),
            message: _("Are you sure that you don't want to give a mana to your opponent?"),
         },
      });
   }

   private actionSelectManaCoolDownPlayer() {
      const msg = _("Select a mana card under one of your spell");
      const { name } = this.game.getCardType(this.getCurrentCard());

      const player_table = this.game.getCurrentPlayerTable();
      const exclude: number[] = [];
      for (let index = 1; index <= 6; index++) {
         if (
            player_table.mana_cooldown[index].getCards().length == 0 ||
            index == Number(this.getCurrentCard().location_arg)
         ) {
            exclude.push(index);
         }
      }

      const args: SelectManaDeckArgs = {
         player_id: this.game.getPlayerId(),
         card: this.getCurrentCard(),
         count: 1,
         exact: true,
         exclude,
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private actionSelectManaCoolDownOpponent(canIgnore: boolean = false) {
      const msg = _("Select a mana card under one of your opponent's spell");
      const { name } = this.game.getCardType(this.getCurrentCard());

      const player_table = this.game.getPlayerTable(this.game.getOpponentId());
      const exclude: number[] = [];
      for (let index = 1; index <= 6; index++) {
         if (player_table.mana_cooldown[index].getCards().length == 0) {
            exclude.push(index);
         }
      }

      const args = {
         player_id: this.game.getOpponentId(),
         card: this.getCurrentCard(),
         count: 1,
         exact: true,
         exclude,
         ignore: null,
      };

      if (canIgnore) {
         args.ignore = () => {
            this.activateNextAction();
         };
      }

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private actionSelectManaFrom() {
      const player_table = this.game.getCurrentPlayerTable();

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
            ? _("${you} may select ${nbr} mana card(s) to move")
            : _("${you} must select ${nbr} mana card(s)");
      this.selectManaDeck(1, msgFrom, true, argsSuppl);
   }

   private actionSelectManaTo() {
      const manaDeckPosition: number = Number(this.actions_args[this.actions_args.length - 1]);
      const player_table = this.game.getCurrentPlayerTable();
      player_table.mana_cooldown[manaDeckPosition].forceSelected();

      const argsSuppl = {
         exclude: [manaDeckPosition],
      };

      const msg = _("${you} must select ${nbr} mana cool down pile for the destination");
      this.selectManaDeck(1, msg, true, argsSuppl);
   }

   private actionSelectTwoManaCardFromDiscard() {
      const msg = _("${you} may select ${nbr} mana card(s) from the discard").replace("${nbr}", "2");
      const { name } = this.game.getCardType(this.getCurrentCard());
      this.game.setClientState(states.client.selectManaDiscard, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            player_id: this.game.getPlayerId(),
            count: 2,
            exact: false,
         } as SelectManaDiscardArgs,
      });
   }

   private actionSelectSpellOpponent() {
      const { name } = this.game.getCardType(this.getCurrentCard());
      const msg = _("${you} must select an opponent's spell");
      this.game.setClientState(states.client.selectSpell, {
         descriptionmyturn: _(name) + " : " + msg,
         args: {
            player_id: this.game.getOpponentId(),
            cancel: true,
         } as SelectSpellArgs,
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
      const { name } = this.game.getCardType(this.getCurrentCard());
      this.game.setClientState(states.client.question, {
         descriptionmyturn: _(name),
         args,
      });
   }

   private selectMana(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.getCurrentCard());
      msg = msg.replace("${nbr}", count.toString());

      argsSuppl.exclude = argsSuppl.exclude ?? [];
      argsSuppl.exclude.push(Number(this.getCurrentCard().location_arg));

      const args = {
         ...argsSuppl,
         player_id: this.game.getPlayerId(),
         card: this.getCurrentCard(),
         count,
         exact,
      };

      this.game.setClientState(states.client.selectMana, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private selectManaHand(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.getCurrentCard());
      msg = msg.replace("${nbr}", count.toString());

      const args = {
         ...argsSuppl,
         player_id: this.game.getPlayerId(),
         card: this.getCurrentCard(),
         count,
         exact,
      };

      this.game.setClientState(states.client.selectManaHand, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private selectManaDeck(count: number, msg: string, exact: boolean, argsSuppl: any = {}) {
      const { name } = this.game.getCardType(this.getCurrentCard());
      msg = msg.replace("${nbr}", count.toString());

      argsSuppl.exclude = argsSuppl.exclude ?? [];
      if (!argsSuppl.player_id || argsSuppl.player_id == this.game.getPlayerId()) {
         argsSuppl.exclude.push(Number(this.getCurrentCard().location_arg));
      }

      const args = {
         player_id: this.game.getPlayerId(),
         card: this.getCurrentCard(),
         count,
         exact,
         ...argsSuppl,
      };

      this.game.setClientState(states.client.selectManaDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }

   private returnManaCardToDeck(msg: string, count: number, canCancel: boolean, canPass: boolean = false) {
      msg = msg.replace("${nbr}", count.toString());

      const args = { count, canCancel, exact: true, canPass } as SelectManaReturnDeckStatesArgs;

      const { name } = this.game.getCardType(this.getCurrentCard());
      this.game.setClientState(states.client.selectManaReturnDeck, {
         descriptionmyturn: _(name) + " : " + msg,
         args,
      });
   }
}
