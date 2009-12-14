<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

    global $typex;
    global $subtypex;


    $connection = dctl_sql_connect(DCTL_DB_NAME);
    if ($connection) {


    $collector = '';
    if (isset($_REQUEST['collector'])) $collector = $_REQUEST['collector'];
    $name = '';
    if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];
    $note = '';
    if (isset($_REQUEST['note'])) $name = $_REQUEST['note'];
    $lang = 'it';
    $type = $typex[0];
    $subtype = $subtypex[0];
    //$name = ucwords($name);

    $is_root = ($collector == '' OR $collector == 0);

    echo "<h2>Aggiungi Nome</h2>";
    echo "<h3>";
    if (! $is_root) {
    	echo "Variante di ";
    	$query = "SELECT tNAME.name, tNAME.type FROM tNAME WHERE tNAME.id='$collector'";
					$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
    	$row = mysql_fetch_array($result, MYSQL_ASSOC);
    	echo my_strtoupper($row['name']);
     $type = $row['type'];
    	echo ' (key = '.sprintf("%06s", $collector).')';
    } else {
    	echo "Forma Normalizzata ";
				}
    echo "</h3>";
    echo '<form action="processName.php?collector='.$collector.'" method="'.DCTL_FORM_METHOD.'">';
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
						if ($type == $type1) {
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
    echo '<input type="submit" value="Aggiungi" name="submit">';
    echo '</form>';

// footer
mysql_close($connection);
//

 };
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
?>
