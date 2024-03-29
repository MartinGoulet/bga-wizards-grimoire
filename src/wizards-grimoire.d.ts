interface WizardsGrimoirePlayerData extends BgaPlayer {
   turn: number;
   // Add Player data
}

type PlayerBoardObject = { [player_id: number]: PlayerBoardInfo };

interface WizardsGrimoireGamedatas extends BgaGamedatas<WizardsGrimoirePlayerData> {
   slot_count: 8 | 10;
   slot_cards: SpellCard[];

   spells: SpellInfo;
   manas: ManaInfo;

   first_player: number;
   card_types: { [card_type: number]: CardType };
   player_board: PlayerBoardObject;
   players_order: number[];
   opponent_id: number;
   ongoing_spells: OngoingSpell[];
   globals: {
      previous_basic_attack: number;
      last_basic_attack_damage: number;
   };

   images: {
      front_1: boolean;
      front_2: boolean;
   };
}

interface OngoingSpell {
   name: string;
   active: boolean;
}

interface PlayerBoardInfo {
   manas: { [position: number]: ManaCard[] };
   spells: SpellCard[];
   hand: ManaCard[];
}

interface Card {
   id: number;
   type: string;
   // type_arg: string;
   location: string;
   location_arg: number;
}

interface CardType {
   name: string;
   description: string;
   type: "red" | "green" | "purple";
   activation: "instant" | "delayed" | "ongoing";
   cost: number;
   icon: "Base_1" | "Base_2" | "KickStarter_1";
   js_actions?: string[] | string;
   js_actions_interaction?: string[] | string;
   js_actions_delayed?: string[] | string;
}

interface SpellCard extends Card {
   isHidden?: boolean;
}

interface ManaCard extends Card {
   isHidden?: boolean;
}

interface SpellInfo {
   deck: SpellCard[];
   discard: SpellCard[];
}
interface ManaInfo {
   deck: ManaCard[];
   discard: ManaCard[];
   attack: ManaCard[];
   revealed: ManaCard[];
}

/////////////////////////////////////////////
// States
interface StateHandler {
   onEnteringState(args: any): void;
   onLeavingState(): void;
   onUpdateActionButtons(args: any): void;
   restoreGameState(): Promise<boolean>;
}

/**
 * Notifications
 */

interface NotifChooseSpellArgs {
   player_id: number;
   card: SpellCard;
}
interface NotifDiscardSpellArgs {
   player_id: number;
   card: SpellCard;
}

interface NotifRefillSpellArgs {
   card: SpellCard;
}

interface NotifDrawManaCardsArgs {
   player_id: number;
   cards: ManaCard[];
}

interface NotifManaDeckShuffleArgs {
   cards: ManaCard[];
}

interface NotifMoveManaCardsArgs {
   player_id: number;
   // nbr: number;
   // cards_before: ManaCard[];
   // cards_after: ManaCard[];
   cards_after: ManaCard[];
}

interface NotifOngoingSpellActive {
   variable: string;
   value: boolean;
}

interface NotifRevealManaCardArgs {
   player_id: number;
   mana_values: number[];
}

interface NotifRevealManaCardCooldown {
   player_id: number;
   card: ManaCard;
}

interface NotifHealthChangedArgs {
   player_id: number;
   life_remaining: number;
   nbr_damage: number;
}
