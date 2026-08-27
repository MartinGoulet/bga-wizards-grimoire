# Wizards Grimoire BGA project guidelines

## Scope

This repository implements a Board Game Arena game. Keep the existing BGA framework contracts, namespaces, state names, card locations, notification names, and generated-file workflow intact. Prefer a small change in the owning layer over a cross-layer refactor.

## Architecture

- `Game` is the BGA server entry point in `modules/php/Game.php` (`Bga\Games\wizardsgrimoireext\Game`). It composes the server traits and owns the two BGA decks: `deck_spells` and `deck_manas`.
- BGA configuration and lifecycle files are at the repository root: `gameinfos.inc.php`, `gameoptions.json`, `states.inc.php`, `stats.json`, `dbmodel.sql`, and `wizardsgrimoireext_wizardsgrimoireext.tpl`.
- `states.inc.php` is the authoritative server state-machine declaration. It defines state IDs, phases, possible actions, arguments, and transitions. Do not invent a client state without a matching BGA state contract when server interaction is required.
- `modules/php/states.php` implements game-state actions (`st...`). `modules/php/actions.php` implements player actions (`act...`) and must validate every client-provided ID, position, owner, location, and quantity on the server. `modules/php/args.php` exposes state arguments to the client.
- `modules/php/Core/` contains shared domain services and traits such as `Globals`, `Players`, `SpellCard`, `ManaCard`, `Notifications`, `Events`, `Stats`, and `CardLocation`.
- `modules/php/Cards/` contains card-specific rule classes grouped by card set. Card classes extend `BaseCard`, `OngoingBaseCard`, or `RelicCard`; shared card effects belong in the base/domain layer only when they are genuinely reusable.
- `modules/php/material.inc.php` is the card metadata source. Its class, set/icon, activation, cost, interaction, and JavaScript-action fields must remain consistent with the corresponding card class and client behavior.
- `modules/php/Objects/` contains small domain objects such as card-location helpers. `modules/php/States/` contains specialized server-side state objects when a flow needs one.
- `src/wizards-grimoire.ts` is the client entry point and constructs the managers and table views. `src/player-panel.ts`, `src/player-table.ts`, `src/table-center.ts`, `src/game-options.ts`, and `src/modal.ts` own view-level UI behavior.
- `src/managers/state.ts` maps BGA state names to client state handlers. `src/managers/action.ts` sequences card interactions and submits BGA actions. `src/managers/notification.ts` applies server notifications to the UI. Interactive client flows live in `src/states/`.
- `src/deck.ts` and `src/managers/cards.ts` wrap card/deck UI behavior. Shared game-facing types belong in `src/types.ts` or the project declaration files, not in individual state handlers.
- SCSS starts at `src/wizards-grimoire.scss`; component styles are split across the other `src/*.scss` files.

## Server and client flow

- The server is authoritative for rules, randomness, card movement, ownership, costs, damage, life, global values, and state transitions. Never trust a client selection or reproduce rule resolution only in TypeScript.
- A normal interaction flows from a BGA state to a server `st...` or `act...` method, then through `Notifications`, and finally to a handler in `NotificationManager` or a client state handler.
- Use BGA deck APIs and `CardLocation` for card movement. Keep database/deck state changes and their corresponding notifications together so every player can update consistently.
- Use `Globals`, `Players`, `Stats`, and `Events` through their existing APIs. Do not add ad-hoc global storage or direct client-side rule state when an existing service already owns that concern.
- When adding or changing an action, update the full contract: `states.inc.php` possible actions and transitions, PHP action validation, state arguments if needed, client `bgaPerformAction` calls, and notification/client-state handling.
- Keep notification payload keys stable and explicit. If a new notification is required, add the server notifier and the matching `notif_...` client handler together.

## Adding a card or feature

1. Add or update the card metadata in `modules/php/material.inc.php`.
2. Add the PHP card class in the matching `modules/php/Cards/<set>/` directory and use the existing base-card helpers for effects, mana, damage, healing, and notifications.
3. For interaction, declare the relevant JavaScript action metadata, implement or extend the client action sequence in `src/managers/action.ts`, and add a handler in `src/states/` when the interaction needs a client state.
4. Register every new TypeScript file in `tsconfig.json`; this project uses an explicit `files` list, so an unregistered file is omitted from the bundle.
5. Update state/notification contracts and focused documentation or data files only when the feature requires them.

## Source files and generated files

- Edit TypeScript in `src/`, SCSS in `src/`, and PHP in `modules/php/` or the relevant root BGA configuration file.
- `wizardsgrimoireext.js` is generated by `npm run build:ts`; `wizardsgrimoireext.css` is generated by `npm run build:scss`. Do not hand-edit these outputs. Regenerate them after source changes when they are part of the deliverable.
- Preserve the existing TypeScript compiler target and `module: none` setup in `tsconfig.json`, because BGA loads the emitted global script directly.
- Keep PHP namespaces and the custom autoloader compatible with the directory layout. Use the existing naming conventions for `act...`, `st...`, `arg...`, `notif_...`, and card classes.

## Build and validation

- Install dependencies with `npm ci` when dependencies are missing.
- Build the client bundle with `npm run build:ts`.
- Build styles with `npm run build:scss`.
- Use `npm run watch:ts` or `npm run watch:scss` during local iteration when useful.
- `npm test` is currently a placeholder that exits with an error; do not treat it as an available automated test suite.
- For a gameplay change, validate the smallest affected BGA state flow and check both the active player and the opponent view, especially hidden cards, notifications, and temporary player switching.
- Avoid committing unrelated generated output or formatting churn. Review the final diff and keep source and generated artifacts synchronized when required by deployment.
