<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

    $connection = dctl_sql_connect(DCTL_DB_ICONCLASS);
    if ($connection) {
    $name = '';
    if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];
    $note = '';
    if (isset($_REQUEST['note'])) $name = $_REQUEST['note'];
    $iconclass = '';
    if (isset($_REQUEST['iconclass'])) $name = $_REQUEST['iconclass'];

    echo "<h2>Aggiungi</h2>";
    echo "<h3>";
    echo "Soggetto ";
    echo "</h3>";
    echo '<form action="processName.php?" method="'.DCTL_FORM_METHOD.'">';
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
    echo '<input type="submit" value="Aggiungi" name="submit">';
    echo '</form>';

// footer
mysql_close($connection);
//

 };
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'footer.php');
?>
