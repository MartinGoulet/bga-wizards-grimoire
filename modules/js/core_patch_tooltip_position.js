define([
    "dojo", "dojo/_base/declare",
    "dojo/dom-geometry",
    "dijit/Tooltip"
],
function (dojo, declare, domGeometry, Tooltip) {
    return declare("ebg.core.core_patch_tooltip_position", null, {
        constructor: function(){
            console.log('ebg.core.core_patch_tooltip_position constructor');
            this._checkIfPosCorrNeeded();
        },

        _checkIfPosCorrNeeded: function(){
            if (Tooltip._MasterTooltip.prototype._origShow)
                return;
            const scrollX = window.pageXOffset;
            const scrollY = window.pageYOffset;
            const el = document.createElement("div");
            el.style = 'top : 10px; left: 10px; zoom: 2.0; width: 4000px; height: 4000px; position: absolute';
            document.body.appendChild(el);
            const el2 = document.createElement("div");
            el2.style = 'top : 20px; left: 5px; zoom: 4.0; width: 10px; height: 10px; position: absolute';
            el.appendChild(el2);
            window.scroll(0,0);
            const tBox = domGeometry.position(el2);
            const bCorrPos = (tBox.x==7.5);
            console.log("_checkIfPosCorrNeeded", bCorrPos, tBox.x);
            if (bCorrPos){
                window.scroll(80,0);
                const tBox2 = domGeometry.position(el2);
                domGeometry._bCorrScroll = (tBox2.x==-72.5);
                domGeometry._origPosition = domGeometry.position;
                domGeometry._getZoom = this._getZoom;
                Tooltip._MasterTooltip.prototype._origShow = Tooltip._MasterTooltip.prototype.show;
                Tooltip._MasterTooltip.prototype.show = this._masterTT_Show;
                Tooltip._MasterTooltip.prototype._geom_position = this._geom_position;
                Tooltip._checkIfPosCorrNeeded = this._checkIfPosCorrNeeded;
                console.log("_checkIfPosCorrNeeded", domGeometry._bCorrScroll, tBox2.x);
            }
            document.body.removeChild(el);
            window.scroll(scrollX,scrollY);
        },

        _getZoom: function(node){
            if(typeof node == "string") {
                node = document.getElementById(node);
            }
            let zoom = 1.0;
            if (typeof node.style.zoom !== "undefined") {
                zoom = node.style.zoom;
                if (zoom =="")
                    zoom = 1.0;
            }
            const parent = node.parentElement; 
            if (parent)
                zoom = zoom * this._getZoom(parent);
                return zoom;
        },

        _masterTT_Show: function(innerHTML, aroundNode, position, rtl, textDir, onMouseEnter, onMouseLeave){
            domGeometry.position = this._geom_position;
            try {
                this._origShow(innerHTML, aroundNode, position, rtl, textDir, onMouseEnter, onMouseLeave);
            } finally {
                domGeometry.position = domGeometry._origPosition;
            }
        },

        _geom_position : function(/*DomNode*/ node, /*Boolean?*/ includeScroll){
            const zoom = this._getZoom(node);
            if (zoom != 1) {
                let position = this._origPosition(node, false);
                position.x = position.x*zoom;
                position.y = position.y*zoom;
                if (includeScroll) {
                    if (this._bCorrScroll){
                        position.x += window.pageXOffset*zoom;
                        position.y += window.pageYOffset*zoom;
                    } else {
                        position.x += window.pageXOffset;
                        position.y += window.pageYOffset;                            
                    }
                }
                position.w = position.w*zoom;
                position.h = position.h*zoom;
                return position;
            } 
            return this._origPosition(node, includeScroll);
        }
    });
});