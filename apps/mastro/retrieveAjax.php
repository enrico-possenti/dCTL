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
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'../_shared/exist-api.inc.php');
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'./config.inc.php');
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

		case DCTL_DB_NAME:
   $what = isset($_REQUEST['q']) ? $_REQUEST['q'] : $what;
   $selector = $block;
			$returnText .= getRetrieverDB($doc, $what, $selector);
		break;

		case 'palette':
			$returnText .= getRetrievePalette($doc, $where);
			break;

		case 'navigator':
			forceUTF8($high);
			$configFile = simplexml_load_file($high, 'SimpleXMLElement', DCTL_XML_LOADER);
			$high = '';
			$node = $configFile->xpath('menu[@xslt="'.$what.'"]');
   $label = $node[0]['label'];
			$desc = (string)$node[0]->div->asXML();
   $label = $label.DISTINCT_SEP.$desc;
			$filter = explode(',', (string)$node[0]['package_kind']);
   $returnText .= getRetrieveNavigator($doc, $where, $what, $label, $filter);
			break;

		default:
   $returnText1 = '';
			$xslt = $what.'.xsl';

   switch ($what) {
				case MASTRO_RETRIEVE:
					$xslt = 'query_by_block.xsl';
					$label = $_REQUEST['terms'];
     $mode = $_REQUEST['criteria'];
     $returnText1 .= queryXML(MASTRO_RETRIEVE, $doc, $xslt, $_REQUEST, $label, $mode);
					break;

				default:
					$returnText1 .= transformXMLwithXSLT(MASTRO_RETRIEVE, $doc, $where, $xslt, $block, '', '', $label);
					break;

			};
			$title = 'Selezione';
			$goto = $title;
//			generateVerticalString($title, $goto, $db_collection);
			$label = explode(DISTINCT_SEP, $label);
			$label = $label[0];
   $returnText .= '<div class="box">';
			$label = stripslashes($title.' > '.$label);
			$returnText .= '<div class="box_head">';
			$returnText .= '<h2 class="page_curr" rel="'.$db_collection.'-'.$goto.'">';
			$returnText .= $label;
			$returnText .= '</h2>';
			$returnText .= '</div>';
			$returnText .= $returnText1;
			$returnText .= '</div>';
			break;

	};
};
// return
//echo '<div>'.$returnText.'</div>';
echo $returnText;

?>
