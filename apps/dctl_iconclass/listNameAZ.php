<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

	// header
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');


$connection = dctl_sql_connect(DCTL_DB_ICONCLASS);
	    if ($connection) {
//
		$query = "SELECT * FROM tNAME ORDER BY tNAME.name";
		$result = mysql_query($query) or die ("Error in query: $query.  ".mysql_error());
	echo "<h2>Elenco dei Soggetti (A-Z)</h2>";
	if (mysql_num_rows($result) > 0) {
	 echo '<div align="center">';
	 for ($i=65; $i<=90; $i++) {
	  echo '<a href="#'.chr($i).'">'.chr($i).'</a>';
	  if ($i<90) echo ' | ';
	 };
	 echo '</div>';
	 echo '<br />';

 echo '<h3>Consulta la banca dati Iconclass...<a class="external" href="http://www.iconclass.org/rkd/9/" title="">http://www.iconclass.org</a></h3>';

		echo '<table>';
		echo '<tr>';
		echo '<th>Soggetto</th>';
		echo '<th>Key</th>';
		echo '<th>Iconclass</th>';
		echo '</tr>';
		$prev = '';
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		 if (strtoupper(substr($row['name'], 0, 1)) != $prev) {
		  $prev = strtoupper(substr($row['name'], 0, 1));
				echo '<tr valign="top" id="'.strtoupper($prev).'">';
 			echo '<td style="background-color:#eeeeee" colspan="4">';
			 echo '<strong>[ '.strtoupper($prev).' ]</strong>';
			 echo '&emsp;<a class="small" href="#"><img src="'.DCTL_IMAGES.'arrow_up.gif" border="0"/></a></td>';
    echo "</tr>";
		 };
			echo '<tr valign="top">';
			echo '<td>';
			echo '<a href="editName.php?id='.$row['id'].'" title="modifica Soggetto">';
				echo $row['name'];
			echo '&emsp;<img src="'.DCTL_IMAGES.'edit.gif" alt="edit" /></a>';
				echo '<br /><i>'.$row['note'].'</i>';
			echo '</td>';
			echo '<td>';
			echo '<strong>';
				echo sprintf("%06s", $row['id']).'</strong>';
			echo '</td>';
			echo '<td>';
			echo '<strong>'.$row['iconclass'].'</strong>';
			echo '<a class="external" title="vedi Iconclass" ';
			echo 'href="http://www.iconclass.org/rkd/9/?q_s=1&amp;lang=it&amp;show_notations=1&amp;q='.convert2ic($row['iconclass']).'"';
			echo '>';
			echo '</a>';
			echo '</td>';
			echo '</tr>';
		};
		echo '</table>';
	} else {
		echo 'Nessun record trovato...';
	};
	// footer
	mysql_close($connection);
		//
	};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
?>
