<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR)."functions.inc.php");

	if (!defined('DCTL_EXT_IMT')) define('DCTL_EXT_IMT', true); // use Image Tool Mapper
	if (!defined('DCTL_EXT_IMT_CBP')) define('DCTL_EXT_IMT_CBP', 'imt');
	if (!defined('DCTL_EXT_IMT_CB')) define('DCTL_EXT_IMT_CB', WWW_NAME.$_SERVER['PHP_SELF'].'?action=update_imt'); //

	define('DCTL_EXT_URL', dirname(DCTL_EXT_IMT_CB));
	define('DCTL_EXT_IMT_BASE', DCTL_EXT_URL.'/tools/imt/');
	define('DCTL_EXT_IMT_CALL_TEST', DCTL_EXT_URL.'/tools/test_imt.php');
	define('DCTL_EXT_IMT_CALL_REAL', DCTL_EXT_IMT_BASE.'index.php');


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $collectionList = array();
 $packageList = array();
 $mediaList = array();
 $partList = array();
 $linksCheckList = array();
 $collectionRecord = array();
 $packageRecord = array();
 $mediaRecord = array();
 $partRecord = array();
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 if (!is_dir(DCTL_PROJECT_PATH)) mkdir(DCTL_PROJECT_PATH, CHMOD);
 @chmod(DCTL_PROJECT_PATH, CHMOD);
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $isEditCollection =  FALSE;
 if(isset($_REQUEST['editColl'])) {
  $isEditCollection = $_REQUEST['editColl'] != '';
 };
 $isSaveCollection = FALSE;
 if(isset($_REQUEST['saveColl'])) {
  $isSaveCollection = $_REQUEST['saveColl'] != '';
 };
 $collection_id = '';
 if(isset($_REQUEST['collection_id'])) {
  $collection_id = $_REQUEST['collection_id'];
 };
 if ($collection_id != '*') {
		$collection_id = strtolower(basename($collection_id));
		$collection_id = normalize($collection_id);
		$_REQUEST['collection_id'] = $collection_id;
	};
	$collection_short = '';
	if(isset($_REQUEST['collection_short'])) {
  $collection_short = $_REQUEST['collection_short'];
 };
// 	$collection_short = strtolower(basename($collection_short));
// 	$collection_short = strtoupper(normalize($collection_short));
	$collectionRecord['collection_short'] = $collection_short;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$isEditPackage =  FALSE;
	if(isset($_REQUEST['editPack'])) {
		$isEditPackage = $_REQUEST['editPack'] != '';
	};
	$isSavePackage = FALSE;
	if(isset($_REQUEST['savePack'])) {
		$isSavePackage = $_REQUEST['savePack'] != '';
	};
	$package_ext = '';
	if(isset($_REQUEST['package_ext'])) {
		$package_ext = $_REQUEST['package_ext'];
	};
	$package_id = '';
	if(isset($_REQUEST['package_id'])) {
		$package_id = $_REQUEST['package_id'];
	};
 if (($package_id != '*') && ($package_id != '')) {
		$package_id = strtolower(basename($package_id));
		$package_id = normalize($package_id);
		$_REQUEST['package_id'] = $package_id;
	};
	$package_short = '';
	if(isset($_REQUEST['package_short'])) {
		$package_short = $_REQUEST['package_short'];
	};
// 	$package_short = strtolower(basename($package_short));
// 	$package_short = strtoupper(normalize($package_short));
	$packageRecord['package_short'] = $package_short;
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$media_id = '';
	if(isset($_REQUEST['media_id'])) {
		$media_id = basename($_REQUEST['media_id']);
	};
 if ($media_id != '*') {
		$media_id = strtolower(basename($media_id));
		$media_id = normalize($media_id);
		$_REQUEST['media_id'] = $media_id;
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$part_id = '';
	if(isset($_REQUEST['part_id'])) {
		$part_id = basename($_REQUEST['part_id']);
	};
 if ($part_id != '*') {
		$part_id = strtolower(basename($part_id));
		$part_id = normalize($part_id);
		$_REQUEST['part_id'] = $part_id;
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 $resultText = '';
 $fCount = 0;
	$updateDone = FALSE;
	$doPreview = FALSE;
	$loc4msg = '';
	if (isset($_REQUEST['posx'])) {
		$loc4msg = $_REQUEST['posx'];
	};
	$resultMsg = '';
	if (isset($_REQUEST['msg'])) {
		$resultMsg = stripslashes(urldecode($_REQUEST['msg']));
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

/* NO ?> IN FILE .INC */
