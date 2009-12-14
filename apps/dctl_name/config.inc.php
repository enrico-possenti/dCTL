<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'functions.inc.php');


 $typex = array ('person', 'place', 'work', 'object', 'animal', 'astronomical');
 $subtypex = array ('', 'historical', 'mythological', 'proverbial', 'fictional', 'biblical');

/* NO ?> IN FILE .INC */
