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
    $delete_id = '';
    if (isset($_REQUEST['delete_id'])) $delete_id = $_REQUEST['delete_id'];
    $delete_coll = '';
    if (isset($_REQUEST['delete_coll'])) $delete_coll = $_REQUEST['delete_coll'];

    // $name = ucwords($name);
    $nameSoundex = my_soundex($name);
    $nameX = my_strtoupper ($name);

    $is_root = ($collector == '' OR $collector == 0);

    if(($delete_id != '') || ($delete_coll != '')) {

    echo "<h2>Elimina Nome";
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
				echo "Nome: <b>".$name."</b>";
				echo "<br />";
				echo "Lingua: <b>".$lang."</b>";
				echo "<br />";
				if ($is_root) {
					echo "Tipo: <b>".$type."</b>";
			echo "<br />";
			echo "SubTipo: <b>".$subtype."</b>";
				};
				echo "<br />";
				if (! $is_root) {
					echo "Valore key: <b>".sprintf("%06s", $collector)."</b>";
				} else {
					echo "Valore key: <b>".sprintf("%06s", $id)."</b>";
	 			echo "<br />";
	 			echo "<br />";
	 			echo "Varianti:<br />";
		   $query2 = "SELECT * FROM tNAME WHERE tNAME.collector='".$id."' ORDER BY tNAME.name";
					$result2 = mysql_query($query2) or die ("Error in query: $query. ".mysql_error());
   		while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
  				echo '- <strong>'.$row2['name']."</strong> (".$row2['lang'].")<br />";
   		};
				};
				echo "<br />";
				echo "Note: <i>".$note."</i>";
				echo "<br />";
				echo "<br />";
				echo '<span class="warning"><img src="'.DCTL_IMAGES.'freccia_alert.gif"> Attenzione: l\'azione non è reversibile...</span>';
				echo "<br />";
				echo "<br />";
    echo '<form action="deleteName.php';
 			if (! $is_root) {
 	   echo '?delete_id='.$id.'" method="'.DCTL_FORM_METHOD.'">';
    	echo '<input type="submit" value="Elimina le variante" name="submit" />';
 	  } else {
 	   echo '?delete_coll='.$id.'" method="'.DCTL_FORM_METHOD.'">';
 	   echo '<input type="submit" value="Elimina la forma e tutte le varianti" name="submit" />';
 	  };
    echo '</form>';


			} else {

//
    echo "<h2>Modifica Nome";

    if ($name !='') {

		$query_x = "UPDATE tNAME SET ";

    echo "<h3>";
    if (! $is_root) {
    	echo "Variante di ";
    	$query = "SELECT tNAME.name FROM tNAME WHERE tNAME.id='$collector'";
					$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
    	$row = mysql_fetch_array($result, MYSQL_ASSOC);
    	echo my_strtoupper($row['name']);
    	echo ' (key = '.sprintf("%06s", $collector).')';
    	$query_x .= " tNAME.collector = '$collector'";
    } else {
    	echo "Forma Normalizzata";
    	$query_x .= " tNAME.collector = '0'";
				}
    echo "</h3>";

	$query_x .= "
	  , tNAME.name = '$name'
			, tNAME.lang = '$lang'
			, tNAME.nameSoundex = '$nameSoundex'
			, tNAME.nameNormalized = '$nameX'
			, tNAME.type = '$type'
			, tNAME.subtype = '$subtype'
			, tNAME.note = '$note'
			WHERE tNAME.id = '$id'";

	$result = mysql_query($query_x) or die ("Error in query: $query. ".mysql_error());

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
	};
	echo "<br />";
	if (! $is_root) {
	 echo "Valore key: <b>".sprintf("%06s", $collector)."</b>";
	echo "<br />";
				echo "Note: <i>".$note."</i>";
				echo "<br />";
	echo "<br />";
		echo '<a href="search.php?id='.$collector.'">Verifica</a>';
	} else {
	 echo "Valore key: <b>".sprintf("%06s", $id)."</b>";
	echo "<br />";
				echo "Note: <i>".$note."</i>";
				echo "<br />";
	echo "<br />";
		echo '<a href="search.php?id='.$id.'">Verifica</a>';
	};

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
