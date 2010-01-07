<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

	// header
	require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

	$connection = dctl_sql_connect(DCTL_DB_NAME);
    if ($connection) {
	//
	$type = '';
	if(isset($_REQUEST['type'])) $type = $_REQUEST['type'];
	if ($type !='') {
		$query = "SELECT * FROM tNAME WHERE tNAME.type='$type' AND tNAME.collector='0' ORDER BY UPPER(tNAME.name)";
	} else {
		$query = "SELECT * FROM tNAME WHERE tNAME.collector='0' ORDER BY UPPER(tNAME.name)";
	}
	$result = mysql_query($query) or die ("Error in query: $query.  ".mysql_error());
	echo "<h2>".my_strtoupper($type)." : Elenco dei Nomi (KEY)</h2>";
	if (mysql_num_rows($result) > 0) {

	 echo '<div align="center">';
	 for ($i=65; $i<=90; $i++) {
	  echo '<a href="#'.chr($i).'">'.chr($i).'</a>';
	  if ($i<90) echo ' | ';
	 };
	 echo '</div>';
	 echo '<br />';

		echo '<table>';
		echo '<tr>';
		echo '<th>SubTipo</th>';
		echo '<th>Forma Normalizzata</th>';
		echo '<th>Key</th>';
		echo '<th>Varianti</th>';
		echo '<th>&#160;</th>';
		echo '</tr>';
		$prev = '';
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		 if (strtoupper(substr($row['name'], 0, 1)) != $prev) {
		  $prev = strtoupper(substr($row['name'], 0, 1));
				echo '<tr valign="top" id="'.strtoupper($prev).'">';
 			echo '<td style="background-color:#eeeeee"><a class="small" href="#"><img src="'.DCTL_IMAGES.'arrow_up.gif" border="0"/></a></td>';
			 echo '<td style="background-color:#eeeeee" colspan="4">';
			 echo '<strong>[ '.strtoupper($prev).' ]</strong>';
			 echo '</td>';
    echo "</tr>";
		 };
			echo '<tr valign="top">';
			echo '<td>'.$row['subtype'].'</td>';
			echo '<td>';
			echo '<a href="editName.php?id='.$row['id'].'" title="modifica Forma Normalizzata">'.$row['name'].'</a> <i>('.$row['lang'].")</i>";
			echo '</td>';
			echo '<td>';
			echo '<a href="editName.php?id='.$row['id'].'" title="modifica Forma Normalizzata">'.SYS_DBL_SPACE.sprintf("%06s", $row['id']).SYS_DBL_SPACE.'</a>';
			echo '</td>';
			echo '<td>';
			$query = "SELECT tNAME.id,tNAME.name,tNAME.lang FROM tNAME WHERE tNAME.collector='".$row['id']."' ORDER BY tNAME.name";
			$result2 = mysql_query($query) or die ("Error in query: $query.  ".mysql_error());
			if (mysql_num_rows($result2) > 0) {
				while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
					echo '<i><a href="editName.php?id='.$row2['id'].'" title="modifica Variante">'.$row2['name'].'</a> ('.$row2['lang'].')</i>';
					echo ', ';
				};
			};
			echo '</td>';
			echo '<td>';
			echo '<a href="addName.php?collector='.$row['id'].'" title="aggiungi Variante">[+]</a>';
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
	require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'footer.php');
?>
