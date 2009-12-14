/* * * * * * * */
/* GENERAL FUNCTIONS */
/* * * * * * * */
(function($){ 
  $.fn.accordion = function(item, level, stick) { 
   var speed = 'fast';
   if (!level) level = '';
   if (!stick) stick = false;
			$('.accordion_head' + level, $(this)).click(function(evt) {
				$('.accordion_body', $(this)).slideUp(speed);
				var item = $('.accordion_head' + level, $(this));
				item.css('background-image', x).replace('opened','collapsed');
				if ($(this).next('.accordion_body').is(':hidden')) {
				 $(this).css('background-image', x).replace('opened','collapsed');
					$(this).next('.accordion_body').slideToggle(speed);
				};
			});
  };
	})(jQuery); 
/* * * * * * * */
(function($){ 
  $.fn.collapsible = function(item, level, structure) { 
   var speed = 'fast';
   if (!level) level = '';
   if (!structure) structure = 'flat';
   $('.collapsible_body', $(this)).slideUp();   
			$('.collapsible_handle' + level, $(this)).click(function(evt) {
			 if (structure == 'flat') {
			  obj = $(this).next('.collapsible_body:first');
			 } else {
			  obj = $('.collapsible_body:first', $(this).parents('.widget:first'));
			 };
				if ($(obj).is(':hidden')) {
					$(this).css('background-image', $(this).css('background-image').replace('collapsed','opened'));
} else {
					$(this).css('background-image', $(this).css('background-image').replace('opened', 'collapsed'));
				};
				$(obj).slideToggle(speed);
			});
  };
	})(jQuery); 
/* * * * * * * */
	
/* * * * * * * */
(function($){ 
  $.fn.waiting = function() { 
   return $(this).html('<div class="spinner"><img src="'+g_CSS_IMG+'snake_transparent.gif"/></div>');
  };
	})(jQuery); 
/* * * * * * * */



/* * * * * * * */
/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
(function($){ 
  $.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }

  };
	})(jQuery); 
/* * * * * * * */

/* READY */
/* * * * * * * */
$().ready(function() {
	/* * * * * * * */
	// External link.
	this.blankwin = function(){
	var hostname = window.location.hostname;
	hostname = hostname.replace("www.","").toLowerCase();
	var a = document.getElementsByTagName("a");	
	this.check = function(obj){
		var href = obj.href.toLowerCase();
		return (href.indexOf("http://")!=-1 && href.indexOf(hostname)==-1) ? true : false;				
	};
	this.set = function(obj){
		obj.target = "_blank";
		obj.className = "external";
	};	
	for (var i=0;i<a.length;i++){
		if(check(a[i])) set(a[i]);
	};		
};
	/* * * * * * * */
});
