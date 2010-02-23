<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
 $returnText = '';
	$returnText .='<div id="manager_xml" class="layout clearfix">';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .= '<h2>Gestione Collection & Package</h2>';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$isCreateCollection =  FALSE;
if(isset($_REQUEST['createColl'])) {
 $isCreateCollection = $_REQUEST['createColl'] != '';
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$isCreatePackage =  FALSE;
if(isset($_REQUEST['createPack'])) {
 $isCreatePackage = $_REQUEST['createPack'] != '';
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$isCreatePart =  FALSE;
if(isset($_REQUEST['createPart'])) {
 $isCreatePart = $_REQUEST['createPart'] != '';
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$errorInDownload = '';
if (isset($_REQUEST['error'])) {
 $errorInDownload = $_REQUEST['error'] != '';
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// DELETE IMAGE //
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$isUpdateimg =  FALSE;
if(isset($_REQUEST['update_img'])) {
 $isUpdateimg = $_REQUEST['update_img'] != '';
};
$isDeleteimg =  FALSE;
if(isset($_REQUEST['delete_img'])) {
 $isDeleteimg = $_REQUEST['delete_img'] != '';
};
$fOperationImg =  '';
if(isset($_REQUEST['file_img'])) {
	$fOperationImg = $_REQUEST['file_img'];
	$fOperationImg = ($fOperationImg == str_ireplace(DCTL_MEDIA_SML, DCTL_MEDIA_BIG, str_ireplace(DCTL_MEDIA_BIG, DCTL_MEDIA_SML, $fOperationImg))) ? $fOperationImg : false;
};
if ($isDeleteimg) {
 if (is_file($fOperationImg)) {
		unlink($fOperationImg);
		$oldfile=str_ireplace(DCTL_MEDIA_BIG,DCTL_MEDIA_SML,$fOperationImg);
		if (is_file($oldfile)) {
			unlink($oldfile);
		};
		$oldfile=str_ireplace(DCTL_MEDIA_BIG,DCTL_MEDIA_MED,$fOperationImg);
		if (is_file($oldfile)) {
			unlink($oldfile);
		};
		$resultMsg .= '<span class="ok">Ho eliminato il file "'.strtoupper(basename($fOperationImg)).'"...</span><br />';
	} else {
		$resultMsg .= '<span class="ko">Impossibile elminare il file "'.strtoupper(basename($fOperationImg)).'"...</span><br />';
	};
};

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// RESAMPLE JPEG //
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$do_createPreview = FALSE;
if (isset($_REQUEST['createPreview'])) {
 $jpegPath = $_REQUEST['createPreview'];
 $jpegPath = str_ireplace(HOST_BASE_PATH, FS_BASE_PATH, $jpegPath);
 if ((substr($jpegPath, 0, strlen(DCTL_PROJECT_PATH)) == DCTL_PROJECT_PATH) && (is_dir($jpegPath))) {
  createPreview($jpegPath);
  $resultMsg .= '<span class="ok">OK: ho rigenerato le anteprime</span><br />';
 };
};

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// RESET //
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$isReset = FALSE;
if (isSet($_REQUEST['reset'])) {
 $isReset = TRUE;
};
if ($isReset) {
 $array_estensioni_ammesse = array();
 $upKind = '';
 if (isSet($_REQUEST['ext'])) {
  $upKind = $_REQUEST['ext'];
 };
 if ($upKind == 'txt') {
  $array_estensioni_ammesse = array_merge_recursive($array_estensioni_ammesse, $EXTENSION_TEXT);
 };
 $ftp_path = DCTL_PROJECT_PATH;
	$pathToUpload = $_REQUEST['PATH'];
 $dirToUpload = dirname($pathToUpload);
	$dirToUpload = str_ireplace($ftp_path, '', $dirToUpload);
	$dirToUpload = str_ireplace('..','',$dirToUpload);
	$dirToUpload = str_ireplace('.','',$dirToUpload);
	$dirToUpload = str_ireplace(SYS_PATH_SEP.SYS_PATH_SEP,SYS_PATH_SEP,$dirToUpload);
	$dirToUpload = $ftp_path.$dirToUpload;
	$ext = explode('.', $pathToUpload);
	$extToUpload = $ext[count($ext)-1];
	if (in_array($extToUpload, $array_estensioni_ammesse)) {
		if (is_dir($dirToUpload)) {
   $updateDone = true;
			if ($updateDone) {
				$who = '';
				$content = array ();
				$isLocked = checkIfLocked($pathToUpload, &$who, &$content);
				if ($isLocked) {
					if (is_file($content[0])) {
						unlink($content[0]);
					};
				};
			};
			$resultMsg .= '<span class="ok">Sblocco il file "'.strtoupper(basename($pathToUpload)).'"... ok.</span><br />';
		} else {
			$resultMsg .= '<span class="error">ERRORE: directory "'.strtoupper($dirToUpload).'" non trovata... aggiornamento non riuscito.</span><br />';
		};
	} else {
		$resultMsg .= '<span class="error">ERRORE: il file di tipo "'.strtoupper($extToUpload).'" non è valido...</span><br />';
	};
};

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// UPLOAD //
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$isUpload = FALSE;
if (isSet($_REQUEST['upload'])) {
 $isUpload = TRUE;
};
if ($isUpload) {
 $array_estensioni_ammesse = array ();
 $upKind = '';
 if (isSet($_REQUEST['ext'])) {
  $upKind = $_REQUEST['ext'];
 };
 if ($upKind == 'txt') {
  $array_estensioni_ammesse = array_merge_recursive($array_estensioni_ammesse, $EXTENSION_TEXT);
 };
 if ($upKind == 'img') {
  $array_estensioni_ammesse = array_merge_recursive($array_estensioni_ammesse, $EXTENSION_GRAPHIC);
 };
 $ftp_path = DCTL_PROJECT_PATH;
 $pathToUpload0 = $_REQUEST['PATH'];
 foreach ($_FILES as $k=>$v) {
  if ($v['size'] > 0) {
   $pathToUpload = $pathToUpload0;
   $fileNameUpload = strtolower(normalize($v['name']));
   $fileNameTemp = $v['tmp_name'];
   $dirToUpload = dirname($pathToUpload).SYS_PATH_SEP;
   $dirToUpload = str_ireplace($ftp_path, '', $dirToUpload);
   $dirToUpload = str_ireplace('..', '', $dirToUpload);
   $dirToUpload = str_ireplace('.', '', $dirToUpload);
   $dirToUpload = str_ireplace(SYS_PATH_SEP, SYS_PATH_SEP, $dirToUpload);
   $dirToUpload = $ftp_path.$dirToUpload;
   $fileToUpload = basename($pathToUpload);
   $isMedia = (($fileToUpload) == basename(DCTL_MEDIA_BIG));
   $ext = explode('.', $fileNameUpload);
   $extToUpload = $ext[count($ext)-1];
   if (in_array($extToUpload, $array_estensioni_ammesse)) {
    if (is_dir($dirToUpload)) {
     if ($isMedia) {
      $fileToUpload .= SYS_PATH_SEP;
      if ($isUpdateimg) {
       $fileNameUpload = basename($fOperationImg);
       if (is_file($fOperationImg))unlink($fOperationImg);
       $pathToUpload = $dirToUpload.$fileToUpload.$fileNameUpload;
      }
      else {
       $oldName = $fileNameUpload;
       $fileNameUpload = basename(dirname($dirToUpload)).'-'.$oldName;
       $old = $fileNameUpload;
       $pathToUpload = $dirToUpload.$fileToUpload.$fileNameUpload;
       $seqncr = 1;
       $pathToUpload2 = $pathToUpload;
       $ext0 = explode('.', $fileNameUpload);
       $ext0 = $ext0[count($ext0)-1];
       while (file_exists($pathToUpload2)):
        $pathToUpload2 = str_ireplace($ext0, $seqncr++.'.'.$ext0, $pathToUpload);
       endwhile;
       $pathToUpload = $pathToUpload2;
       $old2 = basename($pathToUpload2);
       $fileNameUpload = str_ireplace($old, $old2, $fileNameUpload);
      };
      $fileToUpload = $fileNameUpload;
      $doPreview = TRUE;
     };
     if ($fileToUpload == $fileNameUpload) {
      if (is_file($pathToUpload) && (substr($pathToUpload, -4, 4) == '.xml')) {
       doBackup($fileNameTemp);
       doBackup($pathToUpload);
      };
      if (move_uploaded_file($fileNameTemp, $pathToUpload)) {
       @chmod($pathToUpload, CHMOD); //permessi per poterci sovrascrivere/scaricare
       $updateDone = true;
       $resultMsg .= '<span class="ok">OK: ho caricato il file "'.strtoupper($fileNameUpload).'" in "'.strtoupper(dirname(str_ireplace($ftp_path, '', $pathToUpload))).'".</span><br />';
       if ($isMedia) {
        $resultMsg .= '<form action="javascript:void(0);">';
        $theCode = 'img://'.$fileNameUpload;
        $resultMsg .= '<fieldset><label>URI: </label><input name="new_name" onclick="javascript:this.form.new_name.focus();this.form.new_name.select();" class="linkRule" type="text" value="'.$theCode.'" size="'.strlen($theCode).'"/><label>'.SYS_DBL_SPACE.'
         <span class="help">fai un click sul testo per selezionare tutto l\'identificativo, poi premi command+c o ctrl+c per copiare...</span></label></fieldset></form><br />';
       };
       if ($doPreview) {
        createPreview($pathToUpload, TRUE);
       };
       if ($updateDone) {
        $who = '';
        $content = array ();
        $isLocked = checkIfLocked($pathToUpload, &$who, &$content);
        if ($isLocked) {
         if (is_file($content[0])) {
          unlink($content[0]);
         };
        };
       };
      } else {
       $resultMsg .= '<span class="error">ERRORE: non riesco a modificare il file "'.strtoupper($fileNameUpload).'"... aggiornamento non riuscito.</span><br />';
      };
     } else {
      $resultMsg .= '<span class="error">ERRORE: il file "'.strtoupper($fileNameUpload).'" non corrisponde a "'.strtoupper($fileToUpload).'"... aggiornamento non riuscito.</span><br />';
     };
    } else {
     $resultMsg .= '<span class="error">ERRORE: directory "'.strtoupper($dirToUpload).'" non trovata... aggiornamento non riuscito.</span><br />';
    };
   } else {
    $resultMsg .= '<span class="error">ERRORE: il file di tipo "'.strtoupper($extToUpload).'" non è valido...</span><br />';
   };
  };
 };
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// COLLECTION //
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// CREATE COLLECTION
if ($isCreateCollection) {
 if ($collection_short != '') {
  getCollectionList(DCTL_PROJECT_PATH, &$collectionList, true);
  $collection_id = strtolower(normalize($collection_short));
  if (array_search($collection_id, $collectionList['collection_id']) === FALSE) {
   $collectionTemplate = DCTL_SETTINGS_TEMPLATES_COLLECTION;
			$collectionPath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEP;
   dircopy($collectionTemplate, $collectionPath);
   $header = $collectionPath.DCTL_FILE_HEADER;
   if (is_file($header)) {
    $contents = cleanUpIndentation(file_get_contents($header));
    $contents = str_ireplace('collection_id ""', 'collection_id "'.$collection_id.'"', $contents);
    $contents = str_ireplace('collection_short ""', 'collection_short "'.$collection_short.'"', $contents);
    if (file_put_contents($header, forceUTF8($contents, $header)) !== FALSE) {
					@chmod($header, CHMOD);
     getCollectionList(DCTL_PROJECT_PATH, &$collectionList, true);
     $prosecute = array_search($collection_short, $collectionList['collection_short']) !== FALSE;
     if ($prosecute) {
      $resultMsg .= '<span class="ok">OK: ho creato una nuova Collection con nome "'.$collection_short.'" e identificativo "'.$collection_id.'". Completa la procedura modificando i dati...</span><br />';
      $isEditCollection = TRUE;
     } else {
      $resultMsg .= '<span class="error">ERRORE: non riesco a creare una Collection con nome "'.$collection_short.'"... aggiornamento non riuscito!</span><br />';
     };
    } else {
     $resultMsg .= '<span class="error">ERRORE: non riesco a scrivere il file "'.$header.'" ... aggiornamento non riuscito!</span><br />';
    };
   } else {
    $resultMsg .= '<span class="error">ERRORE: non riesco a trovare il file "'.$header.'" ... aggiornamento non riuscito!</span><br />';
   };
  } else {
   if ($collection_short != '') {
    $resultMsg .= '<span class="error">ERRORE: una Collection con identificativo "'.$collection_id.'" (da "'.$collection_short.'") esiste già...</span><br />';
   };
  };
 };
};
//
// SAVE COLLECTION
if ($isSaveCollection) {
 if ($collection_id != '') {
  $collectionPath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEP;
  $header1 = DCTL_SETTINGS_TEMPLATES_COLLECTION.DCTL_FILE_HEADER;
  $header2 = $collectionPath.DCTL_FILE_HEADER;
  if (is_file($header1)) {
   $contents = cleanUpIndentation(file_get_contents($header1));
   $select = $COLLECTION_FIELDS['label'];
   foreach ($select as $field=>$label) {
    if (isset($_REQUEST[$field])) {
     $value = $_REQUEST[$field];
					$value = stripslashes($value);
					$valueString = cleanWebString($value);
     $contents = str_ireplace($field.' ""', $field.' "'.$value.'"', $contents);
    };
   };
   doBackup($header2);
   if (file_put_contents($header2, forceUTF8($contents, $header2)) !== FALSE) {
				@chmod($header2, CHMOD);
    $resultMsg .= '<span class="ok">Ho aggiornato le informazioni per "'.$collection_short.'"</span><br />';
   } else {
    $resultMsg .= '<span class="error">ERRORE: non riesco a scrivere il file "'.$header2.'" ... aggiornamento non riuscito!</span><br />';
   };
  } else {
   $resultMsg .= '<span class="error">ERRORE: non riesco a trovare il file "'.$header1.'" ... aggiornamento non riuscito!</span><br />';
  };
 };
};
//
// NEW COLLECTION
if (DCTL_USER_IS_ADMIN) {
 $fDiv0 = 'divCreateCollection';
 $returnText .= putOpenCloseLevel($fDiv0, $loc4msg, false, 'Crea nuova Collection', &$resultMsg);
 $returnText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
 $returnText .= '<fieldset>';
 $returnText .= '<label>Nome:</label>';
 $returnText .= SYS_DBL_SPACE;
 $returnText .= '<input type="text" name="collection_short" size="'.FIELD_CODE_LENGTH.'" maxlength="'.FIELD_CODE_LENGTH.'" value="';
 if ($resultMsg != '') {
  $returnText .= $collection_short;
 };
 $returnText .= '" />';
 $returnText .= SYS_DBL_SPACE;
 $returnText .= '<input type="submit" name="createColl" value="crea" />';
 $returnText .= '</fieldset>';
 $returnText .= '<fieldset>';
 $returnText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
 $returnText .= '</fieldset>';
 $returnText .= '</form>';
 $returnText .= '<br /></div>';
};
//

// SELEZIONA COLLECTION
$fDiv0 = 'divSelectCollection';
$collectionPath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEP;
getCollectionList(DCTL_PROJECT_PATH, &$collectionList, true);
$returnText .= putOpenCloseLevel($fDiv0, $loc4msg, $collection_id != '', 'Seleziona una Collection ('.(count($collectionList['path'])-1).')', &$resultMsg);
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
 if ($fPath != '') {
		getCollectionRecord($fPath, &$collectionRecord);
		$returnText .= $collectionRecord['collection_full'];
	};
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
 $returnText .= '<h2>Collection "'.$collectionRecord['collection_short'].' - '.$collectionRecord['collection_work'].'"</h2>';
 $header = $collectionPath.DCTL_FILE_HEADER;
 $contents = cleanUpIndentation(file_get_contents($header));
 $select = $COLLECTION_FIELDS['label'];
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 //
 // EDIT COLLECTION
 $fDiv0 = 'divEditCollection';
 $returnText .= putOpenCloseLevel($fDiv0, $loc4msg, $isEditCollection, 'Frontespizio Elettronico di "'.$collection_short.'"', &$resultMsg);
 $returnText .= putEdit('editColl');
  if ($isEditCollection) {
  $returnText .= '<form action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
  $returnText .= '<fieldset>';
 };
 $returnText .= '<table>';
 $returnText .= '<thead>';
 $returnText .= '<tr>';
 $returnText .= '<th class="label">nome</th>';
 $returnText .= '<th>contenuto</th>';
 $returnText .= '</tr>';
 $returnText .= '</thead>';
 $returnText .= '<tbody>';
 $lines = explode('<!ENTITY ', $contents);
 foreach ($select as $field=>$label) {
  $linex = array_values(preg_grep('/'.$field.'.*/', $lines));
  $line = $linex[0];
  if (stripos($line, $field) !== FALSE) {
   $linex = explode('"', $line);
   $value = $linex[1];
			$value = stripslashes($value);
   $valueString = cleanWebString($value);
   $valueCode = normalize($value);
   $valueText = str_ireplace('$NL$', "\n", cleanWebString(str_ireplace('<lb />','$NL$', $value)));
   $returnText .= '<tr>';
   $returnText .= '<td>'.$label.'</td>';
   $returnText .= '<td>';
   if ($isEditCollection) {
    switch ($COLLECTION_FIELDS['type'][$field]) {
    case 'id':
     $returnText .= '<strong>'.$valueString.'</strong><br />'.'</td>';
     $returnText .= '<input type="hidden" name="'.$field.'" value="'.$value.'" />';
     break;
    case 'auto':
     switch ($field) {
     case 'collection_packageCount':
      getPackageList($collectionPath, &$packageList, true);
      $valueCode = sprintf("%03d", count($packageList['path'])-1);
      break;
     };
     $returnText .= '<strong>'.$valueCode.'</strong><br />'.'</td>';
     $returnText .= '<input type="hidden" name="'.$field.'" value="'.$valueCode.'" />';
     break;
    case 'code':
     if ($valueCode == '') {
      $returnText .= '<input name="'.$field.'" type="text" size="'.FIELD_CODE_LENGTH.'" maxlength="'.FIELD_CODE_LENGTH.'" value="'.$valueCode.'" />'.'</td>';
     } else {
      $returnText .= '<strong>'.$valueCode.'</strong><br />'.'</td>';
      $returnText .= '<input type="hidden" name="'.$field.'" value="'.$valueCode.'" />';
     };
     break;
    case 'string':
     $returnText .= '<input name="'.$field.'" type="text" size="'.FIELD_STRING_LENGTH.'" maxlength="'.FIELD_STRING_MAXLENGTH.'" value="'.$valueString.'" />'.'</td>';
     break;
    case 'date':
     $returnText .= '<input name="'.$field.'" type="text" size="'.FIELD_DATE_LENGTH.'" maxlength="'.FIELD_DATE_LENGTH.'" value="'.$valueString.'" />'.'</td>';
     break;
    case 'list':
     $obj = $collection_id.'_'.$field;
     $returnText .= '<input id="'.$obj.'" name="'.$field.'" type="text" size="'.FIELD_STRING_LENGTH.'" maxlength="'.FIELD_STRING_MAXLENGTH.'" value="'.$valueString.'" />';
     $returnText .= getTextClassList (getListNameForField($COLLECTION_FIELDS, $field), $obj);
     $returnText .= '</td>';
     break;
    case 'text':
     $returnText .= '<textarea name="'.$field.'" cols="'.FIELD_TEXT_LENGTH.'" rows="'.FIELD_TEXT_HEIGHT.'">'.$valueText.'</textarea>'.'</td>';
     break;
    };
   } else {
    $returnText .= '<strong>'.$valueText.'</strong><br />'.'</td>';
   };
   $returnText .= '</tr>';
  };
 };
 $returnText .= '</tbody>';
 $returnText .= '</table>';
 if ($isEditCollection) {
  $returnText .= '<input type="submit" name="saveColl" value="registra" />';
  $returnText .= '<input type="hidden" name="editColl" value="" />';
  $returnText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
  $returnText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
  $returnText .= '</fieldset>';
  $returnText .= '</form>';
 };
 $returnText .= '<br /></div>';
 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// $returnText .= getManagementOfImages('divImagesCollection', 'dei Media', $collection_id, $collection_short, $loc4msg, &$fCount, $resultMsg);
  // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 // PACKAGE //
 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $returnText .= '<h2>Gestione Package di "'.$collection_short.'"</h2>';
 //
 // CREATE PACKAGE
	if ($isCreatePackage) {
		if ($package_short != '') {
		 if (!in_array($package_ext, $EXTENSION_PACKAGE)) {
				$resultMsg .= '<span class="error">ERRORE: estensione "'.$package_ext.'" non riconosciuta!</span><br />';
		 } else {
		  $package_short = explode('_', $package_short);
		  $package_short = strtoupper($package_short[0].$package_ext);
				getPackageList($collectionPath, &$packageList, true);
				$package_id = strtolower(normalize($package_short));
				if (array_search($package_id, $packageList['package_id']) === FALSE) {
					$packageTemplate = DCTL_SETTINGS_TEMPLATES_PACKAGE;
					$packagePath = $collectionPath.$package_id.SYS_PATH_SEP;
					dircopy($packageTemplate, $packagePath);
					$idx = 0;
					$idx = sprintf("%03d", $idx+1);
					$from = $packagePath.DCTL_PACKAGE_BODY;
					$to = $packagePath.str_ireplace('$', $idx, DCTL_PACKAGE_BODY);
					rename($from, $to);
					$header = $packagePath.DCTL_FILE_HEADER;
					if (is_file($header)) {
						$contents = cleanUpIndentation(file_get_contents($header));
						$contents = str_ireplace('package_id ""', 'package_id "'.$package_id.'"', $contents);
						$contents = str_ireplace('package_short ""', 'package_short "'.$package_short.'"', $contents);
						if (file_put_contents($header, forceUTF8($contents, $header)) !== FALSE) {
							@chmod($header, CHMOD);
							getPackageList($collectionPath, &$packageList, true);
							$prosecute = array_search($package_short, $packageList['package_short']) !== FALSE;
							if ($prosecute) {
								$resultMsg .= '<span class="ok">OK: ho creato un nuovo Package con nome "'.$package_short.'" e identificativo "'.$package_id.'". Completa la procedura modificando i dati...</span><br />';
								$isEditPackage = TRUE;
							} else {
								$resultMsg .= '<span class="error">ERRORE: non riesco a creare un Package con nome "'.$package_short.'"... aggiornamento non riuscito!</span><br />';
							};
						} else {
							$resultMsg .= '<span class="error">ERRORE: non riesco a scrivere il file "'.$header.'" ... aggiornamento non riuscito!</span><br />';
						};
					} else {
						$resultMsg .= '<span class="error">ERRORE: non riesco a trovare il file "'.$header.'" ... aggiornamento non riuscito!</span><br />';
					};
				} else {
					if ($package_short != '') {
						$resultMsg .= '<span class="error">ERRORE: un Package con identificativo "'.$package_id.'" (da "'.$package_short.'") esiste già...</span><br />';
					};
				};
			};
		};
	};
 //
 // SAVE PACKAGE
	if ($isSavePackage) {
		if ($package_id != '') {
			$package_ext = explode('_', $package_id);
			$package_ext = strtolower('_'.$package_ext[count($package_ext)-1]);
			if (!in_array($package_ext, $EXTENSION_PACKAGE)) {
				$resultMsg .= '<span class="error">ERRORE: correggere il package "'.$package_id.'" ... estensione non riconosciuta!</span><br />';
			} else {
				$packagePath = $collectionPath.$package_id.SYS_PATH_SEP;
				$header1 = DCTL_SETTINGS_TEMPLATES_PACKAGE.DCTL_FILE_HEADER;
				$header2 = $packagePath.DCTL_FILE_HEADER;
				if (is_file($header1)) {
					$contents = cleanUpIndentation(file_get_contents($header1));
					$select = $PACKAGE_FIELDS['label'];
					foreach ($select as $field=>$label) {
						if (isset($_REQUEST[$field])) {
							$value = $_REQUEST[$field];
							if (($field == 'package_work') && ($value == '')) {
								$value = $_REQUEST['source_author'].' - '.$_REQUEST['source_title_main'];
							};
       $value = stripslashes($value);
							$value = trim(preg_replace('/'.WS.WS.'+/', ' ', $value));
							$value = htmlentities($value, ENT_QUOTES, 'UTF-8');
							$value = preg_replace('/'.WS.WS.'+/', '<lb />', $value);
							$value = stripslashes($value);
							$contents = str_ireplace($field.' ""', $field.' "'.$value.'"', $contents);
						};
					};
					doBackup($header2);
					if (file_put_contents($header2, forceUTF8($contents, $header2)) !== FALSE) {
					 @chmod($header2, CHMOD);
						$resultMsg .= '<span class="ok">Ho aggiornato le informazioni per "'.$package_short.'"</span><br />';
					} else {
						$resultMsg .= '<span class="error">ERRORE: non riesco a scrivere il file "'.$header2.'" ... aggiornamento non riuscito!</span><br />';
					};
				} else {
					$resultMsg .= '<span class="error">ERRORE: non riesco a trovare il file "'.$header1.'" ... aggiornamento non riuscito!</span><br />';
				};
 		};
		};
	};
	//
	// CREATE PART
	if ($isCreatePart) {
		if ($package_id != '') {
   $from = DCTL_SETTINGS_TEMPLATES_PACKAGE.DCTL_PACKAGE_BODY;
		 if (is_file($from)) {
				$dPath = $collectionPath.$package_id.SYS_PATH_SEP;
				$fSelectorX = DCTL_PACKAGE_BODY;
				$regexp = str_ireplace(DCTL_PACKAGE_BODY_REGEXP1, DCTL_PACKAGE_BODY_REGEXP2, $fSelectorX);
				$variants = array();
				$handle = opendir($dPath);
				while ($entry = readdir($handle)) {
					if (substr($entry, 0, 1) != '.') {
						$variants[] = $entry;
					};
				};
				$variants = array_values(preg_grep('/^'.$regexp.'/', $variants));
				rsort($variants);
				$idx = preg_replace('/'.DCTL_PACKAGE_BODY_REGEXP3.'/', '$1', $variants[1]) + 1; // p999 ultimo....
				if ($idx<999) {
					$idx = sprintf("%03d", $idx);
					$to = $dPath.str_ireplace('$', $idx, DCTL_PACKAGE_BODY);
					copy($from, $to);
					@chmod($to, CHMOD);
					$resultMsg .= '<span class="ok">Ho creato una nuova parte del testo (n. '.$idx.')</span><br />';
				} else {
					$resultMsg .= '<span class="error">ERRORE: raggiunto il massimo numero di parti (n. '.$idx.')</span><br />';
				};
			};
		} else {
			$resultMsg .= '<span class="error">ERRORE: non riesco a trovare il file "'.$from.'" ... aggiornamento non riuscito!</span><br />';
		};
	};
	//
	// NEW PACKAGE
 if (DCTL_USER_IS_ADMIN) {
  $fDiv0 = 'divCreatePackage';
  $returnText .= putOpenCloseLevel($fDiv0, $loc4msg, false, 'Crea nuovo Package di "'.$collection_short.'"', &$resultMsg);
  $returnText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
  $returnText .= '<fieldset>';
  $returnText .= '<label>Tipo:</label>';
  $returnText .= SYS_DBL_SPACE;
  foreach ($EXTENSION_PACKAGE as $label=>$ext) {
			$returnText .= '<input type="radio" name="package_ext" value="'.$ext.'"';
			if ($resultMsg != '') {
				if ($package_ext == $ext) $returnText .= ' checked="checked"';
			};
			$returnText .= '/>&#160;<strong>'.$label.'</strong>'.SYS_DBL_SPACE;
  };
  $returnText .= '<br />';
  $returnText .= '<label>Nome:</label>';
  $returnText .= SYS_DBL_SPACE;
  $returnText .= '<input type="text" name="package_short" size="'.FIELD_CODE_LENGTH.'" maxlength="'.FIELD_CODE_LENGTH.'" value="';
  if ($resultMsg != '') {
   $returnText .= $package_short;
  };
  $returnText .= '" />';
  $returnText .= SYS_DBL_SPACE;
  $returnText .= '<input type="submit" name="createPack" value="crea" />';
  $returnText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
  $returnText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
  $returnText .= '</fieldset>';
  $returnText .= '</form>';
  $returnText .= '<br /></div>';
 };
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
		if ($fPath != '') {
			getPackageRecord($fPath, &$packageRecord);
			$returnText .= $packageRecord['package_full'];
		};
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
		$package_ext = explode('_', $package_id);
		$package_ext = strtolower('_'.$package_ext[count($package_ext)-1]);
  if (!in_array($package_ext, $EXTENSION_PACKAGE)) {
			$returnText .= '<span class="error">ERRORE: correggere il package "'.$packageRecord['package_short'].'" ... estensione non riconosciuta!</span><br />';
		} else {
			$header = $packagePath.DCTL_FILE_HEADER;
			$contents = cleanUpIndentation(file_get_contents($header));
			$select = $PACKAGE_FIELDS['label'];
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
			//
			// EDIT PACKAGE
			$fDiv0 = 'divEditPackage';
			$returnText .= putOpenCloseLevel($fDiv0, $loc4msg, $isEditPackage, 'Frontespizio Elettronico di "'.$package_short.'"', &$resultMsg);
			$returnText .= putEdit('editPack');
			if ($isEditPackage) {
				$returnText .= '<form action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
				$returnText .= '<fieldset>';
			};
			$returnText .= '<table>';
			$returnText .= '<thead>';
			$returnText .= '<tr>';
			$returnText .= '<th class="label">nome</th>';
			$returnText .= '<th>contenuto</th>';
			$returnText .= '</tr>';
			$returnText .= '</thead>';
			$returnText .= '<tbody>';
			$lines = explode('<!ENTITY ', $contents);
			foreach ($select as $field=>$label) {
				$linex = array_values(preg_grep('/'.$field.'.*/i', $lines));
				if (!isset($linex[0])) { // aggiunge parametro
					$linex[0] ='<!ENTITY '.$field.' "">';
				};
				$line = $linex[0];
				if (stripos($line, $field) !== FALSE) {
					$linex = explode('"', $line);
					$value = $linex[1];
					$valueString = cleanWebString($value);
					$valueCode = normalize($value);

					$valueText = str_ireplace('$NL$', "\n", cleanWebString(str_ireplace('<lb />','$NL$', $value)));
					$returnText .= '<tr>';
					$returnText .= '<td>'.$label.'</td>';
					$returnText .= '<td>';
					if ($isEditPackage) {
						switch ($PACKAGE_FIELDS['type'][$field]) {
						case 'id':
							$returnText .= '<strong>'.$valueCode.'</strong><br />'.'</td>';
							$returnText .= '<input type="hidden" name="'.$field.'" value="'.$valueCode.'" />';
							break;
						case 'auto':
							$returnText .= '<strong>'.$value.'</strong><br />'.'</td>';
							$returnText .= '<input type="hidden" name="'.$field.'" value="'.$valueString.'" />';
							break;
						case 'code':
							if ($valueCode == '') {
								$returnText .= '<input name="'.$field.'" type="text" size="'.FIELD_CODE_LENGTH.'" maxlength="'.FIELD_CODE_LENGTH.'" value="'.$valueCode.'" />'.'</td>';
							} else {
								$returnText .= '<strong>'.$valueCode.'</strong><br />'.'</td>';
								$returnText .= '<input type="hidden" name="'.$field.'" value="'.$valueCode.'" />';
							};
							break;
						case 'string':
							$returnText .= '<input name="'.$field.'" type="text" size="'.FIELD_STRING_LENGTH.'" maxlength="'.FIELD_STRING_MAXLENGTH.'" value="'.$valueString.'" />'.'</td>';
							break;
						case 'date':
							$returnText .= '<input name="'.$field.'" type="text" size="'.FIELD_DATE_LENGTH.'" maxlength="'.FIELD_DATE_LENGTH.'" value="'.$valueString.'" />'.'</td>';
							break;
						case 'radio':
							foreach(explode('|',$PACKAGE_FIELDS['opts'][$field]) as $k=>$opt) {
								$optx = explode('=',$opt);
								$opt = $optx[count($optx)-1];
								$lbl = $optx[0];
								$returnText .= '<input name="'.$field.'" type="radio"';
								if ((isset($PACKAGE_FIELDS['lock'][$field]))?$PACKAGE_FIELDS['lock'][$field]:false) {
									if ($valueString != '') {
											$returnText .= ' disabled="disabled"';
									};
								};
        if ((($k==0) && ($valueString == '')) || ($valueString==$opt)) {
										$returnText .= ' checked="checked"';
								};
								$returnText .= ' value="'.$opt.'" />';
								$returnText .= '<label>'.$lbl.'&emsp;</label>';
							};
							$returnText .= '</td>';
							break;
						case 'list':
							$obj = $package_id.'_'.$field;
							$returnText .= '<input id="'.$obj.'" name="'.$field.'" type="text" size="'.FIELD_STRING_LENGTH.'" maxlength="'.FIELD_STRING_MAXLENGTH.'" value="'.$valueString.'" />';
							$returnText .= getTextClassList (getListNameForField($PACKAGE_FIELDS, $field), $obj);
							$returnText .= '</td>';
							break;
						case 'text':
							$returnText .= '<textarea name="'.$field.'" cols="'.FIELD_TEXT_LENGTH.'" rows="'.FIELD_TEXT_HEIGHT.'">'.$valueText.'</textarea>'.'</td>';
							break;
						};
					} else {
						switch ($PACKAGE_FIELDS['type'][$field]) {
							case 'radio':
								foreach(explode('|',$PACKAGE_FIELDS['opts'][$field]) as $k=>$opt) {
									$optx = explode('=',$opt);
									$opt = $optx[count($optx)-1];
									$lbl = $optx[0];
									if ($valueText ==$opt) $returnText .= '<strong>'.$lbl.'</strong><br />'.'</td>';
								};
							break;
						 default:
								$returnText .= '<strong>'.$valueText.'</strong><br />'.'</td>';
							break;
						};
					};
					$returnText .= '</tr>';
				};
			};
			$returnText .= '</tbody>';
			$returnText .= '</table>';
			if ($isEditPackage) {
				$returnText .= '<input type="submit" name="savePack" value="registra" />';
				$returnText .= '<input type="hidden" name="editPack" value="" />';
				$returnText .= '<input type="hidden" name="package_id" value="'.$package_id.'" />';
				$returnText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
				$returnText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
				$returnText .= '</fieldset>';
				$returnText .= '</form>';
			};
			$returnText .= '<br /></div>';
			// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//   $returnText .= getManagementOfXML(DCTL_PACKAGE_FRONT, 'divHandlePackageFront', 'Parte Iniziale', $collection_id, $package_id, $package_short, $errorInDownload, $loc4msg, &$fCount, $resultMsg);
			// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
			$returnText .= getManagementOfXML(DCTL_PACKAGE_BODY, 'divHandlePackageBody$', 'Parte $', $collection_id, $package_id, $package_short, $errorInDownload, $loc4msg, &$fCount, $resultMsg);
			// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//   $returnText .= getManagementOfXML(DCTL_PACKAGE_BACK, 'divHandlePackageBack', 'Parte Finale', $collection_id, $package_id, $package_short, $errorInDownload, $loc4msg, &$fCount, $resultMsg);
			// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

					$fCount = 0;
		};
	};
};
$returnText .= '</div>';
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $returnText .= '</div>';

	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .='</div>';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	echo $returnText;

/* NO ?> IN FILE .INC */
