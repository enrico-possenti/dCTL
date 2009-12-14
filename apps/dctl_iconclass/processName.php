<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

 $connection = dctl_sql_connect(DCTL_DB_ICONCLASS);
    if ($connection) {
//
	//set up shortname for variables
    $id = '';
    if (isset($_REQUEST['id'])) $id = $_REQUEST['id'];
    $name = '';
    if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];
    $note = '';
    if (isset($_REQUEST['note'])) $note = $_REQUEST['note'];
    $iconclass = '';
    if (isset($_REQUEST['iconclass'])) $iconclass = strtoupper($_REQUEST['iconclass']);

    // $name = ucwords($name);
    $nameSoundex = my_soundex($name);
    $nameX = my_strtoupper ($name);

//
    echo "<h2>Aggiungi";

    if ($name !='') {

    echo "<h3>";
    	echo "Soggetto";
    echo "</h3>";

	# don't forget null
		$query = "INSERT into tNAME  values (NULL,'$name','$iconclass','$nameSoundex','$nameX','$note')";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());


		$id = mysql_insert_id();

// if (mysql_affected_rows() >0) {

 echo "Registrazione effettuata con successo:";
	echo "<br />";
	echo "<br />";
	echo "Termine: <b>".$name."</b>";
	echo "<br />";
	echo "Valore key: <b>".sprintf("%06s", $id)."</b>";
	echo "<br />";
	echo "Iconclass: <b>".$iconclass."</b>";
	echo "<br />";
				echo "Note: <i>".$note."</i>";
				echo "<br />";
	echo "<br />";
	echo '<a href="search.php?id='.$id.'">Verifica</a>';

		} else {
  echo "</h2>";
  echo "<h3>Errore: impossibile registrare le modifiche!</h3>";
	 echo "<br />";
	 	}

// footer
mysql_close($connection);
//
};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
?>
