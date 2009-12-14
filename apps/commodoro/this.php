<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	$resultText = '';
 $xml = simplexml_load_file('this/this.xml');
	$resultText .= $xml->body->div->asXML();
	$resultText = preg_replace('/'.WHITESPACES.'+/',' ',$resultText);
 echo $resultText;
 // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');

?>
