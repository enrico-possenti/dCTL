<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');
 $returnText = '';
 $returnText .= '<br />';
 $returnText .= '<hr />';
 $returnText .= '<em>end of page (<a href="#main">go top</a>)</em>';
 $returnText .= '<hr />';
 echo $returnText;
 require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'../_shared/apps.footer.inc.php');

?>
