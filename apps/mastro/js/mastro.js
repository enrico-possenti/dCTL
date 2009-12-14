/**
 +----------------------------------------------------------------------+
 | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
 +----------------------------------------------------------------------+
 | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
 | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
 |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
 +----------------------------------------------------------------------+
 | A js file for "dCTL"                                                 |
 +----------------------------------------------------------------------+
*/

var initIdxPalette = 1;
var initIdxTabize = 1;

var activeLayerID;
var activeContextNode;
var activePanelN;

var panelW_init;
var panelW_hidden = 0;
var panelW_collapsed = 25;
var panelW_initDisplay = 290;
var panelW_initRetrieve = 359;

var costePath = '../../data/db/coste/';
var tooltipSelectors = "[tooltip], [title]";

var retrieveFormOptions = {
	target:        '#retrieveview1',   // target element(s) to be updated with server response
	beforeSubmit:  showRequest,  // pre-submit callback
	success:       showResponse,  // post-submit callback
	// other available options:
	// url:       'URL',         // override for form's 'action' attribute
	//type:      'post'        // 'get' or 'post', override for form's 'method' attribute
	//dataType:  'xml'        // 'xml', 'script', or 'json' (expected server response type)
	//clearForm: true        // clear all form fields after successful submit
	//resetForm: true        // reset the form after successful submit
	// $.ajax options can be used here too, for example:
	//timeout:   3000
};

(function ($) {
 $.fn.tooltip = function (options){
		return this.each (function () {
// 			$(tooltipSelectors, this).mbTooltip({
// 				selectors : tooltipSelectors,
// 			}).removeAttr("title");
		});
	};
})(jQuery);

(function ($) {
 $.fn.paletteHandle = function (){
  var anchors = this;
		return this.click(function(){
			anchors.each(function() {
				$(this).css('background-image', $(this).css('background-image').replace('h.gif', '.gif'));
			});
			$(this).css('background-image', $(this).css('background-image').replace('.gif', 'h.gif'));
		});
	};
})(jQuery);


// pre-submit callback
function showRequest(formData, jqForm, options) {
				// formData is an array; here we use $.param to convert it to a string to display it
				// but the form plugin does this for you automatically when it submits the data
	var queryString = $.param(formData);
	$(this.target).waiting();
				// jqForm is a jQuery object encapsulating the form element.  To access the
				// DOM element for the form do this:
				// var formElement = jqForm[0];
				// alert('About to submit: \n\n' + queryString);
				// here we could return false to prevent the form from being submitted;
				// returning anything other than false will allow the form submit to continue
	return true;
};
// post-submit callback
function showResponse(responseText, statusText)  {
		$(retrieveFormOptions.target).collapsible('.collapsible', 2);
		// current LINKS
		$('a[onclick]', $(retrieveFormOptions.target)).click(function() {
			$('.current', $(retrieveFormOptions.target)).removeClass('current');
			$(this).addClass('current').addClass('visited');
		});
		/* TOOLTIP */
// 		$(this.target).anchorClick();
// 		$(this.target).tooltip();
				// for normal html responses, the first argument to the success callback
				// is the XMLHttpRequest object's responseText property
				// if the ajaxForm method was passed an Options Object with the dataType
				// property set to 'xml' then the first argument to the success callback
				// is the XMLHttpRequest object's responseXML property
				// if the ajaxForm method was passed an Options Object with the dataType
				// property set to 'json' then the first argument to the success callback
				// is the json data object returned by the server
				// alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + '\n\nThe output div should have already been updated with the responseText.');
};

