<?php
/**
 +----------------------------------------------------------------------+
 | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
 +----------------------------------------------------------------------+
 | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
 | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
 |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
 +----------------------------------------------------------------------+
 | A main file for "mastro"                                          |
 +----------------------------------------------------------------------+
*/

 if (!defined('_INCLUDE')) define('_INCLUDE', true);

/* INITIALIZE */
require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'../_shared/exist-api.inc.php');
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'./config.inc.php');
/* */
// init engine
$returnText = '';
// setup engine
if ($doc != ''){
	// start engine
	$doc_combo = explode('://', $doc);
	$prefix = $doc_combo[0];
	$doc = isset($doc_combo[1])?$doc_combo[1]:$doc;
	$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
	$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
	switch ($where) {

		case 'head':
			// IDENTIFICA IL DOC
			global $exist;
			$xml_resource = XMLDB_PATH_BASE.$db_collection.DB_PATH_SEPARATOR.$doc;
			$packageRecord = array();
			getPackageRecord ($exist, $xml_resource, &$packageRecord);
			switch ($what) {
				case 'collection':
					$returnText .= $packageRecord['collection'];
				break;
				case 'package':
					$returnText .= $packageRecord['short'];
				break;
				default:
					$returnText .= '<div class="error">'.$what.'</div>';
				break;
			};
		break;

		case 'palette':
			$title = 'Palette';
			$goto = $title;
			$returnText .= '<div class="box_head">';
			$returnText .= '<h2 class="page_curr" rel="'.$db_collection.'-'.$goto.'">'.$title.'</h2>';
			generateVerticalString($title, $goto, $db_collection, true);
			$returnText .= '</div>';
   $returnText .= getDisplayPalette($doc, $where, $what, $block, $at, $high, $label, $terms);
			break;

		case 'navigator':
			if ($what=='') {
				$what = 'menu_edition.xsl';
			};
			forceUTF8($config);
			$configFile = simplexml_load_file($config, 'SimpleXMLElement', DCTL_XML_LOADER);
			$node = $configFile->xpath('menu[@xslt="'.$what.'"]');
			$label = $node[0]['label'];
			$desc = (string)$node[0]->div->asXML();
   $label = $label.DISTINCT_SEP.$desc;
   $returnText .= getDisplayNavigator($doc, $where, $what, $block, $at, $high, $label, $terms);
			break;

		default:
			$returnText .= '<div class="box">';
			switch ($prefix) {
				case 'img':
					$rend = $label;
					$label = stripslashes($label);
					$label = explode(DISTINCT_SEP, $label);
					$alt = isset($label[0]) ? $label[0] : '';
					$path = WEB_PUBLISH.DCTL_MEDIA_BIG.$doc;
					$path .= '?'.filemtime($path);

					$returnText4img = '<img class="magnify fancyzoom widget_image" src="'.$path.'" rel="'.$path.'" alt="'.$alt.'" />';

// 						$returnText4img .=	'<script> $(".fancyzoom").magnify(); </script>';

					if (false) {
						$returnText .= $returnText4img;
					} else {
					 if ($label != '') {
							$returnText .= '<div class="box_head">';
							$title = $label[1];
							$goto = $title;
							$returnText .= '<h2 class="page_curr" rel="'.$doc.'-'.$goto.'">'.$title.'</h2>';
	//						generateVerticalString($title, $goto, $doc);
							$returnText .= '</div>';
							$returnText .= '<ul>';
							$returnText .= '<li class="widget" id="x'.$where.'_w'.md5(uniqid(rand(), true)).'">';
							$returnText .= '<div class="widget_head">';
							$returnText .= '<div class="align_left">';
//							$returnText .= '<a class="collapsible_handle" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
							$returnText .= '&#160;&#160;&#160;';
							$returnText .= '</div>';
							$returnText .= '<div class="align_left widget_name">'.$label[2].', '.$label[3].'</div>';
							$returnText .= '<div class="align_right">';
							$returnText .= '<a class="view_handle"';
							$returnText .= ' onclick="$(\'.magnify:first\',$(this).parents(\'.widget:first\')).magnify();"';
							$returnText .= ' title="{$tooltip_zoom}">&#160;</a>';

							$returnText .= '<div class="align_right">';
							$returnText .= '<a class="reservation_handle" onclick="$(this).reservation(\''.$rend.'\');" title="'.TOOLTIP_ADDTOBASKET.'">&#160;</a>';
							$returnText .= '</div>';
							$returnText .= '</div>';
							$returnText .= '<div class="widget_body">';
							$returnText .= $returnText4img;
							$returnText .= '</div>';
							$returnText .= '</li>';
							$returnText .= '</ul>';
						} else {
							$returnText .= $returnText4img;
						};
					};
// //
					break;
				default:
					global $cachedID;
					preloadID($doc, &$cachedID); // load all IDs to cache for next XSLT
					$xslt = 'body_navigator.xsl';
					$returnText .= transformXMLwithXSLT(MASTRO_DISPLAY, $doc, $where, $xslt, $block, $at, $high, $label);
					$doc_ext = explode('_', $doc);
					$doc_ext = 'body_'.str_ireplace('.xml', '', $doc_ext[1]);
					$xslt = $doc_ext.'.xsl';
					$returnText .= transformXMLwithXSLT(MASTRO_DISPLAY, $doc, $where, $xslt, $block, $at, $high, $label);
				break;
			};
			$returnText .= '</div>';
		break;
	};
};
// return
//echo '<div>'.$returnText.'</div>';
echo $returnText;

?>
