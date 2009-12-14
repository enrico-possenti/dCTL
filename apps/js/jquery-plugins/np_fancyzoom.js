/**
* jQuery fancyzoom plugin.
* This is an adaptation of the fancyzoom effect as a jQuery plugin
*
* Author: Mathieu Vilaplana <mvilaplana@df-e.com>
* Date: March 2008
* rev 1.0
* rev: 1.1
* Add title if alt in the img
* rev 1.2
* Correction of the image dimension and close button on top right of the image
* rev 1.3
* now fancyzoom can be apply on an image, no need any more link wrapper
* rev 1.4 correct the bug for the overlay in ie6
*/
(function($) {
// => NOVEOPIU
	var strImgDir = g_CSS_IMG;
// <= NOVEOPIU
   	var oImgZoomBox=$('<div>');
 	var oImgClose = $('<img>').css({position:'absolute',top:0,left:0,cursor:'pointer',zIndex:10101});
	
	$.fn.fancyzoom = function(userOptions) {
		//the var to the image box div
	 	var oOverlay = $('<div>').css({
			height: '100%',
			width: '100%',
   			position:'fixed',
			left: 0,
			top: 0,
			cursor:"wait",
			zIndex:10001
		});
		function openZoomBox(imgSrc,o){
			if(o.showoverlay) {
				oOverlay
					.prependTo('body')
					.click(function(){closeZoomBox(o);});
				if( $.browser.msie && $.browser.version < 7 ){
					oOverlay.css({position:'absolute',height:$(document).height(),width:$(document).width()});
				}
			}

            //calculate the start point of the animation, it start from the image of the element clicked
            pos=imgSrc.offset();
			o=$.extend(o,{dimOri:{width:imgSrc.outerWidth(),height:imgSrc.outerHeight(),left:pos.left,top:pos.top,'opacity':0}});

			//calculate the end point of the animaton
            var oImgDisplay = $('img', oImgZoomBox);
			oImgZoomBox.css({'opacity':0,'text-align':'center','border':'0px solid red', 			zIndex:10100}).appendTo('body');
			var iWidth = oImgZoomBox.outerWidth();
			var iHeight = oImgZoomBox.outerHeight();

			
			//the target is in the center without the extra margin du to close Image
			dimBoxTarget=$.extend({},{width:iWidth,height:iHeight,'opacity':1}, __posCenter((iWidth+15),(iHeight+30)));
            
            //place the close button at the right of the zoomed Image
            oImgClose.css({left:(dimBoxTarget.left+ dimBoxTarget.width-22-(dimBoxTarget.width-oImgDisplay.width())/2),top:dimBoxTarget.top});
            
            var $fctEnd = function(){
            	//end of open, show the shadow
            	if($.fn.shadow && !$.browser.msie){ $('img:first',oImgZoomBox).shadow(o.shadowOpts);}
				if(o.speed>0 && !$.browser.msie) {oImgClose.fadeIn('slow');$('div',oImgZoomBox).fadeIn('slow');}
				else {oImgClose.show();$('div',oImgZoomBox).show();}
            };
            //cache le titre
            $('div',oImgZoomBox).hide();
  			if(o.speed > 0) {
  				oImgZoomBox.css(o.dimOri).animate(dimBoxTarget,o.speed,$fctEnd);
  			}
  			else {
  				oImgZoomBox.css(dimBoxTarget);
  				$fctEnd();
  			}
	 	 }//end openZoomBox
 	 	 
 	 	 /**
 	 	  * First hide the closeBtn, then remove the ZoomBox and the overlay
 	 	  * Animate if speed > 0 
 	 	  */
 	 	 function closeZoomBox(o){
	 	 	oImgClose.remove();
		 	 if(o.speed > 0){
		 	 	oImgZoomBox.animate(o.dimOri,o.speed,function(){
			 		$(this).empty().remove();
		 		});
				if(o.showoverlay) {oOverlay.animate({'opacity':0},o.speed,function(){$(this).empty().remove();});}
	 	 	}else {
			 	oImgZoomBox.empty().remove();
				if(o.showoverlay) {oOverlay.empty().remove();}
	 	 	}
 	 	 }
    		
		/**
		 * The plugin chain.
		 */
   		return this.each(function() {
   			var $this = $(this);
   			var imgTarget = $this.is('img')?$this:$('img:first',$this);
   			var imgTargetSrc=null;
   			if($this.attr('href')){
   				imgTargetSrc = $this.attr('href');
   			}
   			if($this.is('img')){
// => NOVEOPIU
imgTargetSrc = $this.attr('rel');
// <= NOVEOPIU
$this.css('cursor','pointer');
   			}
			// build main options before element iteration		
	    	var opts = $.extend({},$.fn.fancyzoom.defaultsOptions, userOptions||{},{dimOri:{},
	    		oImgZoomBoxProp:{position:'absolute',left:0,top:0}
	    	});
	    	oOverlay.css({
				opacity: opts.overlay,
				background:opts.overlayColor
	    	});

   			//make action only on link that point to an href
   			if(!/\.jpg|\.png|\.gif/i.test(imgTargetSrc) || ($('img',$this).size()===0 && !$this.is('img'))){
	   			return;
   			}
   			$this.click(function(){
   				if(oLoading && oLoading.is(':visible') || timerLoadingImg){
   					//if user click on an other image, cancel the previous loading
					if(oImgZoomBox && $('img:first',oImgZoomBox).attr('src') != imgTargetSrc){
	   					__cancelLoading();
					}
	   				else {//solve the double click pb
	   					return false;
	   				}
   				}
   				var o = $.extend({},opts,userOptions);
   				if(oImgZoomBox && oImgZoomBox.parent().size()>0){
   					var imCurrent = $('img:first',oImgZoomBox);
   					if(imgTargetSrc == imCurrent.attr('src')){
						//calculate the start point of the animation, it start from the image of the element clicked
						pos=imgTarget.offset();
						o=$.extend(
							o,
							{dimOri:{width:imgTarget.outerWidth(),height:imgTarget.outerHeight(),left:pos.left,top:pos.top,'opacity':0}}
							);
							closeZoomBox(o);
							return false;
   					}else {
   						//user click on an other image, destroy it
   						oImgClose.remove();
   						oImgZoomBox.empty().remove();	
   					}
   				}
   				
   				//remove the overlay and Reset
		 	 	if(o.showoverlay && oOverlay) {oOverlay.empty().remove().css({'opacity':o.overlay});}
				
				//reset the img close and fix png on it if plugin available
				oImgClose.attr('src',o.imgDir+'closebox.png').appendTo('body').hide();
				if($.fn.ifixpng) {$.ifixpng(o.imgDir+'blank.gif');oImgClose.ifixpng(o.imgDir+'blank.gif');}
				oImgClose.unbind('click').click(function(){closeZoomBox(o);});

				//reset zoom box prop and add image zoom with a margin top of 15px = imgclose height / 2
   				oImgZoomBox=$('<div>').empty().css(o.oImgZoomBoxProp);
   				var strTitle = imgTarget.attr('alt');
   				if(strTitle){
   					var oTitle = $('<div><center><table height=0 border="0" cellspacing=0 cellpadding=0><tr><td></td><td class="fancyTitle">'+strTitle+'</td><td></td></table></center></div>').css({marginTop:10,marginRight:15});
   					
   					var tdL = oTitle.find('td:first').css({'background':'url('+o.imgDir+'zoom-caption-l.png)',width:'13px',height:'26px'});
   					var tdR = oTitle.find('td:last').css({'background':'url('+o.imgDir+'zoom-caption-r.png)',width:'13px',height:'26px'});
   					var tdC = $('.fancyTitle',oTitle).css({'background':'url('+o.imgDir+'zoom-caption-fill.png)',
   							'padding':'0px 20px',
   							color:'#FFF',
   							'font-size':'14px'
   							});

   					if($.fn.ifixpng){
   						tdL.ifixpng(o.imgDir+'blank.gif');
   						tdR.ifixpng(o.imgDir+'blank.gif');
   						tdC.ifixpng(o.imgDir+'blank.gif');
   					}
   					oTitle.appendTo(oImgZoomBox);   					
   				}
   				var oImgZoom=$('<img />').attr('src',imgTargetSrc).css({zIndex:100,marginTop:15,marginRight:15}).click(function(){closeZoomBox(o);}).prependTo(oImgZoomBox);
				
				//be shure that the image to display is loaded open the zoom box, if not display a loading Image.
   				var imgPreload = new Image();
   				imgPreload.src = imgTargetSrc;
   				var $fctEndLoading = function(){
					if(bCancelLoading) {bCancelLoading=false;}
					else {
						if(__getFileName(imgPreload.src) == __getFileName($('img:first',oImgZoomBox).attr('src')) ){
							fctCalculateImageSize();
							openZoomBox(imgTarget, o);
							__stoploading();
						}
					}
   				};
   				var fctCalculateImageSize = function () {
   					//calcul de la taille de l'image
   					var divCalculate = $('<div></div>').css({position:'absolute','top':0,'left':0,opacity:0,'border':'0px solid red'});
   					oImgZoom.appendTo(divCalculate);
					divCalculate.appendTo('body');
					imWidth = oImgZoom.width();
					imHeight = oImgZoom.height();
					maxWidth = $(window).width()*0.9;
					maxHeight = $(window).height()*0.8;
					if( maxHeight < imHeight ){
      oImgZoom.css('height', maxHeight);
					}else if( maxWidth < imWidth ){
						oImgZoom.css('width', maxWidth);
					}
					divCalculate.remove();
   					oImgZoom.prependTo(oImgZoomBox);
   				};
   				
   				if(imgPreload.complete)	{
   					fctCalculateImageSize();
   					openZoomBox(imgTarget, o);	
	   				/*__displayLoading(imgPreload);
	   				setTimeout($fctEndLoading,4000);*/
   				}
	   			else {
	   				__displayLoading();
	   				imgPreload.onload = function(){
	   					//when loading is finish display the zoombox if user not click on cancel
	   					$fctEndLoading();
	   				};
	   			}
        
   				return false;		
   			});
   		}
   	);//end return this
    };//end Plugin

    
    //Default Options
    $.fn.fancyzoom.defaultsOptions = {
    	overlayColor: '#000',
    	overlay: 0.6,
    	showoverlay:false,
    	speed:400,
    	shadowOpts:{ color: "#000", offset: 4, opacity: 0.2 },
    	imgDir:strImgDir
 	 };
 	 
	function __posCenter(iWidth,iHeight){
		var iLeft = ($(window).width() - iWidth) / 2 + $(window).scrollLeft();
		var iTop = ($(window).height() - iHeight) / 2 + $(window).scrollTop();
		iLeft=(iLeft < 0)?0:iLeft;
		iTop=(iTop < 0)?0:iTop;
	  		return {left:iLeft,top:iTop};
    }
    
    //
    // LOADING MANAGEMENT
    //
    var oLoading =null ;
	var bCancelLoading = false;
	var timerLoadingImg = null;
	function __displayLoading(){
		if(!oLoading){
			oLoading = $('<div></div>').css({width:50,height:50,position:'absolute','background':'transparent',
			opacity:8/10,color:'#FFF',padding:'5px','font-size':'10px'});
		}
		oLoading.css(__posCenter(50,50)).html('<img src="'+$.fn.fancyzoom.defaultsOptions.imgDir+'blank.gif" />').click(function(){__cancelLoading();}).appendTo('body').show();
		timerLoadingImg=setTimeout(__changeimageLoading,400);
	}
	function __cancelLoading(){
		bCancelLoading=true;
		__stoploading();
	}
	function __stoploading(){
		oLoading.hide().remove();
		if(timerLoadingImg){
			clearTimeout(timerLoadingImg);
			timerLoadingImg=null;
		}
	}
	
	/**
	 * Animate the png loading image.
	 */
	function __changeimageLoading(){
		if(!oLoading.is(':visible')){
			timerLoadingImg=null;
			return;
		}
		
		var $im=$('img',oLoading);
		//First call im.src ="", set it to the fire png zoom spin
		if(!$im.attr('src') || /blank\.gif/.test($im.attr('src'))){
			strImgSrc = $.fn.fancyzoom.defaultsOptions.imgDir+"zoom-spin-1.png";
		}
		//rotate the im src until 12
		else {
			tab = $im.attr('src').split(/[- .]+/);
			iImg = parseInt(tab[2]);
			iImg = (iImg < 12)? (iImg+1):1;
			strImgSrc= tab[0]+"-"+tab[1]+"-"+iImg+"."+tab[3];
		}
		var pLoad = new Image();
		pLoad.src=strImgSrc;
		var $fct = function (){
			oLoading.css(__posCenter(50,50));
			$im.attr('src',strImgSrc);
			timerLoadingImg = setTimeout(__changeimageLoading,100);
		};
		//to preserve bug if img not exist change it only if load complete.
		if(pLoad.complete){$fct();}
		else{pLoad.onload=$fct;}
	}
 	
 	function __getFileName(strPath){
 		if(!strPath) {return false;}
		var tabPath = strPath.split('/');
		return ((tabPath.length<1)?strPath:tabPath[(tabPath.length-1)]);		
 	}
 	
})(jQuery);