/* * * * * * * */
/* SPECIFIC FUNCTIONS */
/* * * * * * * */
/* * * * * * * */
(function($){
	$.fn.searchDB = function(doc, where, what, block, at, high) {
	  var reset = true;
 		if ($('#criteria').val() == 'db'){
			if ($('#criteria_name_0:checked').size()){
				reset = false;
				var minChars = 3;
				if (!doc) var doc = '';
				if (!where) var where = '';
				if (!what) var what = '';
				if (!block) var block = '';
				if (!at) var at = '';
				if (!high) var high = '';
    $(what+'0').val('');
    $(what+'1').val('');
				$(what+'0').autocomplete('retrieveAjax.php',
				{
					delay: 500,
					cacheLength: 1,
					minChars: minChars,
					matchContains: 1,
					autoFill:false,
					extraParams: {doc:doc, where:where, what:this.value, block:block, at:at, high:high, temp:temp},
				})
				.result(function(event, data, formatted) {
						$(what+'0').val(data[0]);
						$(what+'1').val(data[1]);
					});
			};
		};
		if (reset) {
			$(what+'0').unautocomplete();
			$(what+'0').val('');
			$(what+'1').val('');
		};
		return false;
	}
})(jQuery);
/* * * * * * * */

/* * * * * * * */
(function ($) {
 $.fn.resizeImg = function () {
  return this.each(function () {
   boxW = $(this).parents('.widget:first').width();
   boxH = $(this).parents('.widget:first').height();
   imgW = $(this).width();
   imgH = $(this).height();
   var obj = this;
   var src = this.src;
   var img = new Image();
   $(img).load(function () {
    szW = this.width;
    if (szW < imgW) $(obj).css('max-width', szW + 'px');
    szH = this.height;
    if (szH < imgH) $(obj).css('max-height', szH + 'px');
   }).attr('src', src);
  })
 }
})(jQuery);
/* * * * * * * */

/* * * * * * * */
(function ($) {
 $.fn.reservation = function (ref) {
  var widget = this.parents('.widget:first');
  var box = this.parents('.box:first');
		var id = 'b'+widget.attr('id');
//		var label = $('.widget_head:first .widget_name', widget).text();
		var label = ref.split(g_DISTINCT_SEP); // $('.box_head:first', box).text();
		var obj = $('.widget_body:first', widget);
		var data = $.trim($(obj).html()
		.replace(/(\s)+/g, ' ')
		.replace(/>\s</g, '><')
		.replace(/<a[^>]*?>(.*?)<\/a>/gi, "$1")
		.replace(/display\s*:\s*none\s*;?/g, "")
		.replace(/cursor\s*:\s*pointer\s*;?/g, "")
		.replace(/magnify/g, "")
		.replace(/fancyzoom/g, "")
		);
  var offset = 50+($('.containerPlus').size()*10);
  var ww = $(window).width()/2;
  var hh = widget.outerHeight();
		var content = '';
		content += '<div id="'+id+'" rel="'+ref+'" class="containerPlus draggable resizable" width="'+ww+'" height="'+hh+'" buttons="i,c"  style="position:absolute;z-index:10000;top:'+offset+'px;left:'+offset+'px" icon=""><div class="no"><div class="ne"><div class="n">'+' <div class="widget_name"><img src="'+g_CSS_IMG+'package_icon'+label.shift()+'.gif" alt="'+label.shift()+'" style="float:left"/>&#160;'+label.shift()+': '+label.join(", ")+'</div>'+'</div></div><div class="o"><div class="e"><div class="c"><div class="content">'+' <div class="widget_body">'+data+'</div>'+'</div></div></div></div><div ><div class="so"><div class="se"><div class="s"></div></div></div></div></div></div>';

		if ($('#'+id).size() == 0) {
		 $("body").append(content);
			$('#'+id).buildContainers();
		} else {
		$('#'+id+' .restoreContainer').click();
		};

 };
})(jQuery);
/* * * * * * * */

