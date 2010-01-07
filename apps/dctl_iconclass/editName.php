<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

$connection = dctl_sql_connect(DCTL_DB_ICONCLASS);
    if ($connection) {
//
//

    $id = '';
    if (isset($_REQUEST['id'])) $id = $_REQUEST['id'];

    $query = "SELECT * FROM tNAME WHERE tNAME.id='$id'";
				$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());

if (mysql_num_rows($result) > 0) {
   $row = mysql_fetch_array($result, MYSQL_ASSOC);

    $name = $row['name'];
    $note = $row['note'];
    $iconclass = $row['iconclass'];
    // $name = ucwords($name);

    echo "<h2>Modifica</h2>";
    echo "<h3>";
				echo "Soggetto ";
				echo my_strtoupper($name);
				echo ' (key = '.sprintf("%06s", $id).')';
    echo "</h3>";

    echo '<form action="updateName.php?id='.$id.'" method="'.DCTL_FORM_METHOD.'">';
    echo '<table border="0">';
    echo '<tr><td>Soggetto:</td>';
    echo '<td>';
    echo '<input type="text" name="name" size="40" maxlength="80" value="';
    echo $name;
    echo '">';
    echo '</td>';
    echo '</tr>';
    echo '<tr><td>Iconclass:</td>';
    echo '<td>';
    echo '<input type="text" name="iconclass" size="40" maxlength="80" value="';
    echo $iconclass;
    echo '">';
    echo '</td>';
    echo '</tr>';
    echo '<tr><td>Note:</td>';
    echo '<td>';
    echo '<input type="text" name="note" size="40" maxlength="80" value="';
    echo $note;
    echo '">';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br />';
    echo '<input type="submit" value="Registra le modifiche" name="submit">';
    echo '<br />';
    echo '<br />';
    echo '<hr />';
    echo '<br />';
     echo '<input type="submit" value="Elimina" name="delete_id">';
    echo '</form>';

	} else {
		echo 'Nessun record trovato...';
	}

// footer
mysql_close($connection);

};
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'footer.php');
//
?>
