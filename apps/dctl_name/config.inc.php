<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'functions.inc.php');


 $typex = array ('person', 'place', 'work', 'object', 'animal', 'astronomical');
 $subtypex = array ('', 'historical', 'mythological', 'proverbial', 'fictional', 'biblical');

/* NO ?> IN FILE .INC */
