<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

	/* AUTHENTICATE */
	if (is_file(str_replace('//','/',dirname(__FILE__).'/').'.htaccess')) {
		require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/authenticate.inc.php');
	} else {
  die('ERROR: access control not found... Fix it. Me, i abort.');
	};
	/* INITIALIZE */
DEFINE('ENGINE_TIMEOUT_MULTIPLIER', 30);
require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'./config.inc.php');
/* */
$curr_lang = 'it';
$string[$curr_lang]['enable_js'] = '';
/* */
$returnText = '';
$returnText .= '<?xml version="1.0" encoding="UTF-8"?>';
$returnText .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
$returnText .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$curr_lang.'">';
$returnText .= '<head>';
$returnText .= '<meta http-equiv="Content-Language" content="'.$curr_lang.'" />';
$returnText .= '<meta http-equiv="Content-Script-Type" content="text/javascript" />';
$returnText .= '<meta http-equiv="Content-Style-Type" content="text/css" />';
$returnText .= '<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />';
$returnText .= '<title>Risorse - dCTL</title>';
$returnText .= '<style type="text/css" media="screen">';
$returnText .= '<!-- @import url(../css/apps.css) screen; -->';
$returnText .= '</style>';
// BEGIN JS
$returnText .= '<script src="../js/_setup.js" type="text/javascript"><!-- --></script>';

$returnText .= '<script src="../js/jquery.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-ui.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/support.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/simple.tree.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/form.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/imgareaselect.js" type="text/javascript"><!-- --></script>';

$returnText .= '<script src="../js/common.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="js/commodoro.js" type="text/javascript"><!-- --></script>';
// $returnText .= '<script src="js/codemirror/js/codemirror.js" type="text/javascript"><!-- --></script>';

// $returnText .= '<script type="text/javascript">
// $(ready) = function  () {
// var editor = CodeMirror.fromTextArea("xml_chunk", {
//  height: "350px",
// 	parserfile: "parsexml.js",
// 	stylesheet: "js/codemirror/css/xmlcolors.css",
// 	path: "js/codemirror/js/",
// 	continuousScanning: 500,
// 	lineNumbers: true,
// 	textWrapping: true
// 	});
// }
// </script>';

$returnText .= '</head>';
$returnText .= '<body>';
$returnText .= '<noscript>';
$returnText .= '<p>'.$string[$curr_lang]['enable_js'].'</p>';
$returnText .= '</noscript>';

$returnText .= '<div id="wrapper">';
$returnText .= '<div id="header">';
$returnText .= '<h1 class="sitename">Laboratorio dCTL ('.APPS_VERSION.'): Banca dati autoritativa dei Codici Iconclass&#160;&#160;<img id="progress" src="'.DCTL_IMAGES.'progress.gif" alt="(progress bar)" /></h1>';
$returnText .= '</div>';
$returnText .= '<div class="sidebar left">
	<h2>db::iconclass</h2>
<ul class="menu">

	<li><ul>
			<li><a href="index.php">Introduzione</a></li>
</ul>
 </li>

	<li>Ricerca</li>
	<li>	<ul>
			<li><a href="searchByName.php">Cerca SOGGETTO</a></li>
			<li><a href="searchById.php">Cerca KEY</a></li>
			</ul>
 </li>

	<li>Aggiungi</li>
        <li>
		<ul>
			<li><a href="addName.php">Soggetto</a></li>
	    		</ul>
	</li>


<li>Soggetti</li>
        <li>
		<ul>
	<li><a href="listNameAZ.php">Elenco IC</a></li>
	    		</ul>
	</li>

</ul>

</div><!-- sidebar -->
';

$returnText .= require_here('../_shared/apps.nav.inc.php');

$returnText .= '<div id="main">';
echo $returnText;
