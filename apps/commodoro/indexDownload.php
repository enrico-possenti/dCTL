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
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'./config.inc.php');

	/* */
 $downloadPath = '';

$isGenerate = FALSE;
if (isset($_REQUEST['generate'])) {
	$isGenerate = TRUE;
 $filePath = $_REQUEST['generate'];
};

$isModel = FALSE;
if (isset($_REQUEST['model'])) {
	$isModel = TRUE;
 $filePath = $_REQUEST['model'];
};

$isFile = FALSE;
if (isset($_REQUEST['file'])) {
	$isFile = TRUE;
 $filePath = $_REQUEST['file'];
};

$isFolder = FALSE;
if (isset($_REQUEST['folder'])) {
	$isFolder = TRUE;
 $filePath = $_REQUEST['folder'];
};

$filePath = trim($filePath);
$filePath0 = $filePath;
$ftp_path = DCTL_PROJECT_PATH;
$filePath = str_ireplace($ftp_path, '', $filePath);
$filePath = str_ireplace('..'.SYS_PATH_SEPARATOR, SYS_PATH_SEPARATOR, $filePath);
$filePath = str_ireplace('.'.SYS_PATH_SEPARATOR, SYS_PATH_SEPARATOR, $filePath);
$filePath = $ftp_path.$filePath;
$filePath = str_ireplace(SYS_PATH_SEPARATOR.SYS_PATH_SEPARATOR, SYS_PATH_SEPARATOR, $filePath);
$f = basename($_SERVER['PHP_SELF']);
if (
	(stripos($filePath, $f) === false)
	&& (stripos($filePath, '.ht') === false)
	&& (stripos($filePath, '.inc') === false)
	&& (stripos($filePath, '.php') === false)
	&& (substr(basename($filePath), 0, 1) != '.')
	&& (substr($filePath, 0, strlen($ftp_path)) == $ftp_path)
	) {
	switch (TRUE) {

	 case $isFolder:
			if (is_dir($filePath)) {
			 $downloadPath = getModel($filePath);
			} else {
				$resultMsg = '<span class="error">Nome di cartella non valido ('.$filePath.')...'.'</span>';
				header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
				exit();
			};
			break;

	 case $isFile:
			if (is_file($filePath)) {
				$who = '';
				$content = array();
				$isLocked = checkIfLocked ($filePath, &$who, &$content);
				if ($isLocked) {
					$resultMsg = '<span class="error">File già bloccato ('.$filePath.')...'.'</span>';
					header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
					exit();
				} else {
					$lockIt = isset($_REQUEST['lock']);
					if ($lockIt) {
						$downloadPath = getModel($filePath);
						$current_user = $_REQUEST['user'];
						$filePathH = dirname($filePath).SYS_PATH_SEPARATOR.'x-'.$current_user.'-'.basename($filePath);
						copy($filePath, $filePathH);
		 			@chmod($filePathH, CHMOD);
					};
				};
			} else {
				$resultMsg = '<span class="error">Nome di file non valido ('.$filePath.')...'.'</span>';
				header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
				exit();
			};
			break;

	 case $isModel:
				$filePath = getModel($filePath);
			break;

	 case $isGenerate:
			$collection_id = basename(dirname($filePath));
			$fName = basename($filePath);
			$filePath = $ftp_path.$collection_id.SYS_PATH_SEPARATOR.$fName;
			$content = array();
			$isLocked = checkIfLocked ($filePath, &$who, &$content);
			if ($isLocked) {
				$resultMsg = '<span class="error">File già bloccato ('.$filePath.')...'.'</span>';
				header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
				exit();
			} else {
// 				$link_id = preg_replace('/'.DCTL_LINKERSEG.'\.(.*)\.xml/', '$1', $fName);
// 				$linkerPath = $ftp_path.'/'.$collection_id.'/'.DCTL_FILE_LINKER;
// 				$templatePath = DCTL_SETTINGS_TEMPLATES_COLLECTION.'/'.DCTL_FILE_LINKERSEG;
// 				// OTTENGO IL PATH XML
//	   	forceUTF8($linkerPath);
// 				$xml = simplexml_load_file($linkerPath, 'SimpleXMLElement', DCTL_XML_LOADER); //
// 				$namespaces = $xml->getDocNamespaces();
// 				foreach ($namespaces as $nsk=>$ns) {
// 					if ($nsk == '') $nsk = 'tei';
// 					$xml->registerXPathNamespace($nsk, $ns);
// 				};
// 				$resultXML = $xml->xpath('id("'.$link_id.'")/..');
// 				if (count($resultXML)>0) {
// 					// OTTENGO IL TEMPLATE COME TESTO
// 					$textContent = cleanUpIndentation(file_get_contents($templatePath));
// 					// TROVO I CAMPI DA SOSTITUIRE
// 					$select = $LINKER_RCB_FIELDS;
// 					$asAttr = array();
// 					foreach ($select['label'] as $field=>$label) {
// 						if (isset($resultXML[0]->$field)) {
// 							$value = $resultXML[0]->$field;
// 							$value = preg_replace('/\n+/', '<lb />', $value);
// 							$value = stripslashes($value);
// 							$asAttr['prev'][$field] = strval($resultXML[0]->$field);
// 							$asAttr['next'][$field] = $value;
// 							$textContent = str_ireplace('%'.$field.'%', $value, $textContent);
// 						};
// 					};
// 					$textContent = str_ireplace('%link_id%', $link_id, $textContent);
// 					// PRENDO TRA HEADER E FOOTER
// 					$textContent2 = $textContent;
// 					$header = '%BEGIN% -->';
// 					$textContent2 = substr($textContent2, stripos($textContent2, $header) + strlen($header));
// 					$textContent2 = substr($textContent2, 0, stripos($textContent2, '<!-- %END%'));
// 					// OTTENGO IL PATH XML COME TESTO
// 					$resultXML = $xml->xpath('id("'.$link_id.'")/*');
// 					if (count($resultXML)>0) {
// 						$xml_1x = array($resultXML[0]);
// 						$textContent3 = '';
// 						while(list( , $node) = each($xml_1x)) {
// 							$textContent3 .= $node->asXML();
// 						};
// 						$textContent3 = "\n".$textContent3."\n";
// 						// RICOSTRUISCO
// 						$textContent4 = str_ireplace($textContent2, $textContent3, $textContent);
// 						// SALVO
// 						file_put_contents($filePath, $textContent4);
// 					 @chmod($filePath, CHMOD);
// 						$downloadPath = getModel($filePath);
// 						$current_user = $_REQUEST['user'];
// 						$filePathH = dirname($filePath).'/'.'x-'.$current_user.'-'.basename($filePath);
// 						copy($filePath, $filePathH);
// 		 			@chmod($filePathH, CHMOD);
// 					} else {
// 						$resultMsg = '<span class="error">Id "'.$link_id.'" senza contenuto ('.$filePath.')...'.'</span>';
// 						header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
// 						exit();
// 					};
// 				} else {
// 					$resultMsg = '<span class="error">Id "'.$link_id.'" non trovato ('.$filePath.')...'.'</span>';
// 					header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
// 					exit();
// 				};
			};
			break;

	 default:
			$resultMsg = '<span class="error">Comando sconosciuto...'.'</span>';
			header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
			exit();
			break;
	};
	if ($downloadPath != '') {
	 if (is_file($downloadPath)) {
			// required for IE, otherwise Content-disposition is ignored
			if(ini_get('zlib.output_compression'))
				ini_set('zlib.output_compression', 'Off');
			$ctype = "application/force-download";
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"".basename($downloadPath)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($downloadPath));
			readfile($downloadPath);
			exit();
		} else {
				$resultMsg = '<span class="error">Download impossibile ('.$downloadPath.')...</span>';
				header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
				exit();
		};
	};
} else {
		$resultMsg = '<span class="error">Nome non valido ('.$filePath.')...</span>';
		header('Location: indexManager.php?'.$_SERVER['QUERY_STRING'].'&msg='.urlencode($resultMsg)); // required
		exit();
};

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
?>
