<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
 $returnText = '';
	$returnText .='<div id="manager_lnk" class="layout clearfix">';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $returnText .= '<h2>Gestione Collegamenti</h2>';
 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$collection_id = (isset($_REQUEST['collection_id'])) ? $_REQUEST['collection_id'] : '';
	$package_id = (isset($_REQUEST['package_id'])) ? $_REQUEST['package_id'] : '';
	$part_id = (isset($_REQUEST['part_id'])) ? $_REQUEST['part_id'] : '';
	$item_id = (isset($_REQUEST['item_id'])) ? $_REQUEST['item_id'] : '';
	$what = (isset($_REQUEST['what'])) ? $_REQUEST['what'] : 'lnk';

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
	$returnText .='<div id="xml_chunk">';
	$returnText .='</div>';

	$returnText .='</div>';

	$returnText .='<div class="lnk_right">';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Selettore ID #1</div>';
	$returnText .='<div id="xml_tree1" class="src_tree">';
	$returnText .= ajax_loadTree(1, $collection_id, $package_id, $part_id, $item_id, $what);
	$returnText .='</div>';
	$returnText .='</div>';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Selettore ID #2</div>';
	$returnText .='<div id="xml_tree2" class="src_tree">';
	$returnText .= ajax_loadTree(2, $collection_id, $package_id, $part_id, $item_id, $what);
	$returnText .='</div>';
	$returnText .='</div>';

	$returnText .= '<div id="lnk_result" class="lnk_result"></div>';
	$returnText .='<div class="src_col2">';
	$returnText .= '<div class="lineH2">Crea un nuovo collegamento tra ID #1 e ID #2</div>';
	$returnText .='<form class="linker" action="">';
	$returnText .='<fieldset>';
	$returnText .='<label>tra:</label>';
	$returnText .='<input type="text" name="xml_id1" value="" disabled="disabled" />';
	$returnText .= '<label>e:</label>';
	$returnText .='<input type="text" name="xml_id2" value="" disabled="disabled" />';
	$returnText .= '<br/>';
	$returnText .= '<label>con etichetta:</label>';
	$returnText .='<input type="text" name="xml_label" value="" />';
	$returnText .= '<br/>';
	$returnText .='<input type="button" name="bSaveA" value="collega" />';
	$returnText .='</fieldset>';
	$returnText .='</form>';
	$returnText .='</div>';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Collegamenti ID #1</div>';
	$returnText .='<div id="xml_lnk1" class="src_tree2"> </div>';
	$returnText .='</div>';

	$returnText .='<div class="src_col">';
	$returnText .= '<div class="lineH1">Collegamenti ID #2</div>';
	$returnText .='<div id="xml_lnk2" class="src_tree2"> </div>';
	$returnText .='</div>';

	$returnText .='<div class="src_col2">';
	$returnText .= '<div class="lineH2">Aggiungi ID #1 a collegamento ID #2</div>';
	$returnText .='<form class="linker" action="">';
	$returnText .='<fieldset>';
	$returnText .= '<label>aggiungi:</label>';
	$returnText .='<input type="text" name="xml_id1" value="" disabled="disabled" />';
	$returnText .= '<label>a:</label>';
	$returnText .='<input type="text" name="xml_lnk2" value="" disabled="disabled" />';
	$returnText .='<input type="hidden" name="xml_lnk2id" value="" disabled="disabled" />';
	$returnText .= '<br/>';
	$returnText .='<input type="button" name="bSaveB" value="aggiungi" />';
	$returnText .='</fieldset>';
	$returnText .='</form>';
	$returnText .='</div>';

	$returnText .='</div>';

	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .='</div>';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	echo $returnText;

/* NO ?> IN FILE .INC */
