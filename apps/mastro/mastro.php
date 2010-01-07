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
$returnText = '';
$returnText .= '<?xml version="1.0" encoding="UTF-8"?>';
$returnText .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
$returnText .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$curr_lang.'">';
$returnText .= '<head>';
$returnText .= '<meta http-equiv="Content-Language" content="'.$curr_lang.'" />';
$returnText .= '<meta http-equiv="Content-Script-Type" content="text/javascript" />';
$returnText .= '<meta http-equiv="Content-Style-Type" content="text/css" />';
$returnText .= '<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />';
$returnText .= '<title>Explore Archives :: dCTL Project - CTL (SNS)</title>';
$returnText .= '<style type="text/css" media="screen">';
$returnText .= '<!-- @import url(css/'.MASTRO.'.css) screen; -->';
$returnText .= '</style>';
// BEGIN JS
$returnText .= '<script src="../js/_setup.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-ui.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/form.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/checktree.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/splitter.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/simplemodal.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/shadow.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/ifixpng.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/np_fancyzoom.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/contact.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/highlight.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/np_autocomplete.js" type="text/javascript"><!-- --></script>';
// $returnText .= '<script src="../js/jquery-plugins/unwrap.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/np_magnifier.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/np_mbContainer.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-plugins/np_tooltip.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="../js/jquery-common.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script src="js/'.MASTRO.'.js" type="text/javascript"><!-- --></script>';
$returnText .= '<script type="text/javascript">';
$returnText .= 'var doc = "'.$doc.'";';
$returnText .= 'var what = "'.$what.'";';
$returnText .= 'var where = "'.$where.'";';
$returnText .= 'var block = "'.$block.'";';
$returnText .= 'var at = "'.$at.'";';
$returnText .= 'var high = "'.$high.'";';
$returnText .= 'var label = "'.$label.'";';
$returnText .= 'var temp = "'.$temp.'";';
$returnText .= '</script>';
// END JS
$returnText .= '</head>';
$returnText .= '<body>';
$returnText .= '<noscript>';
$returnText .= '<p>'.'Enable JavaScript to correctly view this site...'.'</p>';// $string[$curr_lang]['enable_js']
$returnText .= '</noscript>';

$returnText .= '<div class="wrapper">';

	$returnText .= '<div class="header">';
		$returnText .= '<div id="activeLayerID" class="head_brand" style="background-image:url('.DCTL_IMAGES.'head_brand_'.DCTL_RSRC_COLLECTIONS.'.jpg);"><a href="../../../'.DCTL_RSRC_COLLECTIONS.'/" title="Home">&#160;</a></div>';
		$returnText .= '<div class="head_head">';
			$returnText .= '<div id="system_message">';
			$returnText .= TEMPORARY_SYSTEM ? '!!! SISTEMA TEMPORANEO DI PUBBLICAZIONE !!!' : '';
			$returnText .= '&#160;</div>';
			$returnText .= '<h3 class="package">'.'[package]'.'</h3>';
		$returnText .= '</div>';
	$returnText .= '</div>';

/* RETRIEVE */

$returnText .= '<div class="container">';

$layer = 'retrieve';

$returnText .= '<div id="'.$layer.'" class="splitter">'; // SPLITTER #1

  $returnText .= '<div id="'.$layer.'sidebar" class="sidebar panel">'; // #1
			$returnText .= '<div>';
				$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
			$returnText .= '</div>';
			$returnText .= '<div class="content">';
				$returnText .= '<div id="'.$layer.'palette" class="palette">';
					$returnText .= 'loading #palette...';
				$returnText .= '</div>';
				$returnText .= '<div id="'.$layer.'navigator" class="navigator">';
					$returnText .= 'loading #navigator...';
				$returnText .= '</div>';
			$returnText .= '</div>';
		$returnText .= '</div>';

$returnText .= '<div class="splitter">'; // SPLITTER #2

		$returnText .= '<div id="'.$layer.'panel1" class="panel">'; // #2
			$returnText .= '<div>';
				$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
			$returnText .= '</div>';
			$returnText .= '<div id="'.$layer.'view1" class="content">';
				$returnText .= 'loading #selection1...';
			$returnText .= '</div>';
		$returnText .= '</div>';

