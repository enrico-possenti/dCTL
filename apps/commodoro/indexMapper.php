<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
 $returnText = '';
	$returnText .='<div id="manager_map" class="'.(DCTL_EXT_IMT ? 'imt' : '').' layout clearfix">';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .= '<h2>Gestione Mappature</h2>';
 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

$collection_id = (isset($_REQUEST['collection_id'])) ? $_REQUEST['collection_id'] : '';
$package_id = (isset($_REQUEST['package_id'])) ? $_REQUEST['package_id'] : '';
$part_id = (isset($_REQUEST['part_id'])) ? $_REQUEST['part_id'] : '';
$item_id = (isset($_REQUEST['item_id'])) ? $_REQUEST['item_id'] : '';
$what = (isset($_REQUEST['what'])) ? $_REQUEST['what'] : 'map';

if (DCTL_EXT_IMT) {
	$js = '
<script type="text/javascript" src="'.DCTL_EXT_IMT_BASE.'b64.js"><!-- --></script>
';

	$flashMarkup = '
		<script type="text/javascript">

function getFlashObject(movieName) {
	if (navigator.appName.indexOf("Microsoft") != -1) {
		return window[movieName];
	} else {
		var obj = document[movieName];
		if(obj.length != "undefined") {
			for(var i=0;i<obj.length;i++) {
				if(obj[i].tagName.toLowerCase() == "embed") return obj[i];
			}
		}
		return obj;
	}
}

function commdodoro_initializeIMT(xmldata) {
 var txt = Base64.decode(xmldata);

'.(NOVEOPIU ? '
	$("#xml_chunk").val(txt);
	' : ''). '
 var rex = /xml>\s*<a/i
 var rem = /img>\s*<a/i
 if (rex.test(txt) && rem.test(txt)) { //if match pass
		$(".lnk_result").empty();
 } else {
  var msg = "";
  xmldata = "";
		if (!rex.test(txt)) msg += "- Nessuna chiave";
		if (!rem.test(txt)) msg += "- Nessuna immagine";
		$(".lnk_result").html("<span class=\'warning\'>"+msg+"</span>");
 }
	getFlashObject("'.DCTL_EXT_IMT_CBP.'").initialize(xmldata);
}

function jsapi_saveReply(xmldata) {
	$("#lnk_result").html(xmldata);
}

function jsapi_dataSaved(xmldata) {
'.(NOVEOPIU ? '
	$("#xml_chunk").val(Base64.decode(xmldata));
' : ''). '
}

	</script>
	    	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
				codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab" name="'.DCTL_EXT_IMT_CBP.'" width="100%" height="400"
				id="'.DCTL_EXT_IMT_CBP.'">
		      <param name="movie" value="'.DCTL_EXT_IMT_BASE.'ImageMapper.swf?js_prefix=jsapi_" />
		      <param name="quality" value="high" />
		      <param name="bgcolor" value="#323232" />
		      <param name="allowScriptAccess" value="sameDomain" />
		      <param name="allowFullScreen" value="true" />
		      <embed src="'.DCTL_EXT_IMT_BASE.'ImageMapper.swf?js_prefix=jsapi_" quality="high" bgcolor="#323232"
						width="100%" height="400" name="'.DCTL_EXT_IMT_CBP.'" align="middle"
						play="true"
						loop="false"
						allowscriptaccess="sameDomain"
						allowfullscreen="true"
						type="application/x-shockwave-flash"
						pluginspage="http://www.adobe.com/go/getflashplayer" />
		    </object>
';
	$returnText .='<div class="lnk_left">';

	$returnText .= '<div class="lineH1">Collection</div>';
	$returnText .='<div class="lnk_col">';
	$returnText .='<form>';
	$returnText .='<fieldset>';
	$basePath = DCTL_PROJECT_PATH;
	getCollectionList($basePath, &$collectionList);
	foreach ($collectionList['path'] as $key=>$fPath) {
		getCollectionRecord($fPath, &$collectionRecord);
		$returnText .= '<input type="radio" name="collection_id[]" value="'.$collectionRecord['collection_id'].'" />';
		$returnText .= '<label class="text" onclick="$(this).prev().click();"> '.cleanWebString($collectionRecord['collection_short'], FIELD_STRING_LENGTH).'</label>';
	$returnText .='<br/>';
	};
	$returnText .='</fieldset>';
	$returnText .='</form>';
	$returnText .='</div>';

	$returnText .= $js;
	if (NOVEOPIU) {
		$returnText .= '<div class="lineH1">Test POST</div>';
		$returnText .='<div id="xml_test">
		<a href="javascript:void(0);" onclick="$(\'#lnk_result\').load(\''.DCTL_EXT_IMT_CB.'\', {'.DCTL_EXT_IMT_CBP.': Base64.encode($(\'#xml_chunk\').val())});">test POST</a><hr/>
		</div>';
		$returnText .= '<div class="lineH1">Codice XML</div>';
		$returnText .='<textarea id="xml_chunk">...</textarea>';
	};
	$returnText .='</div>';

	$returnText .='<div class="lnk_right">';

 $returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Selettore Part</div>';
	$returnText .= '<div id="xml_tree1" class="src_tree"></div>';
	$returnText .='</div>';
 $returnText .= '<div id="lnk_result" class="lnk_result"></div>';

	$returnText .= '<div class="imt">'.$flashMarkup.'</div>';


	$returnText .='</div>';

} else {
	$returnText .='<div class="lnk_left">';

	$returnText .= '<div class="lineH1">Collection</div>';
	$returnText .='<div class="lnk_col">';
	$returnText .='<form>';
	$returnText .='<fieldset>';
	$basePath = DCTL_PROJECT_PATH;
	getCollectionList($basePath, &$collectionList);
	foreach ($collectionList['path'] as $key=>$fPath) {
		getCollectionRecord($fPath, &$collectionRecord);
		$returnText .= '<input type="radio" name="collection_id[]" value="'.$collectionRecord['collection_id'].'" />';
		$returnText .= '<label class="text" onclick="$(this).prev().click();"> '.cleanWebString($collectionRecord['collection_short'], FIELD_STRING_LENGTH).'</label>';
	$returnText .='<br/>';
	};
	$returnText .='</fieldset>';
	$returnText .='</form>';
	$returnText .='</div>';

	$returnText .= '<div class="lineH1">Codice XML</div>';
	$returnText .='<div id="xml_chunk"></div>';

	$returnText .='</div>';

	$returnText .='<div class="lnk_right">';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Selettore ID</div>';
	$returnText .='<div id="xml_tree1" class="src_tree">';
	$returnText .='</div>';
	$returnText .='</div>';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Selettore GRAPHIC</div>';
	$returnText .='<div id="xml_tree2" class="src_tree">';
	$returnText .='</div>';
	$returnText .='</div>';

	$returnText .= '<div id="lnk_result" class="lnk_result"></div>';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH2">Gestione mappatura</div>';
	$returnText .= '<div class="lnk_result"></div>';
	$returnText .='<form class="linker" action="">';
	$returnText .='<fieldset>';
	$returnText .='<label>tra (parola chiave):</label>';
	$returnText .='<input type="text" name="xml_id1" value="" disabled="disabled" />';
	$returnText .='<input type="hidden" name="xml_lnk1id" value="" disabled="disabled" />';
	$returnText .='<br/>';
	$returnText .= '<label>e (coordinate):</label>';
	$returnText .='<input type="text" name="xml_id2" value="" disabled="disabled" />';
	$returnText .= '<br/>';
	$returnText .= '<label>con etichetta:</label>';
	$returnText .='<input type="text" name="xml_label" value="" />';
	$returnText .= '<br/>';
	$returnText .='<input type="button" name="bSaveA" value="registra" />';
	$returnText .='</fieldset>';
	$returnText .='</form>';
	$returnText .='</div>';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Immagine</div>';
	$returnText .= '<div id="img_area" class="src_col2">';
	$returnText .= '<img id="img_edit" />';
	$returnText .= '</div>';
	$returnText .= '</div>';
};


$returnText .='</div>';

	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .='</div>';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	echo $returnText;

/* NO ?> IN FILE .INC */
