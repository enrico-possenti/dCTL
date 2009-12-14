<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

$connection = dctl_sql_connect(DCTL_DB_NAME);
    if ($connection) {
//

$name = '';
if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
$id = '';
if(isset($_REQUEST['id'])) $id = $_REQUEST['id'];

if ($name !='') {
	$nameX = my_strtoupper ($name);
	$nameSoundex = my_soundex($nameX);
	echo "<h2>Ricerca per NOME: ".$name." ($nameX - $nameSoundex)</h2>";
	$query = "SELECT  * FROM tNAME WHERE tNAME.nameSoundex='$nameSoundex' OR tNAME.nameNormalized LIKE '%$nameX%' OR SUBSTRING(SOUNDEX(tNAME.nameNormalized), 1, 4) =  SUBSTRING(SOUNDEX('".$name."'), 1, 4) ORDER BY tNAME.name";
} else {
	echo "<h2>Ricerca per KEY: $id</h2>";
	$query = "SELECT  * FROM tNAME WHERE tNAME.id='$id'";
}
$result = mysql_query($query) or die ("Error in query: $query.  ".mysql_error());
if (mysql_num_rows($result) > 0) {
	echo '<table>';
	echo '<tr>';
	echo '<th>Tipo</th>';
	echo '<th>SubTipo</th>';
	echo '<th>Forma Normalizzata</th>';
	echo '<th>Key</th>';
	echo '<th>Varianti</th>';
	echo '<th>&#160;</th>';
	echo '</tr>';
	$prev = '';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		 if (substr($row['name'], 0, 1) != $prev) {
		  $prev = substr($row['name'], 0, 1);
				echo '<tr valign="top" id="'.strtoupper($prev).'">';
 			echo '<td style="background-color:#eeeeee"><a class="small" href="#"><img src="'.DCTL_IMAGES.'arrow_up.gif" border="0"/></a></td>';
			 echo '<td style="background-color:#eeeeee" colspan="4">';
			 echo '<strong>[ '.strtoupper($prev).' ]</strong>';
			 echo '</td>';
			 echo '<td style="background-color:#eeeeee">&#160;</td>';
    echo '</tr>';
		 };
			$is_root = $row['collector'] == 0;
			echo '<tr valign="top">';
			echo '<td>'.$row['type'].'</td>';
			echo '<td>'.$row['subtype'].'</td>';
			echo '<td>';
			echo '<a href="editName.php?id='.$row['id'].'" title="modifica Nome">';
			if ($is_root) {
				echo $row['name'];
			} else {
				echo '<i>'.$row['name'].'</i>';
			};
			echo '</a>';
			echo ' <i>('.$row['lang'].")</i>";
			echo '</td>';
			echo '<td>';
			if ($is_root) {
			echo '<a href="editName.php?id='.$row['id'].'" title="modifica Nome">';
				echo sprintf("%06s", $row['id']).'</a>';
			} else {
			echo '<a href="editName.php?id='.$row['collector'].'" title="modifica Nome">';
				echo sprintf("%06s", $row['collector']).'</a>';
			};
			echo '</td>';
			if ($is_root) {
				echo '<td>';
				$id = $row['id'];
				$query = "SELECT tNAME.id,tNAME.name,tNAME.lang FROM tNAME WHERE tNAME.collector='$id' AND tNAME.id!='$id' ORDER BY tNAME.name";
				$result2 = mysql_query($query) or die ("Error in query: $query.  ".mysql_error());
				if (mysql_num_rows($result2) > 0) {
					while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
						echo '<i><a href="editName.php?id='.$row2['id'].'" title="modifica Nome">'.$row2['name'].'</a> ('.$row2['lang'].')</i>';
						echo '<br />';
					};
				};
				echo '</td>';
				echo '<td>';
				echo '<a href="addName.php?collector='.$row['id'].'" title="aggiungi Variante">[+]</a>';
				echo '</td>';
			} else {
				$collector = $row['collector'];
				$query = "SELECT tNAME.id,tNAME.name,tNAME.lang FROM tNAME WHERE tNAME.id='$collector'";
				$result2 = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
				$row2 = mysql_fetch_array($result2, MYSQL_ASSOC);
				echo '<td>di ';
				echo '<a href="editName.php?id='.$row2['id'].'" title="modifica Nome">'.$row2['name'].'</a> <i>('.$row2['lang'].')</i>';
				echo '</td>';
				echo '<td>';
				echo '&#160;';
				echo '</td>';
			};
			echo '</tr>';
		};
		echo '</table>';
} else {
	if ($name !='') {
	echo "Nessuna corrispondenza trovata per <b>$name</b> !";
	} else {
	echo "Nessuna corrispondenza trovata per <b>$id</b> !";
	};
};
echo '<br />';
echo '<br />';
if ($name !='') {
echo '<a href="searchByName.php?name='.$name.'">Riprova la ricerca</a>';
} else {
echo '<a href="searchByid.php?id='.$id.'">Riprova la ricerca</a>';
}
echo '<br />';
echo '<br />';
if ($name !='') {
echo '<a href="addName.php?name='.$name.'">Aggiungi Forma Normalizzata: '.my_strtoupper($name).'</a>';
}

// footer
mysql_close($connection);
};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
//
?>
