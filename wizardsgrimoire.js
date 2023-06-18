var DEFAULT_ZOOM_LEVELS = [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1];
var ZoomManager = (function () {
    function ZoomManager(settings) {
        var _this = this;
        var _a, _b, _c, _d, _e;
        this.settings = settings;
        if (!settings.element) {
            throw new DOMException('You need to set the element to wrap in the zoom element');
        }
        this.zoomLevels = (_a = settings.zoomLevels) !== null && _a !== void 0 ? _a : DEFAULT_ZOOM_LEVELS;
        this._zoom = this.settings.defaultZoom || 1;
        if (this.settings.localStorageZoomKey) {
            var zoomStr = localStorage.getItem(this.settings.localStorageZoomKey);
            if (zoomStr) {
                this._zoom = Number(zoomStr);
            }
        }
        this.wrapper = document.createElement('div');
        this.wrapper.id = 'bga-zoom-wrapper';
        this.wrapElement(this.wrapper, settings.element);
        this.wrapper.appendChild(settings.element);
        settings.element.classList.add('bga-zoom-inner');
        if ((_b = settings.smooth) !== null && _b !== void 0 ? _b : true) {
            settings.element.dataset.smooth = 'true';
            settings.element.addEventListener('transitionend', function () { return _this.zoomOrDimensionChanged(); });
        }
        if ((_d = (_c = settings.zoomControls) === null || _c === void 0 ? void 0 : _c.visible) !== null && _d !== void 0 ? _d : true) {
            this.initZoomControls(settings);
        }
        if (this._zoom !== 1) {
            this.setZoom(this._zoom);
        }
        window.addEventListener('resize', function () {
            var _a;
            _this.zoomOrDimensionChanged();
            if ((_a = _this.settings.autoZoom) === null || _a === void 0 ? void 0 : _a.expectedWidth) {
                _this.setAutoZoom();
            }
        });
        if (window.ResizeObserver) {
            new ResizeObserver(function () { return _this.zoomOrDimensionChanged(); }).observe(settings.element);
        }
        if ((_e = this.settings.autoZoom) === null || _e === void 0 ? void 0 : _e.expectedWidth) {
            this.setAutoZoom();
        }
    }
    Object.defineProperty(ZoomManager.prototype, "zoom", {
        get: function () {
            return this._zoom;
        },
        enumerable: false,
        configurable: true
    });
    ZoomManager.prototype.setAutoZoom = function () {
        var _this = this;
        var _a, _b, _c;
        var zoomWrapperWidth = document.getElementById('bga-zoom-wrapper').clientWidth;
        if (!zoomWrapperWidth) {
            setTimeout(function () { return _this.setAutoZoom(); }, 200);
            return;
        }
        var expectedWidth = (_a = this.settings.autoZoom) === null || _a === void 0 ? void 0 : _a.expectedWidth;
        var newZoom = this.zoom;
        while (newZoom > this.zoomLevels[0] && newZoom > ((_c = (_b = this.settings.autoZoom) === null || _b === void 0 ? void 0 : _b.minZoomLevel) !== null && _c !== void 0 ? _c : 0) && zoomWrapperWidth / newZoom < expectedWidth) {
            newZoom = this.zoomLevels[this.zoomLevels.indexOf(newZoom) - 1];
        }
        if (this._zoom == newZoom) {
            if (this.settings.localStorageZoomKey) {
                localStorage.setItem(this.settings.localStorageZoomKey, '' + this._zoom);
            }
        }
        else {
            this.setZoom(newZoom);
        }
    };
    ZoomManager.prototype.setZoom = function (zoom) {
        var _a, _b, _c, _d;
        if (zoom === void 0) { zoom = 1; }
        this._zoom = zoom;
        if (this.settings.localStorageZoomKey) {
            localStorage.setItem(this.settings.localStorageZoomKey, '' + this._zoom);
        }
        var newIndex = this.zoomLevels.indexOf(this._zoom);
        (_a = this.zoomInButton) === null || _a === void 0 ? void 0 : _a.classList.toggle('disabled', newIndex === this.zoomLevels.length - 1);
        (_b = this.zoomOutButton) === null || _b === void 0 ? void 0 : _b.classList.toggle('disabled', newIndex === 0);
        this.settings.element.style.transform = zoom === 1 ? '' : "scale(".concat(zoom, ")");
        (_d = (_c = this.settings).onZoomChange) === null || _d === void 0 ? void 0 : _d.call(_c, this._zoom);
        this.zoomOrDimensionChanged();
    };
    ZoomManager.prototype.manualHeightUpdate = function () {
        if (!window.ResizeObserver) {
            this.zoomOrDimensionChanged();
        }
    };
    ZoomManager.prototype.zoomOrDimensionChanged = function () {
        var _a, _b;
        this.settings.element.style.width = "".concat(this.wrapper.getBoundingClientRect().width / this._zoom, "px");
        this.wrapper.style.height = "".concat(this.settings.element.getBoundingClientRect().height, "px");
        (_b = (_a = this.settings).onDimensionsChange) === null || _b === void 0 ? void 0 : _b.call(_a, this._zoom);
    };
    ZoomManager.prototype.zoomIn = function () {
        if (this._zoom === this.zoomLevels[this.zoomLevels.length - 1]) {
            return;
        }
        var newIndex = this.zoomLevels.indexOf(this._zoom) + 1;
        this.setZoom(newIndex === -1 ? 1 : this.zoomLevels[newIndex]);
    };
    ZoomManager.prototype.zoomOut = function () {
        if (this._zoom === this.zoomLevels[0]) {
            return;
        }
        var newIndex = this.zoomLevels.indexOf(this._zoom) - 1;
        this.setZoom(newIndex === -1 ? 1 : this.zoomLevels[newIndex]);
    };
    ZoomManager.prototype.setZoomControlsColor = function (color) {
        if (this.zoomControls) {
            this.zoomControls.dataset.color = color;
        }
    };
    ZoomManager.prototype.initZoomControls = function (settings) {
        var _this = this;
        var _a, _b, _c, _d, _e, _f;
        this.zoomControls = document.createElement('div');
        this.zoomControls.id = 'bga-zoom-controls';
        this.zoomControls.dataset.position = (_b = (_a = settings.zoomControls) === null || _a === void 0 ? void 0 : _a.position) !== null && _b !== void 0 ? _b : 'top-right';
        this.zoomOutButton = document.createElement('button');
        this.zoomOutButton.type = 'button';
        this.zoomOutButton.addEventListener('click', function () { return _this.zoomOut(); });
        if ((_c = settings.zoomControls) === null || _c === void 0 ? void 0 : _c.customZoomOutElement) {
            settings.zoomControls.customZoomOutElement(this.zoomOutButton);
        }
        else {
            this.zoomOutButton.classList.add("bga-zoom-out-icon");
        }
        this.zoomInButton = document.createElement('button');
        this.zoomInButton.type = 'button';
        this.zoomInButton.addEventListener('click', function () { return _this.zoomIn(); });
        if ((_d = settings.zoomControls) === null || _d === void 0 ? void 0 : _d.customZoomInElement) {
            settings.zoomControls.customZoomInElement(this.zoomInButton);
        }
        else {
            this.zoomInButton.classList.add("bga-zoom-in-icon");
        }
        this.zoomControls.appendChild(this.zoomOutButton);
        this.zoomControls.appendChild(this.zoomInButton);
        this.wrapper.appendChild(this.zoomControls);
        this.setZoomControlsColor((_f = (_e = settings.zoomControls) === null || _e === void 0 ? void 0 : _e.color) !== null && _f !== void 0 ? _f : 'black');
    };
    ZoomManager.prototype.wrapElement = function (wrapper, element) {
        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);
    };
    return ZoomManager;
}());
var __spreadArray = (this && this.__spreadArray) || function (to, from, pack) {
    if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
        if (ar || !(i in from)) {
            if (!ar) ar = Array.prototype.slice.call(from, 0, i);
            ar[i] = from[i];
        }
    }
    return to.concat(ar || Array.prototype.slice.call(from));
};
var JumpToEntry = (function () {
    function JumpToEntry(label, targetId, data) {
        if (data === void 0) { data = {}; }
        this.label = label;
        this.targetId = targetId;
        this.data = data;
    }
    return JumpToEntry;
}());
var JumpToManager = (function () {
    function JumpToManager(game, settings) {
        var _a, _b, _c;
        this.game = game;
        this.settings = settings;
        var entries = __spreadArray(__spreadArray([], ((_a = settings === null || settings === void 0 ? void 0 : settings.topEntries) !== null && _a !== void 0 ? _a : []), true), ((_b = settings === null || settings === void 0 ? void 0 : settings.playersEntries) !== null && _b !== void 0 ? _b : this.createEntries(Object.values(game.gamedatas.players))), true);
        this.createPlayerJumps(entries);
        var folded = (_c = settings === null || settings === void 0 ? void 0 : settings.defaultFolded) !== null && _c !== void 0 ? _c : false;
        if (settings === null || settings === void 0 ? void 0 : settings.localStorageFoldedKey) {
            var localStorageValue = localStorage.getItem(settings.localStorageFoldedKey);
            if (localStorageValue) {
                folded = localStorageValue == 'true';
            }
        }
        document.getElementById('bga-jump-to_controls').classList.toggle('folded', folded);
    }
    JumpToManager.prototype.createPlayerJumps = function (entries) {
        var _this = this;
        var _a, _b, _c, _d;
        document.getElementById("game_play_area_wrap").insertAdjacentHTML('afterend', "\n        <div id=\"bga-jump-to_controls\">        \n            <div id=\"bga-jump-to_toggle\" class=\"bga-jump-to_link ".concat((_b = (_a = this.settings) === null || _a === void 0 ? void 0 : _a.entryClasses) !== null && _b !== void 0 ? _b : '', " toggle\" style=\"--color: ").concat((_d = (_c = this.settings) === null || _c === void 0 ? void 0 : _c.toggleColor) !== null && _d !== void 0 ? _d : 'black', "\">\n                \u21D4\n            </div>\n        </div>"));
        document.getElementById("bga-jump-to_toggle").addEventListener('click', function () { return _this.jumpToggle(); });
        entries.forEach(function (entry) {
            var _a, _b, _c, _d, _e, _f, _g, _h, _j;
            var html = "<div id=\"bga-jump-to_".concat(entry.targetId, "\" class=\"bga-jump-to_link ").concat((_b = (_a = _this.settings) === null || _a === void 0 ? void 0 : _a.entryClasses) !== null && _b !== void 0 ? _b : '', "\">");
            if ((_d = (_c = _this.settings) === null || _c === void 0 ? void 0 : _c.showEye) !== null && _d !== void 0 ? _d : true) {
                html += "<div class=\"eye\"></div>";
            }
            if (((_f = (_e = _this.settings) === null || _e === void 0 ? void 0 : _e.showAvatar) !== null && _f !== void 0 ? _f : true) && ((_g = entry.data) === null || _g === void 0 ? void 0 : _g.id)) {
                var cssUrl = (_h = entry.data) === null || _h === void 0 ? void 0 : _h.avatarUrl;
                if (!cssUrl) {
                    var img = document.getElementById("avatar_".concat(entry.data.id));
                    var url = img === null || img === void 0 ? void 0 : img.src;
                    if (url) {
                        cssUrl = "url('".concat(url, "')");
                    }
                }
                if (cssUrl) {
                    html += "<div class=\"bga-jump-to_avatar\" style=\"--avatar-url: ".concat(cssUrl, ";\"></div>");
                }
            }
            html += "\n                <span class=\"bga-jump-to_label\">".concat(entry.label, "</span>\n            </div>");
            document.getElementById("bga-jump-to_controls").insertAdjacentHTML('beforeend', html);
            var entryDiv = document.getElementById("bga-jump-to_".concat(entry.targetId));
            Object.getOwnPropertyNames((_j = entry.data) !== null && _j !== void 0 ? _j : []).forEach(function (key) {
                entryDiv.dataset[key] = entry.data[key];
                entryDiv.style.setProperty("--".concat(key), entry.data[key]);
            });
            entryDiv.addEventListener('click', function () { return _this.jumpTo(entry.targetId); });
        });
        var jumpDiv = document.getElementById("bga-jump-to_controls");
        jumpDiv.style.marginTop = "-".concat(Math.round(jumpDiv.getBoundingClientRect().height / 2), "px");
    };
    JumpToManager.prototype.jumpToggle = function () {
        var _a;
        var jumpControls = document.getElementById('bga-jump-to_controls');
        jumpControls.classList.toggle('folded');
        if ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.localStorageFoldedKey) {
            localStorage.setItem(this.settings.localStorageFoldedKey, jumpControls.classList.contains('folded').toString());
        }
    };
    JumpToManager.prototype.jumpTo = function (targetId) {
        document.getElementById(targetId).scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
    };
    JumpToManager.prototype.getOrderedPlayers = function (unorderedPlayers) {
        var _this = this;
        var players = unorderedPlayers.sort(function (a, b) { return Number(a.playerNo) - Number(b.playerNo); });
        var playerIndex = players.findIndex(function (player) { return Number(player.id) === Number(_this.game.player_id); });
        var orderedPlayers = playerIndex > 0 ? __spreadArray(__spreadArray([], players.slice(playerIndex), true), players.slice(0, playerIndex), true) : players;
        return orderedPlayers;
    };
    JumpToManager.prototype.createEntries = function (players) {
        var orderedPlayers = this.getOrderedPlayers(players);
        return orderedPlayers.map(function (player) { return new JumpToEntry(player.name, "player-table-".concat(player.id), {
            'color': '#' + player.color,
            'colorback': player.color_back ? '#' + player.color_back : null,
            'id': player.id,
        }); });
    };
    return JumpToManager;
}());
var BgaAnimation = (function () {
    function BgaAnimation(animationFunction, settings) {
        this.animationFunction = animationFunction;
        this.settings = settings;
        this.played = null;
        this.result = null;
        this.playWhenNoAnimation = false;
    }
    return BgaAnimation;
}());
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
function attachWithAnimation(animationManager, animation) {
    var _a;
    var settings = animation.settings;
    var element = settings.animation.settings.element;
    var fromRect = element.getBoundingClientRect();
    settings.animation.settings.fromRect = fromRect;
    settings.attachElement.appendChild(element);
    (_a = settings.afterAttach) === null || _a === void 0 ? void 0 : _a.call(settings, element, settings.attachElement);
    return animationManager.play(settings.animation);
}
var BgaAttachWithAnimation = (function (_super) {
    __extends(BgaAttachWithAnimation, _super);
    function BgaAttachWithAnimation(settings) {
        var _this = _super.call(this, attachWithAnimation, settings) || this;
        _this.playWhenNoAnimation = true;
        return _this;
    }
    return BgaAttachWithAnimation;
}(BgaAnimation));
function cumulatedAnimations(animationManager, animation) {
    return animationManager.playSequence(animation.settings.animations);
}
var BgaCumulatedAnimation = (function (_super) {
    __extends(BgaCumulatedAnimation, _super);
    function BgaCumulatedAnimation(settings) {
        var _this = _super.call(this, cumulatedAnimations, settings) || this;
        _this.playWhenNoAnimation = true;
        return _this;
    }
    return BgaCumulatedAnimation;
}(BgaAnimation));
function slideAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d;
        var settings = animation.settings;
        var element = settings.element;
        var _e = getDeltaCoordinates(element, settings), x = _e.x, y = _e.y;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        element.style.zIndex = "".concat((_b = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _b !== void 0 ? _b : 10);
        element.style.transition = null;
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_c = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _c !== void 0 ? _c : 0, "deg)");
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionCancel);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms linear");
        element.offsetHeight;
        element.style.transform = (_d = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _d !== void 0 ? _d : null;
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaSlideAnimation = (function (_super) {
    __extends(BgaSlideAnimation, _super);
    function BgaSlideAnimation(settings) {
        return _super.call(this, slideAnimation, settings) || this;
    }
    return BgaSlideAnimation;
}(BgaAnimation));
function slideToAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d;
        var settings = animation.settings;
        var element = settings.element;
        var _e = getDeltaCoordinates(element, settings), x = _e.x, y = _e.y;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        element.style.zIndex = "".concat((_b = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _b !== void 0 ? _b : 10);
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionEnd);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms linear");
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_c = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _c !== void 0 ? _c : 0, "deg) scale(").concat((_d = settings.scale) !== null && _d !== void 0 ? _d : 1, ")");
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaSlideToAnimation = (function (_super) {
    __extends(BgaSlideToAnimation, _super);
    function BgaSlideToAnimation(settings) {
        return _super.call(this, slideToAnimation, settings) || this;
    }
    return BgaSlideToAnimation;
}(BgaAnimation));
function shouldAnimate(settings) {
    var _a;
    return document.visibilityState !== 'hidden' && !((_a = settings === null || settings === void 0 ? void 0 : settings.game) === null || _a === void 0 ? void 0 : _a.instantaneousMode);
}
function getDeltaCoordinates(element, settings) {
    var _a;
    if (!settings.fromDelta && !settings.fromRect && !settings.fromElement) {
        throw new Error("[bga-animation] fromDelta, fromRect or fromElement need to be set");
    }
    var x = 0;
    var y = 0;
    if (settings.fromDelta) {
        x = settings.fromDelta.x;
        y = settings.fromDelta.y;
    }
    else {
        var originBR = (_a = settings.fromRect) !== null && _a !== void 0 ? _a : settings.fromElement.getBoundingClientRect();
        var originalTransform = element.style.transform;
        element.style.transform = '';
        var destinationBR = element.getBoundingClientRect();
        element.style.transform = originalTransform;
        x = (destinationBR.left + destinationBR.right) / 2 - (originBR.left + originBR.right) / 2;
        y = (destinationBR.top + destinationBR.bottom) / 2 - (originBR.top + originBR.bottom) / 2;
    }
    if (settings.scale) {
        x /= settings.scale;
        y /= settings.scale;
    }
    return { x: x, y: y };
}
function logAnimation(animationManager, animation) {
    var settings = animation.settings;
    var element = settings.element;
    if (element) {
        console.log(animation, settings, element, element.getBoundingClientRect(), element.style.transform);
    }
    else {
        console.log(animation, settings);
    }
    return Promise.resolve(false);
}
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var AnimationManager = (function () {
    function AnimationManager(game, settings) {
        this.game = game;
        this.settings = settings;
        this.zoomManager = settings === null || settings === void 0 ? void 0 : settings.zoomManager;
        if (!game) {
            throw new Error('You must set your game as the first parameter of AnimationManager');
        }
    }
    AnimationManager.prototype.getZoomManager = function () {
        return this.zoomManager;
    };
    AnimationManager.prototype.setZoomManager = function (zoomManager) {
        this.zoomManager = zoomManager;
    };
    AnimationManager.prototype.getSettings = function () {
        return this.settings;
    };
    AnimationManager.prototype.animationsActive = function () {
        return document.visibilityState !== 'hidden' && !this.game.instantaneousMode;
    };
    AnimationManager.prototype.play = function (animation) {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l;
        return __awaiter(this, void 0, void 0, function () {
            var settings, _m;
            return __generator(this, function (_o) {
                switch (_o.label) {
                    case 0:
                        animation.played = animation.playWhenNoAnimation || this.animationsActive();
                        if (!animation.played) return [3, 2];
                        settings = animation.settings;
                        (_a = settings.animationStart) === null || _a === void 0 ? void 0 : _a.call(settings, animation);
                        (_b = settings.element) === null || _b === void 0 ? void 0 : _b.classList.add((_c = settings.animationClass) !== null && _c !== void 0 ? _c : 'bga-animations_animated');
                        animation.settings = __assign(__assign({}, animation.settings), { duration: (_e = (_d = this.settings) === null || _d === void 0 ? void 0 : _d.duration) !== null && _e !== void 0 ? _e : 500, scale: (_g = (_f = this.zoomManager) === null || _f === void 0 ? void 0 : _f.zoom) !== null && _g !== void 0 ? _g : undefined });
                        _m = animation;
                        return [4, animation.animationFunction(this, animation)];
                    case 1:
                        _m.result = _o.sent();
                        (_j = (_h = animation.settings).animationEnd) === null || _j === void 0 ? void 0 : _j.call(_h, animation);
                        (_k = settings.element) === null || _k === void 0 ? void 0 : _k.classList.remove((_l = settings.animationClass) !== null && _l !== void 0 ? _l : 'bga-animations_animated');
                        return [3, 3];
                    case 2: return [2, Promise.resolve(animation)];
                    case 3: return [2];
                }
            });
        });
    };
    AnimationManager.prototype.playParallel = function (animations) {
        return __awaiter(this, void 0, void 0, function () {
            var _this = this;
            return __generator(this, function (_a) {
                return [2, Promise.all(animations.map(function (animation) { return _this.play(animation); }))];
            });
        });
    };
    AnimationManager.prototype.playSequence = function (animations) {
        return __awaiter(this, void 0, void 0, function () {
            var result, others;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!animations.length) return [3, 3];
                        return [4, this.play(animations[0])];
                    case 1:
                        result = _a.sent();
                        return [4, this.playSequence(animations.slice(1))];
                    case 2:
                        others = _a.sent();
                        return [2, __spreadArray([result], others, true)];
                    case 3: return [2, Promise.resolve([])];
                }
            });
        });
    };
    AnimationManager.prototype.playWithDelay = function (animations, delay) {
        return __awaiter(this, void 0, void 0, function () {
            var promise;
            var _this = this;
            return __generator(this, function (_a) {
                promise = new Promise(function (success) {
                    var promises = [];
                    var _loop_1 = function (i) {
                        setTimeout(function () {
                            promises.push(_this.play(animations[i]));
                            if (i == animations.length - 1) {
                                Promise.all(promises).then(function (result) {
                                    success(result);
                                });
                            }
                        }, i * delay);
                    };
                    for (var i = 0; i < animations.length; i++) {
                        _loop_1(i);
                    }
                });
                return [2, promise];
            });
        });
    };
    AnimationManager.prototype.attachWithAnimation = function (animation, attachElement) {
        var attachWithAnimation = new BgaAttachWithAnimation({
            animation: animation,
            attachElement: attachElement
        });
        return this.play(attachWithAnimation);
    };
    return AnimationManager;
}());
var CardStock = (function () {
    function CardStock(manager, element, settings) {
        this.manager = manager;
        this.element = element;
        this.settings = settings;
        this.cards = [];
        this.selectedCards = [];
        this.selectionMode = 'none';
        manager.addStock(this);
        element === null || element === void 0 ? void 0 : element.classList.add('card-stock');
        this.bindClick();
        this.sort = settings === null || settings === void 0 ? void 0 : settings.sort;
    }
    CardStock.prototype.getCards = function () {
        return this.cards.slice();
    };
    CardStock.prototype.isEmpty = function () {
        return !this.cards.length;
    };
    CardStock.prototype.getSelection = function () {
        return this.selectedCards.slice();
    };
    CardStock.prototype.isSelected = function (card) {
        var _this = this;
        return this.selectedCards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
    };
    CardStock.prototype.contains = function (card) {
        var _this = this;
        return this.cards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
    };
    CardStock.prototype.getCardElement = function (card) {
        return this.manager.getCardElement(card);
    };
    CardStock.prototype.canAddCard = function (card, settings) {
        return !this.contains(card);
    };
    CardStock.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a, _b, _c;
        if (!this.canAddCard(card, settings)) {
            return Promise.resolve(false);
        }
        var promise;
        var originStock = this.manager.getCardStock(card);
        var index = this.getNewCardIndex(card);
        var settingsWithIndex = __assign({ index: index }, (settings !== null && settings !== void 0 ? settings : {}));
        var updateInformations = (_a = settingsWithIndex.updateInformations) !== null && _a !== void 0 ? _a : true;
        if (originStock === null || originStock === void 0 ? void 0 : originStock.contains(card)) {
            var element = this.getCardElement(card);
            promise = this.moveFromOtherStock(card, element, __assign(__assign({}, animation), { fromStock: originStock }), settingsWithIndex);
            if (!updateInformations) {
                element.dataset.side = ((_b = settingsWithIndex === null || settingsWithIndex === void 0 ? void 0 : settingsWithIndex.visible) !== null && _b !== void 0 ? _b : this.manager.isCardVisible(card)) ? 'front' : 'back';
            }
        }
        else if ((animation === null || animation === void 0 ? void 0 : animation.fromStock) && animation.fromStock.contains(card)) {
            var element = this.getCardElement(card);
            promise = this.moveFromOtherStock(card, element, animation, settingsWithIndex);
        }
        else {
            var element = this.manager.createCardElement(card, ((_c = settingsWithIndex === null || settingsWithIndex === void 0 ? void 0 : settingsWithIndex.visible) !== null && _c !== void 0 ? _c : this.manager.isCardVisible(card)));
            promise = this.moveFromElement(card, element, animation, settingsWithIndex);
        }
        if (settingsWithIndex.index !== null && settingsWithIndex.index !== undefined) {
            this.cards.splice(index, 0, card);
        }
        else {
            this.cards.push(card);
        }
        if (updateInformations) {
            this.manager.updateCardInformations(card);
        }
        if (!promise) {
            console.warn("CardStock.addCard didn't return a Promise");
            promise = Promise.resolve(false);
        }
        if (this.selectionMode !== 'none') {
            promise.then(function () { var _a; return _this.setSelectableCard(card, (_a = settingsWithIndex.selectable) !== null && _a !== void 0 ? _a : true); });
        }
        return promise;
    };
    CardStock.prototype.getNewCardIndex = function (card) {
        if (this.sort) {
            var otherCards = this.getCards();
            for (var i = 0; i < otherCards.length; i++) {
                var otherCard = otherCards[i];
                if (this.sort(card, otherCard) < 0) {
                    return i;
                }
            }
            return otherCards.length;
        }
        else {
            return undefined;
        }
    };
    CardStock.prototype.addCardElementToParent = function (cardElement, settings) {
        var _a;
        var parent = (_a = settings === null || settings === void 0 ? void 0 : settings.forceToElement) !== null && _a !== void 0 ? _a : this.element;
        if ((settings === null || settings === void 0 ? void 0 : settings.index) === null || (settings === null || settings === void 0 ? void 0 : settings.index) === undefined || !parent.children.length || (settings === null || settings === void 0 ? void 0 : settings.index) >= parent.children.length) {
            parent.appendChild(cardElement);
        }
        else {
            parent.insertBefore(cardElement, parent.children[settings.index]);
        }
    };
    CardStock.prototype.moveFromOtherStock = function (card, cardElement, animation, settings) {
        var promise;
        var element = animation.fromStock.contains(card) ? this.manager.getCardElement(card) : animation.fromStock.element;
        var fromRect = element.getBoundingClientRect();
        this.addCardElementToParent(cardElement, settings);
        this.removeSelectionClassesFromElement(cardElement);
        promise = this.animationFromElement(cardElement, fromRect, {
            originalSide: animation.originalSide,
            rotationDelta: animation.rotationDelta,
            animation: animation.animation,
        });
        if (animation.fromStock && animation.fromStock != this) {
            animation.fromStock.removeCard(card);
        }
        if (!promise) {
            console.warn("CardStock.moveFromOtherStock didn't return a Promise");
            promise = Promise.resolve(false);
        }
        return promise;
    };
    CardStock.prototype.moveFromElement = function (card, cardElement, animation, settings) {
        var promise;
        this.addCardElementToParent(cardElement, settings);
        if (animation) {
            if (animation.fromStock) {
                promise = this.animationFromElement(cardElement, animation.fromStock.element.getBoundingClientRect(), {
                    originalSide: animation.originalSide,
                    rotationDelta: animation.rotationDelta,
                    animation: animation.animation,
                });
                animation.fromStock.removeCard(card);
            }
            else if (animation.fromElement) {
                promise = this.animationFromElement(cardElement, animation.fromElement.getBoundingClientRect(), {
                    originalSide: animation.originalSide,
                    rotationDelta: animation.rotationDelta,
                    animation: animation.animation,
                });
            }
        }
        else {
            promise = Promise.resolve(false);
        }
        if (!promise) {
            console.warn("CardStock.moveFromElement didn't return a Promise");
            promise = Promise.resolve(false);
        }
        return promise;
    };
    CardStock.prototype.addCards = function (cards, animation, settings, shift) {
        if (shift === void 0) { shift = false; }
        return __awaiter(this, void 0, void 0, function () {
            var promises, result, others, _loop_2, i, results;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!this.manager.animationsActive()) {
                            shift = false;
                        }
                        promises = [];
                        if (!(shift === true)) return [3, 4];
                        if (!cards.length) return [3, 3];
                        return [4, this.addCard(cards[0], animation, settings)];
                    case 1:
                        result = _a.sent();
                        return [4, this.addCards(cards.slice(1), animation, settings, shift)];
                    case 2:
                        others = _a.sent();
                        return [2, result || others];
                    case 3: return [3, 5];
                    case 4:
                        if (typeof shift === 'number') {
                            _loop_2 = function (i) {
                                setTimeout(function () { return promises.push(_this.addCard(cards[i], animation, settings)); }, i * shift);
                            };
                            for (i = 0; i < cards.length; i++) {
                                _loop_2(i);
                            }
                        }
                        else {
                            promises = cards.map(function (card) { return _this.addCard(card, animation, settings); });
                        }
                        _a.label = 5;
                    case 5: return [4, Promise.all(promises)];
                    case 6:
                        results = _a.sent();
                        return [2, results.some(function (result) { return result; })];
                }
            });
        });
    };
    CardStock.prototype.removeCard = function (card, settings) {
        if (this.contains(card) && this.element.contains(this.getCardElement(card))) {
            this.manager.removeCard(card, settings);
        }
        this.cardRemoved(card, settings);
    };
    CardStock.prototype.cardRemoved = function (card, settings) {
        var _this = this;
        var index = this.cards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
        if (index !== -1) {
            this.cards.splice(index, 1);
        }
        if (this.selectedCards.find(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); })) {
            this.unselectCard(card);
        }
    };
    CardStock.prototype.removeCards = function (cards, settings) {
        var _this = this;
        cards.forEach(function (card) { return _this.removeCard(card, settings); });
    };
    CardStock.prototype.removeAll = function (settings) {
        var _this = this;
        var cards = this.getCards();
        cards.forEach(function (card) { return _this.removeCard(card, settings); });
    };
    CardStock.prototype.setSelectionMode = function (selectionMode, selectableCards) {
        var _this = this;
        if (selectionMode !== this.selectionMode) {
            this.unselectAll(true);
        }
        this.cards.forEach(function (card) { return _this.setSelectableCard(card, selectionMode != 'none'); });
        this.element.classList.toggle('bga-cards_selectable-stock', selectionMode != 'none');
        this.selectionMode = selectionMode;
        if (selectionMode === 'none') {
            this.getCards().forEach(function (card) { return _this.removeSelectionClasses(card); });
        }
        else {
            this.setSelectableCards(selectableCards !== null && selectableCards !== void 0 ? selectableCards : this.getCards());
        }
    };
    CardStock.prototype.setSelectableCard = function (card, selectable) {
        if (this.selectionMode === 'none') {
            return;
        }
        var element = this.getCardElement(card);
        var selectableCardsClass = this.getSelectableCardClass();
        var unselectableCardsClass = this.getUnselectableCardClass();
        if (selectableCardsClass) {
            element.classList.toggle(selectableCardsClass, selectable);
        }
        if (unselectableCardsClass) {
            element.classList.toggle(unselectableCardsClass, !selectable);
        }
        if (!selectable && this.isSelected(card)) {
            this.unselectCard(card, true);
        }
    };
    CardStock.prototype.setSelectableCards = function (selectableCards) {
        var _this = this;
        if (this.selectionMode === 'none') {
            return;
        }
        var selectableCardsIds = (selectableCards !== null && selectableCards !== void 0 ? selectableCards : this.getCards()).map(function (card) { return _this.manager.getId(card); });
        this.cards.forEach(function (card) {
            return _this.setSelectableCard(card, selectableCardsIds.includes(_this.manager.getId(card)));
        });
    };
    CardStock.prototype.selectCard = function (card, silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        if (this.selectionMode == 'none') {
            return;
        }
        var element = this.getCardElement(card);
        var selectableCardsClass = this.getSelectableCardClass();
        if (!element.classList.contains(selectableCardsClass)) {
            return;
        }
        if (this.selectionMode === 'single') {
            this.cards.filter(function (c) { return _this.manager.getId(c) != _this.manager.getId(card); }).forEach(function (c) { return _this.unselectCard(c, true); });
        }
        var selectedCardsClass = this.getSelectedCardClass();
        element.classList.add(selectedCardsClass);
        this.selectedCards.push(card);
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), card);
        }
    };
    CardStock.prototype.unselectCard = function (card, silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        var element = this.getCardElement(card);
        var selectedCardsClass = this.getSelectedCardClass();
        element.classList.remove(selectedCardsClass);
        var index = this.selectedCards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
        if (index !== -1) {
            this.selectedCards.splice(index, 1);
        }
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), card);
        }
    };
    CardStock.prototype.selectAll = function (silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        if (this.selectionMode == 'none') {
            return;
        }
        this.cards.forEach(function (c) { return _this.selectCard(c, true); });
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), null);
        }
    };
    CardStock.prototype.unselectAll = function (silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        var cards = this.getCards();
        cards.forEach(function (c) { return _this.unselectCard(c, true); });
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), null);
        }
    };
    CardStock.prototype.bindClick = function () {
        var _this = this;
        var _a;
        (_a = this.element) === null || _a === void 0 ? void 0 : _a.addEventListener('click', function (event) {
            var cardDiv = event.target.closest('.card');
            if (!cardDiv) {
                return;
            }
            var card = _this.cards.find(function (c) { return _this.manager.getId(c) == cardDiv.id; });
            if (!card) {
                return;
            }
            _this.cardClick(card);
        });
    };
    CardStock.prototype.cardClick = function (card) {
        var _this = this;
        var _a;
        if (this.selectionMode != 'none') {
            var alreadySelected = this.selectedCards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
            if (alreadySelected) {
                this.unselectCard(card);
            }
            else {
                this.selectCard(card);
            }
        }
        (_a = this.onCardClick) === null || _a === void 0 ? void 0 : _a.call(this, card);
    };
    CardStock.prototype.animationFromElement = function (element, fromRect, settings) {
        var _a;
        return __awaiter(this, void 0, void 0, function () {
            var side, cardSides_1, animation, result;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        side = element.dataset.side;
                        if (settings.originalSide && settings.originalSide != side) {
                            cardSides_1 = element.getElementsByClassName('card-sides')[0];
                            cardSides_1.style.transition = 'none';
                            element.dataset.side = settings.originalSide;
                            setTimeout(function () {
                                cardSides_1.style.transition = null;
                                element.dataset.side = side;
                            });
                        }
                        animation = settings.animation;
                        if (animation) {
                            animation.settings.element = element;
                            animation.settings.fromRect = fromRect;
                        }
                        else {
                            animation = new BgaSlideAnimation({ element: element, fromRect: fromRect });
                        }
                        return [4, this.manager.animationManager.play(animation)];
                    case 1:
                        result = _b.sent();
                        return [2, (_a = result === null || result === void 0 ? void 0 : result.played) !== null && _a !== void 0 ? _a : false];
                }
            });
        });
    };
    CardStock.prototype.setCardVisible = function (card, visible, settings) {
        this.manager.setCardVisible(card, visible, settings);
    };
    CardStock.prototype.flipCard = function (card, settings) {
        this.manager.flipCard(card, settings);
    };
    CardStock.prototype.getSelectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectableCardClass) === undefined ? this.manager.getSelectableCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectableCardClass;
    };
    CardStock.prototype.getUnselectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.unselectableCardClass) === undefined ? this.manager.getUnselectableCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.unselectableCardClass;
    };
    CardStock.prototype.getSelectedCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectedCardClass) === undefined ? this.manager.getSelectedCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectedCardClass;
    };
    CardStock.prototype.removeSelectionClasses = function (card) {
        this.removeSelectionClassesFromElement(this.getCardElement(card));
    };
    CardStock.prototype.removeSelectionClassesFromElement = function (cardElement) {
        var selectableCardsClass = this.getSelectableCardClass();
        var unselectableCardsClass = this.getUnselectableCardClass();
        var selectedCardsClass = this.getSelectedCardClass();
        cardElement.classList.remove(selectableCardsClass, unselectableCardsClass, selectedCardsClass);
    };
    return CardStock;
}());
var SlideAndBackAnimation = (function (_super) {
    __extends(SlideAndBackAnimation, _super);
    function SlideAndBackAnimation(manager, element, tempElement) {
        var _this = this;
        var distance = (manager.getCardWidth() + manager.getCardHeight()) / 2;
        var angle = Math.random() * Math.PI * 2;
        var fromDelta = {
            x: distance * Math.cos(angle),
            y: distance * Math.sin(angle),
        };
        _this = _super.call(this, {
            animations: [
                new BgaSlideToAnimation({ element: element, fromDelta: fromDelta, duration: 250 }),
                new BgaSlideAnimation({ element: element, fromDelta: fromDelta, duration: 250, animationEnd: tempElement ? (function () { return element.remove(); }) : undefined }),
            ]
        }) || this;
        return _this;
    }
    return SlideAndBackAnimation;
}(BgaCumulatedAnimation));
var Deck = (function (_super) {
    __extends(Deck, _super);
    function Deck(manager, element, settings) {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k;
        var _this = _super.call(this, manager, element) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('deck');
        var cardWidth = _this.manager.getCardWidth();
        var cardHeight = _this.manager.getCardHeight();
        if (cardWidth && cardHeight) {
            _this.element.style.setProperty('--width', "".concat(cardWidth, "px"));
            _this.element.style.setProperty('--height', "".concat(cardHeight, "px"));
        }
        else {
            throw new Error("You need to set cardWidth and cardHeight in the card manager to use Deck.");
        }
        _this.thicknesses = (_a = settings.thicknesses) !== null && _a !== void 0 ? _a : [0, 2, 5, 10, 20, 30];
        _this.setCardNumber((_b = settings.cardNumber) !== null && _b !== void 0 ? _b : 52);
        _this.autoUpdateCardNumber = (_c = settings.autoUpdateCardNumber) !== null && _c !== void 0 ? _c : true;
        _this.autoRemovePreviousCards = (_d = settings.autoRemovePreviousCards) !== null && _d !== void 0 ? _d : true;
        var shadowDirection = (_e = settings.shadowDirection) !== null && _e !== void 0 ? _e : 'bottom-right';
        var shadowDirectionSplit = shadowDirection.split('-');
        var xShadowShift = shadowDirectionSplit.includes('right') ? 1 : (shadowDirectionSplit.includes('left') ? -1 : 0);
        var yShadowShift = shadowDirectionSplit.includes('bottom') ? 1 : (shadowDirectionSplit.includes('top') ? -1 : 0);
        _this.element.style.setProperty('--xShadowShift', '' + xShadowShift);
        _this.element.style.setProperty('--yShadowShift', '' + yShadowShift);
        if (settings.topCard) {
            _this.addCard(settings.topCard, undefined);
        }
        else if (settings.cardNumber > 0) {
            console.warn("Deck is defined with ".concat(settings.cardNumber, " cards but no top card !"));
        }
        if (settings.counter && ((_f = settings.counter.show) !== null && _f !== void 0 ? _f : true)) {
            if (settings.cardNumber === null || settings.cardNumber === undefined) {
                throw new Error("You need to set cardNumber if you want to show the counter");
            }
            else {
                _this.createCounter((_g = settings.counter.position) !== null && _g !== void 0 ? _g : 'bottom', (_h = settings.counter.extraClasses) !== null && _h !== void 0 ? _h : 'round', settings.counter.counterId);
                if ((_j = settings.counter) === null || _j === void 0 ? void 0 : _j.hideWhenEmpty) {
                    _this.element.querySelector('.bga-cards_deck-counter').classList.add('hide-when-empty');
                }
            }
        }
        _this.setCardNumber((_k = settings.cardNumber) !== null && _k !== void 0 ? _k : 52);
        return _this;
    }
    Deck.prototype.createCounter = function (counterPosition, extraClasses, counterId) {
        var left = counterPosition.includes('right') ? 100 : (counterPosition.includes('left') ? 0 : 50);
        var top = counterPosition.includes('bottom') ? 100 : (counterPosition.includes('top') ? 0 : 50);
        this.element.style.setProperty('--bga-cards-deck-left', "".concat(left, "%"));
        this.element.style.setProperty('--bga-cards-deck-top', "".concat(top, "%"));
        this.element.insertAdjacentHTML('beforeend', "\n            <div ".concat(counterId ? "id=\"".concat(counterId, "\"") : '', " class=\"bga-cards_deck-counter ").concat(extraClasses, "\"></div>\n        "));
    };
    Deck.prototype.getCardNumber = function () {
        return this.cardNumber;
    };
    Deck.prototype.setCardNumber = function (cardNumber, topCard) {
        var _this = this;
        if (topCard === void 0) { topCard = null; }
        if (topCard) {
            this.addCard(topCard);
        }
        this.cardNumber = cardNumber;
        this.element.dataset.empty = (this.cardNumber == 0).toString();
        var thickness = 0;
        this.thicknesses.forEach(function (threshold, index) {
            if (_this.cardNumber >= threshold) {
                thickness = index;
            }
        });
        this.element.style.setProperty('--thickness', "".concat(thickness, "px"));
        var counterDiv = this.element.querySelector('.bga-cards_deck-counter');
        if (counterDiv) {
            counterDiv.innerHTML = "".concat(cardNumber);
        }
    };
    Deck.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a, _b;
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.autoUpdateCardNumber) !== null && _a !== void 0 ? _a : this.autoUpdateCardNumber) {
            this.setCardNumber(this.cardNumber + 1);
        }
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        if ((_b = settings === null || settings === void 0 ? void 0 : settings.autoRemovePreviousCards) !== null && _b !== void 0 ? _b : this.autoRemovePreviousCards) {
            promise.then(function () {
                var previousCards = _this.getCards().slice(0, -1);
                _this.removeCards(previousCards, { autoUpdateCardNumber: false });
            });
        }
        return promise;
    };
    Deck.prototype.cardRemoved = function (card, settings) {
        var _a;
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.autoUpdateCardNumber) !== null && _a !== void 0 ? _a : this.autoUpdateCardNumber) {
            this.setCardNumber(this.cardNumber - 1);
        }
        _super.prototype.cardRemoved.call(this, card, settings);
    };
    Deck.prototype.getTopCard = function () {
        var cards = this.getCards();
        return cards.length ? cards[cards.length - 1] : null;
    };
    Deck.prototype.shuffle = function (animatedCardsMax, fakeCardSetter) {
        if (animatedCardsMax === void 0) { animatedCardsMax = 10; }
        return __awaiter(this, void 0, void 0, function () {
            var animatedCards, elements, i, newCard, newElement;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!this.manager.animationsActive()) {
                            return [2, Promise.resolve(false)];
                        }
                        animatedCards = Math.min(10, animatedCardsMax, this.getCardNumber());
                        if (!(animatedCards > 1)) return [3, 2];
                        elements = [this.getCardElement(this.getTopCard())];
                        for (i = elements.length; i <= animatedCards; i++) {
                            newCard = {};
                            if (fakeCardSetter) {
                                fakeCardSetter(newCard, i);
                            }
                            else {
                                newCard.id = -100000 + i;
                            }
                            newElement = this.manager.createCardElement(newCard, false);
                            newElement.dataset.tempCardForShuffleAnimation = 'true';
                            this.element.prepend(newElement);
                            elements.push(newElement);
                        }
                        return [4, this.manager.animationManager.playWithDelay(elements.map(function (element) { return new SlideAndBackAnimation(_this.manager, element, element.dataset.tempCardForShuffleAnimation == 'true'); }), 50)];
                    case 1:
                        _a.sent();
                        return [2, true];
                    case 2: return [2, Promise.resolve(false)];
                }
            });
        });
    };
    return Deck;
}(CardStock));
var LineStock = (function (_super) {
    __extends(LineStock, _super);
    function LineStock(manager, element, settings) {
        var _a, _b, _c, _d;
        var _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('line-stock');
        element.dataset.center = ((_a = settings === null || settings === void 0 ? void 0 : settings.center) !== null && _a !== void 0 ? _a : true).toString();
        element.style.setProperty('--wrap', (_b = settings === null || settings === void 0 ? void 0 : settings.wrap) !== null && _b !== void 0 ? _b : 'wrap');
        element.style.setProperty('--direction', (_c = settings === null || settings === void 0 ? void 0 : settings.direction) !== null && _c !== void 0 ? _c : 'row');
        element.style.setProperty('--gap', (_d = settings === null || settings === void 0 ? void 0 : settings.gap) !== null && _d !== void 0 ? _d : '8px');
        return _this;
    }
    return LineStock;
}(CardStock));
var SlotStock = (function (_super) {
    __extends(SlotStock, _super);
    function SlotStock(manager, element, settings) {
        var _a, _b;
        var _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        _this.slotsIds = [];
        _this.slots = [];
        element.classList.add('slot-stock');
        _this.mapCardToSlot = settings.mapCardToSlot;
        _this.slotsIds = (_a = settings.slotsIds) !== null && _a !== void 0 ? _a : [];
        _this.slotClasses = (_b = settings.slotClasses) !== null && _b !== void 0 ? _b : [];
        _this.slotsIds.forEach(function (slotId) {
            _this.createSlot(slotId);
        });
        return _this;
    }
    SlotStock.prototype.createSlot = function (slotId) {
        var _a;
        this.slots[slotId] = document.createElement("div");
        this.slots[slotId].dataset.slotId = slotId;
        this.element.appendChild(this.slots[slotId]);
        (_a = this.slots[slotId].classList).add.apply(_a, __spreadArray(['slot'], this.slotClasses, true));
    };
    SlotStock.prototype.addCard = function (card, animation, settings) {
        var _a, _b;
        var slotId = (_a = settings === null || settings === void 0 ? void 0 : settings.slot) !== null && _a !== void 0 ? _a : (_b = this.mapCardToSlot) === null || _b === void 0 ? void 0 : _b.call(this, card);
        if (slotId === undefined) {
            throw new Error("Impossible to add card to slot : no SlotId. Add slotId to settings or set mapCardToSlot to SlotCard constructor.");
        }
        if (!this.slots[slotId]) {
            throw new Error("Impossible to add card to slot \"".concat(slotId, "\" : slot \"").concat(slotId, "\" doesn't exists."));
        }
        var newSettings = __assign(__assign({}, settings), { forceToElement: this.slots[slotId] });
        return _super.prototype.addCard.call(this, card, animation, newSettings);
    };
    SlotStock.prototype.setSlotsIds = function (slotsIds) {
        var _this = this;
        if (slotsIds.length == this.slotsIds.length && slotsIds.every(function (slotId, index) { return _this.slotsIds[index] === slotId; })) {
            return;
        }
        this.removeAll();
        this.element.innerHTML = '';
        this.slotsIds = slotsIds !== null && slotsIds !== void 0 ? slotsIds : [];
        this.slotsIds.forEach(function (slotId) {
            _this.createSlot(slotId);
        });
    };
    SlotStock.prototype.canAddCard = function (card, settings) {
        var _a, _b;
        if (!this.contains(card)) {
            return true;
        }
        else {
            var currentCardSlot = this.getCardElement(card).closest('.slot').dataset.slotId;
            var slotId = (_a = settings === null || settings === void 0 ? void 0 : settings.slot) !== null && _a !== void 0 ? _a : (_b = this.mapCardToSlot) === null || _b === void 0 ? void 0 : _b.call(this, card);
            return currentCardSlot != slotId;
        }
    };
    SlotStock.prototype.swapCards = function (cards, settings) {
        var _this = this;
        if (!this.mapCardToSlot) {
            throw new Error('You need to define SlotStock.mapCardToSlot to use SlotStock.swapCards');
        }
        var promises = [];
        var elements = cards.map(function (card) { return _this.manager.getCardElement(card); });
        var elementsRects = elements.map(function (element) { return element.getBoundingClientRect(); });
        var cssPositions = elements.map(function (element) { return element.style.position; });
        elements.forEach(function (element) { return element.style.position = 'absolute'; });
        cards.forEach(function (card, index) {
            var _a, _b;
            var cardElement = elements[index];
            var promise;
            var slotId = (_a = _this.mapCardToSlot) === null || _a === void 0 ? void 0 : _a.call(_this, card);
            _this.slots[slotId].appendChild(cardElement);
            cardElement.style.position = cssPositions[index];
            var cardIndex = _this.cards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
            if (cardIndex !== -1) {
                _this.cards.splice(cardIndex, 1, card);
            }
            if ((_b = settings === null || settings === void 0 ? void 0 : settings.updateInformations) !== null && _b !== void 0 ? _b : true) {
                _this.manager.updateCardInformations(card);
            }
            _this.removeSelectionClassesFromElement(cardElement);
            promise = _this.animationFromElement(cardElement, elementsRects[index], {});
            if (!promise) {
                console.warn("CardStock.animationFromElement didn't return a Promise");
                promise = Promise.resolve(false);
            }
            promise.then(function () { var _a; return _this.setSelectableCard(card, (_a = settings === null || settings === void 0 ? void 0 : settings.selectable) !== null && _a !== void 0 ? _a : true); });
            promises.push(promise);
        });
        return Promise.all(promises);
    };
    return SlotStock;
}(LineStock));
var CardManager = (function () {
    function CardManager(game, settings) {
        var _a;
        this.game = game;
        this.settings = settings;
        this.stocks = [];
        this.animationManager = (_a = settings.animationManager) !== null && _a !== void 0 ? _a : new AnimationManager(game);
    }
    CardManager.prototype.animationsActive = function () {
        return this.animationManager.animationsActive();
    };
    CardManager.prototype.addStock = function (stock) {
        this.stocks.push(stock);
    };
    CardManager.prototype.getId = function (card) {
        var _a, _b, _c;
        return (_c = (_b = (_a = this.settings).getId) === null || _b === void 0 ? void 0 : _b.call(_a, card)) !== null && _c !== void 0 ? _c : "card-".concat(card.id);
    };
    CardManager.prototype.createCardElement = function (card, visible) {
        var _a, _b, _c, _d, _e, _f;
        if (visible === void 0) { visible = true; }
        var id = this.getId(card);
        var side = visible ? 'front' : 'back';
        if (this.getCardElement(card)) {
            throw new Error('This card already exists ' + JSON.stringify(card));
        }
        var element = document.createElement("div");
        element.id = id;
        element.dataset.side = '' + side;
        element.innerHTML = "\n            <div class=\"card-sides\">\n                <div id=\"".concat(id, "-front\" class=\"card-side front\">\n                </div>\n                <div id=\"").concat(id, "-back\" class=\"card-side back\">\n                </div>\n            </div>\n        ");
        element.classList.add('card');
        document.body.appendChild(element);
        (_b = (_a = this.settings).setupDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element);
        (_d = (_c = this.settings).setupFrontDiv) === null || _d === void 0 ? void 0 : _d.call(_c, card, element.getElementsByClassName('front')[0]);
        (_f = (_e = this.settings).setupBackDiv) === null || _f === void 0 ? void 0 : _f.call(_e, card, element.getElementsByClassName('back')[0]);
        document.body.removeChild(element);
        return element;
    };
    CardManager.prototype.getCardElement = function (card) {
        return document.getElementById(this.getId(card));
    };
    CardManager.prototype.removeCard = function (card, settings) {
        var _a;
        var id = this.getId(card);
        var div = document.getElementById(id);
        if (!div) {
            return false;
        }
        div.id = "deleted".concat(id);
        div.remove();
        (_a = this.getCardStock(card)) === null || _a === void 0 ? void 0 : _a.cardRemoved(card, settings);
        return true;
    };
    CardManager.prototype.getCardStock = function (card) {
        return this.stocks.find(function (stock) { return stock.contains(card); });
    };
    CardManager.prototype.isCardVisible = function (card) {
        var _a, _b, _c, _d;
        return (_c = (_b = (_a = this.settings).isCardVisible) === null || _b === void 0 ? void 0 : _b.call(_a, card)) !== null && _c !== void 0 ? _c : ((_d = card.type) !== null && _d !== void 0 ? _d : false);
    };
    CardManager.prototype.setCardVisible = function (card, visible, settings) {
        var _this = this;
        var _a, _b, _c, _d, _e, _f, _g, _h, _j;
        var element = this.getCardElement(card);
        if (!element) {
            return;
        }
        var isVisible = visible !== null && visible !== void 0 ? visible : this.isCardVisible(card);
        element.dataset.side = isVisible ? 'front' : 'back';
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.updateFront) !== null && _a !== void 0 ? _a : true) {
            var updateFrontDelay = (_b = settings === null || settings === void 0 ? void 0 : settings.updateFrontDelay) !== null && _b !== void 0 ? _b : 500;
            if (!isVisible && updateFrontDelay > 0 && this.animationsActive()) {
                setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupFrontDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element.getElementsByClassName('front')[0]); }, updateFrontDelay);
            }
            else {
                (_d = (_c = this.settings).setupFrontDiv) === null || _d === void 0 ? void 0 : _d.call(_c, card, element.getElementsByClassName('front')[0]);
            }
        }
        if ((_e = settings === null || settings === void 0 ? void 0 : settings.updateBack) !== null && _e !== void 0 ? _e : false) {
            var updateBackDelay = (_f = settings === null || settings === void 0 ? void 0 : settings.updateBackDelay) !== null && _f !== void 0 ? _f : 0;
            if (isVisible && updateBackDelay > 0 && this.animationsActive()) {
                setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupBackDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element.getElementsByClassName('back')[0]); }, updateBackDelay);
            }
            else {
                (_h = (_g = this.settings).setupBackDiv) === null || _h === void 0 ? void 0 : _h.call(_g, card, element.getElementsByClassName('back')[0]);
            }
        }
        if ((_j = settings === null || settings === void 0 ? void 0 : settings.updateData) !== null && _j !== void 0 ? _j : true) {
            var stock = this.getCardStock(card);
            var cards = stock.getCards();
            var cardIndex = cards.findIndex(function (c) { return _this.getId(c) === _this.getId(card); });
            if (cardIndex !== -1) {
                stock.cards.splice(cardIndex, 1, card);
            }
        }
    };
    CardManager.prototype.flipCard = function (card, settings) {
        var element = this.getCardElement(card);
        var currentlyVisible = element.dataset.side === 'front';
        this.setCardVisible(card, !currentlyVisible, settings);
    };
    CardManager.prototype.updateCardInformations = function (card, settings) {
        var newSettings = __assign(__assign({}, (settings !== null && settings !== void 0 ? settings : {})), { updateData: true });
        this.setCardVisible(card, undefined, newSettings);
    };
    CardManager.prototype.getCardWidth = function () {
        var _a;
        return (_a = this.settings) === null || _a === void 0 ? void 0 : _a.cardWidth;
    };
    CardManager.prototype.getCardHeight = function () {
        var _a;
        return (_a = this.settings) === null || _a === void 0 ? void 0 : _a.cardHeight;
    };
    CardManager.prototype.getSelectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectableCardClass) === undefined ? 'bga-cards_selectable-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectableCardClass;
    };
    CardManager.prototype.getUnselectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.unselectableCardClass) === undefined ? 'bga-cards_disabled-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.unselectableCardClass;
    };
    CardManager.prototype.getSelectedCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectedCardClass) === undefined ? 'bga-cards_selected-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectedCardClass;
    };
    return CardManager;
}());
function sortFunction() {
    var sortedFields = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        sortedFields[_i] = arguments[_i];
    }
    return function (a, b) {
        for (var i = 0; i < sortedFields.length; i++) {
            var direction = 1;
            var field = sortedFields[i];
            if (field[0] == '-') {
                direction = -1;
                field = field.substring(1);
            }
            else if (field[0] == '+') {
                field = field.substring(1);
            }
            var type = typeof a[field];
            if (type === 'string') {
                var compare = a[field].localeCompare(b[field]);
                if (compare !== 0) {
                    return compare;
                }
            }
            else if (type === 'number') {
                var compare = (a[field] - b[field]) * direction;
                if (compare !== 0) {
                    return compare * direction;
                }
            }
        }
        return 0;
    };
}
var isDebug = window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
var log = isDebug ? console.log.bind(window.console) : function () { };
var LOCAL_STORAGE_ZOOM_KEY = "wizards-grimoire-zoom";
var LOCAL_STORAGE_JUMP_TO_FOLDED_KEY = "wizards-grimoire-jump-to-folded";
var arrayRange = function (start, end) { return Array.from(Array(end - start + 1).keys()).map(function (x) { return x + start; }); };
var WizardsGrimoire = (function () {
    function WizardsGrimoire() {
        this.TOOLTIP_DELAY = document.body.classList.contains("touch-device") ? 1500 : undefined;
        this.playersTables = [];
    }
    WizardsGrimoire.prototype.setup = function (gamedatas) {
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
    };
    WizardsGrimoire.prototype.toggleOngoingSpell = function (value) {
        document.getElementById("table").classList.toggle("wg-ongoing-spell-".concat(value.name), value.active);
    };
    WizardsGrimoire.prototype.setupDebug = function (gamedatas) {
        var arrCardType = [];
        Object.keys(gamedatas.card_types).forEach(function (index) { return arrCardType.push(gamedatas.card_types[index]); });
        log("----------------");
        arrCardType
            .filter(function (x) { return x.debug !== "lightgreen"; })
            .sort(function (a, b) { return a.name.localeCompare(b.name); })
            .sort(function (a, b) { return a.icon.localeCompare(b.icon); })
            .forEach(function (card) {
            log(card.name, ",", card.icon, ",", card.debug);
        });
        log("----------------");
    };
    WizardsGrimoire.prototype.onEnteringState = function (stateName, args) {
        this.stateManager.onEnteringState(stateName, args);
    };
    WizardsGrimoire.prototype.onLeavingState = function (stateName) {
        this.stateManager.onLeavingState(stateName);
    };
    WizardsGrimoire.prototype.onUpdateActionButtons = function (stateName, args) {
        this.stateManager.onUpdateActionButtons(stateName, args);
    };
    WizardsGrimoire.prototype.addActionButtonDisabled = function (id, label, action) {
        this.addActionButton(id, label, action);
        this.disableButton(id);
    };
    WizardsGrimoire.prototype.addActionButtonClientCancel = function () {
        var _this = this;
        var handleCancel = function (evt) {
            dojo.stopEvent(evt);
            _this.restoreGameState();
        };
        this.addActionButtonGray("btnCancelAction", _("Cancel"), handleCancel);
    };
    WizardsGrimoire.prototype.addActionButtonPass = function () {
        var _this = this;
        var handlePass = function () {
            _this.takeAction("pass");
        };
        this.addActionButtonRed("btn_pass", _("Pass"), handlePass);
    };
    WizardsGrimoire.prototype.addActionButtonGray = function (id, label, action) {
        this.addActionButton(id, label, action, null, null, "gray");
    };
    WizardsGrimoire.prototype.addActionButtonRed = function (id, label, action) {
        this.addActionButton(id, label, action, null, null, "red");
    };
    WizardsGrimoire.prototype.createPlayerTables = function (gamedatas) {
        var _this = this;
        gamedatas.players_order.forEach(function (player_id) {
            var player = gamedatas.players[Number(player_id)];
            var table = new PlayerTable(_this, player);
            _this.playersTables.push(table);
        });
    };
    WizardsGrimoire.prototype.toggleButtonEnable = function (id, enabled, color) {
        if (color === void 0) { color = "blue"; }
        if (enabled) {
            this.enableButton(id, color);
        }
        else {
            this.disableButton(id);
        }
    };
    WizardsGrimoire.prototype.disableButton = function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.classList.remove("bgabutton_blue");
            el.classList.remove("bgabutton_red");
            el.classList.add("bgabutton_disabled");
        }
    };
    WizardsGrimoire.prototype.enableButton = function (id, color) {
        if (color === void 0) { color = "blue"; }
        var el = document.getElementById(id);
        if (el) {
            el.classList.add("bgabutton_".concat(color));
            el.classList.remove("bgabutton_disabled");
        }
    };
    WizardsGrimoire.prototype.getCardType = function (card) {
        return this.gamedatas.card_types[card.type];
    };
    WizardsGrimoire.prototype.getOpponentId = function () {
        return Number(this.gamedatas.opponent_id);
    };
    WizardsGrimoire.prototype.getPlayerId = function () {
        return Number(this.player_id);
    };
    WizardsGrimoire.prototype.getPlayerTable = function (playerId) {
        return this.playersTables.find(function (playerTable) { return playerTable.player_id === playerId; });
    };
    WizardsGrimoire.prototype.markCardAsSelected = function (card) {
        var div = this.spellsManager.getCardElement(card);
        div.classList.add("wg-selected");
    };
    WizardsGrimoire.prototype.restoreGameState = function () {
        log("restoreGameState");
        this.actionManager.reset();
        this.stateManager.restoreGameState();
        this.clearSelection();
        this.restoreServerGameState();
    };
    WizardsGrimoire.prototype.clearSelection = function () {
        log("clearSelection");
        this.tableCenter.spellPool.unselectAll();
        this.playersTables.forEach(function (table) {
            table.spell_repertoire.unselectAll();
        });
        document.querySelectorAll(".wg-selected").forEach(function (node) {
            node.classList.remove("wg-selected");
        });
        document.querySelectorAll(".wg-selectable").forEach(function (node) {
            node.classList.remove("wg-selectable");
        });
        document.querySelectorAll(".wg-deck-was-selected").forEach(function (node) {
            node.classList.remove("wg-deck-was-selected");
        });
    };
    WizardsGrimoire.prototype.setGamestateDescription = function (property) {
        if (property === void 0) { property = ""; }
        var originalState = this.gamedatas.gamestates[this.gamedatas.gamestate.id];
        this.gamedatas.gamestate.description = "".concat(originalState["description" + property]);
        this.gamedatas.gamestate.descriptionmyturn = "".concat(originalState["descriptionmyturn" + property]);
        this.updatePageTitle();
    };
    WizardsGrimoire.prototype.setTooltip = function (id, html) {
        this.addTooltipHtml(id, html, this.TOOLTIP_DELAY);
    };
    WizardsGrimoire.prototype.takeAction = function (action, data, onSuccess, onComplete) {
        data = data || {};
        data.lock = true;
        onSuccess = onSuccess !== null && onSuccess !== void 0 ? onSuccess : function (result) { };
        onComplete = onComplete !== null && onComplete !== void 0 ? onComplete : function (is_error) { };
        this.ajaxcall("/wizardsgrimoire/wizardsgrimoire/".concat(action, ".html"), data, this, onSuccess, onComplete);
    };
    WizardsGrimoire.prototype.setupNotifications = function () {
        log("notifications subscriptions setup");
        this.notifManager.setup();
    };
    WizardsGrimoire.prototype.format_string_recursive = function (log, args) {
        try {
            if (log && args && !args.processed) {
                args.processed = true;
                if (args.card_name !== undefined) {
                    args.card_name = "<b>" + _(args.card_name) + "</b>";
                }
                if (args.damage !== undefined) {
                    args.damage = "<div class=\"wg-icon-log i-dmg_undef\"><span>".concat(args.damage, "</span></div>");
                }
                if (args.nbr_mana_card !== undefined) {
                    args.nbr_mana_card = "<div class=\"wg-icon-log i-cards\"><span>".concat(args.nbr_mana_card, " </span></div>");
                }
                if (args.mana_values !== undefined) {
                    args.mana_values = args.mana_values
                        .map(function (value) { return "<div class=\"wg-icon-log i-mana-x\">".concat(value, "</div>"); })
                        .join(" ");
                }
            }
        }
        catch (e) {
            console.error(log, args, "Exception thrown", e.stack);
        }
        return this.inherited(arguments);
    };
    WizardsGrimoire.prototype.formatGametext = function (rawText) {
        if (!rawText)
            return "";
        var value = rawText.replace(",", ",<br />").replace(":", ":<br />");
        return "<p>" + value.split(".").join(".</p><p>") + "</p>";
    };
    return WizardsGrimoire;
}());
var cardId = 200000;
function getCardId() {
    return cardId++;
}
var HiddenDeck = (function (_super) {
    __extends(HiddenDeck, _super);
    function HiddenDeck(manager, element) {
        var _this = _super.call(this, manager, element, {
            cardNumber: 0,
            counter: {
                hideWhenEmpty: false,
            },
            autoRemovePreviousCards: false,
        }) || this;
        _this.manager = manager;
        _this.element = element;
        return _this;
    }
    HiddenDeck.prototype.addCard = function (card, animation, settings) {
        var _a;
        settings = settings !== null && settings !== void 0 ? settings : {};
        settings.index = (_a = settings.index) !== null && _a !== void 0 ? _a : 0;
        return _super.prototype.addCard.call(this, card, animation, settings);
    };
    return HiddenDeck;
}(Deck));
var VisibleDeck = (function (_super) {
    __extends(VisibleDeck, _super);
    function VisibleDeck(manager, element) {
        var _this = _super.call(this, manager, element, {
            cardNumber: 0,
            counter: {
                hideWhenEmpty: false,
            },
            autoRemovePreviousCards: false,
        }) || this;
        _this.manager = manager;
        _this.element = element;
        return _this;
    }
    return VisibleDeck;
}(Deck));
var ManaDeck = (function (_super) {
    __extends(ManaDeck, _super);
    function ManaDeck(manager, element, location) {
        var _this = _super.call(this, manager, element, {
            cardNumber: 0,
            counter: {},
            autoRemovePreviousCards: false,
        }) || this;
        _this.location = location;
        _this.isDeckSelectable = false;
        _this._isDeckSelected = false;
        return _this;
    }
    Object.defineProperty(ManaDeck.prototype, "isDeckSelected", {
        get: function () {
            return this._isDeckSelected;
        },
        set: function (value) {
            this._isDeckSelected = value;
            this.element.classList.toggle("wg-deck-selected", this.isDeckSelected);
        },
        enumerable: false,
        configurable: true
    });
    ManaDeck.prototype.addCard = function (card, animation, settings) {
        settings = settings !== null && settings !== void 0 ? settings : {};
        settings.index = Number(card.location_arg);
        return _super.prototype.addCard.call(this, card, animation, settings);
    };
    ManaDeck.prototype.setDeckIsSelectable = function (value) {
        this.isDeckSelectable = value;
        if (!this.isDeckSelectable && this.isDeckSelected) {
            this.isDeckSelected = false;
        }
    };
    ManaDeck.prototype.bindClick = function () {
        var _this = this;
        var _a;
        (_a = this.element) === null || _a === void 0 ? void 0 : _a.addEventListener("click", function (event) {
            if (_this.isDeckSelectable) {
                _this.deckClick();
            }
            else {
                var cardDiv_1 = event.target.closest(".card");
                if (!cardDiv_1) {
                    return;
                }
                var card = _this.cards.find(function (c) { return _this.manager.getId(c) == cardDiv_1.id; });
                if (!card) {
                    return;
                }
                _this.cardClick(card);
            }
        });
    };
    ManaDeck.prototype.deckClick = function () {
        var _a;
        if (this.isDeckSelectable) {
            this.isDeckSelected = !this.isDeckSelected;
            (_a = this.onDeckSelectionChanged) === null || _a === void 0 ? void 0 : _a.call(this);
        }
    };
    ManaDeck.prototype.forceSelected = function () {
        var _a;
        (_a = this.element) === null || _a === void 0 ? void 0 : _a.classList.add("wg-deck-was-selected");
    };
    ManaDeck.prototype.unselectDeck = function () {
        this.isDeckSelected = false;
        this.element.classList.toggle("wg-deck-selected", this.isDeckSelected);
    };
    return ManaDeck;
}(Deck));
var ActionManager = (function () {
    function ActionManager(game) {
        this.game = game;
        this.actions = [];
        this.actions_args = [];
    }
    ActionManager.prototype.setup = function (takeAction, newAction) {
        if (takeAction === void 0) { takeAction = "castSpell"; }
        log("actionmanager.reset");
        this.reset();
        this.take_action = takeAction;
        if (newAction) {
            this.actions.push(newAction);
        }
        return this;
    };
    ActionManager.prototype.reset = function () {
        log("actionmanager.reset");
        this.current_card = null;
        this.take_action = null;
        this.actions = [];
        this.actions_args = [];
        return this;
    };
    ActionManager.prototype.addAction = function (card) {
        this.current_card = card;
        var card_type = this.game.getCardType(card);
        log("actionmanager.addAction", card, card_type);
        this.addActionPriv(card_type.js_actions);
        log("actionmanager.actions values", this.actions);
        return this;
    };
    ActionManager.prototype.addActionInteraction = function (card) {
        this.current_card = card;
        var card_type = this.game.getCardType(card);
        log("actionmanager.addActionInteraction", card, card_type);
        this.addActionPriv(card_type.js_actions_interaction);
        return this;
    };
    ActionManager.prototype.addActionDelayed = function (card) {
        this.current_card = card;
        var card_type = this.game.getCardType(card);
        log("actionmanager.addActionDelayed", card, card_type);
        this.addActionPriv(card_type.js_actions_delayed);
        return this;
    };
    ActionManager.prototype.addActionPriv = function (actions) {
        var _this = this;
        if (!actions) {
            log("actionmanager.addActionPriv no actions");
        }
        else if (Array.isArray(actions)) {
            actions.forEach(function (action) { return _this.actions.push(action); });
        }
        else if (typeof actions === "string") {
            this.actions.push(actions);
        }
        log("actionmanager.addActionPriv values", this.actions);
    };
    ActionManager.prototype.addArgument = function (arg) {
        log("actionmanager.addArgument", arg);
        this.actions_args.push(arg);
        return this;
    };
    ActionManager.prototype.activateNextAction = function () {
        var _this = this;
        if (this.actions.length > 0) {
            var nextAction = this.actions.shift();
            this[nextAction]();
            return;
        }
        var card_type = this.game.getCardType(this.current_card);
        log("actionmanager.activateNextAction", this.current_card, card_type, this.actions_args);
        var handleError = function (is_error) {
            is_error ? _this.game.restoreGameState() : _this.game.clearSelection();
        };
        var data = {
            card_id: this.current_card.id,
            args: this.actions_args.join(";"),
        };
        this.game.takeAction(this.take_action, data, null, handleError);
    };
    ActionManager.prototype.actionArcaneTactics = function () {
        var msg = _("${you} may select ${nbr} mana card from your hand");
        this.returnManaCardToDeck(msg, 4, false);
    };
    ActionManager.prototype.actionFreeze = function () {
        var _this = this;
        this.question({
            cancel: true,
            options: [
                {
                    label: _("Draw 4 cards"),
                    action: function () {
                        _this.activateNextAction();
                    },
                },
                {
                    label: _("Place a mana card from the mana deck on one of your opponent's spells"),
                    action: function () {
                        _this.selectManaDeck(1, _("Select an opponent's spell deck"), true, {
                            player_id: _this.game.getOpponentId(),
                        });
                    },
                },
            ],
        });
    };
    ActionManager.prototype.actionFriendlyTruce = function () {
        var msg = _("${you} may give ${nbr} cards from your hand or pass");
        this.selectManaHand(3, msg, true, { canCancel: false, skip: { label: "Pass" } });
    };
    ActionManager.prototype.actionGuiltyBond = function () {
        var msg = _("${you} may select ${nbr} mana card from your hand");
        this.selectManaHand(1, msg, true);
    };
    ActionManager.prototype.actionMistOfPain = function () {
        var msg = _("${you} may discard up to ${nbr} mana card from your hand");
        this.selectManaHand(4, msg, false, { canCancel: false });
    };
    ActionManager.prototype.actionRejuvenation = function () {
        var _this = this;
        this.question({
            cancel: true,
            options: [
                {
                    label: _("Gain 4 mana cards"),
                    action: function () {
                        _this.activateNextAction();
                    },
                },
                {
                    label: _("Take 2 mana cards of any power from the discard pile"),
                    action: function () {
                        _this.actions.push("actionSelectTwoManaCardFromDiscard");
                        _this.activateNextAction();
                    },
                },
            ],
        });
    };
    ActionManager.prototype.actionShackledMotion = function () {
        var _this = this;
        this.question({
            cancel: true,
            options: [
                {
                    label: _("Draw 4 cards"),
                    action: function () {
                        _this.addArgument("1");
                        _this.activateNextAction();
                    },
                },
                {
                    label: _("Your opponent must discard their hand"),
                    action: function () {
                        _this.addArgument("2");
                        _this.activateNextAction();
                    },
                },
            ],
        });
    };
    ActionManager.prototype.actionSneakyDeal = function () {
        var _this = this;
        this.question({
            cancel: true,
            options: [
                {
                    label: _("Deal 1 damage"),
                    action: function () {
                        _this.activateNextAction();
                    },
                },
                {
                    label: _("Discard a mana card off 1 of your other spells"),
                    action: function () {
                        _this.actions.push("actionSelectManaFrom");
                        _this.activateNextAction();
                    },
                },
            ],
        });
    };
    ActionManager.prototype.actionTimeDistortion = function () {
        var msg = _("${you} may select up to ${nbr} mana card");
        this.selectMana(2, msg, false);
    };
    ActionManager.prototype.actionTrapAttack = function () {
        this.actionSelectManaFrom();
    };
    ActionManager.prototype.actionWrath = function () {
        var msg = _("${you} may discard ${nbr} mana card from your hand");
        this.selectManaHand(2, msg, true, {
            canCancel: false,
            skip: {
                label: _("Pass"),
                message: _("Are you sure that you didn't want to discard mana cards?"),
            },
        });
    };
    ActionManager.prototype.actionAfterShock = function () {
        this.selectManaHand(1, _("Select ${nbr} mana cards to return on top of the mana deck"), true, {
            canCancel: false,
        });
    };
    ActionManager.prototype.actionSilentSupport = function () {
        this.actions.push("actionSelectManaFrom");
        this.activateNextAction();
    };
    ActionManager.prototype.actionCastMana = function () {
        var _a = this.game.getCardType(this.current_card), name = _a.name, cost = _a.cost, type = _a.type;
        var player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var modifiedCost = Math.max(cost - player_table.getDiscountNextSpell(), 0);
        if (type == "red") {
            modifiedCost = Math.max(modifiedCost - player_table.getDiscountNextAttack(), 0);
        }
        var msg = _("${you} must pay ${nbr} mana card").replace("${nbr}", modifiedCost.toString());
        this.game.setClientState(states.client.castSpellWithMana, {
            descriptionmyturn: _(name) + " : " + msg,
            args: {
                card: this.current_card,
                cost: modifiedCost,
            },
        });
    };
    ActionManager.prototype.actionGiveManaFromHandToOpponent = function () {
        var msg = _("${you} may select ${nbr} mana card to give to your opponent");
        this.selectManaHand(1, msg, false, {
            skip: {
                label: _("Pass"),
                message: _("Are you sure that you didn't want to give a mana to your opponent?"),
            },
        });
    };
    ActionManager.prototype.actionSelectManaCoolDownPlayer = function () {
        var msg = _("Select a mana card under one of your spell");
        var name = this.game.getCardType(this.current_card).name;
        var player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var exclude = [];
        for (var index = 1; index <= 6; index++) {
            if (player_table.mana_cooldown[index].getCards().length == 0 ||
                index == Number(this.current_card.location_arg)) {
                exclude.push(index);
            }
        }
        var args = {
            player_id: this.game.getPlayerId(),
            card: this.current_card,
            count: 1,
            exact: true,
            exclude: exclude,
        };
        this.game.setClientState(states.client.selectManaDeck, {
            descriptionmyturn: _(name) + " : " + msg,
            args: args,
        });
    };
    ActionManager.prototype.actionSelectManaCoolDownOpponent = function () {
        var msg = _("Select a mana card under one of your opponent's spell");
        var name = this.game.getCardType(this.current_card).name;
        var player_table = this.game.getPlayerTable(this.game.getOpponentId());
        var exclude = [];
        for (var index = 1; index <= 6; index++) {
            if (player_table.mana_cooldown[index].getCards().length == 0) {
                exclude.push(index);
            }
        }
        var args = {
            player_id: this.game.getOpponentId(),
            card: this.current_card,
            count: 1,
            exact: true,
            exclude: exclude,
        };
        this.game.setClientState(states.client.selectManaDeck, {
            descriptionmyturn: _(name) + " : " + msg,
            args: args,
        });
    };
    ActionManager.prototype.actionSelectManaFrom = function () {
        var _this = this;
        var player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var emptyDecks = player_table
            .getManaDeckWithSpellOver()
            .filter(function (deck) { return deck.isEmpty(); })
            .map(function (deck) { return deck.location; });
        var argsSuppl = {
            exclude: emptyDecks,
            ignore: null,
        };
        if (this.actions.length > 0 && this.actions[0] == "actionSelectManaTo") {
            argsSuppl.ignore = function () {
                _this.actions.shift();
                _this.activateNextAction();
            };
        }
        var msgFrom = this.actions.length > 0
            ? _("${you} must select ${nbr} mana card - source")
            : _("${you} must select ${nbr} mana card");
        this.selectManaDeck(1, msgFrom, true, argsSuppl);
    };
    ActionManager.prototype.actionSelectManaTo = function () {
        var manaDeckPosition = Number(this.actions_args[this.actions_args.length - 1]);
        var player_table = this.game.getPlayerTable(this.game.getPlayerId());
        player_table.mana_cooldown[manaDeckPosition].forceSelected();
        var argsSuppl = {
            exclude: [manaDeckPosition],
        };
        var msg = _("${you} must select ${nbr} mana card - destination");
        this.selectManaDeck(1, msg, true, argsSuppl);
    };
    ActionManager.prototype.actionSelectTwoManaCardFromDiscard = function () {
        var msg = _("${you} may select ${nbr} mana card from the discard").replace("${nbr}", "2");
        var name = this.game.getCardType(this.current_card).name;
        this.game.setClientState(states.client.selectManaDiscard, {
            descriptionmyturn: _(name) + " : " + msg,
            args: {
                player_id: this.game.getPlayerId(),
                count: 2,
                exact: true,
            },
        });
    };
    ActionManager.prototype.question = function (args) {
        var name = this.game.getCardType(this.current_card).name;
        this.game.setClientState(states.client.question, {
            descriptionmyturn: _(name),
            args: args,
        });
    };
    ActionManager.prototype.selectMana = function (count, msg, exact, argsSuppl) {
        var _a;
        if (argsSuppl === void 0) { argsSuppl = {}; }
        var name = this.game.getCardType(this.current_card).name;
        msg = msg.replace("${nbr}", count.toString());
        argsSuppl.exclude = (_a = argsSuppl.exclude) !== null && _a !== void 0 ? _a : [];
        argsSuppl.exclude.push(Number(this.current_card.location_arg));
        var args = __assign(__assign({}, argsSuppl), { player_id: this.game.getPlayerId(), card: this.current_card, count: count, exact: exact });
        this.game.setClientState(states.client.selectMana, {
            descriptionmyturn: _(name) + " : " + msg,
            args: args,
        });
    };
    ActionManager.prototype.selectManaHand = function (count, msg, exact, argsSuppl) {
        if (argsSuppl === void 0) { argsSuppl = {}; }
        var name = this.game.getCardType(this.current_card).name;
        msg = msg.replace("${nbr}", count.toString());
        var args = __assign(__assign({}, argsSuppl), { player_id: this.game.getPlayerId(), card: this.current_card, count: count, exact: exact });
        this.game.setClientState(states.client.selectManaHand, {
            descriptionmyturn: _(name) + " : " + msg,
            args: args,
        });
    };
    ActionManager.prototype.selectManaDeck = function (count, msg, exact, argsSuppl) {
        var _a;
        if (argsSuppl === void 0) { argsSuppl = {}; }
        var name = this.game.getCardType(this.current_card).name;
        msg = msg.replace("${nbr}", count.toString());
        argsSuppl.exclude = (_a = argsSuppl.exclude) !== null && _a !== void 0 ? _a : [];
        if (!argsSuppl.player_id || argsSuppl.player_id == this.game.getPlayerId()) {
            argsSuppl.exclude.push(Number(this.current_card.location_arg));
        }
        var args = __assign({ player_id: this.game.getPlayerId(), card: this.current_card, count: count, exact: exact }, argsSuppl);
        this.game.setClientState(states.client.selectManaDeck, {
            descriptionmyturn: _(name) + " : " + msg,
            args: args,
        });
    };
    ActionManager.prototype.returnManaCardToDeck = function (msg, count, canCancel) {
        msg = msg.replace("${nbr}", count.toString());
        var args = { count: count, canCancel: canCancel, exact: true };
        var name = this.game.getCardType(this.current_card).name;
        this.game.setClientState(states.client.selectManaReturnDeck, {
            descriptionmyturn: _(name) + " : " + msg,
            args: args,
        });
    };
    return ActionManager;
}());
var card_width = 120;
var card_height = 168;
var SpellCardManager = (function (_super) {
    __extends(SpellCardManager, _super);
    function SpellCardManager(game) {
        var _this = _super.call(this, game, {
            getId: function (card) { return "spell-card-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add("wg-card");
                div.classList.add("wg-card-spell");
                div.dataset.cardId = "" + card.id;
                div.dataset.type = "" + card.type;
            },
            setupFrontDiv: function (card, div) {
                div.id = "".concat(_this.getId(card), "-front");
                div.dataset.type = "" + card.type;
                div.classList.add("wg-card-spell-front");
                if (div.childNodes.length == 1 && card.type) {
                    var helpMarkerId_1 = "".concat(_this.getId(card), "-help-marker");
                    var color = !isDebug ? "white" : _this.game.getCardType(card).debug;
                    div.insertAdjacentHTML("afterbegin", "<div id=\"".concat(helpMarkerId_1, "\" class=\"help-marker\">\n                     <svg class=\"feather feather-help-circle\" fill=\"").concat(color, "\" height=\"24\" stroke=\"currentColor\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" width=\"24\" xmlns=\"http://www.w3.org/2000/svg\"><circle cx=\"12\" cy=\"12\" r=\"10\"></circle><path d=\"M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3\"></path><line x1=\"12\" x2=\"12.01\" y1=\"17\" y2=\"17\"></line></svg>\n                  </div>"));
                    game.setTooltip(helpMarkerId_1, _this.getTooltip(card));
                    document.getElementById(helpMarkerId_1).addEventListener("click", function () {
                        _this.game.tooltips[helpMarkerId_1].open(helpMarkerId_1);
                    });
                }
            },
            setupBackDiv: function (card, div) {
                div.classList.add("wg-card-spell-back");
            },
            isCardVisible: function (card) {
                if (card.isHidden == true) {
                    return false;
                }
                else {
                    return card.type !== null && card.type !== undefined;
                }
            },
            cardWidth: card_width,
            cardHeight: card_height,
        }) || this;
        _this.game = game;
        return _this;
    }
    SpellCardManager.prototype.getCardById = function (id) {
        return this.getCardStock({ id: id })
            .getCards()
            .find(function (x) { return x.id == id; });
    };
    SpellCardManager.prototype.getTooltip = function (card) {
        var card_type = this.game.getCardType(card);
        var name = card_type.name, cost = card_type.cost, description = card_type.description;
        var gametext = this.game.formatGametext(description);
        var html = "<div class=\"wg-tooltip-card\">\n         <div class=\"wg-tooltip-left\">\n            <div class=\"wg-tooltip-header\">".concat(name, "</div>\n            <div class=\"wg-tooltip-cost\">").concat(_("Cost :"), " ").concat(cost, "</div>\n            <div class=\"wg-tooltip-gametext\">").concat(gametext, "</div>\n         </div>\n      </div>");
        return html;
    };
    return SpellCardManager;
}(CardManager));
var ManaCardManager = (function (_super) {
    __extends(ManaCardManager, _super);
    function ManaCardManager(game) {
        var _this = _super.call(this, game, {
            getId: function (card) { return "mana-card-".concat(card.id); },
            setupDiv: function (card, div) {
                div.classList.add("wg-card");
                div.classList.add("wg-card-mana");
                div.dataset.cardId = "" + card.id;
                div.dataset.type = "" + card.type;
            },
            setupFrontDiv: function (card, div) {
                div.dataset.type = "" + card.type;
                div.classList.add("wg-card-mana-front");
                var growthID = "".concat(_this.getId(card), "-growth-id");
                if (!document.getElementById(growthID)) {
                    div.insertAdjacentHTML("afterbegin", "<div id=\"".concat(growthID, "\" class=\"wg-mana-icon wg-icon-growth\">+1</div>"));
                }
            },
            setupBackDiv: function (card, div) {
                div.classList.add("wg-card-mana-back");
            },
            isCardVisible: function (card) {
                if (card.isHidden == true) {
                    return false;
                }
                else {
                    return card.type !== null && card.type !== undefined;
                }
            },
            cardWidth: card_width,
            cardHeight: card_height,
        }) || this;
        _this.game = game;
        return _this;
    }
    ManaCardManager.prototype.getCardById = function (id) {
        return this.getCardStock({ id: id })
            .getCards()
            .find(function (x) { return x.id == id; });
    };
    return ManaCardManager;
}(CardManager));
var ANIMATION_MS = 1500;
var NotificationManager = (function () {
    function NotificationManager(game) {
        this.game = game;
    }
    NotificationManager.prototype.setup = function () {
        var _this = this;
        this.subscribeEvent("onChooseSpell", 500);
        this.subscribeEvent("onDiscardSpell", 500);
        this.subscribeEvent("onRefillSpell", 500);
        this.subscribeEvent("onDrawManaCards", 1000, true);
        this.subscribeEvent("onMoveManaCards", 1000, true);
        this.subscribeEvent("onManaDeckShuffle", 2500);
        this.subscribeEvent("onHealthChanged", 500);
        this.subscribeEvent("onOngoingSpellActive", 0);
        this.game.notifqueue.setIgnoreNotificationCheck("message", function (notif) { return notif.args.excluded_player_id && notif.args.excluded_player_id == _this.game.player_id; });
    };
    NotificationManager.prototype.subscribeEvent = function (eventName, time, setIgnore) {
        var _this = this;
        if (setIgnore === void 0) { setIgnore = false; }
        try {
            dojo.subscribe(eventName, this, "notif_" + eventName);
            if (time) {
                this.game.notifqueue.setSynchronous(eventName, time);
            }
            if (setIgnore) {
                this.game.notifqueue.setIgnoreNotificationCheck(eventName, function (notif) {
                    return notif.args.excluded_player_id && notif.args.excluded_player_id == _this.game.player_id;
                });
            }
        }
        catch (_a) {
            console.error("NotificationManager::subscribeEvent", eventName);
        }
    };
    NotificationManager.prototype.notif_onChooseSpell = function (notif) {
        var _a = notif.args, player_id = _a.player_id, card = _a.card;
        log("onChooseSpell", card);
        this.game.getPlayerTable(player_id).onChooseSpell(card);
    };
    NotificationManager.prototype.notif_onDiscardSpell = function (notif) {
        var _a = notif.args, player_id = _a.player_id, card = _a.card;
        log("onDiscardSpell", card);
        this.game.tableCenter.spellDiscard.addCard(card);
    };
    NotificationManager.prototype.notif_onRefillSpell = function (notif) {
        var card = notif.args.card;
        log("onRefillSpell", card);
        this.game.tableCenter.onRefillSpell(card);
    };
    NotificationManager.prototype.notif_onDrawManaCards = function (notif) {
        var _a = notif.args, player_id = _a.player_id, cards = _a.cards;
        log("onDrawManaCards", cards);
        this.game.getPlayerTable(player_id).onDrawManaCard(cards);
    };
    NotificationManager.prototype.notif_onManaDeckShuffle = function (notif) {
        log("onManaDeckShuffle");
        this.game.tableCenter.shuffleManaDeck(notif.args.cards);
    };
    NotificationManager.prototype.notif_onMoveManaCards = function (notif) {
        var _a = notif.args, player_id = _a.player_id, cards_before = _a.cards_before, cards_after = _a.cards_after, nbr = _a.nbr;
        log("onMoveManaCards", cards_before, cards_after);
        var _loop_3 = function (index) {
            var before = cards_before[index];
            var after = cards_after.find(function (x) { return x.id == before.id; });
            this_1.game.getPlayerTable(player_id).onMoveManaCard(before, after);
        };
        var this_1 = this;
        for (var index = 0; index < nbr; index++) {
            _loop_3(index);
        }
    };
    NotificationManager.prototype.notif_onOngoingSpellActive = function (notif) {
        var _a = notif.args, name = _a.variable, active = _a.value;
        log("notif_onOngoingSpellActive", name, active);
        this.game.toggleOngoingSpell({ name: name, active: active });
    };
    NotificationManager.prototype.notif_onHealthChanged = function (notif) {
        log("notif_onHealthChanged", notif.args);
        var _a = notif.args, player_id = _a.player_id, life_remaining = _a.life_remaining, damage = _a.damage;
        this.game.scoreCtrl[player_id].toValue(life_remaining);
        if (damage > 0) {
            this.game.displayScoring("player-table-".concat(player_id), "ff0000", -damage, 1000);
        }
    };
    return NotificationManager;
}());
var states = {
    client: {
        castSpellWithMana: "client_castSpellWithMana",
        question: "client_question",
        selectMana: "client_selectMana",
        selectManaDeck: "client_selectManaDeck",
        selectManaDiscard: "client_selectManaDiscard",
        selectManaHand: "client_selectManaHand",
        selectManaReturnDeck: "client_selectManaReturnDeck",
    },
    server: {
        discardMana: "discardMana",
        castSpell: "castSpell",
        castSpellInteraction: "castSpellInteraction",
        chooseNewSpell: "chooseNewSpell",
        basicAttack: "basicAttack",
        activateDelayedSpell: "activateDelayedSpell",
    },
};
var StateManager = (function () {
    function StateManager(game) {
        var _a;
        this.game = game;
        this.client_states = [];
        this.states = (_a = {},
            _a[states.client.castSpellWithMana] = new CastSpellWithManaStates(game),
            _a[states.client.question] = new QuestionStates(game),
            _a[states.client.selectMana] = new SelectManaStates(game),
            _a[states.client.selectManaDeck] = new SelectManaDeckStates(game),
            _a[states.client.selectManaDiscard] = new SelectManaDiscardStates(game),
            _a[states.client.selectManaHand] = new SelectManaHandStates(game),
            _a[states.client.selectManaReturnDeck] = new SelectManaReturnDeckStates(game),
            _a[states.server.activateDelayedSpell] = new ActivateDelayedSpellStates(game),
            _a[states.server.discardMana] = new DiscardManaStates(game),
            _a[states.server.basicAttack] = new BasicAttackStates(game),
            _a[states.server.castSpell] = new CastSpellStates(game),
            _a[states.server.castSpellInteraction] = new CastSpellInteractionStates(game),
            _a[states.server.chooseNewSpell] = new ChooseNewSpellStates(game),
            _a);
    }
    StateManager.prototype.onEnteringState = function (stateName, args) {
        var _this = this;
        var _a;
        log("Entering state: " + stateName);
        if (args.phase) {
            this.game.gameOptions.setPhase(Number(args.phase));
        }
        else {
            this.game.gameOptions.setPhase(99);
        }
        if ((_a = args.args) === null || _a === void 0 ? void 0 : _a.ongoing_spells) {
            args.args.ongoing_spells.forEach(function (value) {
                _this.game.toggleOngoingSpell(value);
            });
        }
        if (this.states[stateName] !== undefined) {
            this.states[stateName].onEnteringState(args.args);
            if (stateName.startsWith("client_")) {
                this.client_states.push(this.states[stateName]);
            }
            else {
                this.client_states.splice(0);
            }
        }
        else {
            this.client_states.splice(0);
            console.warn("State not handled", stateName);
        }
        console.log("client states", this.client_states);
    };
    StateManager.prototype.onLeavingState = function (stateName) {
        log("Leaving state: " + stateName);
        if (this.states[stateName] !== undefined) {
            this.states[stateName].onLeavingState();
        }
    };
    StateManager.prototype.onUpdateActionButtons = function (stateName, args) {
        log("onUpdateActionButtons: " + stateName);
        if (this.states[stateName] !== undefined) {
            if (this.game.isCurrentPlayerActive()) {
                this.states[stateName].onUpdateActionButtons(args);
            }
        }
    };
    StateManager.prototype.restoreGameState = function () {
        while (this.client_states.length > 0) {
            var state = this.client_states.pop();
            state.restoreGameState();
        }
    };
    return StateManager;
}());
var Hand = (function (_super) {
    __extends(Hand, _super);
    function Hand(manager, element, current_player) {
        var _this = _super.call(this, manager, element, {
            center: true,
            wrap: "wrap",
            sort: sortFunction("type", "type_arg"),
        }) || this;
        _this.current_player = current_player;
        return _this;
    }
    Hand.prototype.addCard = function (card, animation, settings) {
        var copy = __assign(__assign({}, card), { isHidden: !this.current_player });
        if (!this.current_player) {
            copy.type = null;
        }
        return _super.prototype.addCard.call(this, copy, animation, settings);
    };
    return Hand;
}(LineStock));
var PlayerTable = (function () {
    function PlayerTable(game, player) {
        var _this = this;
        var _a;
        this.game = game;
        this.mana_cooldown = {};
        this.player_id = Number(player.id);
        this.current_player = this.player_id == this.game.getPlayerId();
        var pId = player.id, pName = player.name, pColor = player.color;
        var pCurrent = this.current_player.toString();
        var html = "\n            <div style=\"--color: #".concat(pColor, "\" data-color=\"").concat(pColor, "\">\n               <div id=\"player-table-").concat(pId, "\" class=\"player-table whiteblock\" data-discount-next-spell=\"0\" data-discount-next-attack=\"0\">\n                  <span class=\"wg-title\">").concat(_("Spell Repertoire"), "</span>\n                  <div id=\"player-table-").concat(pId, "-spell-repertoire\" class=\"spell-repertoire\"></div>\n                  <div id=\"player-table-").concat(pId, "-mana-cooldown\" class=\"mana-cooldown\">\n                     <div id=\"player_table-").concat(pId, "-mana-deck-1\" class=\"mana-deck\"></div>\n                     <div id=\"player_table-").concat(pId, "-mana-deck-2\" class=\"mana-deck\"></div>\n                     <div id=\"player_table-").concat(pId, "-mana-deck-3\" class=\"mana-deck\"></div>\n                     <div id=\"player_table-").concat(pId, "-mana-deck-4\" class=\"mana-deck\"></div>\n                     <div id=\"player_table-").concat(pId, "-mana-deck-5\" class=\"mana-deck\"></div>\n                     <div id=\"player_table-").concat(pId, "-mana-deck-6\" class=\"mana-deck\"></div>\n                  </div>\n               </div>\n               <div class=\"player-table whiteblock player-hand\">\n                  <span class=\"wg-title\">").concat(_("Hand"), "</span>\n                  <div id=\"player-table-").concat(pId, "-hand-cards\" class=\"hand cards\" data-player-id=\"").concat(pId, "\" data-current-player=\"").concat(pCurrent, "\" data-my-hand=\"").concat(pCurrent, "\"></div>\n               </div>\n            </div>");
        dojo.place(html, "tables");
        this.spell_repertoire = new SlotStock(game.spellsManager, document.getElementById("player-table-".concat(this.player_id, "-spell-repertoire")), {
            slotsIds: [1, 2, 3, 4, 5, 6],
            slotClasses: ["wg-spell-slot"],
            mapCardToSlot: function (card) { return card.location_arg; },
        });
        for (var index = 1; index <= 6; index++) {
            var divDeck = document.getElementById("player_table-".concat(pId, "-mana-deck-").concat(index));
            this.mana_cooldown[index] = new ManaDeck(game.manasManager, divDeck, index);
        }
        var board = game.gamedatas.player_board[pId];
        this.spell_repertoire.addCards(board.spells);
        Object.keys(board.manas).forEach(function (pos) {
            var deck = _this.mana_cooldown[Number(pos)];
            board.manas[pos].forEach(function (card) {
                deck.addCard(card, null, { index: 0 });
            });
        });
        this.hand = new Hand(game.manasManager, document.getElementById("player-table-".concat(pId, "-hand-cards")), this.current_player);
        this.hand.addCards((_a = board.hand) !== null && _a !== void 0 ? _a : []);
    }
    PlayerTable.prototype.canCast = function (card) {
        var cost = this.game.getCardType(card).cost;
        return this.hand.getCards().length >= cost;
    };
    PlayerTable.prototype.getSpellSlotAvailables = function () {
        var _this = this;
        if (this.spell_repertoire.getCards().length < 6) {
            return arrayRange(this.spell_repertoire.getCards().length + 1, 6);
        }
        var slots = [];
        Object.keys(this.mana_cooldown).forEach(function (index) {
            var deck = _this.mana_cooldown[index];
            if (deck.getCards().length == 0) {
                slots.push(Number(index));
            }
        });
        return slots;
    };
    PlayerTable.prototype.getManaDecks = function (exclude) {
        var _this = this;
        if (exclude === void 0) { exclude = []; }
        var positions = [];
        for (var index = 1; index <= 6; index++) {
            if (exclude.indexOf(index) < 0) {
                positions.push(index);
            }
        }
        return positions.map(function (position) { return _this.mana_cooldown[position]; });
    };
    PlayerTable.prototype.getManaDeckWithSpellOver = function (exclude) {
        var _this = this;
        if (exclude === void 0) { exclude = []; }
        var spellsPosition = this.spell_repertoire
            .getCards()
            .map(function (card) { return Number(card.location_arg); });
        var positions = [];
        for (var index = 1; index <= 6; index++) {
            if (exclude.indexOf(index) < 0 && spellsPosition.indexOf(index) >= 0) {
                positions.push(index);
            }
        }
        return positions.map(function (position) { return _this.mana_cooldown[position]; });
    };
    PlayerTable.prototype.onChooseSpell = function (card) {
        this.spell_repertoire.addCard(card, {
            fromStock: this.game.tableCenter.spellPool,
        });
    };
    PlayerTable.prototype.onDrawManaCard = function (cards) {
        return __awaiter(this, void 0, void 0, function () {
            var index, card, topHiddenCard, manaDeck;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        index = 0;
                        _a.label = 1;
                    case 1:
                        if (!(index < cards.length)) return [3, 4];
                        card = cards[index];
                        topHiddenCard = __assign(__assign({}, card), { isHidden: true });
                        manaDeck = this.game.tableCenter.manaDeck;
                        manaDeck.setCardNumber(manaDeck.getCardNumber(), topHiddenCard);
                        return [4, this.hand.addCard(card)];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3:
                        index++;
                        return [3, 1];
                    case 4: return [2];
                }
            });
        });
    };
    PlayerTable.prototype.onMoveManaCard = function (before, after) {
        return __awaiter(this, void 0, void 0, function () {
            var stockBeforeManager, stockAfter, newCard;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        stockBeforeManager = this.game.manasManager.getCardStock(before);
                        stockAfter = this.getStock(after);
                        if (stockBeforeManager === stockAfter) {
                            return [2];
                        }
                        if (!!stockAfter.contains(after)) return [3, 2];
                        newCard = __assign(__assign({}, after), { isHidden: this.isStockHidden(stockAfter) });
                        return [4, stockAfter.addCard(newCard)];
                    case 1:
                        _a.sent();
                        _a.label = 2;
                    case 2: return [2];
                }
            });
        });
    };
    PlayerTable.prototype.isStockHidden = function (stock) {
        return (this.hand == stock && !this.current_player) || this.game.tableCenter.manaDeck == stock;
    };
    PlayerTable.prototype.getStock = function (card) {
        if (card.location == "hand") {
            if (card.location_arg == this.player_id) {
                return this.hand;
            }
            else {
                return this.game.getPlayerTable(Number(card.location_arg)).hand;
            }
        }
        if (card.location == "deck") {
            return this.game.tableCenter.manaDeck;
        }
        if (card.location == "discard") {
            return this.game.tableCenter.manaDiscard;
        }
        var index = Number(card.location.substring(card.location.length - 1));
        return this.mana_cooldown[index];
    };
    PlayerTable.prototype.getDiscountNextAttack = function () {
        return Number(this.getPlayerTableDiv().dataset.discountNextAttack);
    };
    PlayerTable.prototype.setDiscountNextAttack = function (amount) {
        this.getPlayerTableDiv().dataset.discountNextAttack = amount.toString();
    };
    PlayerTable.prototype.getDiscountNextSpell = function () {
        return Number(this.getPlayerTableDiv().dataset.discountNextSpell);
    };
    PlayerTable.prototype.setDiscountNextSpell = function (amount) {
        this.getPlayerTableDiv().dataset.discountNextSpell = amount.toString();
    };
    PlayerTable.prototype.getPlayerTableDiv = function () {
        return document.getElementById("player-table-".concat(this.player_id));
    };
    return PlayerTable;
}());
var EIGHT_CARDS_SLOT = [1, 5, 2, 6, 3, 7, 4, 8];
var TEN_CARDS_SLOT = [1, 6, 2, 7, 3, 8, 4, 9, 5, 10];
var TableCenter = (function () {
    function TableCenter(game) {
        var _this = this;
        this.game = game;
        this.spellDeck = new HiddenDeck(game.spellsManager, document.getElementById("spell-deck"));
        this.manaDeck = new HiddenDeck(game.manasManager, document.getElementById("mana-deck"));
        this.spellDiscard = new VisibleDeck(game.spellsManager, document.getElementById("spell-discard"));
        this.manaDiscard = new VisibleDeck(game.manasManager, document.getElementById("mana-discard"));
        this.spellPool = new SlotStock(game.spellsManager, document.getElementById("spell-pool"), {
            slotsIds: game.gamedatas.slot_count == 8 ? EIGHT_CARDS_SLOT : TEN_CARDS_SLOT,
            slotClasses: ["wg-spell-slot"],
            mapCardToSlot: function (card) { return card.location_arg; },
            direction: "column",
        });
        this.manaDiscardDisplay = new LineStock(game.manasManager, document.getElementById("mana-discard-display"), { gap: "2px" });
        this.manaDiscardDisplay.setSelectionMode("multiple");
        game.gamedatas.spells.deck.forEach(function (card) {
            _this.spellDeck.addCard(__assign(__assign({}, card), { isHidden: true }));
        });
        game.gamedatas.manas.deck.forEach(function (card) {
            _this.manaDeck.addCard(__assign(__assign({}, card), { isHidden: true }));
        });
        game.gamedatas.spells.discard.forEach(function (card) {
            _this.spellDiscard.addCard(card);
        });
        var sortAscending = function (a, b) { return Number(a.location_arg) - Number(b.location_arg); };
        game.gamedatas.manas.discard.sort(sortAscending).forEach(function (card) {
            _this.manaDiscard.addCard(card);
        });
        this.spellPool.addCards(game.gamedatas.slot_cards);
    }
    TableCenter.prototype.shuffleManaDeck = function (cards) {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0: return [4, this.manaDeck.addCards(cards)];
                    case 1:
                        _a.sent();
                        return [4, this.manaDeck.shuffle(8)];
                    case 2:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    TableCenter.prototype.moveManaDiscardPile = function (toDisplay) {
        var _this = this;
        this.manaDiscardDisplay.unselectAll();
        if (toDisplay) {
            var cards = __spreadArray([], this.manaDiscard.getCards(), true);
            this.manaDiscardDisplay.addCards(cards);
        }
        else {
            this.manaDiscardDisplay
                .getCards()
                .sort(function (x, y) { return x.location_arg - y.location_arg; })
                .forEach(function (card) {
                _this.manaDiscard.addCard(card);
            });
        }
    };
    TableCenter.prototype.onRefillSpell = function (card) {
        var topHiddenCard = __assign(__assign({}, card), { isHidden: true });
        this.spellDeck.setCardNumber(this.spellDeck.getCardNumber(), topHiddenCard);
        this.spellPool.addCard(card);
    };
    return TableCenter;
}());
var GameOptions = (function () {
    function GameOptions(game) {
        this.game = game;
        var playerBoards = document.getElementById("");
        var _a = {
            phase1: _("Choose a New Spell"),
            phase2: _("Spell Cool Down"),
            phase3: _("Gain 3 Mana"),
            phase4: _("Cast Spells"),
            phase5: _("Basic Attack"),
        }, phase1 = _a.phase1, phase2 = _a.phase2, phase3 = _a.phase3, phase4 = _a.phase4, phase5 = _a.phase5;
        var html = "\n            <div class=\"player-board\">\n                <div class=\"player-board-inner\">\n                    <div id=\"wg-phase-selector\" data-phase=\"1\"></div>\n                    <ul id=\"wg-phases\">\n                        <li><div class=\"wg-icon\"></div><div class=\"wg-phase-name\">1. ".concat(phase1, "</div></li>\n                        <li><div class=\"wg-icon\"></div><div class=\"wg-phase-name\">2. ").concat(phase2, "</div></li>\n                        <li><div class=\"wg-icon\"></div><div class=\"wg-phase-name\">3. ").concat(phase3, "</div></li>\n                        <li><div class=\"wg-icon\"></div><div class=\"wg-phase-name\">4. ").concat(phase4, "</div></li>\n                        <li><div class=\"wg-icon\"></div><div class=\"wg-phase-name\">5. ").concat(phase5, "</div></li>\n                    </ul>\n                </div>\n            </div>");
        dojo.place(html, "player_boards");
        this.game.updatePlayerOrdering();
    }
    GameOptions.prototype.setPhase = function (phase) {
        document.getElementById("wg-phase-selector").dataset.phase = phase.toString();
    };
    return GameOptions;
}());
var DiscardManaStates = (function () {
    function DiscardManaStates(game) {
        this.game = game;
    }
    DiscardManaStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
        this.nbr_cards_to_discard = this.player_table.hand.getCards().length - 10;
        var handleChange = function () {
            var nbr_cards_selected = _this.player_table.hand.getSelection().length;
            _this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected == _this.nbr_cards_to_discard);
        };
        this.player_table.hand.setSelectionMode("multiple");
        this.player_table.hand.onSelectionChange = handleChange;
    };
    DiscardManaStates.prototype.onLeavingState = function () {
        this.player_table.hand.setSelectionMode("none");
        this.player_table.hand.onSelectionChange = null;
        this.nbr_cards_to_discard = 0;
    };
    DiscardManaStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            var selected_card_ids = _this.player_table.hand.getSelection().map(function (x) { return x.id; });
            if (selected_card_ids.length == _this.nbr_cards_to_discard) {
                _this.game.takeAction("discardMana", {
                    args: selected_card_ids.join(";"),
                });
            }
        };
        this.game.addActionButtonDisabled("btn_confirm", _("Confirm"), handleConfirm);
    };
    DiscardManaStates.prototype.restoreGameState = function () { };
    return DiscardManaStates;
}());
var CastSpellStates = (function () {
    function CastSpellStates(game) {
        this.game = game;
    }
    CastSpellStates.prototype.onEnteringState = function (args) {
        var _this = this;
        this.game.clearSelection();
        if (!this.game.isCurrentPlayerActive())
            return;
        var player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var repertoire = player_table.spell_repertoire;
        player_table.setDiscountNextAttack(args.discount_attack_spell);
        player_table.setDiscountNextSpell(args.discount_next_spell);
        repertoire.setSelectionMode("single");
        repertoire.onSelectionChange = function (selection, lastChange) {
            var canSelect = selection &&
                selection.length === 1 &&
                player_table.canCast(selection[0]) &&
                player_table.mana_cooldown[lastChange.location_arg].getCardNumber() == 0;
            _this.game.toggleButtonEnable("btn_cast", canSelect);
        };
    };
    CastSpellStates.prototype.onLeavingState = function () {
        var repertoire = this.game.getPlayerTable(this.game.getPlayerId()).spell_repertoire;
        repertoire.setSelectionMode("none");
        repertoire.onSelectionChange = null;
    };
    CastSpellStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleCastSpell = function () {
            var repertoire = _this.game.getPlayerTable(_this.game.getPlayerId()).spell_repertoire;
            var selectedSpell = repertoire.getSelection()[0];
            _this.game.markCardAsSelected(selectedSpell);
            _this.game.actionManager.setup("castSpell", "actionCastMana");
            _this.game.actionManager.addAction(selectedSpell);
            _this.game.actionManager.activateNextAction();
        };
        var handlePass = function () {
            _this.game.takeAction("pass");
        };
        if (this.hasSpellAvailable()) {
            this.game.addActionButtonDisabled("btn_cast", _("Cast spell"), handleCastSpell);
            this.game.addActionButtonRed("btn_pass", _("Move to basic attack"), handlePass);
        }
        else {
            this.game.addActionButton("btn_pass", _("Move to basic attack"), handlePass);
        }
    };
    CastSpellStates.prototype.restoreGameState = function () { };
    CastSpellStates.prototype.hasSpellAvailable = function () {
        var nbr_empty_deck = this.game
            .getPlayerTable(this.game.getPlayerId())
            .getManaDeckWithSpellOver()
            .filter(function (deck) { return deck.isEmpty(); }).length;
        return nbr_empty_deck > 0;
    };
    return CastSpellStates;
}());
var CastSpellInteractionStates = (function () {
    function CastSpellInteractionStates(game) {
        this.game = game;
    }
    CastSpellInteractionStates.prototype.onEnteringState = function (args) {
        var _this = this;
        this.game.markCardAsSelected(args.spell);
        if (!this.game.isCurrentPlayerActive())
            return;
        this.game.actionManager.setup("castSpellInteraction");
        this.game.actionManager.addActionInteraction(args.spell);
        setTimeout(function () {
            _this.game.actionManager.activateNextAction();
        }, 10);
    };
    CastSpellInteractionStates.prototype.onLeavingState = function () { };
    CastSpellInteractionStates.prototype.onUpdateActionButtons = function (args) { };
    CastSpellInteractionStates.prototype.restoreGameState = function () { };
    return CastSpellInteractionStates;
}());
var ChooseNewSpellStates = (function () {
    function ChooseNewSpellStates(game) {
        this.game = game;
    }
    ChooseNewSpellStates.prototype.onEnteringState = function (args) {
        if (!this.game.isCurrentPlayerActive())
            return;
        this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
        if (this.player_table.getSpellSlotAvailables().length == 0) {
            this.clearSelectionMode();
        }
        else if (this.player_table.spell_repertoire.getCards().length < 6) {
            this.onEnteringStateChoose();
        }
        else {
            this.onEnteringStateReplace();
        }
    };
    ChooseNewSpellStates.prototype.onEnteringStateChoose = function () {
        var _this = this;
        var handleSelection = function (selection, lastChange) {
            if (selection && selection.length === 1) {
                _this.game.enableButton("btn_confirm", "blue");
            }
            else {
                _this.game.disableButton("btn_confirm");
            }
        };
        this.game.tableCenter.spellPool.setSelectionMode("single");
        this.game.tableCenter.spellPool.onSelectionChange = handleSelection;
    };
    ChooseNewSpellStates.prototype.onEnteringStateReplace = function () {
        var _this = this;
        var handleSelection = function () {
            var chooseSpell = _this.player_table.spell_repertoire.getSelection();
            var replaceSpell = _this.game.tableCenter.spellPool.getSelection();
            _this.game.toggleButtonEnable("btn_replace", chooseSpell.length == 1 && replaceSpell.length == 1);
        };
        this.game.tableCenter.spellPool.setSelectionMode("single");
        this.game.tableCenter.spellPool.onSelectionChange = handleSelection;
        this.player_table.spell_repertoire.setSelectionMode("single");
        this.player_table.spell_repertoire.onSelectionChange = handleSelection;
        var positions = this.player_table.getSpellSlotAvailables();
        var selectableCards = this.player_table.spell_repertoire
            .getCards()
            .filter(function (card) { return positions.indexOf(Number(card.location_arg)) >= 0; });
        this.player_table.spell_repertoire.setSelectableCards(selectableCards);
        this.game.setGamestateDescription("Replace");
    };
    ChooseNewSpellStates.prototype.onLeavingState = function () {
        this.clearSelectionMode();
    };
    ChooseNewSpellStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var handleConfirm = function () {
            var selectedSpell = _this.game.tableCenter.spellPool.getSelection()[0];
            if (selectedSpell != null) {
                _this.game.takeAction("chooseSpell", { id: selectedSpell.id });
            }
        };
        var handleReplace = function () {
            var selectedSpell = _this.game.tableCenter.spellPool.getSelection()[0];
            var replacedSpell = _this.player_table.spell_repertoire.getSelection()[0];
            _this.game.takeAction("replaceSpell", {
                new_spell_id: selectedSpell.id,
                old_spell_id: replacedSpell.id,
            });
        };
        if (this.player_table.spell_repertoire.getCards().length < 6) {
            this.game.addActionButtonDisabled("btn_confirm", _("Choose"), handleConfirm);
        }
        else {
            var available_slots = this.player_table.getSpellSlotAvailables();
            if (available_slots.length > 0) {
                this.game.addActionButtonDisabled("btn_replace", _("Replace"), handleReplace);
            }
        }
        if (this.player_table.spell_repertoire.getCards().length == 6) {
            this.game.addActionButtonPass();
        }
    };
    ChooseNewSpellStates.prototype.restoreGameState = function () { };
    ChooseNewSpellStates.prototype.clearSelectionMode = function () {
        this.game.tableCenter.spellPool.setSelectionMode("none");
        this.game.tableCenter.spellPool.onSelectionChange = null;
        if (this.player_table) {
            this.player_table.spell_repertoire.setSelectionMode("none");
            this.player_table.spell_repertoire.onSelectionChange = null;
        }
    };
    return ChooseNewSpellStates;
}());
var BasicAttackStates = (function () {
    function BasicAttackStates(game) {
        this.game = game;
    }
    BasicAttackStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        var player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var hand = player_table.hand;
        hand.setSelectionMode("single");
        hand.onSelectionChange = function (selection, lastChange) {
            _this.game.toggleButtonEnable("btn_attack", selection && selection.length === 1);
        };
    };
    BasicAttackStates.prototype.onLeavingState = function () {
        var hand = this.game.getPlayerTable(this.game.getPlayerId()).hand;
        hand.setSelectionMode("none");
        hand.onSelectionChange = null;
    };
    BasicAttackStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleCastSpell = function () {
            var hand = _this.game.getPlayerTable(_this.game.getPlayerId()).hand;
            var selectedMana = hand.getSelection()[0];
            if (selectedMana) {
                _this.game.takeAction("basicAttack", { id: selectedMana.id });
            }
        };
        this.game.addActionButtonDisabled("btn_attack", _("Attack"), handleCastSpell);
        this.game.addActionButtonPass();
    };
    BasicAttackStates.prototype.restoreGameState = function () { };
    return BasicAttackStates;
}());
var ActivateDelayedSpellStates = (function () {
    function ActivateDelayedSpellStates(game) {
        this.game = game;
    }
    ActivateDelayedSpellStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
        var handleSelection = function (selection, lastChange) {
            _this.game.toggleButtonEnable("btn_confirm", selection.length == 1);
        };
        this.player_table.spell_repertoire.setSelectionMode("single");
        this.player_table.spell_repertoire.onSelectionChange = handleSelection;
        var selectable_cards = this.player_table.spell_repertoire
            .getCards()
            .filter(function (card) { return args.spells.indexOf(card.id.toString()) >= 0; });
        this.player_table.spell_repertoire.setSelectableCards(selectable_cards);
    };
    ActivateDelayedSpellStates.prototype.onLeavingState = function () { };
    ActivateDelayedSpellStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            var selection = _this.player_table.spell_repertoire.getSelection()[0];
            _this.game.actionManager.setup("activateDelayedSpell");
            _this.game.actionManager.addActionDelayed(selection);
            _this.game.actionManager.activateNextAction();
        };
        this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
        this.game.disableButton("btn_confirm");
        if (args.spells.length == 0) {
            this.game.addActionButtonPass();
        }
    };
    ActivateDelayedSpellStates.prototype.restoreGameState = function () { };
    return ActivateDelayedSpellStates;
}());
var CastSpellWithManaStates = (function () {
    function CastSpellWithManaStates(game) {
        this.game = game;
    }
    CastSpellWithManaStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        this.mana_cards = [];
        this.spell = args.card;
        var _a = this.game.getCardType(this.spell), cost = _a.cost, type = _a.type;
        this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
        this.mana_deck = this.player_table.mana_cooldown[this.spell.location_arg];
        cost = Math.max(cost - this.player_table.getDiscountNextSpell(), 0);
        if (type == "red") {
            cost = Math.max(cost - this.player_table.getDiscountNextAttack(), 0);
        }
        var handleHandCardClick = function (card) {
            _this.mana_cards.push(card);
            _this.mana_deck.addCard(card);
            _this.game.toggleButtonEnable("btnConfirm", _this.mana_cards.length === cost);
        };
        var handleDeckCardClick = function (card) {
            if (_this.mana_cards.length > 0) {
                _this.moveCardFromManaDeckToHand();
                _this.game.toggleButtonEnable("btnConfirm", _this.mana_cards.length === cost);
            }
        };
        this.player_table.hand.onCardClick = handleHandCardClick;
        this.mana_deck.onCardClick = handleDeckCardClick;
        log("mana deck", this.mana_deck);
    };
    CastSpellWithManaStates.prototype.onLeavingState = function () {
        this.player_table.hand.onCardClick = null;
        this.mana_deck.onCardClick = null;
    };
    CastSpellWithManaStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            _this.game.actionManager.addArgument(_this.mana_cards.map(function (x) { return x.id; }).join(","));
            _this.game.actionManager.activateNextAction();
        };
        this.game.addActionButton("btnConfirm", _("Confirm"), handleConfirm);
        this.game.addActionButtonClientCancel();
        this.game.toggleButtonEnable("btnConfirm", args.cost == 0);
    };
    CastSpellWithManaStates.prototype.restoreGameState = function () {
        this.restore();
    };
    CastSpellWithManaStates.prototype.restore = function () {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!(this.mana_cards.length > 0)) return [3, 2];
                        return [4, this.moveCardFromManaDeckToHand()];
                    case 1:
                        _a.sent();
                        return [3, 0];
                    case 2: return [2];
                }
            });
        });
    };
    CastSpellWithManaStates.prototype.moveCardFromManaDeckToHand = function () {
        var _this = this;
        return new Promise(function (resolve) {
            var card = _this.mana_cards.pop();
            _this.player_table.hand.addCard(card).then(function () {
                resolve(true);
            });
            if (_this.mana_cards.length > 0) {
                var topCard = _this.mana_cards[_this.mana_cards.length - 1];
                _this.mana_deck.setCardNumber(_this.mana_cards.length, topCard);
            }
        });
    };
    return CastSpellWithManaStates;
}());
var QuestionStates = (function () {
    function QuestionStates(game) {
        this.game = game;
    }
    QuestionStates.prototype.onEnteringState = function (args) { };
    QuestionStates.prototype.onLeavingState = function () { };
    QuestionStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var options = args.options, cancel = args.cancel;
        var index = 0;
        options === null || options === void 0 ? void 0 : options.forEach(function (_a) {
            var label = _a.label, action = _a.action, color = _a.color;
            _this.game.addActionButton("btn_action_".concat(index++), label, action, null, null, color);
        });
        if (cancel) {
            this.game.addActionButtonClientCancel();
        }
    };
    QuestionStates.prototype.restoreGameState = function () { };
    return QuestionStates;
}());
var SelectManaStates = (function () {
    function SelectManaStates(game) {
        this.game = game;
    }
    SelectManaStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        var exclude = args.exclude, player_id = args.player_id, count = args.count;
        this.player_table = this.game.getPlayerTable(player_id);
        var handleChange = function (selection, lastChange) {
            var nbr_cards_selected = _this.player_table
                .getManaDecks(exclude)
                .filter(function (x) { return x.getSelection().length > 0; }).length;
            if (args.exact && args.count == 1 && nbr_cards_selected > 1) {
                _this.player_table.getManaDecks(exclude).forEach(function (deck) {
                    if (!deck.contains(lastChange)) {
                        deck.unselectAll();
                    }
                });
                nbr_cards_selected = 1;
            }
            if (args.exact) {
                _this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected == count);
            }
            else {
                _this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected <= count);
            }
        };
        this.player_table.getManaDecks(exclude).forEach(function (deck) {
            deck.setSelectionMode("single");
            deck.onSelectionChange = handleChange;
        });
    };
    SelectManaStates.prototype.onLeavingState = function () {
        this.player_table.getManaDecks().forEach(function (deck) {
            deck.setSelectionMode("none");
            deck.onSelectionChange = null;
        });
    };
    SelectManaStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            var selected_cards = _this.player_table
                .getManaDecks(args.exclude)
                .filter(function (x) { return x.getSelection().length > 0; })
                .map(function (x) { return x.getSelection()[0].id; });
            if (selected_cards.length < args.count) {
                if (!args.exact) {
                    var text = _("Are-you sure to not take all mana card?");
                    _this.game.confirmationDialog(text, function () {
                        _this.game.actionManager.addArgument(selected_cards.join(","));
                        _this.game.actionManager.activateNextAction();
                    });
                }
            }
            else {
                _this.game.actionManager.addArgument(selected_cards.join(","));
                _this.game.actionManager.activateNextAction();
            }
        };
        this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
        this.game.addActionButtonClientCancel();
    };
    SelectManaStates.prototype.restoreGameState = function () {
        this.player_table.getManaDecks().forEach(function (deck) { return deck.unselectAll(); });
    };
    return SelectManaStates;
}());
var SelectManaDeckStates = (function () {
    function SelectManaDeckStates(game) {
        this.game = game;
    }
    SelectManaDeckStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        var exclude = args.exclude, player_id = args.player_id, count = args.count;
        this.player_table = this.game.getPlayerTable(player_id);
        var decks = this.player_table.getManaDeckWithSpellOver(exclude);
        var handleChange = function (lastDeck) {
            var nbr_decks_selected = decks.filter(function (x) { return x.isDeckSelected; }).length;
            if (args.exact && args.count == 1 && nbr_decks_selected > 1) {
                decks.forEach(function (deck) {
                    if (deck !== lastDeck) {
                        deck.unselectDeck();
                    }
                });
                nbr_decks_selected = 1;
            }
            if (args.exact) {
                _this.game.toggleButtonEnable("btn_confirm", nbr_decks_selected == count);
            }
            else {
                _this.game.toggleButtonEnable("btn_confirm", nbr_decks_selected <= count);
            }
        };
        decks.forEach(function (deck) {
            deck.setDeckIsSelectable(true);
            deck.onDeckSelectionChanged = function () { return handleChange(deck); };
        });
    };
    SelectManaDeckStates.prototype.onLeavingState = function () {
        this.player_table.getManaDecks().forEach(function (deck) {
            deck.setDeckIsSelectable(false);
            deck.onDeckSelectionChanged = null;
        });
    };
    SelectManaDeckStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            var decks = _this.player_table.getManaDeckWithSpellOver(args.exclude);
            var selected_decks = decks.filter(function (x) { return x.isDeckSelected; }).map(function (x) { return x.location; });
            if (selected_decks.length < args.count) {
                if (!args.exact) {
                    var text = _("Are-you sure to not take all mana card?");
                    _this.game.confirmationDialog(text, function () {
                        _this.game.actionManager.addArgument(selected_decks.join(","));
                        _this.game.actionManager.activateNextAction();
                    });
                }
            }
            else {
                _this.game.actionManager.addArgument(selected_decks.join(","));
                _this.game.actionManager.activateNextAction();
            }
        };
        var handleIgnore = function () {
            var text = _("Are-you sure you want to ignore this effect?");
            _this.game.confirmationDialog(text, args.ignore);
        };
        this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
        if (args.ignore) {
            this.game.addActionButtonRed("btn_ignore", _("Ignore"), handleIgnore);
        }
        this.game.addActionButtonClientCancel();
        if (args.exact) {
            this.game.toggleButtonEnable("btn_confirm", args.count == 0);
        }
        else {
            this.game.toggleButtonEnable("btn_confirm", true);
        }
    };
    SelectManaDeckStates.prototype.restoreGameState = function () {
        this.player_table.getManaDecks().forEach(function (deck) {
            deck.setDeckIsSelectable(false);
            deck.onDeckSelectionChanged = null;
        });
    };
    return SelectManaDeckStates;
}());
var SelectManaDiscardStates = (function () {
    function SelectManaDiscardStates(game) {
        this.game = game;
    }
    SelectManaDiscardStates.prototype.onEnteringState = function (args) {
        var _this = this;
        this.game.tableCenter.moveManaDiscardPile(true);
        var deck = this.game.tableCenter.manaDiscardDisplay;
        var count = args.count, exact = args.exact;
        deck.setSelectionMode(count == 1 && exact ? "single" : "multiple");
        var handleChange = function (selection, lastChange) {
            _this.game.toggleButtonEnable("btn_confirm", exact ? deck.getSelection().length == count : deck.getSelection().length <= count);
        };
        deck.onSelectionChange = handleChange;
    };
    SelectManaDiscardStates.prototype.onLeavingState = function () {
        this.game.tableCenter.moveManaDiscardPile(false);
        this.game.tableCenter.manaDiscardDisplay.onSelectionChange = null;
    };
    SelectManaDiscardStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            var selected_card_ids = _this.game.tableCenter.manaDiscardDisplay.getSelection().map(function (x) { return x.id; });
            if (selected_card_ids.length < args.count) {
                if (!args.exact) {
                    var text = _("Are-you sure to not take all mana card?");
                    _this.game.confirmationDialog(text, function () {
                        _this.game.actionManager.addArgument(selected_card_ids.join(","));
                        _this.game.actionManager.activateNextAction();
                    });
                }
            }
            else {
                _this.game.actionManager.addArgument(selected_card_ids.join(","));
                _this.game.actionManager.activateNextAction();
            }
        };
        this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
        this.game.toggleButtonEnable("btn_confirm", !args.exact);
        this.game.addActionButtonClientCancel();
    };
    SelectManaDiscardStates.prototype.restoreGameState = function () {
        this.game.tableCenter.moveManaDiscardPile(false);
        this.game.tableCenter.manaDiscardDisplay.onSelectionChange = null;
    };
    return SelectManaDiscardStates;
}());
var SelectManaHandStates = (function () {
    function SelectManaHandStates(game) {
        this.game = game;
    }
    SelectManaHandStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        var card = args.card, player_id = args.player_id, count = args.count;
        this.player_table = this.game.getPlayerTable(player_id);
        var handleChange = function () {
            var nbr_cards_selected = _this.player_table.hand.getSelection().length;
            if (args.exact) {
                _this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected == count);
            }
            else {
                _this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected <= count);
            }
        };
        this.player_table.hand.setSelectionMode("multiple");
        this.player_table.hand.onSelectionChange = handleChange;
    };
    SelectManaHandStates.prototype.onLeavingState = function () {
        this.player_table.hand.setSelectionMode("none");
        this.player_table.hand.onSelectionChange = null;
    };
    SelectManaHandStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            var selected_card_ids = _this.player_table.hand.getSelection().map(function (x) { return x.id; });
            if (selected_card_ids.length < args.count) {
                if (!args.exact) {
                    var text = _("Are-you sure to not take all mana card?");
                    _this.game.confirmationDialog(text, function () {
                        _this.game.actionManager.addArgument(selected_card_ids.join(","));
                        _this.game.actionManager.activateNextAction();
                    });
                }
            }
            else {
                _this.game.actionManager.addArgument(selected_card_ids.join(","));
                _this.game.actionManager.activateNextAction();
            }
        };
        var handleSkip = function () {
            var _a;
            if ((_a = args.skip) === null || _a === void 0 ? void 0 : _a.message) {
                _this.game.confirmationDialog(_(args.skip.message), function () {
                    _this.game.actionManager.activateNextAction();
                });
            }
            else {
                _this.game.actionManager.activateNextAction();
            }
        };
        this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
        if (args.skip) {
            this.game.addActionButtonRed("btn_skip", _(args.skip.label), handleSkip);
        }
        if (args.canCancel !== false) {
            this.game.addActionButtonClientCancel();
        }
        this.game.toggleButtonEnable("btn_confirm", !args.exact);
    };
    SelectManaHandStates.prototype.restoreGameState = function () { };
    return SelectManaHandStates;
}());
var SelectManaReturnDeckStates = (function () {
    function SelectManaReturnDeckStates(game) {
        this.game = game;
    }
    SelectManaReturnDeckStates.prototype.onEnteringState = function (args) {
        var _this = this;
        if (!this.game.isCurrentPlayerActive())
            return;
        this.mana_cards = [];
        var count = args.count;
        var handleHandCardClick = function (card) {
            _this.mana_cards.push(card);
            _this.game.tableCenter.manaDeck.addCard(__assign(__assign({}, card), { type: null, isHidden: true }));
            _this.game.toggleButtonEnable("btnConfirm", _this.mana_cards.length === count);
            _this.game.toggleButtonEnable("btnCancelAction", _this.mana_cards.length > 0, "gray");
        };
        var handleDeckCardClick = function (card) {
            if (_this.mana_cards.length > 0) {
                _this.moveCardFromManaDeckToHand();
                _this.game.toggleButtonEnable("btnConfirm", _this.mana_cards.length === count);
                _this.game.toggleButtonEnable("btnCancelAction", _this.mana_cards.length > 0);
            }
        };
        this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
        this.player_table.hand.onCardClick = handleHandCardClick;
        this.game.tableCenter.manaDeck.onCardClick = handleDeckCardClick;
    };
    SelectManaReturnDeckStates.prototype.onLeavingState = function () {
        this.player_table.hand.onCardClick = null;
        this.game.tableCenter.manaDeck.onCardClick = null;
    };
    SelectManaReturnDeckStates.prototype.onUpdateActionButtons = function (args) {
        var _this = this;
        var handleConfirm = function () {
            _this.game.actionManager.addArgument(_this.mana_cards.map(function (x) { return x.id; }).join(","));
            _this.game.actionManager.activateNextAction();
        };
        this.game.addActionButtonDisabled("btnConfirm", _("Confirm"), handleConfirm);
        if (args.canCancel) {
            this.game.addActionButtonClientCancel();
        }
        else {
            var handleCancel = function (evt) {
                _this.restoreGameState();
            };
            this.game.addActionButtonDisabled("btnCancelAction", _("Cancel"), handleCancel);
        }
    };
    SelectManaReturnDeckStates.prototype.restoreGameState = function () {
        this.restore();
        this.game.disableButton("btnConfirm");
        this.game.disableButton("btnCancelAction");
    };
    SelectManaReturnDeckStates.prototype.restore = function () {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!(this.mana_cards.length > 0)) return [3, 2];
                        return [4, this.moveCardFromManaDeckToHand()];
                    case 1:
                        _a.sent();
                        return [3, 0];
                    case 2: return [2];
                }
            });
        });
    };
    SelectManaReturnDeckStates.prototype.moveCardFromManaDeckToHand = function () {
        var _this = this;
        return new Promise(function (resolve) {
            var card = _this.mana_cards.pop();
            console.log("Mana pop", __assign({}, _this.mana_cards));
            _this.player_table.hand.addCard(card).then(function () {
                resolve(true);
            });
        });
    };
    return SelectManaReturnDeckStates;
}());
define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter", "ebg/stock"], function (dojo, declare) {
    return declare("bgagame.wizardsgrimoire", ebg.core.gamegui, new WizardsGrimoire());
});