/* * * * * * * */
(function ($) {
$.fn.tabize = function (options){
	options = options || {};
	var settings = {
		linkClass : 'tab-anchor',
		containerClass : 'tab-content',
		linkSelectedClass : 'selected',
		containerSelectedClass : 'selected',
		onComplete : false,
		speed : 300,
		selected : 1
	}
	$.extend(settings,options);
	return this.each(function() {
	 var container = this;
		$('.'+settings.linkClass, container).each(function(i){
			$(this).show().attr('rel',settings.containerClass+i);
			if (i) {
			 thisLeft = 1+$(this).prev().find('> a').outerWidth()+$(this).prev().find('> a').position().left;
				$('> a', this).css('left', thisLeft);
			};
		});
		$('.'+settings.containerClass, container).each(function(i){
			$(this).attr('id',settings.containerClass+i).hide();
		});
		$('.'+settings.linkClass+' > a', container).click(function(){
			$('.'+settings.containerClass+'.'+settings.containerSelectedClass, container).slideUp(settings.speed).removeClass(settings.containerSelectedClass);
			$('.'+settings.linkClass+'.'+settings.linkSelectedClass, container).removeClass(settings.linkSelectedClass);
			$(this).parent().addClass(settings.linkSelectedClass);
			$('#'+$(this).parent().attr('rel')).addClass(settings.containerSelectedClass).slideToggle(settings.speed);
			if(settings.onComplete){
				settings.onComplete();
			}

			return false;
			});
		$('.'+settings.linkClass+':nth-child('+settings.selected+') > a', container).click();
		});
	}
})(jQuery);
/* * * * * * * */

/* * * * * * * */
/* READY */
/* * * * * * * */
$().ready(function () {
// http://methvin.com/splitter/
 $('.splitter').splitter({
  outline: true,
//   anchorToWindow: true, NO, sballa in verticale
  resizeToWidth: true,
 	splitVertical: true,
  sizeRight: panelW_collapsed,
//   minLeft: panelW_hidden,
//   minRight: panelW_hidden,
 	type: 'v',
  initB: panelW_collapsed,
 });
 /* FANCYZOOM */
 $.fn.fancyzoom.defaultsOptions.imgDir = g_CSS_IMG;
 /* TOOLTIP */
// 	$().anchorClick();
// 	$().tooltip();
	$('.panel .toggler').click(function () {
		$(this).parents('.panel:first').panel('mastro');
	});
 $().mastro('retrieve', doc, where, what, block, at, high, label);

	$(window).resize(function () {

/* RELOCATE SPLITTER */
	/* * * * * * * */
	// Manually set the outer splitter's height to fill the browser window.
	// This must be re-done any time the browser window is resized.
		var $ms = $('.splitter:first');
		if ($($ms).size() >0) {
			var top = $ms.offset().top;		// from dimensions.js
			var wh = $(window).height();
			var bottom = wh - $('.footer').offset().top;
			// Account for margin or border on the splitter container
			var mrg = parseInt($ms.css('marginBottom')) || 0;
			var brd = parseInt($ms.css('borderBottomWidth')) || 0;
			$ms.css('height', (wh-top-mrg-brd-bottom)+'px');
			// IE fires resize for splitter; others don't so do it here
			if ( !jQuery.browser.msie )
				$ms.trigger('resize');
			};

/* RESELECT CURRENT SPLITTER */
		$('#'+activeLayerID+'panel'+activePanelN).panel('mastro');

 }).trigger('resize');
});
/* * * * * * * */


