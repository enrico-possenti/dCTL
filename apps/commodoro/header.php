<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');
 $returnText = '';

	/* AUTHENTICATE */
	if (is_file(str_replace('//','/',dirname(__FILE__).'/').'.htaccess')) {
		require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/authenticate.inc.php');
	} else {
  die('ERROR: access control not found... Fix it. Me, i abort.');
	};
	/* INITIALIZE */
DEFINE('ENGINE_TIMEOUT_MULTIPLIER', 30);
require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'./config.inc.php');
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

$returnText .= '<script type="text/javascript" src="../_shared/syntaxhighlighter/my/shCore.js"></script>';
$returnText .= '<script type="text/javascript" src="../_shared/syntaxhighlighter/scripts/shBrushXml.js"></script>';
$returnText .= '<link type="text/css" rel="stylesheet" href="../_shared/syntaxhighlighter/styles/shCore.css"/>';
$returnText .= '<link type="text/css" rel="stylesheet" href="../_shared/syntaxhighlighter/styles/shThemeDefault.css"/>';
$returnText .= '<script type="text/javascript">';
$returnText .= '	SyntaxHighlighter.config.clipboardSwf = "../_shared/syntaxhighlighter/scripts/clipboard.swf";';
$returnText .= '</script>';

 $returnText .= '<style type="text/css" media="screen">';
 $returnText .= '.code {overflow:auto;font-size:95%;font-family: Courier, mono; white-space:normal;} ';
 $returnText .= '.syntaxhighlighter {
	font-size: 0.85em !important;
 margin: 0 !important;
	/* height: 30em !important; */
	overflow: auto !important;
	display: block !important;
 } ';
 $returnText .= '</style>';

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
$returnText .= '<h1 class="sitename">Laboratorio dCTL ('.APPS_VERSION.'): Sistema di Gestione dei Contenuti&#160;&#160;<img id="progress" src="'.DCTL_IMAGES.'progress.gif" alt="(progress bar)" /></h1>';
$returnText .= '</div>';
$returnText .= '
<div class="sidebar left">
	<h2>dctl::commodoro</h2>
	<ul class="menu">
		<li><a href="index.php">Introduzione</a></li>
		</li>
	</ul>
	<ul class="menu">
		<li>Sorgenti</li>
		<li>
			<ul>
				<li><a href="indexManager.php?" title="xml">File XML</a></li>
				<li><a href="indexMedia.php?" title="media">File Media</a></li>
			</ul>
		</li>
	</ul>
	<ul class="menu">
		<li>Connessioni</li>
		<li>
			<ul>
				<li><a href="indexLinker.php?" title="links">Collegamenti</a></li>
				<li><a href="indexMapper.php?" title="maps">Mappature</a></li>
			</ul>
		</li>
	</ul>
	<ul class="menu">
		<li>Repository</li>
		<li>
			<ul>
				<li><a href="indexPublisher.php?" title="publish">Pubblicazione</a></li>
				<li><a href="indexEngine.php?" title="api">API</a></li>
			</ul>
		</li>
	</ul>
</div><!-- sidebar -->
';

$returnText .= require_here('../_shared/apps.nav.inc.php');

$returnText .= '<div id="main">';
echo $returnText;
