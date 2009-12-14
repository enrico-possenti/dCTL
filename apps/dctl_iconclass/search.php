<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

$connection = dctl_sql_connect(DCTL_DB_ICONCLASS);
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
	echo '<th>Soggetto</th>';
	echo '<th>Key</th>';
	echo '<th>Iconclass</th>';
	echo '</tr>';
	$prev = '';
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		 if (substr($row['name'], 0, 1) != $prev) {
		  $prev = substr($row['name'], 0, 1);
				echo '<tr valign="top" id="'.strtoupper($prev).'">';
			 echo '<td style="background-color:#eeeeee" colspan="4">';
			 echo '<strong>[ '.strtoupper($prev).' ]</strong>';
			 echo '&emsp;<a class="small" href="#"><img src="'.DCTL_IMAGES.'arrow_up.gif" border="0"/></a></td>';
    echo '</tr>';
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
			echo 'href="http://www.iconclass.nl/libertas/ic?style=notationbb.xsl&task=getnotation&taal=en&datum='.convert2ic($row['iconclass']).'"';
			echo '>&emsp;<img src="'.DCTL_IMAGES.'extlink.gif" alt="iconclass" />';
			echo '</a>';
			echo '</td>';
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
echo '<a href="addName.php?name='.$name.'">Aggiungi Soggetto: '.my_strtoupper($name).'</a>';
}

// footer
mysql_close($connection);
};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
//
?>
