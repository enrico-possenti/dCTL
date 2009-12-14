/** jquery.dragable&resizeable&dropable plugin 
 * jquery.dimensions required
 * 
 * required parameter: elem - whatever that might be given to jquery / only if called directly: new dynamic(elem, options)
 * 
 * optional parameters:  $('#object').dragable({params}).resizeable({params});
 * ----------------------
 * d - enable dragging // only if called directly
 * r - enable resizing // only if called directly
 * direction, resizeDirection - 'h' or 'v'
 * noevents - do not bind event handlers (for manual use)
 * edge - {x1, x2, y1, y2}
 * start, move, end - dragable callback functions( o ) // start(o, dom event), move(o, dom event)
 * over, moveover, out, drop - dropable target callback functions( o , target jquery object )
 * resizeStart, resize, resizeEnd - resizeable callback functions( o )
 * handlerPosition - custom function(o, handler jquery object) for setting the resize handler position
 * moveHandler - move handler object / whatever that might be given to $()
 * moveHandlerOutside - bool (false by default) - tells plugin to look moveHandler (jquery selector) outside of elem object (useful with mass dragging)
 * / if not set, plugin will look inside of elem - $(moveHandler, o.elem)
 * target - collection of target objects for dropping / whatever that might be given to $()
 * renewTarget - target collection should be renewed before moving
 * tolerance - 'intersect', 'leftcorn', 'pointer' - the way two object should intersect / todo: more modes
 * clone - bool or callback(o, new jquery object) - do not drag the actual object, use clone 
 * cloneRemove - callback(o, cloned jquery object) - called before object.remove(); 
 * / if returns false, object.remove() will not be called and also the position of the original element will not be updated
 * / useful for drag'n'drop creation of objects
 * moveReplacer - jquery object / can be used instead of clone / if both set, plugin will choose replacer 
 * / clone and cloneRemove callbacks, if set, will be called too with the same logic
 * / moveReplacer will not be removed from dom anyway
 * opacity, initialOpacity - (string) double - opacity of dragged object ( default element, cloned element, or replacer element - no matter)
 * zIndex - if set, it will be used while dragging, and initial z-index from style will be restored on mouseup event
 * proportion, min, max - {w, h}
 * resizeHandlerClass - initial class for resize handler
 * parent - jquery object of parent element / if edge is not set, it will be taken from parent element
 * 
 * -- in development
 * autoscroll - bool - will be used only if parent element is set
 * scrollStep - int / 15 by default
 * scrollMargin - int / 30 by default
 * -----------------------
 * 
 * simple use:
 * $('#object').dragable().resizeable();
 * 
 * @author: johann kuindji (www.kuindji.com , www.stuffedguys.com)
 * @email: jk@kuindji.com
 */

(function($){
$.fn.extend({
    dragable: function(options) {
        if (!this[0]) return this;
        if (!options) options = {};
        for (var j = 0; j < this.length; j++) {
            if (!this[j].dynamic) {
                var options = $.fn.extend({d: true}, options);
                this[j].dynamic = new dynamic(this[j], options);
            }
            else {
                this[j].dynamic.applyOptions(options);
                this[j].dynamic.dragable();
            }
        }
        return this;
    },

    notDragable: function() {
        if (!this[0]) return this;
        for (var j = 0; j < this.length; j++) if (this[j].dynamic) this[j].dynamic.notDragable();
        return this;
    },

    resizeable: function(options) {
        if (!this[0]) return this;
        if (!options) options = {};
        for (var j = 0; j < this.length; j++) {
            if (!this[j].dynamic) {
                var options = $.fn.extend({r: true}, options);
                this[j].dynamic = new dynamic(this[j], options);
            }
            else {
                this[j].dynamic.applyOptions(options);
                this[j].dynamic.resizeable();
            }
        }
        return this;
    },

    notResizeable: function() {
        if (!this[0]) return this;
        for (var j = 0; j < this.length; j++) if (this[j].dynamic) this[j].dynamic.notResizeable();
        return this;
    },

    drag: function(ev) {
        if (!this[0]) return this;
        for (var j = 0; j < this.length; j++) if (this[j].dynamic) this[j].dynamic.mousedown(ev);
        return this;
    },

    stopDrag: function() {
        if (!this[0]) return this;
        for (var j = 0; j < this.length; j++) if (this[j].dynamic) this[j].dynamic._dragable = false;
        return this;    
    },

    dynamic: function() {
        if (!this[0] || !this[0].dynamic) return {};
        return this[0].dynamic;
    }
})})(jQuery);

