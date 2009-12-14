<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

function convert2ic($code) {

 $code = preg_replace('/\(\+(.+)\)/','k$1',$code);

 return urlencode($code);
};

/* NO ?> IN FILE .INC */
