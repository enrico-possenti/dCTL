<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
 $returnText = '';
	$returnText .='<div id="manager_repo" class="layout clearfix">';
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$returnText .= '<h2>Pubblicazione sul Web</h2>';
	echo $returnText;
 $returnText = '';
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$persistentConnection = true;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $action = '';
	if (isset($_REQUEST['action'])) {
	 $action = $_REQUEST['action'];
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $what = '';
	if (isset($_REQUEST['what'])) {
	 $what = $_REQUEST['what'];
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	try {
	 $existQuery = dctl_xmldb_connect('query', $persistentConnection);
	} catch (Exception $e) {
		die('<span class="wctl_error">[exist] ' . $e . '</span>');
	};

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$doPublish = FALSE;
	$doUpdate = FALSE;
	$doUnpublish = FALSE;
 if ($what != '') {
  $resultMsg .= '<h3>Esecuzione in corso...</h3>';
  $resultMsg .= '<ul id="running_ops">';
		hardFlush(&$resultMsg);
		switch ($action) {
		 case 'publish':
					$doPublish = TRUE;
		  break;
		 case 'update':
					$doUpdate = TRUE;
		  break;
		 case 'unpublish':
					$doUnpublish = TRUE;
		  break;
		};
		$doAdmin = $doPublish || $doUnpublish || $doUpdate;
		if ($doAdmin) {
			try {
				$existAdmin = dctl_xmldb_connect('admin', $persistentConnection);
			} catch (Exception $e) {
				die('<span class="wctl_error">[49] ' . $e . '</span>');
			};
		};
		if ($doPublish) {
			if (publish_SendToXDB($existAdmin, $collection_id, $package_id, $part_id, &$resultMsg)) {
     //OK
			};
		};
  if ($doUpdate) {
			$dbPackageList = '';
   if ($collection_id != '') {
    if ($package_id == '') {
					$path2load = XMLDB_DBCTL_PUB.$collection_id.DB_PATH_SEP.$collection_id.DCTL_RESERVED_INFIX.$collection_id.'.xml';
					$xquery = '';
					$xquery .= ' declare namespace xmldb="http://exist-db.org/xquery/xmldb"; ';
					$xquery .= ' if (fn:doc-available("'.$path2load.'")) then ';
					$xquery .= ' let $data := xmldb:get-child-resources("'.XMLDB_DBCTL_PUB.$collection_id.'") ';
					$xquery .= ' return ';
					$xquery .= ' <span>{$data}</span> ';
					$xquery .= ' else fn:string("") ';
					$result = $existQuery->xquery($xquery) or dump($existQuery->getError());
					$resource = (array) $result;
					if ($resource['XML'] != '') {
						$dbPackageList = $resource['XML'];
					};
					$dbPackageList = strip_html($dbPackageList).' ';
					if (publish_RemoveFromXDB($existAdmin, $collection_id, '', '', &$resultMsg)) {
						foreach(explode(' ', $dbPackageList) as $package_id2) {

dump('DVLP: SISTEMARE PROCEDURA #tei_publisher.phps#');
exit();

						 if (preg_match('/^'.DCTL_PACKAGE_PREFIX.'/',$package_id2)) {
        $package_id2 = str_ireplace('.xml', '', $package_id2);
								if (publish_SendToXDB($existAdmin, $collection_id, $package_id2, $part_id, &$resultMsg)) {
										//OK
								};
							};
						};
					};
				} else {
					if (publish_RemoveFromXDB($existAdmin, $collection_id, $package_id, $part_id, &$resultMsg)) {
						if (publish_SendToXDB($existAdmin, $collection_id, $package_id, $part_id, &$resultMsg)) {
								//OK
						};
					};
				};
			};
		};
		if ($doUnpublish) {
			if (publish_RemoveFromXDB($existAdmin, $collection_id, $package_id, $part_id, &$resultMsg)) {
     //OK
			};
		};
		if ($doAdmin) dctl_xmldb_disconnect($existAdmin);
  $resultMsg .= '</ul>';
		hardFlush(&$resultMsg);
	};

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
//
// COLLEZIONI PUBBLICATE
//
// SELEZIONA COLLECTION
$fDiv0 = 'divSelectCollection';
$collectionPath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEP;
getCollectionList(DCTL_PROJECT_PATH, &$collectionList, true);
//putOpenCloseLevel
$returnText .= putOpenLevel($fDiv0, $loc4msg, $collection_id != '', 'Seleziona una Collection ('.(count($collectionList['path'])-1).')', &$resultMsg);
$returnText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
$returnText .= '<fieldset>';
$returnText .= '<label>Scegli:</label>';
$returnText .= SYS_DBL_SPACE;
$returnText .= '<select name="collection_id" onchange="javascript:submitform(\'form'.$fDiv0.'\')">';
foreach ($collectionList['path'] as $key=>$fPath) {
 $returnText .= '<option value="'.$fPath.'"';
 if ($fPath == $collectionPath) {
  $returnText .= ' selected="selected"';
 };
 $returnText .= '>';
 getCollectionRecord($fPath, &$collectionRecord);
 $returnText .= $collectionRecord['collection_full'];
 $returnText .= '</option>';
};
$returnText .= '</select>';
$returnText .= SYS_DBL_SPACE.'<input class="action" type="image" src="'.DCTL_IMAGES.'action_refresh_blue.gif" />';
$returnText .= '<input type="hidden" name="selectColl" value="seleziona" />';
$returnText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
$returnText .= '</fieldset>';
$returnText .= '</form>';
$prosecute = in_array($collectionPath, $collectionList['path']);
if ($prosecute) {
 getCollectionRecord($collectionPath, &$collectionRecord);
 $collection_short = $collectionRecord['collection_short'];
 $returnText .= '<h2>Collection "'.$collectionRecord['collection_short'].'"</h2>';
 $header = $collectionPath.DCTL_FILE_HEADER;
 $contents = cleanUpIndentation(file_get_contents($header));
 $select = $COLLECTION_FIELDS['label'];
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 // PUBBLICA
 //
	$isPublished_pub = false;
	$isPublished_tmp = false;
	$fDiv0 = 'divPublishCollection';
 $returnText .= putOpenCloseLevel($fDiv0, $loc4msg, $collection_id != '', 'Procedure di Pubblicazione di "'.$collectionRecord['collection_short'].'"', &$resultMsg);
 $collectionState = '';
 $collectionState .= '<img src="'.DCTL_IMAGES.'published_no.png" alt="(unpublished icon)" />';
 $collectionState .= '&#160;non pubblicato';
	$path2load = XMLDB_DBCTL_PUB.$collection_id.DB_PATH_SEP.$collection_id.DCTL_RESERVED_INFIX.DCTL_RESERVED_PREFIX.$collection_id.'.xml';
 $xquery = '';
	$xquery .= ' declare namespace xmldb="http://exist-db.org/xquery/xmldb"; ';
	$xquery .= ' if (fn:doc-available("'.$path2load.'")) then ';
	$xquery .= ' let $date := fn:adjust-dateTime-to-timezone(xmldb:last-modified("'.dirname($path2load).'","'.basename($path2load).'")) ';
	$xquery .= ' return ';
	$xquery .= ' <span>{fn:day-from-dateTime($date)}-{fn:month-from-dateTime($date)}-{fn:year-from-dateTime($date)}&#160;@&#160;{fn:hours-from-dateTime($date)}:{fn:minutes-from-dateTime($date)}</span> ';
	$xquery .= ' else fn:string("") ';
	$result = $existQuery->xquery($xquery) or dump($existQuery->getError());
	$resource = (array) $result;
	if (isset($resource['XML'])?$resource['XML']:false) {
		$isPublished_pub = true;
		$collectionState = '';
		$collectionState .= '<img src="'.DCTL_IMAGES.'published_yes.png" alt="(unpublished icon)" />';
		$collectionState .= '&#160;Collection pubblicata/ aggiornata il '.$resource['XML'];
	};

 $returnText .= '<table>';
	$returnText .= '<thead>';
	$returnText .= '<tr>';
	$returnText .= '<th class="label">nome</th>';
	$returnText .= '<th>descrizione</th>';
	$returnText .= '<th>contenuto</th>';
	$returnText .= '</tr>';
	$returnText .= '</thead>';
	$returnText .= '<tbody>';
	$returnText .= '<tr>';
	$returnText .= '<td colspan="2">';
	$returnText .= '<img src="'.DCTL_IMAGES.'alert2.gif" alt="(alert)" />'.SYS_DBL_SPACE;

	$lastUpdate = '&#160;non pubblicato';
	$path2load = XMLDB_DBCTL_TMP.$collection_id.DB_PATH_SEP.$collection_id.DCTL_RESERVED_INFIX.DCTL_RESERVED_PREFIX.$collection_id.'.xml';
	$xquery = '';
	$xquery .= ' declare namespace xmldb="http://exist-db.org/xquery/xmldb"; ';
	$xquery .= ' if (fn:doc-available("'.$path2load.'")) then ';
	$xquery .= ' let $date := fn:adjust-dateTime-to-timezone(xmldb:last-modified("'.dirname($path2load).'","'.basename($path2load).'")) ';
	$xquery .= ' return ';
	$xquery .= ' <span>{fn:day-from-dateTime($date)}-{fn:month-from-dateTime($date)}-{fn:year-from-dateTime($date)}&#160;@&#160;{fn:hours-from-dateTime($date)}:{fn:minutes-from-dateTime($date)}</span> ';
	$xquery .= ' else fn:string("") ';
	$result = $existQuery->xquery($xquery) or dump($existQuery->getError());
	$resource = (array) $result;
	if (isset($resource['XML'])?$resource['XML']:false) {
		$lastUpdate = $resource['XML'];
		$isPublished_tmp = true;
	};
	$returnText .= 'Pubblicazione Temporanea per la Verifica dei contenuti prima della pubblicazione on-line...<br />'.SYS_DBL_SPACE.SYS_DBL_SPACE.SYS_DBL_SPACE.'(*) Ultimo aggiornamento: '.$lastUpdate.'</td>';
	if (DCTL_USER_IS_ADMIN) {
	$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=publish&amp;what=collection&amp;collection_id='.$collection_id.'&amp;temp=true" title="(Pubblica Temporaneamente per Verifica)">Pubblica temporaneamente...</a>';
} else {
$returnText .= '<td>(!!!) Pubblicazione momentaneamente disabilitata...</td>';
	};
	$returnText .= '</tr>';

	$returnText .= '<tr>';
	$returnText .= '<td>Stato di Pubblicazione</td>';
	$returnText .= '<td colspan="2"><strong>'.$collectionState.'</strong></td>';
	$returnText .= '</tr>';
	$returnText .= '<tr>';
	$returnText .= '<td>Pubblicazione</td>';
	$returnText .= '<td>'.'Questa funzionalità permette di pubblicare tutti i contenuti della Collection per la consultazione degli archivi.'.'</td>';
// 	if ($isPublished_pub) {
// 		$returnText .= '<td>--</td>';
// 	} else {
				if (DCTL_USER_IS_ADMIN) {
	 $returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=publish&amp;what=collection&amp;collection_id='.$collection_id.'" title="(Pubblica tutta la Collection)">Pubblica "'.$collection_short.'" on-line...</a></td>';
} else {
$returnText .= '<td>(!!!) Pubblicazione momentaneamente disabilitata...</td>';
	};
// 	};
	$returnText .= '</tr>';
	$returnText .= '<tr>';
	$returnText .= '<td>Aggiornamento</td>';
	$returnText .= '<td>'.'Questa funzionalità permette di aggiornare solamente i contenuti della Collection già pubblicati per la consultazione degli archivi.'.'</td>';
	if ($isPublished_pub) {
		$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=update&amp;what=collection&amp;collection_id='.$collection_id.'" title="(Aggiorna solo elementi pubblicati)">Aggiorna "'.$collection_short.'" on-line...</a></td>';
	} else {
		$returnText .= '<td>--</td>';
	};
	$returnText .= '</tr>';
	$returnText .= '<tr>';
	$returnText .= '<td>Ritira</td>';
	$returnText .= '<td>'.'Questa funzionalità permette di de-pubblicare tutti i contenuti della Collection, rendendoli non visibili al sistema di consultazione degli archivi.'.'</td>';
	if ($isPublished_pub) {
		$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=unpublish&amp;what=collection&amp;collection_id='.$collection_id.'" title="(De-Pubblica tutta la Collection)">Poni "'.$collection_short.'" off-line...</a></td>';
	} else {
		$returnText .= '<td>--</td>';
	};
	$returnText .= '</tr>';
	$returnText .= '</tbody>';
	$returnText .= '</table>';

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 //
 // SELEZIONA PACKAGE
 $fDiv0 = 'divSelectPackage';
	$packagePath = $collectionPath.$package_id.SYS_PATH_SEP;
	getPackageList($collectionPath, &$packageList, true);
 $returnText .= putOpenCloseLevel($fDiv0, $loc4msg, $package_id != '', 'Seleziona un Package di "'.$collection_short.'" ('.(count($packageList['path'])-1).')', &$resultMsg);
	$returnText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
	$returnText .= '<fieldset>';
	$returnText .= '<label>Scegli:</label>';
 $returnText .= SYS_DBL_SPACE;
	$returnText .= '<select name="package_id" onchange="javascript:submitform(\'form'.$fDiv0.'\')">';
	foreach ($packageList['path'] as $key=>$fPath) {
		$returnText .= '<option value="'.$fPath.'"';
		if ($fPath == $packagePath) {
			$returnText .= ' selected="selected"';
		};
		$returnText .= '>';
		getPackageRecord($fPath, &$packageRecord);
		$returnText .= $packageRecord['package_full'];
		$returnText .= '</option>';
	};
	$returnText .= '</select>';
 $returnText .= SYS_DBL_SPACE.'<input class="action" type="image" src="'.DCTL_IMAGES.'action_refresh_blue.gif" />';
	$returnText .= '<input type="hidden" name="selectPack" value="seleziona" />';
	$returnText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
	$returnText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
	$returnText .= '</fieldset>';
	$returnText .= '</form>';
 $prosecute = in_array($packagePath, $packageList['path']);
 if ($prosecute) {
  getPackageRecord($packagePath, &$packageRecord);
  $package_short = $packageRecord['package_short'];
  $returnText .= '<h2>Package "'.$packageRecord['package_short'].' - '.$packageRecord['package_work'].'"</h2>';
  $header = $packagePath.DCTL_FILE_HEADER;
  $contents = cleanUpIndentation(file_get_contents($header));
  $select = $PACKAGE_FIELDS['label'];
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//
	// PUBBLICA
 	$isPublished_pub = false;
		$fDiv0 = 'divPublishPackage';
		$returnText .= putOpenCloseLevel($fDiv0, $loc4msg, $package_id != '', 'Procedure di Pubblicazione di "'.$packageRecord['package_short'].'"', &$resultMsg);
		$packageState = '';
		$packageState .= '<img src="'.DCTL_IMAGES.'published_no.png" alt="(unpublished icon)" />';
		$packageState .= '&#160;non pubblicato';
		$path2load = XMLDB_DBCTL_PUB.$collection_id.DB_PATH_SEP.$collection_id.DCTL_RESERVED_INFIX.$package_id.'.xml';
		$xquery = '';
		$xquery .= ' declare namespace xmldb="http://exist-db.org/xquery/xmldb"; ';
		$xquery .= ' if (fn:doc-available("'.$path2load.'")) then ';
		$xquery .= ' let $date := fn:adjust-dateTime-to-timezone(xmldb:last-modified("'.dirname($path2load).'","'.basename($path2load).'")) ';
		$xquery .= ' return ';
		$xquery .= ' <span>{fn:day-from-dateTime($date)}-{fn:month-from-dateTime($date)}-{fn:year-from-dateTime($date)}&#160;@&#160;{fn:hours-from-dateTime($date)}:{fn:minutes-from-dateTime($date)}</span> ';
		$xquery .= ' else fn:string("") ';
		$result = $existQuery->xquery($xquery) or dump($existQuery->getError());
		$resource = (array) $result;
		if (isset($resource['XML'])?$resource['XML']:false) {
			$isPublished_pub = true;
			$packageState = '';
			$packageState .= '<img src="'.DCTL_IMAGES.'published_yes.png" alt="(unpublished icon)" />';
			$packageState .= '&#160;Package pubblicato/ aggiornato il '.$resource['XML'];
		};
		$returnText .= '<table>';
		$returnText .= '<thead>';
		$returnText .= '<tr>';
		$returnText .= '<th class="label">nome</th>';
		$returnText .= '<th>descrizione</th>';
		$returnText .= '<th>contenuto</th>';
		$returnText .= '</tr>';
		$returnText .= '</thead>';
		$returnText .= '<tbody>';
		$returnText .= '<tr>';
		$returnText .= '<td colspan="2">';
		$returnText .= '<img src="'.DCTL_IMAGES.'alert2.gif" alt="(alert)" />'.SYS_DBL_SPACE;

		$lastUpdate = '&#160;non pubblicato';
		$path2load = XMLDB_DBCTL_TMP.$collection_id.DB_PATH_SEP.$collection_id.DCTL_RESERVED_INFIX.$package_id.'.xml';
		$xquery = '';
		$xquery .= ' declare namespace xmldb="http://exist-db.org/xquery/xmldb"; ';
		$xquery .= ' if (fn:doc-available("'.$path2load.'")) then ';
		$xquery .= ' let $date := fn:adjust-dateTime-to-timezone(xmldb:last-modified("'.dirname($path2load).'","'.basename($path2load).'")) ';
		$xquery .= ' return ';
		$xquery .= ' <span>{fn:day-from-dateTime($date)}-{fn:month-from-dateTime($date)}-{fn:year-from-dateTime($date)}&#160;@&#160;{fn:hours-from-dateTime($date)}:{fn:minutes-from-dateTime($date)}</span> ';
		$xquery .= ' else fn:string("") ';
		$result = $existQuery->xquery($xquery) or dump($existQuery->getError());
		$resource = (array) $result;
		if ($resource['XML'] != '') {
			$lastUpdate = $resource['XML'];
		};

		$returnText .= 'Pubblicazione Temporanea per la Verifica dei contenuti prima della pubblicazione on-line..<br />'.SYS_DBL_SPACE.SYS_DBL_SPACE.SYS_DBL_SPACE.'(*) Ultimo aggiornamento: '.$lastUpdate.'</td>';
				if (DCTL_USER_IS_ADMIN) {
		$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=publish&amp;what=package&amp;collection_id='.$collection_id.'&amp;package_id='.$package_id.'&amp;temp=true" title="(Pubblica Temporaneamente per Verifica)">Pubblica temporaneamente...</a></td>';
		} else {
		$returnText .= '<td>(!!!) Pubblicazione momentaneamente disabilitata...</td>';
		};
		$returnText .= '</tr>';
		$returnText .= '<tr>';
		$returnText .= '<td>Stato di Pubblicazione</td>';
 	$returnText .= '<td colspan="2"><strong>'.$packageState.'</strong></td>';
		$returnText .= '</tr>';
		$returnText .= '<tr>';
		$returnText .= '<td>Pubblicazione</td>';
		$returnText .= '<td>'.'Questa funzionalità permette di pubblicare tutti i contenuti del Package per la consultazione degli archivi.'.'</td>';
// 		if ($isPublished_pub) {
// 			$returnText .= '<td>--</td>';
// 		} else {
				if (DCTL_USER_IS_ADMIN) {
		$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=publish&amp;what=package&amp;collection_id='.$collection_id.'&amp;package_id='.$package_id.'" title="(Pubblica il Package)">Pubblica "'.$package_short.'" on-line...</a></td>';
		} else {
 $returnText .= '<td>(!!!) Pubblicazione momentaneamente disabilitata...</td>';
		};
// 		};
		$returnText .= '</tr>';
		$returnText .= '<tr>';
		$returnText .= '<td>Aggiornamento</td>';
		$returnText .= '<td>'.'Questa funzionalità permette di aggiornare solamente i contenuti del Package già pubblicato per la consultazione degli archivi.'.'</td>';
		if ($isPublished_pub) {
			$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=update&amp;what=package&amp;collection_id='.$collection_id.'&amp;package_id='.$package_id.'" title="(Aggiorna solo elementi pubblicati)">Aggiorna "'.$package_short.'" on-line...</a></td>';
		} else {
			$returnText .= '<td>--</td>';
		};
		$returnText .= '</tr>';
		$returnText .= '<tr>';
		$returnText .= '<td>Ritira</td>';
		$returnText .= '<td>'.'Questa funzionalità permette di de-pubblicare tutti i contenuti del Package, rendendoli non visibili al sistema di consultazione degli archivi.'.'</td>';
		if ($isPublished_pub) {
			$returnText .= '<td><a onclick="javascript:doProgress();" href="'.$_SERVER['PHP_SELF'].'?posx='.$fDiv0.'&amp;action=unpublish&amp;what=package&amp;collection_id='.$collection_id.'&amp;package_id='.$package_id.'" title="(De-Pubblica il Package)">Poni "'.$package_short.'" off-line...</a></td>';
		} else {
			$returnText .= '<td>--</td>';
		};
		$returnText .= '</tr>';
		$returnText .= '</tbody>';
		$returnText .= '</table>';
	};
};
	if ($existQuery) dctl_xmldb_disconnect($existQuery);

	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .='</div>';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	echo $returnText;


/* NO ?> IN FILE .INC */
