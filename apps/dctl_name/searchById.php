<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
//
    echo "<h2>Cerca per KEY</h2>";

    echo 'Scrivi il codice KEY che vuoi ricercare.';
    echo '<br />';
    echo 'Puoi scrivere solo il numero (p.es. "27") oppure il codice intero (p.es. "000027").';
    echo '<br />';
    echo '<br />';

    $id = '';
    if(isset($_REQUEST['id'])) $name = $_REQUEST['id'];

    echo '<form action="search.php?" method="'.DCTL_FORM_METHOD.'">';
    echo '<table border="0">';
    echo '<tr><td>Key:</td>';
    echo '<td>';
    echo '<input type="text" name="id" size="10" maxlength="10" value="';
    echo $id;
    echo '">';
    echo '</td>';
    echo '</tr></table>';
    echo '<br />';
    echo '<input type="submit" value="Ricerca" name="submit">';
    echo '</form>';

// footer
 require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
//
?>