/* * * * * * * * */
(function ($) {
 $.fn.mastro = function (vision, doc, where, what, block, at, high, label, terms, config) {
  if (! doc) var doc = '';
  if (! where) var where = '';
  if (! what) var what = '';
  if (! block) var block = '';
  if (! at) var at = '';
  if (! high) var high = '';
  if (! label) var label = '';
  if (! config) var config = '';
  if (! terms) var terms = '';
  if (! vision) var vision = activeLayerID;
		activeLayerID = vision;
		activeContextNode = $('#'+activeLayerID);

 /* * * * * * * */
 /* * * * * * * */

  switch (where) {

   case '': {
				activePanelN = 1;
				switch (activeLayerID) {
					case 'retrieve':
						break;
					case 'display':
						$('.package').load(activeLayerID+'Ajax.php', {
							temp: temp, doc: doc, where: 'head', what: 'package', label: label, terms: terms
						});
						break;
					};
				$('#'+activeLayerID+'palette').waiting();
				$('#'+activeLayerID+'palette').load(activeLayerID+'Ajax.php', {
					temp: temp, doc: doc, where: 'palette', what: what, block: block, at: at, high: high, label: label, terms: terms
				},
				function () {
					$('#'+activeLayerID+'palette .action li a').paletteHandle();
// 					$(this).anchorClick();
// 					$(this).tooltip();
					$('#'+activeLayerID+'palette .action li:nth-child('+initIdxPalette+') a').click();
					switch (activeLayerID) {
						case 'retrieve':
							break;
						case 'display':
							// azzera la palette
							$('#'+activeLayerID+'palette').load(activeLayerID+'Ajax.php', {
								temp: temp, doc: doc, where: 'palette', what: '', block: '', at: '', high: '', label: '', terms: ''
							},
							function () {
								$('#'+activeLayerID+'palette .action li a').paletteHandle();
							 var thiss = $('#'+activeLayerID+'palette .action li:nth-child('+initIdxPalette+') a');
							 thiss.css('background-image', thiss.css('background-image').replace('.gif', 'h.gif'));
							}
							);
						break;
					};
					// current LINKS
					$('a[onclick]', activeContextNode).click(function() {
						$('.current', activeContextNode).removeClass('current');
						$(this).addClass('current').addClass('visited');
					});
				});
				};
				break;

   case 'navigator' :
				$('#'+activeLayerID+'navigator').waiting();
				$('#'+activeLayerID+'navigator').load(activeLayerID+'Ajax.php', {
					temp: temp, doc: doc, where: 'navigator', what: what, block: block, at: at, high: high, label: label, terms: terms, config: config
				},
				function () {
					// current LINKS
					$('a[onclick]', activeContextNode).click(function() {
						$('.current', activeContextNode).removeClass('current');
						$(this).addClass('current').addClass('visited');
					});
					/* TOOLTIP */
// 					$(this).anchorClick();
// 					$(this).tooltip();
					$(this).collapsible('.collapsible', 2);
					switch (activeLayerID) {
						case 'retrieve':
							$('#retrieveForm').resetForm().ajaxForm(retrieveFormOptions);
							$('ul.checktree', activeContextNode).checkTree();
     		$('.tab-container', activeContextNode).tabize({selected: initIdxTabize});
							break;
						case 'display':
							break;
					};
				});
				where = 1;
				label = '';
				$().mastro(activeLayerID, doc, where, what, block, at, high, label, terms);
    break;

   default:
				var view = $('#'+activeLayerID+'view'+where);
				if (view.size() > 0)  { //
					activePanelN = where;
     //
					view.waiting();
					switch (activeLayerID) {
/* RETRIEVE */
						case 'retrieve':
								view.load(activeLayerID+'Ajax.php', {
									temp: temp, doc:doc, where:where, what:what, block:block, at:at, high:high, label: label
								},
								function () {
									// current LINKS
									$('a[onclick]', view).click(function() {
										$('.current', view).removeClass('current');
										$(this).addClass('current').addClass('visited');
									});
									/* TOOLTIP */
// 									$(this).anchorClick();
// 									$(this).tooltip();
									/* COLLAPSIBLE */
									view.collapsible('.collapsible', 2);
									/* LOCATION */
									window.location.href = '#x'+where+'_'+at;
 							}
							);
							break;
/* DISPLAY */
						case 'display':
							view.load(activeLayerID+'Ajax.php', {
								temp: temp, doc: doc, where: where, what: what, block: block, at: at, high: high, label: label, terms: terms
							},
							function () {
								// current LINKS
								$('a[onclick]', view).click(function() {
									$('.current', view).removeClass('current');
									$(this).addClass('current').addClass('visited');
								});
									/* TOOLTIP */
// 								$(this).anchorClick();
// 								$(this).tooltip();
									/* COLLAPSIBLE */
								$(this).collapsible('.scene_loader', 2);
									/* SORTABLE */
								view.find('.sortable').sortable({
									axis: 'y',
									cursor: 'move',
									handle: '.drag_handle',
// 									opacity: 0.40,
								});
									/* COLLAPSIBLE (2) */
								view.collapsible('.collapsible', '', 'struct');
									/* RESIZE IMG */
	/* FANCYZOOM */
								view.find('.widget_image', view).resizeImg();

								view.find('.fancyzoom', view).fancyzoom({
 									speed:400,showoverlay:true,overlay:4/10
 									}).attr('title', 'Vedi');

// 								view.find('.magnify:first', view).load(function() {
// 									$(this).magnify();
// 								});

									/* LOCATION */
								var highlight = '#x' + where + '_' + high;
								var open = '#x' + where + '_' + at;
								window.location.href = highlight;
								var highlightMe = $(highlight, activeContextNode);
								if (terms.search(/\_.*\_/) != -1) {
  						 if (high.indexOf('.') != -1) {
  						  var highBlockID = 'x' + where + '_' + high.substring(0, high.indexOf('.'))+'.';
	 						 } else {
  						  var highBlockID = 'x' + where + '_' + high;
									};
									$('*[id*='+highBlockID+']', activeContextNode).addClass('current').addClass('visited');
								} else {
									view.highlight(terms);
	       };
//
								var openMe = $('a'+highlight, activeContextNode);
								if (! openMe.length) openMe = $('a'+open, activeContextNode);
								if (! openMe.length) openMe = $(highlight, activeContextNode);
								if (! openMe.length) openMe = $(open, activeContextNode);

								var box_or_panel = openMe.parents('.widget:first').find('.collapsible_handle');
								if (! openMe.length) box_or_panel = view.find('.collapsible_handle:first');
								if (box_or_panel.length) {
									box_or_panel.click();
									box_or_panel.css('background-image', box_or_panel.css('background-image').replace('collapsed', 'opened'));
									var anchor = openMe.parents('.collapsible_body:first').prev('.collapsible_handle2:first');
									if (anchor.length) {
										anchor.click();
										anchor.css('background-image', anchor.css('background-image').replace('collapsed', 'opened'));
									};
									if (openMe.length) {
										openMe.addClass('current').addClass('visited');
										if (openMe[0].getAttribute('onclick')) {
											if (openMe[0].getAttribute('onclick').indexOf('_txt') == -1) {
												openMe.click();
											};
										};
									};
								};

							});
									/* NEXT STEP */
// 							$().mastro(activeLayerID, '', (parseInt(where)+1), '', at, high, label);
	//						$().mastro(activeLayerID, doc, (parseInt(where)+1), what, at, high, label);
							break;
					};
					////
     // ALL (NEEDED) PANELS LOADED => FINALIZE
     /* LAST SELECTED PANEL 4 MASTRO */
					$('#'+activeLayerID+'panel'+activePanelN).panel('mastro');
					$('#'+activeLayerID+'panel'+activePanelN).click(function () { $('#basket').hide(); return true;});
	     /*  */
				};
    break;
  };
  return false;
 }
})(jQuery);
/* * * * * * * */

