<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

    global $typex;
    global $subtypex;


$connection = dctl_sql_connect(DCTL_DB_NAME);
    if ($connection) {
//
//

    $id = '';
    if (isset($_REQUEST['id'])) $id = $_REQUEST['id'];

    $query = "SELECT * FROM tNAME WHERE tNAME.id='$id'";
				$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());

if (mysql_num_rows($result) > 0) {
   $row = mysql_fetch_array($result, MYSQL_ASSOC);

 			$collector = $row['collector'];
    $name = $row['name'];
    $note = $row['note'];
    $lang = $row['lang'];
    $type = $row['type'];
    $subtype = $row['subtype'];
    // $name = ucwords($name);

    $is_root = ($collector == '' OR $collector == 0);

    echo "<h2>Modifica Nome</h2>";
    echo "<h3>";
    if (! $is_root) {
    	echo "Variante di ";
    	$query = "SELECT tNAME.name FROM tNAME WHERE tNAME.id='$collector'";
					$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
    	$row = mysql_fetch_array($result, MYSQL_ASSOC);
    } else {
    	echo "Forma Normalizzata ";
				}
    	echo my_strtoupper($row['name']);
    if (! $is_root) {
    	echo ' (key = '.sprintf("%06s", $collector).')';
    } else {
    	echo ' (key = '.sprintf("%06s", $id).')';
    };
    echo "</h3>";

    echo '<form action="updateName.php?id='.$id.'&collector='.$collector.'" method="'.DCTL_FORM_METHOD.'">';
    echo '<table border="0">';
    echo '<tr><td>Nome:</td>';
    echo '<td>';
    echo '<input type="text" name="name" size="40" maxlength="80" value="';
    echo $name;
    echo '">';
    echo '</td>';
    echo '</tr>';
    echo '<tr><td>Lingua:</td>';
    echo '<td>';
    echo '<input type="text" name="lang" size="2" maxlength="2" value="';
    echo $lang;
    echo '">';
    echo '</td>';
    echo '</tr>';

    if ($is_root) {
					echo '<tr><td>Tipo:</td>';
					echo '<td>';
					echo '<select name="type">';
					foreach ($typex as $type1) {
						echo '<option value="'.$type1.'"';
						if ($type == $type1) {
							echo ' selected';
							}
						echo '>'.$type1.'</option>';
					};
					echo '</select>';
					echo '</td>';
					echo '</tr>';
					echo '<tr><td>SubTipo:</td>';
					echo '<td>';
					echo '<select name="subtype">';
					foreach ($subtypex as $type1) {
						echo '<option value="'.$type1.'"';
						if ($subtype == $type1) {
							echo ' selected';
							}
						echo '>'.$type1.'</option>';
					};
					echo '</select>';
					echo '</td>';
					echo '</tr>';
    };

    echo '<tr><td>Note:</td>';
    echo '<td>';
    echo '<textarea name="note" cols="40" rows="5">';
    echo $note;
    echo '</textarea>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br />';
    echo '<input type="submit" value="Registra le modifiche" name="submit">';
    echo '<br />';
    echo '<br />';
    echo '<hr />';
    echo '<br />';
    if (! $is_root) {
     echo '<input type="submit" value="Elimina la Variante" name="delete_id">';
    } else {
     echo '<input type="submit" value="Elimina la Forma Normalizzata e tutte le Varianti" name="delete_coll">';
    };
    echo '</form>';

	} else {
		echo 'Nessun record trovato...';
	}

// footer
mysql_close($connection);

};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
//
?>
