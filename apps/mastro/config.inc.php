<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$curr_lang = 'it';
if (isSet($_REQUEST['lang'])) {
	$curr_lang = $_REQUEST['lang'];
};
if ($curr_lang == 'it') {
	$other_lang='en';
} else {
	$other_lang='it';
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
if (isSet($_REQUEST['collection'])) {
	$collection = $_REQUEST['collection'];
} else {
 $collection = 'furioso';
};
define('DCTL_RSRC_COLLECTIONS', $collection);
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
$doc = isset($_REQUEST['doc']) ? $_REQUEST['doc'] : DCTL_RSRC_COLLECTIONS;
$where = isset($_REQUEST['where']) ? $_REQUEST['where'] : '';
$what = isset($_REQUEST['what']) ? $_REQUEST['what'] : '';
$block = isset($_REQUEST['block']) ? stripslashes($_REQUEST['block']) : '';
$at = isset($_REQUEST['at']) ? stripslashes($_REQUEST['at']) : '';
$high = isset($_REQUEST['high']) ? stripslashes($_REQUEST['high']) : '';
$label = isset($_REQUEST['label']) ? stripslashes($_REQUEST['label']) : '';
$terms = isset($_REQUEST['terms']) ? stripslashes($_REQUEST['terms']) : '';
$config = isset($_REQUEST['config']) ? $_REQUEST['config'] : '';
$temp = isset($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
define('DCTL_LOAD_XML_FROM','DB+DOM'); // DB DOM SIMPLEXML DB+DOM
define('DCTL_DEBUG',false); //
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
global $cachedID;
global $cachedCLASS;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
try {
	if (!isset($_SESSION['dctl_doc'])){
		$doc_prev = '';
		$_SESSION['dctl_doc'] = $doc;
	} else {
		$doc_prev = $_SESSION['dctl_doc'];
	};
} catch (Exception $e) {
	die('<span class="error">! doc not found...</span>');
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
try {
	if (!isset($_SESSION['dctl_block'])){
		$block_prev = '';
		$_SESSION['dctl_block'] = $block;
	} else {
		$block_prev = $_SESSION['dctl_block'];
	};
} catch (Exception $e) {
	die('<span class="error">! block not found...</span>');
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
try {
	if (!isset($_SESSION['dctl_exist'])){
		$exist = dctl_xmldb_connect('query', true);
		$_SESSION['dctl_exist'] = $exist;
	} else {
		$exist = $_SESSION['dctl_exist'];
	};
} catch (Exception $e) {
	die('<span class="error">! db not found...</span>');
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'functions.inc.php');
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


/* NO ?> IN FILE .INC */
