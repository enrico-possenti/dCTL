/**
 * @author Huy Dinh
 * @date Sept 2008
 * @version 0.1
 * 
 * A plug in to show a magnify effect on images
 */
(function ($) {
	var zoomImageContainerCentreX,
		zoomImageContainerCentreY,
		zoomImageContainerHeight,
		zoomImageContainerWidth,
		thumbImageOffsetX,
		thumbImageOffsetY,
		sensorWidth,
		sensorHeight,
		zoomImageWidth,
		zoomImageHeight,
		ratioW, ratioH, magnifyTimeout,
		zoomImage = $("<img style='position:absolute'/>"),
		zoomImageContainer = $("<div id='hud-magnifier' style='overflow:hidden;position:absolute;display:none;'></div>").append(zoomImage),
// => NOVEOPIU
//   	sensor = $("<div style='z-index:10000;position:absolute;top:0;left:0;background-color:#fff;opacity:0;filter:Alpha(Opacity=0);cursor:crosshair;'></div>").mousemove(function(e) {
// <= NOVEOPIU
		sensor = $("<div style='z-index:10000;position:absolute;top:0;left:0;background-color:#fff;opacity:0;filter:Alpha(Opacity=0);cursor:crosshair;'></div>").mousemove(function(e) {
			zoomImageContainer.css({
				left:e.pageX - zoomImageContainerCentreX,
				top:e.pageY - zoomImageContainerCentreY
			});
			zoomImage.css({
				left:((e.pageX - thumbImageOffsetX) / sensorWidth) * -(zoomImageWidth - zoomImageContainerWidth) ,
				top:((e.pageY - thumbImageOffsetY) / sensorHeight) * -(zoomImageHeight - zoomImageContainerHeight) 
			});
		}).bind("mouseleave", function() {
			$('#magnify-overlay').remove();
			clearTimeout(magnifyTimeout);
			zoomImageContainer.hide();
			sensor.hide();
			$('.magnifyActive').removeClass('magnifyActive').each(function(){
 			//$(this).height('100%').width('100%').resizeImg().attr('src', $(this).attr('src')+'?');
 			return $(this);
			});
		});

	$(function() {
		$("body").append(zoomImageContainer).append(sensor);
		zoomImageContainerWidth = zoomImageContainer.width();
		zoomImageContainerHeight = zoomImageContainer.height();
		zoomImageContainerCentreX = zoomImageContainerWidth * .5;
		zoomImageContainerCentreY = zoomImageContainerHeight * .5;
	});

	$.fn.magnify = function() {
		clearTimeout(magnifyTimeout);
		magnifyTimeout = setTimeout(function() {sensor.mouseout();}, 5*1000);
		var thumbImage = this;
		
		if (! thumbImage.is('.magnifyActive')) {
			thumbImage.addClass('magnifyActive');
			fullWidth = this.width();
			fullHeight = this.height();
			thumbContainer = this.parent().css({
				position:"relative"
			});
			
			thumbImage.before('<div id="magnify-overlay" style="position:absolute;top:'+(thumbImage.position().top+10)+'px;left:'+(thumbImage.position().left-7)+'px;width:'+thumbImage.width()+'px;padding:10px;height:'+thumbImage.height()+'px;z-index:100;float:left;" onmouseover="$(this).remove();" />')
			
			thumbImage.data("fullWidth", this.width());
			thumbImage.data("fullHeight", this.height());
			this.attr("alt","").bind("mouseenter", function(){
				if ($(this).is('.magnifyActive')) {
				$('#magnify-overlay').remove();
				clearTimeout(magnifyTimeout);
				sensorWidth = thumbImage.width();
				sensorHeight = thumbImage.height();
				thumbImageOffsetX = thumbImage.offset().left;
				thumbImageOffsetY = thumbImage.offset().top;
	// => NOVEOPIU
	//  			zoomImage.attr("src", thumbImage.attr("src"));
	// 				zoomImageWidth = thumbImage.data("fullWidth"),
	// 				zoomImageHeight = thumbImage.data("fullHeight"),
	// => NOVEOPIU
			zoomImage.load(function() {
				ratioW = this.width / thumbImage.width();
				ratioH = this.height / thumbImage.height();
			
	// <= NOVEOPIU
				zoomImageWidth = thumbImage.data("fullWidth") * ratioW,
				zoomImageHeight = thumbImage.data("fullHeight") * ratioW, //ratioH
	// <= NOVEOPIU
				sensor.css({
					width:sensorWidth + "px",
					height:sensorHeight + "px",
					left:thumbImageOffsetX + "px",
					top:thumbImageOffsetY + "px"
				}).show();
			}).attr("src", thumbImage.attr("rel"));
			sensor.show();
			zoomImageContainer.fadeIn("fast");
			};
		});
		
// 		var img = new Image();
// 		$(img).load(function () {			
// 			if (thumbImage.width() > thumbImage.height()) {
// 				thumbImage.height(thumbImage.height() / (thumbImage.width() / thumbContainer.width())).width(thumbContainer.width()).css({
// 					bottom:0
// 				});
// 			} else {
// 				var thumbImageWidth = thumbImage.width() / (thumbImage.height() / thumbContainer.height());
// 				thumbImage.width(thumbImageWidth).height(thumbContainer.height()).css({
// 					marginLeft:((thumbContainer.width() - thumbImageWidth) * .5) + "px"
// 				});
// 			};
// 		}).attr('src', thumbImage.attr("rel"));

 }; 
	return this;
	}
})(jQuery);
