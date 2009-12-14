<?php
/**
 +----------------------------------------------------------------------+
 | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
 +----------------------------------------------------------------------+
 | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
 | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
 |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
 +----------------------------------------------------------------------+
 | A main file for "commodoro"                                          |
 +----------------------------------------------------------------------+
*/

 if (!defined('_INCLUDE')) define('_INCLUDE', true);

/**
 * API to a dCTL archive.
 *
 * To use this API you need to access a dCTL environment offering this kind
 * of webservice.
 * {@link http://www.ctl.sns.it/dctl/}
 *
 * @author Enrico Possenti <info@noveopiu.com>
 * @version $Revision: 0.0.1 $
 * @package dCTL-Engine
 */
 // +----------------------------------------------------------------------+
	/* AUTHENTICATE */
	if (is_file(str_replace('//','/',dirname(__FILE__).'/').'.htaccess')) {
	require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/authenticate.inc.php');
	} else {
  die('ERROR: access control not found... Fix it. Me, i abort.');
	};
 // +----------------------------------------------------------------------+
	/* INITIALIZE */
	DEFINE('ENGINE_TIMEOUT_MULTIPLIER', 30);
	require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
	require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'./config.inc.php');
 // +----------------------------------------------------------------------+
 // | INCLUDE THE CORE
 require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'./core.php');
	// +----------------------------------------------------------------------+
 // | HTTP REQUEST (POST or GET)
	$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;
	$private = isset($_REQUEST['private']) ? $_REQUEST['private'] : false;
	$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
	$rsrc = isset($_REQUEST['rsrc']) ? $_REQUEST['rsrc'] : '';
	$xpath = isset($_REQUEST['xpath']) ? $_REQUEST['xpath'] : '';
	$xslt = isset($_REQUEST['xslt']) ? $_REQUEST['xslt'] : '';
	// +----------------------------------------------------------------------+
 // | INSTANTIATE A SINGLETON
 try {
		if (!isset($_SESSION['dctl_obj'])){
			if ($dCTL = dCTLRetriever::singleton()) {
				// | init
				$_SESSION['dctl_obj'] = $dCTL;
			} else {
				// | error
				unset($_SESSION['dctl_obj']);
			};
		} else {
			$dCTL = $_SESSION['dctl_obj'];
		};
	} catch (Exception $e) {
		die('<span class="error">! dCTL not found...</span>');
	};
 // +----------------------------------------------------------------------+
	// | select db
	$dCTL->use_private_db = $private;
	// | debug
	$dCTL->debug = $debug;
 // | PARSE THE CALL
	$resultXML = false;
	$goBack .= 'goto <a href="#'.$method.'" title="go back">'.$method.'()</a> again ';
	$goBack .= 'or <a href="#" onclick="';
	$goBack .= "$('#s').html('loading...');$('#s').load('".basename(__FILE__)."', { method: '".$method."'";
	if ($rsrc) $goBack .= ", rsrc: '".$rsrc."' ";
	if ($xpath) $goBack .= ", xpath: '".$xpath."' ";
	$goBack .= ", debug: debug, private: private}, function() { refreshMe(); } );return false;";
	$goBack .= '" title="reload">reload it</a>';
	$goBack .= ' [pid:'.$dCTL->db->pid.']<br/>';
 switch ($method) {
		// | core methods
		case 'getStructure':
			$resultXML = $dCTL->getStructure($rsrc);
			break;
		case 'getBlock':
   $resultXML = $dCTL->getBlock($rsrc);
			break;
		case 'getOptions':
			$resultXML = $dCTL->getOptions($rsrc, $xpath);
			break;
		case 'getLinks':
			$resultXML = $dCTL->getLinks($rsrc);
			break;
		case 'getMaps':
			$resultXML = $dCTL->getMaps($rsrc);
			break;
		case 'unitTest':
			// | debug
			$dCTL->debug = FALSE;
   echo $goBack;
   require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/simpletest/autorun.php');
			class CoreTester extends TestSuite {
    function __construct() {
        parent::__construct();
        $this->addFile(str_replace('//','/',dirname(__FILE__).'/').'tester.php');
    }
   }
   break;
		// | ignore unknown calls
		default:
			$resultXML = '';
			$xslt = '';
			break;
	};
 // +----------------------------------------------------------------------+
 // | APPLY XSLT
 if (is_file($xslt)) { //
  // do XSLT processing
  $resultXML = $resultXML;
 };
 // to be implemented
 // +----------------------------------------------------------------------+
 // | FOR DEBUG PURPOSES
	// | get human-readable xml (or a valid machine-readable, if omitted)
	if ($dCTL->debug && ($resultXML)) {
		$simplexml = @simplexml_load_string($resultXML, 'asPrettyXMLElement', DCTL_XML_LOADER);
		$resultXML = $goBack.'<script type="syntaxhighlighter" class="brush: xml"><![CDATA['.$simplexml->asPrettyXML(1).']]></script>'.$goBack; //htmlspecialchars_decode
	};
	// |
 // +----------------------------------------------------------------------+
 // | RETURNS
 echo $resultXML;
	// +----------------------------------------------------------------------+
?>
