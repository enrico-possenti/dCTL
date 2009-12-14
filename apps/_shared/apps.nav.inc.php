<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

?>

<div class="sidebar right">
 <?php
  echo '<em>Ciao, '.DCTL_USER_NAME.'</em>';
	?>
	<h2>Web Apps</h2>

<ul class="menu">

 <li><a href="<?php echo '../'.COMMODORO.'/';?>" title="Gestione Contenuti">dctl::commodoro</a></li>
 <li><a href="<?php echo '../'.DCTL_DB_NAME.'/';?>" title="db Nomi Propri">db::name</a></li>
	<li><a href="<?php echo '../'.DCTL_DB_ICONCLASS.'/';?>" title="db Codici Iconclass">db::iconclass</a></li>
	<li><hr/></li>
  </ul>
	<br />


<?php
	$resultText = '';
 // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	$resultText .= '<h2>Archivio Privato (net7)</h2><ul class="menu">';
	$resultText .= '<li><a target="'.MASTRO.'" class="external" href="http://ctl-prova.sns.it/~fonda/svn_trunk/frontend.html" title="Accedi"><img src="'.DCTL_IMAGES.'folder-orange.gif" />&nbsp;AFD</a></li>';
	$resultText .= '</ul><br />';
 // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 define('PATH_TO_ENGINE', str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'..'.SYS_PATH_SEPARATOR.COMMODORO.SYS_PATH_SEPARATOR.'core.php');
 require_once(PATH_TO_ENGINE);
 if ($dCTL = dCTLRetriever::singleton()) {
  if ($dCTL->db) {
			$resultText .= '<h2>Archivio Privato (mastro)</h2><ul class="menu">';
			$resultXML = false;
			$dCTL->use_private_db = true;
			$resultXML = $dCTL->getStructure('*');
			if ($resultXML) {
				$simplexml = @simplexml_load_string($resultXML, 'SimpleXMLElement', DCTL_XML_LOADER);
				$namespaces = $simplexml->getDocNamespaces();
				foreach ($namespaces as $nsk=>$ns) {
					if ($nsk == '') $nsk = 'tei';
					$simplexml->registerXPathNamespace($nsk, $ns);
				};
				if ($simplexml->resource) {
					foreach($simplexml->resource as $k=>$n) {
						$resultText .= '<li><a target="'.MASTRO.'" class="external" href="../'.MASTRO.'/'.MASTRO.'.php?temp=true&collection='.$n->id.'" title="Accedi"><img src="'.DCTL_IMAGES.'folder-red.gif" />&nbsp;'.$n->short.'</a></li>';
					};
				} else {
					$resultText .= '<li class="ko">No published database...</li>';
				};
			} else {
				$resultText .= '<li class="ko">No reachable database...</li>';
			};
		} else {
			$resultText .= '<li class="ko">No reachable database...</li>';
		};
		$resultText .= '</ul><br />';
 // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		$resultText .= '<h2>Archivio Pubblico (www.ctl.sns.it)</h2><ul class="menu">';
		$resultText .= '<li><a target="'.MASTRO.'" class="external" href="http://www.ctl.sns.it/furioso" title="Accedi"><img src="'.DCTL_IMAGES.'folder-blue.gif" />&nbsp;Orlando Furioso</a></li>';
		$resultText .= '</ul><br />';
 // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 } else {
			$resultText .= '<h2>Archivi dCTL</h2><ul class="menu">';
			$resultText .= '<li class="ko">Can\'t connect to dCTL engine...</li>';
			$resultText .= '</ul><br />';
 };
 echo $resultText;
?>

</div>
