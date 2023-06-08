interface WizardsGrimoirePlayerData extends BgaPlayer {
   // Add Player data
}

type PlayerBoardObject = { [player_id: number]: PlayerBoardInfo };

interface WizardsGrimoireGamedatas extends BgaGamedatas<WizardsGrimoirePlayerData> {
   slot_count: 8 | 10;
   slot_cards: SpellCard[];

   spells: SpellInfo;
   manas: ManaInfo;

   card_types: { [card_type: number]: CardType };
   player_board: PlayerBoardObject;
   players_order: number[];
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
   icon: "+" | "++" | "scroll";
   js_actions?: string[] | string;
   debug: "red" | "yellow" | "green";
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
}

/////////////////////////////////////////////
// States
interface StateHandler {
   onEnteringState(args: any): void;
   onLeavingState(): void;
   onUpdateActionButtons(args: any): void;
   restoreGameState();
}

/**
 * Notifications
 */

interface NotifChooseSpellArgs {
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

interface NotifMoveManaCardsArgs {
   player_id: number;
   nbr: number;
   cards_before: ManaCard[];
   cards_after: ManaCard[];
}

interface NotifSpellCoolDownArgs {
   player_id: number;
   mana_cards_discard: { pos: number | ManaCard };
}

interface NotifHealthChangedArgs {
   player_id: number;
   life_remaining: number;
   damage: number;
}
