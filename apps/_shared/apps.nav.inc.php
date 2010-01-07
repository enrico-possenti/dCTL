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
		<li><a target="exist" class="external" href="<?php echo XMLDB_HOST.':'.XMLDB_PORT.'/exist/admin/admin.xql';?>" title="Gestione eXist">dbxml::eXist</a></li>
 </ul>
	<br />


	<?php
	$resultText = '';

	if (NOVEOPIU) {

		$resultText .= '<h2>Locale</h2><ul class="menu">
 <li><a target="simone.local.1" class="external" href="../../../dctl-doni/test/prova.php" title="Accedi">Test</a></li>
 <li><a target="simone.local.2" class="external" href="../../../dctl-doni/frontend.html" title="Accedi">Front-End</a></li>
 <li><a target="mysql.local" class="external" href="http://localhost/MAMP/phpmyadmin/" title="Accedi">MySQL</a></li>

</ul>
';
		$resultText .= '<h2>Remote</h2><ul class="menu">
	<li><a target="trac.remote" class="external" href="http://net7sviluppo.com/trac/ctl/wiki" title="trac">trac::net7</a></li>
	<li><a target="git.remote" class="external" href="http://github.com/noveopiu/dCTL" title="git">git::github</a></li>
 <li><a target="simone.remote.1" class="external" href="http://dev.doni.netseven.it/test/prova.php" title="Accedi">Test::net7</a></li>
</ul>
';
		$resultText .= '<br />';
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		if (false) {
			define('PATH_TO_ENGINE', str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'..'.SYS_PATH_SEP.COMMODORO.SYS_PATH_SEP.'core.php');
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
			} else {
				$resultText .= '<h2>Archivi dCTL</h2><ul class="menu">';
				$resultText .= '<li class="ko">Can\'t connect to dCTL engine...</li>';
				$resultText .= '</ul><br />';
			};
		};
	};


	$resultText .= '<h2>Archivio Privato (ctl-prova.sns.it)</h2><ul class="menu">';
	$resultText .= '<li><a target="frontend" class="external" href="http://dev.doni.netseven.it/" title="Accedi"><img src="'.DCTL_IMAGES.'folder-blue.gif" />&nbsp;Anton Francesco Doni</a></li>';
	$resultText .= '</ul>';
	$resultText .= '<br />';
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	$resultText .= '<h2>Archivio Pubblico (www.ctl.sns.it)</h2><ul class="menu">';
	$resultText .= '<li><a target="'.MASTRO.'" class="external" href="http://www.ctl.sns.it/furioso" title="Accedi"><img src="'.DCTL_IMAGES.'folder-blue.gif" />&nbsp;Orlando Furioso</a></li>';
	$resultText .= '</ul><br />';
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	echo $resultText;
	?>

</div>
