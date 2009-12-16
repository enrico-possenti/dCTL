/**
 +----------------------------------------------------------------------+
 | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
 +----------------------------------------------------------------------+
 | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
 | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
 |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
 +----------------------------------------------------------------------+
 | A js file for "commodoro"                                            |
 +----------------------------------------------------------------------+
*/

 $(document).ready(function(){
 	$('.simpleTree').simpleTree({
 	activeContainer: false,
		afterClick:function(node){
			// alert("text-"+$('span:first',node).text());
			},
		afterDblClick:function(node){
			// alert("text-"+$('span:first',node).text());
		},
		afterMove:function(destination, source, pos){
			// alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
		},
		afterAjax:function(node){
			// alert('Loaded');
		}
	});
	externalLinks();
	hideUnvisible();
	var what = $(this).getCASE();
	switch(what) {
		case 'lnk':
			$('#manager_lnk [name*=\'collection_id\']').click(function(){
				$('.src_tree').empty();
				$('.src_tree2').empty();
				$('#xml_chunk').empty();
				$('.lnk_result').empty();
				$('#xml_chunk').empty();
				var collection_id = this.value;
				doProgress();
				$('#xml_tree1').load('indexAjax.php',{action:'ajax_loadTree', selector:'1', collection_id:collection_id, what:what},function(){
				$(' .simpleTree',this).simpleTree();
				});
				$('#xml_tree2').load('indexAjax.php',{action:'ajax_loadTree', selector:'2', collection_id:collection_id, what:what},function(){
				$(' .simpleTree',this).simpleTree();killProgress();
				});
			});
			$('form').clearForm();
			$('form.linker :button[name*=bSave]').click(function(){
				return $(this).clickOnSubmit();
			});
		break;
		case 'map':
			$('#manager_map [name*=\'collection_id\']').click(function(){
				$('.src_tree').empty();
				$('.src_tree2').empty();
				$('#xml_chunk').empty();
				$('.lnk_result').empty();
				$('#xml_chunk').empty();
				var collection_id = this.value;
				doProgress();
				$('#xml_tree1').load('indexAjax.php',{action:'ajax_loadTree', selector:'1', collection_id:collection_id, what:what}, function(){
				$(' .simpleTree',this).simpleTree();
				});
// 				$('#xml_tree2').load('indexAjax.php', {action:'ajax_loadTree', selector:'2', collection_id:'', what:what}, function(){
// 					killProgress();
// 				});
			});
			$('img#img_edit').imgAreaSelect({ handles: true, outerColor: "red", borderWidth: 2 });
			$('form').clearForm();
			$('form.linker :button[name*=bSave]').click(function(){
				return $(this).clickOnSubmit();
			});
		break;
		default:
			// alert('ERROR: UNIMPLEMENTED CASE IN '.__FUNCTION__);
		break;
	};
});
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
$.fn.getCASE = function () {
	var what = '';
	what = $('#manager_lnk').size() ? 'lnk' : what;
	what = $('#manager_map').size() ? 'map' : what;
	return what;
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
$.fn.clickOnSubmit = function () {
	var WHAT = $(this).getCASE();
	var ID1 = $(':input[name=xml_id1]', $(this).parent()).fieldValue(false)[0];
	var ID2 = $(':input[name=xml_id2]', $(this).parent()).fieldValue(false)[0];
	var LNK1 = $(':input[name=xml_lnk1id]', $(this).parent()).fieldValue(false)[0];
	var LNK2 = $(':input[name=xml_lnk2id]', $(this).parent()).fieldValue(false)[0];
	var LABEL = $(':input[name=xml_label]', $(this).parent()).fieldValue(false)[0];
	switch ($(this).attr('name')) {
		case 'bSaveA':
		switch (true) {
			case ((ID1=='')||(ID2=='')):
				switch(WHAT) {
					case 'lnk':
						alert('Attenzione: seleziona almeno due id...');
					break;
					case 'map':
						alert('Attenzione: seleziona almeno un id e un graphic...');
					break;
					default:
						alert('ERROR: UNIMPLEMENTED CASE IN '.__FUNCTION__);
					break;
				};
			break;
			case (ID1 == ID2) :
				alert('Attenzione: i due id selezionati sono uguali...');
			break;
			case (LABEL == '') :
				alert('Attenzione: definisci un\'etichetta...');
			break;
			default:
				doProgress();
				$('#lnk_result').load('indexAjax.php',
				{action:'ajax_saveLink',
				selector:'new',
				link_id1:ID1,
				link_id2:ID2,
				link_label:LABEL,
				what:WHAT},
				function () {
					switch (WHAT) {
						case 'lnk':
							$('.src_tree .active a').click();
						break;
						case 'map':
							if (LNK1 != '') {
								$('#lnk_result').load('indexAjax.php',{action:'ajax_deleteLink', link_id1:LNK1, link_id2:'', link_label:'', what:WHAT});
							};
							$('.src_tree .active a').parents('li.folder-open:last img.refresh').click();
						break;
						default:
							alert('UNIMPLEMENTED CASE IN deleteLink');
						break;
					};
					$('form.linker').clearForm();
					killProgress();
				});
			break;
		};
		break;

		case 'bSaveB':
		switch (true) {
			case ((ID1=='')||(LNK2=='')):
				alert('Attenzione: seleziona un id e un collegamento...');
			break;
			default:
				doProgress();
				$('#lnk_result').load('indexAjax.php',{action:'ajax_saveLink', selector:'add', link_id1:ID1, link_id2:LNK2, what:WHAT}, function (){
				$('.src_tree .active a').click();
				$('form.linker').clearForm();
				killProgress();
				});
			break;
		};
		break;

		case 'bSaveC':
		switch (true) {
			case ((LABEL=='')||(LNK2=='')||(ID1=='')):
				alert('Attenzione: seleziona un collegamento e definisci un\'etichetta...');
			break;
			default:
				doProgress();
				$('#lnk_result').load('indexAjax.php',{action:'ajax_saveLink', selector:'mod', link_id1:ID1, link_id2:LNK2, link_label:LABEL, what:WHAT}, function (){
					$('.src_tree .active a').click();
					$('form.linker').clearForm();
					killProgress();
				});
			break;
		};
		break;

	};
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function deleteLink (id1, id2, label, what) {
 switch(true) {
  case ((id1!='')&&(id2=='')):
			if (confirm("Elimino l\'intero collegamento \""+label+"\" ?")) {
				doProgress();
				$('#lnk_result').load('indexAjax.php',{action:'ajax_deleteLink', link_id1:id1, link_id2:id2, link_label:label, what:what}, function (){
				switch (what) {
					case 'lnk':
						$('.src_tree .active a').click();
					break;
					case 'map':
						$('.src_tree .active a').parents('li.folder-open:last img.refresh').click(); //
					break;
					default:
					break;
					};
					$('form.linker').clearForm();
					killProgress();
				});
			};
  break;
  case ((id1!='')&&(id2!='')):
   var msg = '';
   switch (what) {
    case 'lnk':
     msg += "Tolgo l\'id \""+id2+"\" dal collegamento \""+label+"\" ?"
    break;
    case 'map':
     msg += "Elimino il collegamento \""+label+"\" ?"
    break;
    default:
    break;
   };
			if (msg != '') {
				if (confirm(msg)) {
					doProgress();
					$('#lnk_result').load('indexAjax.php',{action:'ajax_deleteLink', link_id1:id1, link_id2:id2, link_label:label, what:what}, function (){
					switch (what) {
						case 'lnk':
							$('.src_tree .active a').click();
						break;
						case 'map':
							$('.src_tree .active a').parents('li.folder-open:last img.refresh').click(); //
						break;
						default:
						break;
						};
						$('form.linker').clearForm();
						killProgress();
					});
				};
			};
  break;
  default:
   alert('Seleziona un elemento...');
  break;
 };
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function editLink (obj, id1, id2, label, what) {
	$(obj).parent().html(
	'<form><fieldset>'+
'<input type="text" name="xml_label" value="'+label+'" />'+
'<input type="hidden" name="xml_id1" value="'+id1+'" disabled="disabled" />'+
'<input type="hidden" name="what" value="'+what+'" disabled="disabled" />'+
'<input type="hidden" name="xml_lnk2id" value="'+id2+'" disabled="disabled" />'+
'<input type="button" name="bSaveC" value="modifica" onclick="$(this).clickOnSubmit()" />'+
'</fieldset></form>'
);
 // alert("Modifica etichetta: funzione non ancora implementata...");
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function submitform (theForm) {
  document.getElementById(theForm).submit();
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function doProgress() {
	var obj = document.getElementById("progress");
	obj.style.height = "1.0em";
	obj.style.visibility = 'visible';
 return true;
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function killProgress() {
	var obj = document.getElementById("progress");
 obj.style.visibility = 'hidden';
	return true;
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function externalLinks() {
 var i, obj;
 for (i=0; (obj = document.getElementsByTagName("a")[i]); i++) {
  switch(obj.getAttribute("class")) {
   case 'external':
   case 'link_ext':
   case 'link_pop':
   case 'link_dload':
    obj.target = '_new';
   break;
  };
 };
 for (i=0; (obj = document.getElementsByTagName("form")[i]); i++) {
  switch(obj.getAttribute("class")) {
   case 'external':
   case 'link_ext':
   case 'link_pop':
   case 'link_dload':
    obj.target = '_new';
   break;
  };
 };
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function hideUnvisible() {
 var i, obj;
 for (i=0; (obj = document.getElementsByTagName('table')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('tr')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('ul')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('span')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('div')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function toggleVisibility(theCaller) {
 for (var i=1; i<arguments.length; i++) {
		if (arguments[i].style) {
		 var theObject = arguments[i];
		} else {
				var theObject = document.getElementById(arguments[i]);
		};
		if (theObject) {
		 if (theObject.style) {
		  theObject.style.display = (theObject.style.display != 'none' ? 'none' : '' );
		  var theS = theCaller.childNodes[0].src; // img
		  if (theObject.style.display == 'none') {
		   theS = theS.replace('collapse', 'expand');
		  } else {
		   theS = theS.replace('expand', 'collapse');
		  };
		  theCaller.childNodes[0].src = theS;
   };
	 };
	};
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * Convert a single file-input element into a 'multiple' input list
 *
 * Usage:
 *
 *   1. Create a file input element (no name)
 *      eg. <input type="file" id="first_file_element">
 *
 *   2. Create a DIV for the output to be written to
 *      eg. <div id="files_list"></div>
 *
 *   3. Instantiate a MultiSelector object, passing in the DIV and an (optional) maximum number of files
 *      eg. var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 3 );
 *
 *   4. Add the first element
 *      eg. multi_selector.addElement( document.getElementById( 'first_file_element' ) );
 *
 *   5. That's it.
 *
 *   You might (will) want to play around with the addListRow() method to make the output prettier.
 *
 *   You might also want to change the line
 *       element.name = 'file_' + this.count;
 *   ...to a naming convention that makes more sense to you.
 *
 * Licence:
 *   Use this however/wherever you like, just don't blame me if it breaks anything.
 *
 * Credit:
 *   If you're nice, you'll leave this bit:
 *
 *   Class by Stickman -- http://www.the-stickman.com
 *      with thanks to:
 *      [for Safari fixes]
 *         Luis Torrefranca -- http://www.law.pitt.edu
 *         and
 *         Shawn Parker & John Pennypacker -- http://www.fuzzycoconut.com
 *      [for duplicate name bug]
 *         'neal'
 */
function MultiSelector( list_target, max ){

	// Where to write the list
	this.list_target = list_target;
	// How many elements?
	this.count = 0;
	// How many elements?
	this.id = 0;
	// Is there a maximum?
	if( max ){
		this.max = max;
	} else {
		this.max = -1;
	};

	/**
	 * Add a new file input element
	 */
	this.addElement = function( element ){

		// Make sure it's a file input element
		if( element.tagName == 'INPUT' && element.type == 'file' ){

			// Element name -- what number am I?
			element.name = 'FILE' + this.id++;

			// Add reference to this object
			element.multi_selector = this;

			// What to do when a file is selected
			element.onchange = function(){

				// New file input
				var new_element = document.createElement( 'input' );
				new_element.type = 'file';

				// Add new element
				this.parentNode.insertBefore( new_element, this );

				// Apply 'update' to element
				this.multi_selector.addElement( new_element );

				// Update list
				this.multi_selector.addListRow( this );

				// Hide this: we can't use display:none because Safari doesn't like it
				this.style.position = 'absolute';
				this.style.left = '-1000px';

			};
			// If we've reached maximum number, disable input element
			if( this.max != -1 && this.count >= this.max ){
				element.disabled = true;
			};

			// File element counter
			this.count++;
			// Most recent element
			this.current_element = element;

		} else {
			// This can only be applied to file input elements!
			alert( 'Error: not a file input element' );
		};

	};

	/**
	 * Add a new row to the list of files
	 */
	this.addListRow = function( element ){

		// Row div
		var new_row = document.createElement( 'div' );

		// Delete button
		var new_row_button = document.createElement( 'input' );
		new_row_button.type = 'button';
		new_row_button.value = '-';

		// References
		new_row.element = element;

		// Delete function
		new_row_button.onclick= function(){

			// Remove element from form
			this.parentNode.element.parentNode.removeChild( this.parentNode.element );

			// Remove this row from the list
			this.parentNode.parentNode.removeChild( this.parentNode );

			// Decrement counter
			this.parentNode.element.multi_selector.count--;

			// Re-enable input element (if it's disabled)
			this.parentNode.element.multi_selector.current_element.disabled = false;

			// Appease Safari
			//    without it Safari wants to reload the browser window
			//    which nixes your already queued uploads
			return false;
		};

		// Set row value
		new_row.innerHTML = element.value + "&#160;";

		// Add button
		new_row.appendChild( new_row_button );

		// Add it to the list
		this.list_target.appendChild( new_row );

	};

};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
