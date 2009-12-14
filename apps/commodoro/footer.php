<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');
 $returnText = '';
 $returnText .= '<hr />';
 $returnText .= '<em>end of page (<a href="#main">go top</a>)</em>';
 $returnText .= '<hr />';
 echo $returnText;
 require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'../_shared/apps.footer.inc.php');

?>