/* * * * * * * */
(function ($) {
 $.fn.panel = function (action, size) {
  var panel = this;
  var content = $('.content:first', panel);
  var splitter = $(panel).parents('.splitter:first');
  switch (action) {
   case 'swap':
				if ($(content).is(':hidden')) {
					$(panel).panel('open');
				} else {
					$(panel).panel('close');
				};
				break;
   case 'open':
				switch (activeLayerID) {
					case 'retrieve':
						panelW_init = panelW_initRetrieve;
						break;
					case 'display':
						panelW_init = panelW_initDisplay;
						break;
				};
				if (size < panelW_collapsed) size = panelW_init;
				$(panel).show();
				// if ($(content).is(':hidden')) {
				$(panel).removeClass('shrunk').removeClass('hidden');
				$('.toggler', panel).show().css('background-image', $('.toggler', panel).css('background-image').replace('collapsed', 'opened'));
				$(splitter).trigger("resize",[size]);
				$(content).show();
				$(panel).css('background-image', '');
				// };
				break;
   case 'close':
				var size = panelW_collapsed;
				$(panel).show();
				// if ($(content).is(':not(:hidden)')) {
				$(panel).removeClass('hidden').addClass('shrunk');
				$('.toggler', panel).css('background-image', $('.toggler', panel).css('background-image').replace('opened', 'collapsed'));
				$(splitter).trigger("resize",[size]);
				$(content).hide();
				$(panel).css('background-image', 'url(' + costePath + $('.page_curr', panel).attr('rel') + '.gif)');
				// };
				break;
   case 'hide':
				var size = panelW_hidden;
				$(panel).hide();
				// if ($(content).is(':not(:hidden)')) {
				$(panel).removeClass('shrunk').addClass('hidden');
				$(splitter).trigger("resize",[size]);
				// };
				break;
   case 'mastro':
				var idx = $('.panel').index(panel);
// alert('panel.mastro # '+idx);
    function getAvailableSpace (idx) {
					var availableSpace = $(window).width() - ($('.vsplitbar:first').width() * $('.splitter:visible').size()) - ($('.panel.shrunk').size() * panelW_collapsed) - ($('.panel.hidden').size() * panelW_hidden);
					return availableSpace;
    };

			 switch (idx) {

					case -1:
					 alert('ERROR: max '+($('.splitter').size())+' panels...');
					 break;

					case 0:
					case 1:
					 activeLayerID = 'retrieve';
					 var idx = 1;
						$('.splitter:gt('+ (idx) +')').hide();
						$('.panel:gt('+ (idx+1) +')').panel('hide');
						$('.panel:gt('+ (idx+1) +')').hide();
						$('#retrievepanel1').panel('open');
						$('#retrievesidebar').panel('open', panelW_initRetrieve);
						var availableSpace = getAvailableSpace(idx)-panelW_initRetrieve;
						$('#retrievepanel1').panel('open', availableSpace);
						activePanelN = idx;
						break;

					case 2:
					case 3:
						activeLayerID = 'display';
					 var idx = 3;
						$('.splitter:lt('+ (idx+1) +')').show();
 					$('.splitter:gt('+ (idx) +')').hide();
  				$('.panel:gt('+ (idx+1) +')').panel('hide');
						$('.panel:gt('+ (idx+1) +')').hide();
						$('#retrievesidebar').panel('hide');
						$('#retrievepanel1').panel('close');
						$('.panel:eq('+(idx)+')').panel('open');
						$('.panel:eq('+(idx-1)+')').panel('open', panelW_initDisplay);
						var availableSpace = getAvailableSpace()-panelW_initDisplay;
						$('.panel:eq('+(idx)+')').panel('open', availableSpace);
						$('.panel:eq('+(idx-1)+')').find('a.toggler').css('width', '25px');
						activePanelN = idx-2;
						break;

					default:
						activeLayerID = 'display';
						$('.splitter:lt('+ (idx+1) +')').show();
						$('.splitter:gt('+ (idx) +')').hide();
  				$('.panel:gt('+ (idx+1) +')').panel('hide');
						$('.panel:gt('+ (idx+1) +')').hide();
						$('.panel:eq('+(idx-2)+')').panel('close');
						$('#retrievesidebar').panel('hide');
						$('#retrievepanel1').panel('close');
						$('.panel:eq('+(idx-1)+')').panel('open');
						var availableSpace = getAvailableSpace()/2;
						$('.panel:eq('+(idx-1)+')').panel('open', availableSpace);
						$('.panel:eq('+(idx)+')').panel('open', availableSpace);
						$('.panel:eq('+(idx-1)+')').find('a.toggler').css('width', '25px');
						activePanelN = idx-2;
						break;
				};

				break;
  };
//  $(splitter).trigger("resize");
	   switch(activeLayerID) {
	    case 'retrieve':
						$('.package').text('Navigazione e Ricerca');
						$('#basket').hide();
						$('#b_basket').hide();
						break;
					case 'display':
						$('#display .vsplitbar').hide();
						$('.panel:eq('+(idx)+')').parent().prev('.vsplitbar').show();
						$('#basket').hide();
						$('#b_basket').show();
						break;
					};
    //$('#activeLayerID').html(activeLayerID+' '+activePanelN);
  return false;
 }
})(jQuery);
/* * * * * * * */

