/*
 * jQuery unwrap plugin
 * GPL (GPL-LICENSE.txt) licenses.
 * Created by: Tiziano Treccani
 *
 * $Date: 2008-1-06 12:00:00
 *
 * Version: 1.0
 */
 
(function($){

	$.fn.unwrap = function(elem){
		var elements;
		
		if (elem == null){
			elements = jQuery(this);
		}
		else if (typeof elem == "string"){
			elements = jQuery(this).find(elem);
		}
		else if (typeof elem == "object"){
			elements = elem;
		}
		else alert("unknow elem");
		
		elements.each(function(){
			jQuery(this).parent().replaceWith(jQuery(this));
		});
	}

})(jQuery);  