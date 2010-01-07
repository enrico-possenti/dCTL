<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

 $connection = dctl_sql_connect(DCTL_DB_NAME);
    if ($connection) {
//
	//set up shortname for variables
    $id = '';
    if (isset($_REQUEST['id'])) $id = $_REQUEST['id'];
    $name = '';
    if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];
    $note = '';
    if (isset($_REQUEST['note'])) $note = $_REQUEST['note'];
    $lang = '';
    if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang'];
    $type = '';
    if (isset($_REQUEST['type'])) $type = $_REQUEST['type'];
    $subtype = '';
    if (isset($_REQUEST['subtype'])) $subtype = $_REQUEST['subtype'];
    $collector = '';
    if (isset($_REQUEST['collector'])) $collector = $_REQUEST['collector'];

    // $name = ucwords($name);
    $nameSoundex = my_soundex($name);
    $nameX = my_strtoupper ($name);

    $is_root = ($collector == '' OR $collector == 0);

//
    echo "<h2>Aggiungi Nome";

    if ($name !='') {

    echo "<h3>";
    if (! $is_root) {
    	echo "Variante di ";
    	$query = "SELECT tNAME.name FROM tNAME WHERE tNAME.id='$collector'";
					$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
    	$row = mysql_fetch_array($result, MYSQL_ASSOC);
    	echo my_strtoupper($row['name']);
    	echo ' (key = '.sprintf("%06s", $collector).')';
    } else {
    	echo "Forma Normalizzata";
				}
    echo "</h3>";

	# don't forget null
    if(! $is_root) {
		$query = "INSERT into tNAME  values (NULL, '$collector', '$name',
	'$lang','$nameSoundex',NULL,'$nameX',NULL,'$note')";
	} else {
		$query = "INSERT into tNAME  values (NULL, NULL, '$name',
	'$lang','$nameSoundex','$type','$nameX','$subtype','$note')";
	}
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());


	if (! $is_root) {
		$id = $collector;
	} else {
		$id = mysql_insert_id();
		$query = "update tNAME
			set collector = '0'
			where id = '$id'";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	 $collector = $id;
	 }

// if (mysql_affected_rows() >0) {

 echo "Registrazione effettuata con successo:";
	echo "<br />";
	echo "<br />";
	echo "Nome: <b>".$name."</b>";
	echo "<br />";
	echo "Lingua: <b>".$lang."</b>";
	echo "<br />";
	if ($is_root) {
		echo "Tipo: <b>".$type."</b>";
		echo "<br />";
		echo "SubTipo: <b>".$subtype."</b>";
		echo "<br />";
	};
	echo "Note: <i>".$note."</i>";
	echo "<br />";
	echo "Valore key: <b>".sprintf("%06s", $collector)."</b>";
	echo "<br />";
	echo "<br />";
	echo '<a href="search.php?id='.$collector.'">Verifica</a>';

		} else {
  echo "</h2>";
  echo "<h3>Errore: impossibile registrare le modifiche!</h3>";
	 echo "<br />";
	 	}

// footer
mysql_close($connection);
//
};
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'footer.php');
?>