function dynamic(elem, options) {
    if (!options) options = {};
    options.elem = elem;
    this.elem = null;
    this.originalElem = null;
    this.moveHandler = null;
    this.resizeHandler = null;
    this.target = null;
    this.d = false;
    this.r = false;

    this.edge = options.edge || null;
    this.parent = options.parent || null;
    this.autoscroll =  options.parent ? options.autoscroll || null : null;
    this.renewTarget = options.renewTarget || false;
    this.scrollStep = options.scrollStep || 15;
    this.scrollMargin = options.scrollMargin || 30;
    this.min = options.min || null;
    this.max = options.max || null;
    this.proportion = options.proportion || null;
    this.direction = options.direction || null;
    this.resizeDirection = options.resizeDirection || null;
    this.noevents = options.noevents || null;
    this.zIndex = options.zIndex || null;
    this.tolerance= options.tolerance || 'intersect';
    this.clone = options.clone || false;
    this.cloneRemove = options.cloneRemove || null;
    this.moveReplacer = options.moveReplacer || null;
    this.opacity = options.opacity || null;
    this.initialOpacity = options.initialOpacity || null;

    this.start = options.start || null;
    this.move = options.move || null;
    this.end = options.end || null;

    this.over = options.over || null;
    this.moveover = options.moveover || null;
    this.out = options.out || null;
    this.drop = options.drop || null;

    this.resizeStart = options.resizeStart || null;
    this.resize = options.move || null;
    this.handlerPosition = options.handlerPosition || null;
    this.resizeEnd = options.resizeEnd || null;

    this.options = options;
    this._dragable =false;
    this._resizeable = false;
    this.prevX = 0;
    this.prevY = 0;
    this.left = 0;
    this.top = 0;
    this.w = 0;
    this.h = 0;
    this.prevRX = 0;
    this.prevRY =0;
    this.initialIndex = null;
    this.targetMatrix = null;
    this.currentTarget = null;
    this.prevPosition = null;
    this.prevOpacity = null;
    this.bodyParent = false;

    extendDynamic(this);
}

