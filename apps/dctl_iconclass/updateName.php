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
     $delete_id = '';
    if (isset($_REQUEST['delete_id'])) $delete_id = $_REQUEST['delete_id'];

    // $name = ucwords($name);
    $nameSoundex = my_soundex($name);
    $nameX = my_strtoupper ($name);

    if ($delete_id != '') {

    echo "<h2>Elimina";
    echo "<h3>";
				echo "Soggetto";
    echo "</h3>";
				echo "Termine: <b>".$name."</b>";
				echo "<br />";
				echo "Valore key: <b>".sprintf("%06s", $id)."</b>";
				echo "<br />";
				echo "Iconclass: <b>".$iconclass."</b>";
				echo "<br />";
				echo "Note: <i>".$note."</i>";
				echo "<br />";
				echo "<br />";
				echo '<span class="warning"><img src="'.DCTL_IMAGES.'freccia_alert.gif"> Attenzione: l\'azione non è reversibile...</span>';
				echo "<br />";
				echo "<br />";
    echo '<form action="deleteName.php';
				echo '?delete_id='.$id.'" method="'.DCTL_FORM_METHOD.'">';
				echo '<input type="submit" value="Elimina" name="submit" />';
    echo '</form>';


			} else {

//
    echo "<h2>Modifica";

    if ($name !='') {

		$query_x = "UPDATE tNAME SET ";

    echo "<h3>";
				echo "Soggetto";
    echo "</h3>";

	$query_x .= "
	   tNAME.name = '$name'
	 , tNAME.iconclass = '$iconclass'
		,	tNAME.nameSoundex = '$nameSoundex'
		,	tNAME.nameNormalized = '$nameX'
		,	tNAME.note = '$note'
			WHERE tNAME.id = '$id'";

	$result = mysql_query($query_x) or die ("Error in query: $query. ".mysql_error());

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
	 	};
	 	};

// footer
mysql_close($connection);
};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
//
?>
