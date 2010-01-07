<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
 $returnText = '';
	$returnText .='<div id="manager_media" class="layout clearfix">';
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
$returnText .= '<h2>Gestione Collection & Media</h2>';
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
 $returnText .= getManagementOfImages('divImagesCollection', 'dei Media', $collection_id, $collection_short, $loc4msg, &$fCount, $resultMsg);
};
$returnText .= '</div>';
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $returnText .= '</div>';

	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$returnText .='</div>';
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	echo $returnText;


/* NO ?> IN FILE .INC */
