<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);
 $returnText = '';

	/* AUTHENTICATE */
	if (is_file(str_replace('//','/',dirname(__FILE__).'/').'.htaccess')) {
		require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/authenticate.inc.php');
	} else {
  die('ERROR: access control not found... Fix it. Me, i abort.');
	};
	/* INITIALIZE */
	require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
	require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'./config.inc.php');
	/* */
 $action = '';
 if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
 $level = -1;
 if (isset($_REQUEST['level'])) $level = $_REQUEST['level'];
	$selector = 1;
	if (isset($_REQUEST['selector'])) $selector = $_REQUEST['selector'];
	$where = '';
 if (isset($_REQUEST['id'])) $where = $_REQUEST['id'];
 $collection_id = '';
 if (isset($_REQUEST['collection_id'])) $collection_id = $_REQUEST['collection_id'];
 $package_id = '';
 if (isset($_REQUEST['package_id'])) $package_id = $_REQUEST['package_id'];
 $part_id = '';
 if (isset($_REQUEST['part_id'])) $part_id = $_REQUEST['part_id'];
 $item_id = '';
 if (isset($_REQUEST['item_id'])) $item_id = $_REQUEST['item_id'];
 $what = '';
 if (isset($_REQUEST['what'])) $what = $_REQUEST['what'];
 $link_id1 = '';
 if (isset($_REQUEST['link_id1'])) $link_id1 = $_REQUEST['link_id1'];
 $link_id2 = '';
 if (isset($_REQUEST['link_id2'])) $link_id2 = $_REQUEST['link_id2'];
 $link_label = '';
 if (isset($_REQUEST['link_label'])) $link_label = $_REQUEST['link_label'];
 $media_id = '';
 if (isset($_REQUEST['media_id'])) $media_id = $_REQUEST['media_id'];
	$loc4msg = '';
	if (isset($_REQUEST['posx'])) $loc4msg = $_REQUEST['posx'];
		$fCount = '';
	if (isset($_REQUEST['count'])) $fCount = $_REQUEST['count'];
	$resultMsg = '';
	if (isset($_REQUEST['msg'])) $resultMsg = $_REQUEST['msg'];

	$url = '';
	if (isset($_REQUEST['url'])) $url = $_REQUEST['url'];

	$imt = '';
	if (isset($_REQUEST[DCTL_EXT_IMT_CBP])) $imt = $_REQUEST[DCTL_EXT_IMT_CBP];

	$linkersList = array();

	$mappersList = array();

 switch ($action) {
  case 'update_imt':
   $xml = base64_decode($imt);
   $simplexml = simplexml_load_string($xml);
   if ($simplexml) {
    $returnText .= '<ul>';
    foreach ($simplexml->xml->a as $xml) {
     $ref = (string)$xml['r']; // => @xml:id
     $src = (string)$xml['s']; // => @target[1]
     $tgt = (string)$xml['t']; // => @target[2]
     $lbl = (string)$xml['l']; // => @n
// <ref xml:id="afd-1xvJpQG8U5" type="link" n="pallonzoli" target="xml://afd/marmi_img/p004ki001 img://afd-marmi_p1_08_pw.jpg@0.1160:0:4520:0.4030:0:4520:0.4030:0.7300:0.1160:0.7300">palle di cerchi</ref>
     $returnText .= '<li>';
     if ($tgt == '') {
      if ($ref == '') {
       $returnText .= ''; // IGNORED
      } else {
       $returnText .= ajax_deleteLink($ref, $src, $lbl, 'map', true);
      };
     } else {
      if ($ref == '') {
       $returnText .= ajax_saveLink('new', $src, $tgt, $lbl, 'map', true);
      } else {
       $returnText .= ajax_saveLink('ovw', $src.' '.$tgt, $ref, $lbl ,'map', true);
      };
     };
     $returnText .= NOVEOPIU ? htmlspecialchars($xml->asXML()) : '';
     $returnText .= '</li>';
    };
    $returnText .= '</ul>';
   } else {
    $returnText .= '<span class="warning">niente da interpretare</span><br/>';
   };

   break;

  case 'ajax_loadTree':
  case 'ajax_loadChunk':
  case 'ajax_loadId':
  case 'ajax_loadLinkList':
  case 'ajax_loadImageList':
			$returnText .= $action($selector, $collection_id, $package_id, $part_id, $item_id, $what);
    break;

   case 'ajax_deleteLink':
				$returnText .= $action($link_id1, $link_id2, $link_label, $what);
    break;

   case 'ajax_saveLink':
				$returnText .= $action($selector, $link_id1, $link_id2, $link_label, $what);
    break;

  case 'ajax_loadImage':
   $returnText .= $action($collection_id, $package_id);
   break;

  case 'links_editLink':
   $returnText .= links_editLink($collection_id, $package_id, $link_coll, $link_type, $link_pack, $link_part, $link_id, $loc4msg, $what);
   break;

  case 'get_file':
    $icon = DCTL_APPS_PATH.'img'.SYS_PATH_SEP.'missing.gif';
    $fPath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEP.$url;
    if (is_file($fPath)) {
					$mime=getMIME($fPath);
					$ext = strtolower(substr($fPath, -3, 3));
					if (isImage($mime,$ext)) {
						$icon =	$fPath;
					};
    };
	   $iconMime=image2MIME($icon);
				if ($iconMime==false) $iconMime="image/jpeg";
				header("Content-type: $iconMime",true);
				readfile($icon);
   break;

  case 'load_preview':
			$fPath = preg_replace('%'.HOST_BASE_PATH.'%', FS_BASE_PATH, $url, 1);
			if (is_file($fPath)) {
				$big = $fPath;
				$med = str_ireplace(DCTL_MEDIA_BIG, DCTL_MEDIA_MED, $fPath);
				$sml = str_ireplace(DCTL_MEDIA_BIG, DCTL_MEDIA_SML, $fPath);
				if (!is_file($med) ||!is_file($sml)) {
							createPreview($big);
				};
				$fName = basename($fPath);
				$label = $fName;
				list($fWidth, $fHeight) = getimagesize($fPath);
				$maxH = 120;
				$maxW = 120;
    if($fHeight > $maxH) {
					$fWidth = $maxH * $fWidth / $fHeight;
					$fHeight = $maxH;
				};
				if($fWidth > $maxW) {
					$fHeight = $maxW * $fHeight / $fWidth;
					$fWidth = $maxW;
				};
				$returnText .= '<form id="form_img" action="indexMedia.php" method="'.DCTL_FORM_METHOD_POST.'" enctype="'.DCTL_FORM_ENCTYPE_POST.'">';
    $url = 'indexAjax.php?action=get_file&amp;collection_id='.$collection_id.'&amp;url='.DCTL_MEDIA_BIG.$fName;
    $pvw = 'indexAjax.php?action=get_file&amp;collection_id='.$collection_id.'&amp;url='.DCTL_MEDIA_SML.$fName;
				$ext = strtolower(substr($url, -3, 3));
				if (!in_array($ext, $EXTENSION_PREVIEW)) {
				 $pvw .= '.gif';
				};
				$returnText .= '<div><a target="_new"  href="'.$url.'" title="(...)"><img width="'.$fWidth.'" src="'.$pvw.'" alt="(preview)" /></a></div>';
				$returnText .= '<div>';
				$theCode = 'img://'.implode('/', explode('-', $label));
				$returnText .= '<fieldset><label>URI: </label><input name="new_name" onclick="javascript:this.form.new_name.focus();this.form.new_name.select();" class="linkRule" type="text" value="'.$theCode.'" size="'.strlen($theCode).'"/><label><br /><span class="help">fai un click sul testo per selezionare tutto l\'identificativo, poi premi command+c o ctrl+c per copiare...</span></label></fieldset>';
				$returnText .= '</div>';
				$returnText .= '<div><fieldset>';
				$returnText .= '<label class="help">Elimina l\'immagine selezionata:</label>&#160;';
				$returnText .= '<input type="submit" name="delete_img" value="elimina"/>';
				$returnText .= '</fieldset>';
				$returnText .= '<br />';
				$returnText .= '<fieldset>';
				$returnText .= '<label class="help">Aggiorna l\'immagine selezionata:</label>&#160;';
				$fCount = 1;
				$dPath = dirname($fPath); //str_ireplace(DCTL_MEDIA_SML, DCTL_MEDIA_BIG, $fPath)
				$returnText .= '<input type="file" name="FILE'.$fCount.'" value="'.$dPath.'" />';
				$returnText .= '<input type="hidden" name="PATH" value="'.$dPath.'" />';
   	$returnText .= '<input type="hidden" name="posx" value="'.$loc4msg.'" />';
				$returnText .= '<input type="hidden" name="ext" value="img" />';
				$returnText .= '<input type="hidden" name="update_img" value="true" />';
				$returnText .= SYS_DBL_SPACE.'<input type="submit" name="upload" value="invia" />';
				$returnText .= '</fieldset>';
				$returnText .= '<fieldset>';
				$returnText .= '<input type="hidden" name="file_img" value="'.$fPath.'"/>';
				$returnText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
				$returnText .= '</fieldset>';
				$returnText .= '</form>';
			} else {
				$returnText .= '? AJAX ACTION ('.$action.') :: UNKNOWN FILE '.$fPath.' ?';
			};
   break;

  default:
   $returnText .= '? AJAX ACTION ('.$action.')?';
   break;

 };

	echo $returnText;

?>