function extendDynamic(o) {

    o.applyOptions = function(options) {
        if (!options) return false;
        for (var i in options) o[i]=options[i];
        o.options = $.extend(o.options, options);
    }

    // common functions
    o.setPosition = function( anyway ) {
        if ( !o.direction || anyway ) return o.elem.css({left: o.left, top: o.top});
        if ( o.direction == 'h' ) return o.elem.css('left', o.left);
        if ( o.direction == 'v' ) return o.elem.css('top', o.top);
    }

    o.checkPosition = function() {
        if (!o.edge) return false;
        var shifted = false;
        if ( o.autoscroll && o.parent ) { // not finished yet
            if ( o.left < o.edge.x1 + o.scrollMargin ) {
                o.parent.get(0).scrollLeft -= o.scrollStep;
                if (o.bodyParent) { o.edge.x1 -= o.scrollStep; o.edge.x2 -= o.scrollStep; }
            }
            if ( o.top < o.edge.y1 +  o.scrollMargin ) {
                o.parent.get(0).scrollTop -= o.scrollStep;
                if (o.bodyParent) { o.edge.y1 -= o.scrollStep; o.edge.y2 -= o.scrollStep; }
            }
            if ( o.left + o.w > o.edge.x2 - o.scrollMargin ) {
                o.parent.get(0).scrollLeft += o.scrollStep;
                if (o.bodyParent) { o.edge.x2 += o.scrollStep; o.edge.x1 += o.scrollStep; }
            }
            if ( o.top + o.h > o.edge.y2 -  o.scrollMargin ) {
                o.parent.get(0).scrollTop += o.scrollStep;
                if (o.bodyParent) { o.edge.y2 += o.scrollStep; o.edge.y1 += o.scrollStep; }
            } 
            if (o.edge.x1 < 0) o.edge.x1 = 0;
            if (o.edge.x2 < 0) o.edge.x2 = 0;
            if (o.edge.y1 < 0) o.edge.y1 = 0;
            if (o.edge.y2 < 0) o.edge.y2 = 0;
        }
        if ( o.left < o.edge.x1 ) { o.left = o.edge.x1; shifted = true; }
        if ( o.top < o.edge.y1 ) { o.top = o.edge.y1; shifted = true; }
        if ( o.left + o.w > o.edge.x2 ) { o.left = o.edge.x2 - o.w; shifted = true; }
        if ( o.top + o.h > o.edge.y2 ) {o.top = o.edge.y2 - o.h; shifted = true; }
        return shifted;
    }

    o.updatePosition = function() {
        var ofs = o.elem.offset();
        o.left = ofs.left;
        o.top = ofs.top;
        if (o.edge) { o.updateSize(); o.setPosition( o.checkPosition() ); }
        else o.setPosition();
        return true;
    }

    o.updateSize = function() {
        o.w = o.elem.width();
        o.h = o.elem.height();
    }

    o.setSize = function(w, h) {
        var corrected = false;
        if (!w || w < 1) w = 1;
        if (!h || h < 1) h = 1;
        if (o.min) {
            if ( w < o.min.w ) {w = o.min.w; corrected = true; }
            if ( h < o.min.h) {h = o.min.h; corrected = true; }
        }
        if (o.max) {
            if ( w > o.max.w ) {w = o.max.w; corrected = true; }
            if ( h > o.max.h ) {h = o.max.h; corrected = true; }           
        }        
        if (o.proportion) {
            if (w >= h) {
                var h1 = Math.ceil( (w*o.proportion.h)/o.proportion.w )
                if (h1 != h) correted = true;
                h = h1;
            }
            if (h > w ) {
                var w1 = Math.ceil( (h*o.proportion.w)/o.proportion.h )
                if (w1 != w) corrected = true;
                w = w1;
            }
        }
        if (o.edge) {
            if ( o.left + w > o.edge.x2 ) { w = o.edge.x2 - o.left; corrected = true; }
            if ( o.top + h > o.edge.y2 ) { h = o.edge.y2 - o.top; corrected = true; }
        }
        o.w = w;
        o.h = h;
        o.elem.css({width: o.w, height: o.h});
        if (corrected && o.edge && o.checkPosition()) o.setPosition();
        return corrected;
    }

    // drop functions
    o.prepareTargetMatrix = function() {
        o.targetMatrix = [];
        o.target.each ( function() {
            var t = $(this);
            var ofs = t.offset();
            var w = t.outerWidth();
            var h = t.outerHeight();
            this.targetPosition = {x1: ofs.left, x2: ofs.left+w, y1: ofs.top, y2: ofs.top+h, t: this};
            var inx = o.targetMatrix.push(this.targetPosition);
        })
        if (o.targetMatrix.length == 0) o.targetMatrix = null;
    }

    o.intersect = function(coord) {
        if (coord.t == o.elem[0] || (o.originalElem && coord.t == o.originalElem[0]) || (coord.t.dynamic && coord.t.dynamic._dragable)) return false;
        switch (o.tolerance ) {
        case 'intersect': 
            if ( o.left+o.w < coord.x1 || o.left > coord.x2 ) return false;
            if ( o.top+o.h < coord.y1 || o.top > coord.y2 ) return false;
            return true;
        case 'leftcorn': 
            if ( o.left > coord.x1 && o.left < coord.x2 && o.top > coord.y1 && o.top < coord.y2 ) return true;
            return false;
        case 'pointer':
            if ( o.prevX >= coord.x1 && o.prevX <= coord.x2 && o.prevY >= coord.y1 && o.prevY <= coord.y2 ) return true;
            return false;
        } 
        return false;
    }

    o.checkTargetMatrix = function(ev) {
        if (!o.targetMatrix) return false;
        var e = o.currentTarget ? o.currentTarget.get(0) : false;
        if (o.currentTarget) { //check if still over the object
            if (o.currentTarget.get(0).targetPosition) {
                if ( !o.intersect(o.currentTarget.get(0).targetPosition)) o.currentTarget = null;
            }
            else o.currentTarget = null;
        }
        if (!o.currentTarget) { 
            if (e && o.out) o.out (o, $(e));
            for (var i = 0; i < o.targetMatrix.length; i++) if (o.intersect(o.targetMatrix[i])) o.currentTarget = $(o.targetMatrix[i].t);
            if (o.currentTarget && o.over) o.over(o, o.currentTarget);
        }
    }

    // move functions
    o.createClone = function(x, y) {
        o.prevPosition = 'absolute';
        o.originalElem = o.elem;
        if (!o.moveReplacer) {
            o.elem = o.elem.clone();
            o.elem.hide().appendTo('body').css({position: 'absolute', left: o.left, top: o.top}).show();
        }
        else {
            o.elem = o.moveReplacer;
            o.left = x - Math.ceil(o.elem.width()/2);
            o.top = y - Math.ceil(o.elem.height()/2);
            o.elem.css({position: 'absolute', left: o.left, top: o.top}).show();
        }
        if ( typeof(o.clone) == 'function') o.clone(o, o.elem);
    }

    o.removeClone = function() {
        if (!o.originalElem) return false;
        if (o.cloneRemove && !o.cloneRemove(o, o.elem) ) {
            o.elem = o.originalElem;
            o.originalElem = null;
            return false;
        }
        o.originalElem.css({left: o.left, top: o.top})
        if (!o.moveReplacer) o.elem.remove();
        else o.elem.hide();
        o.elem = o.originalElem;
        o.originalElem = null;
        o.updatePosition();
    }

    o.mousedown = function (ev) {
        if (o._dragable) return false;
        o.prevX = ev.clientX+document.body.scrollLeft;
        o.prevY = ev.clientY+document.body.scrollTop;
        var ofs = o.elem.offset();
        o.left = ofs.left;
        o.top = ofs.top;
        if ( o.clone || o.moveReplacer ) o.createClone(o.prevX, o.prevY);
        if ( o.zIndex ) {
            o.initialIndex = o.elem.css('z-index')  ||  1;
            o.elem.css('z-index', o.zIndex);
        }
        o._dragable = true;
        if (o.opacity) o.elem.css('opacity', o.opacity);
        if (o.renewTarget) o.target = $(o.options.target);
        if (o.target && o.target.length > 0) o.prepareTargetMatrix();
        if (o.resizeHandler) o.toggleHandler( false );
        if (!o.prevPosition) o.prevPosition = o.elem.css('position');
        if (o.prevPosition != 'absolute') o.elem.css({position: 'absolute', left: o.left, top: o.top})
        if (o.start) o.start(o, ev);
        return false;
    }

    o.onmove = function(ev) {
        if (!o._dragable) return false;
        var mx = ev.clientX+document.body.scrollLeft;
        var my = ev.clientY+document.body.scrollTop;
        o.left += mx - o.prevX;
        o.top += my - o.prevY;
        o.prevX = mx;
        o.prevY = my;
        if (o.edge) o.checkPosition();
        o.setPosition();
        if (o.targetMatrix) o.checkTargetMatrix();
        if (o.move) o.move(o, ev);
        if (o.targetMatrix && o.currentTarget && o.moveover) o.moveover( o, o.currentTarget , ev);
        return true;
    }

    o.onmoveend = function() {
        if (!o._dragable) return false;
        o._dragable = false;
        if (o.clone || o.moveReplacer) o.removeClone();
        if (o.opacity && !o.clone) o.elem.css('opacity', o.initialOpacity ? o.initialOpacity : '1');
        if (o.initialIndex) o.elem.css('z-index', o.initialIndex);
        if (o.resizeHandler) {
            o.setHandlerPosition();
            o.toggleHandler( true );
        } 
        if (o.currentTarget && o.drop) o.drop(o, o.currentTarget);
        o.currentTarget = null;
        if (o.prevPosition != 'absolute') o.elem.css('position', o.prevPosition);
        if (o.end) o.end(o);
        return false;
    }

    // resize functions
    o.onResizeStart = function() {
        if (o._resizeable) return false;
        o.prevW = o.w;
        o.prevH = o.h;
        o.prevRX = o.resizeHandler.prevX;
        o.prevRY = o.resizeHandler.prevY;
        if (o.resizeStart) o.resizeStart(o);
        o._resizeable = true;
        return false;
    }

    o.onResizeMove = function() {
        if (!o._resizeable) return false;
        var w = o.w;
        var h = o.h;
        if ( o.resizeDirection != 'h') w += o.resizeHandler.prevX - o.prevRX;
        if ( o.resizeDirection != 'v') h += o.resizeHandler.prevY - o.prevRY;
        o.prevRX = o.resizeHandler.prevX;
        o.prevRY = o.resizeHandler.prevY;
        o.setSize(w, h);
        o.setHandlerPosition();
        if (o.targetMatrix) o.checkTargetMatrix();
        if (o.resize) o.resize(o);
        if (o.targetMatrix && o.currentTarget && o.moveover) o.moveover( o, o.currentTarget );
        return false;
    }

    o.onResizeEnd = function() {
        if (o.resizeEnd) o.resizeEnd(o);
        o._resizeable = false;
        return false;
    }

    o.setHandlerPosition = function() {
        if (o.handlerPosition) return o.handlerPosition( o, o.resizeHandler );
        o.resizeHandler.elem.css({left: (o.left + (o.w - Math.ceil(o.resizeHandler.w/2))), top: ( o.top + (o.h - Math.ceil(o.resizeHandler.h/2)))  });
    }

    o.toggleHandler = function(state) { o.resizeHandler.elem[ state? 'show':'hide' ](); }

    // init
    o.element = function(elem, parent) {
        if ( typeof(elem) == 'undefined') return null;
        if ( typeof(elem) == 'string') return $(elem, parent);
        if (elem.jquery) return elem;
        return $(elem);
    }

    o.dragable = function() {
        $( function() {
        if (o.d) return false;
        o.moveHandler =  o.options.moveHandler ? o.element(o.options.moveHandler, o.options.moveHandlerOutside ? null : o.elem) : o.elem;
        o.moveHandler.bind('mousedown', o.mousedown);
        $(o.elem.get(0).ownerDocument).bind('mousemove', o.onmove);
        $(o.elem.get(0).ownerDocument).bind('mouseup', o.onmoveend);
        if (!o.autoscroll) $(o.elem.get(0).ownerDocument).bind('scroll', o.onmoveend);
        if (o.parent && !o.edge) {
            if (o.parent.get(0).tagName.toLowerCase()=='body') {
                o.edge = {x1:0, x2: document.body.clientWidth, y1: 0, y2: document.body.clientHeight};
                o.bodyParent = true;
            }
            else {
                var ofs = o.parent.offset();
                o.edge = {x1: ofs.left, x2: ofs.left+o.parent.width(), y1: ofs.top, y2: ofs.top+o.parent.height()};
            }
        }
        o.d = true;
        }) 
    }

    o.notDragable = function() {
        if (!o.d) return false;
        o.moveHandler.unbind('mousedown', o.mousedown);
        $(o.elem.get(0).ownerDocument).unbind('mousemove', o.onmove);
        $(o.elem.get(0).ownerDocument).unbind('mouseup', o.onmoveend);
        $(o.elem.get(0).ownerDocument).unbind('scroll', o.onmoveend);
        o.target = null;
        o.moveHandler = null;
        o.d = false;
    }

    o.resizeable = function() {
        $(function() {
        if (o.r) return false;
        o.resizeHandler = new dynamic($('<div></div>')[0], {edge: o.edge, start: o.onResizeStart, move: o.onResizeMove, end:o.onResizeEnd, 
                                                                                                       noevents: o.noevents, direction: o.resizeDirection, d: true});
        $('body').append(o.resizeHandler.elem);
        if (o.options.resizeHandlerClass) o.resizeHandler.elem.addClass(o.options.resizeHandlerClass);
        else o.resizeHandler.elem.css({backgroundColor: '#ffffff', width: '6px', height: '6px', 
                                                    position: 'absolute', cursor: 'nw-resize', zIndex: '999', borderWidth:'1px', borderColor: '#000', borderStyle: 'solid'});
        o.resizeHandler.updateSize();
        o.resizeHandler.checkPosition();
        o.setSize(o.w, o.h);
        o.setHandlerPosition();
        o.r = true;
        })
    }

    o.notResizeable = function() {
        if (!o.r) return false;
        o.resizeHandler.notDragable();
        o.resizeHandler.elem.remove();
        o.resizeHandler = null;
        o.r = false;
    }

    $(function() {
        o.elem =  o.element( o.options.elem );
        o.updateSize();
        o.updatePosition();
        if ( o.noevents ) return false;
        if ( typeof (o.elem.get(0) ) == 'undefined') return false;
        o.target =  o.options.target ?  $(o.options.target) : null;
        if (o.options.d) o.dragable();
        if (o.options.r) o.resizeable();
    })
}