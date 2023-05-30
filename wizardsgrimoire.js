define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter", "ebg/stock"], function (dojo, declare) {
    return declare("bgagame.wizardsgrimoire", ebg.core.gamegui, new WizardsGrimoire());
});
var isDebug = window.location.host == "studio.boardgamearena.com" ||
    window.location.hash.indexOf("debug") > -1;
var log = isDebug ? console.log.bind(window.console) : function () { };
var WizardsGrimoire = (function () {
    function WizardsGrimoire() {
        this.notifications_manager = new NotificationManager(this);
        log("Constructor");
    }
    WizardsGrimoire.prototype.setup = function (gamedatas) {
        log(gamedatas);
        for (var player_id in gamedatas.players) {
        }
        this.setupNotifications();
    };
    WizardsGrimoire.prototype.onEnteringState = function (stateName, args) { };
    WizardsGrimoire.prototype.onLeavingState = function (stateName) { };
    WizardsGrimoire.prototype.onUpdateActionButtons = function (stateName, args) { };
    WizardsGrimoire.prototype.takeAction = function (action, data) {
        data = data || {};
        data.lock = true;
        this.ajaxcall("/wizardsgrimoire/wizardsgrimoire/".concat(action, ".html"), data, this, function () { });
    };
    WizardsGrimoire.prototype.addActionButtonRed = function (id, label, action) {
        this.addActionButton(id, label, action, null, null, "red");
    };
    WizardsGrimoire.prototype.setupNotifications = function () {
        log("notifications subscriptions setup");
        this.notifications_manager.setup();
    };
    WizardsGrimoire.prototype.format_string_recursive = function (log, args) {
        try {
            if (log && args && !args.processed) {
                args.processed = true;
                if (args.card_name !== undefined) {
                    args.card_name = "<b>" + _(args.card_name) + "</b>";
                }
            }
        }
        catch (e) {
            console.error(log, args, "Exception thrown", e.stack);
        }
        return this.inherited(arguments);
    };
    return WizardsGrimoire;
}());
var ANIMATION_MS = 1500;
var NotificationManager = (function () {
    function NotificationManager(game) {
        this.game = game;
    }
    NotificationManager.prototype.setup = function () {
    };
    NotificationManager.prototype.subscribeEvent = function (eventName, time) {
        try {
            dojo.subscribe(eventName, this, "notif_" + eventName);
            if (time) {
                this.game.notifqueue.setSynchronous(eventName, time);
            }
        }
        catch (_a) {
            console.error("NotificationManager::subscribeEvent");
        }
    };
    return NotificationManager;
}());
