<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
//
    echo "<h2>Cerca per NOME</h2>";

    echo 'Scrivi il NOME che vuoi ricercare.';
    echo '<br />';
    echo 'Puoi scrivere solo una parte del nome (p.es. "arist").';
    echo '<br />';
    echo 'Puoi scrivere sia in minuscolo che in maiuscolo; i segni diacritici vengono ignorati.';
    echo '<br />';
    echo '<br />';

    $name = '';
    if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];

    echo '<form action="search.php?" method="'.DCTL_FORM_METHOD.'">';
    echo '<table>';
    echo '<tr><td>Nome:</td>';
    echo '<td>';
    echo '<input type="text" name="name" size="40" maxlength="80" value="';
    echo $name;
    echo '">';
    echo '</td>';
    echo '</tr></table>';
    echo '<br />';
    echo '<input type="submit" value="Ricerca" name="submit">';
    echo '<br />';
    echo '</form>';

// footer
 require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'footer.php');
//
?>