$layer = 'display';
	$returnText .= '<div id="'.$layer.'" class="splitter">'; // SPLITTER #3

		$returnText .= '<div id="'.$layer.'sidebar" class="sidebar panel">'; // #3
			$returnText .= '<div>';
				$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
			$returnText .= '</div>';
			$returnText .= '<div class="content">';
				$returnText .= '<div id="'.$layer.'palette" class="palette">';
					$returnText .= 'loading #palette...';
				$returnText .= '</div>';
				$returnText .= '<div id="'.$layer.'navigator" class="navigator">';
					$returnText .= 'loading #navigator...';
				$returnText .= '</div>';
			$returnText .= '</div>';
		$returnText .= '</div>';

		$returnText .= '<div class="splitter">'; // SPLITTER #4

			$returnText .= '<div id="'.$layer.'panel1" class="panel">'; // #4
				$returnText .= '<div>';
					$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
				$returnText .= '</div>';
				$returnText .= '<div id="'.$layer.'view1" class="content">';
					$returnText .= 'loading #view1...';
				$returnText .= '</div>';
			$returnText .= '</div>';

			$returnText .= '<div class="splitter">'; // SPLITTER #5

				$returnText .= '<div id="'.$layer.'panel2" class="panel">'; // #5
					$returnText .= '<div>';
						$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
					$returnText .= '</div>';
					$returnText .= '<div id="'.$layer.'view2" class="content">';
						$returnText .= 'loading #view2...';
					$returnText .= '</div>';
				$returnText .= '</div>';

			$returnText .= '<div class="splitter">'; // SPLITTER #6

				$returnText .= '<div id="'.$layer.'panel3" class="panel">'; // #6
					$returnText .= '<div>';
						$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
					$returnText .= '</div>';
					$returnText .= '<div id="'.$layer.'view3" class="content">';
						$returnText .= 'loading #view3...';
					$returnText .= '</div>';
				$returnText .= '</div>';

				$returnText .= '<div class="splitter">'; // SPLITTER #7

					$returnText .= '<div id="'.$layer.'panel4" class="panel">'; // #7
						$returnText .= '<div>';
							$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
						$returnText .= '</div>';
						$returnText .= '<div id="'.$layer.'view4" class="content">';
							$returnText .= 'loading #view4...';
						$returnText .= '</div>';
					$returnText .= '</div>';
//
// 					$returnText .= '<div class="splitter">'; // SPLITTER #8
//
						$returnText .= '<div id="'.$layer.'panel5" class="panel">'; // #8
							$returnText .= '<div>';
								$returnText .= '<a class="toggler" title="'.TOOLTIP_TOGGLE.'">&#160;</a>';
							$returnText .= '</div>';
							$returnText .= '<div id="'.$layer.'view5" class="content">';
								$returnText .= 'loading #view5...';
							$returnText .= '</div>';
						$returnText .= '</div>';

// 						$returnText .= '<div/>'; // #9 (DUMMY)
//
// 					$returnText .= '</div>'; // SPLITTER #8

				$returnText .= '</div>'; // SPLITTER #7

			$returnText .= '</div>'; // SPLITTER #6

		$returnText .= '</div>'; // SPLITTER #5

	$returnText .= '</div>'; // SPLITTER #4


	$returnText .= '</div>'; // SPLITTER #3

	$returnText .= '</div>'; // SPLITTER #2

$returnText .= '</div>'; // SPLITTER #1

$returnText .= '</div>'; // CONTAINER

/* FOOTER */

$returnText .= '<div id="basket"></div>';
$returnText .= '<div class="footer">';

$returnText .= '<div class="button" id="b_basket"><a href="javascript:void(0);" onclick="$(\'#basket\').toggle();" title="Baset">Basket</a></div>';
$returnText .= '<div class="footer_text">
			<h2>';
switch (DCTL_RSRC_COLLECTIONS) {
 case 'furioso':
  $returnText .= '<em>Orlando Furioso</em> e la sua traduzione in immagini';
  break;
 default:
  $returnText .= 'Collection :: '.strtoupper(DCTL_RSRC_COLLECTIONS);
  break;
};

$returnText .= '</h2><p>A cura di CTL | Scuola Normale Superiore di Pisa | <a href="http://www.ctl.sns.it/" title="CTL-SNS">www.ctl.sns.it</a></p></div>';
$mailto = 'mailto:'.MAIL_TO.'?subject=dCTL%20Furioso%20-%20Segnala%20un%20problema&body=[PROBLEMA]:%20%0D%0A%0D%0A[MESSAGGIO]:%20%0D%0A%0D%0A[ALTRO]:%20%0D%0A%0D%0A';
$returnText .= '<div class="button" id="b_report"><a href="'.$mailto.'" title="Segnalaci un problema">Segnalaci un problema</a></div>'; //onclick="$().contact(\'contact.php\');"
$returnText .= '</div>';

$returnText .= '</div>';

$returnText .= '</body>';
$returnText .= '</html>';

dctl_xmldb_disconnect($exist);
echo $returnText;
/* - - - - - - - - - - - - - - - - - */

?>
