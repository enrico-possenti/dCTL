jQuery.fn.mbTooltip = function (options){
	return this.each (function () {
		this.options = {
			selectors : "[tooltip]",
		}
		$.extend (this.options, options);
	/* CONFIG */		
		var xOffset = 10;
		var yOffset = 10;		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result		
	/* END CONFIG */		
		var ttEl = $(this).is(this.options.selectors)? $(this): $(this).find(this.options.selectors);
  $(ttEl).each(function(){
			var theEl=$(this);
			if (!theEl.attr("tooltip")) {
				var selectorsS = this.options.selectors.replace('[tooltip]', '').replace(/\s+/, '').replace('[', '').replace(']', '').split(',');
				for (var i=0;i<selectorsS.length;i++) {
					if (theEl.attr(selectorsS[i]) != "") {
						theEl.attr("tooltip", theEl.attr(selectorsS[i]));
					};
				};
			};
			var ttCont= theEl.attr("tooltip");

			var hover = $.browser.msie?"mouseenter":"mouseover";
			ttEl.hover(function(e){
				theEl.title = "";									  
				$("body").append("<p id='tooltip'>"+ ttCont +"</p>");
				$("#tooltip")
					.css("top",(e.pageY - xOffset) + "px")
					.css("left",(e.pageX + yOffset) + "px")
					.css("z-index","1000")
					.fadeIn("fast");	
						},
		
				function(){
					theEl.title = ttCont;		
					$("#tooltip").fadeOut("fast").remove();
				}
				);	
		
 			ttEl.mousemove(function(e){
				$("#tooltip")
					.css("top",(e.pageY - xOffset) + "px")
					.css("left",(e.pageX + yOffset) + "px")
					.css("z-index","1000")
 			});			


		});		
	})
};
  
