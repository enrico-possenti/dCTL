<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');
//
    echo "<h2>Cerca per SOGGETTO</h2>";

    echo 'Scrivi il SOGGETTO che vuoi ricercare.';
    echo '<br />';
    echo 'Puoi scrivere solo una parte del soggetto (p.es. "eremit").';
    echo '<br />';
    echo 'Puoi scrivere sia in minuscolo che in maiuscolo; i segni diacritici vengono ignorati.';
    echo '<br />';
    echo '<br />';

    $name = '';
    if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];

    echo '<form action="search.php?" method="'.DCTL_FORM_METHOD.'">';
    echo '<table>';
    echo '<tr><td>Soggetto:</td>';
    echo '<td>';
    echo '<input type="text" name="name" size="40" maxlength="80" value="';
    echo $name;
    echo '">';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br />';
    echo '<input type="submit" value="Ricerca in banca dati" name="submit" />';
    echo '<br />';
    echo '</form>';

    echo '<br />';
    echo '<br />';
    echo '<hr />';

    echo '<form action="http://www.iconclass.nl/libertas/ic" method="'.DCTL_FORM_METHOD.'" class="external">';
    echo '<table>';
    echo '<tr><td>Iconclass:</td>';
    echo '<td>';
    echo '<input type="text" name="datum" size="40" maxlength="80" value="';
    echo $name;
    echo '"><br />';
    echo '<input type="radio" value="it" name="taal" checked="checked" /><label> it</label>&#160;&#160;';
    echo '<input type="radio" value="en" name="taal" /><label> en</label>&#160;&#160;';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br />';
    echo '<input type="submit" value="Ricerca in Iconclass" name="submit" class="external" />';
    echo '<input type="hidden" value="keywordlistbb.xsl" name="style" />';
    echo '<input type="hidden" value="getkeywords" name="task" />';


    echo '<br />';
    echo '</form>';

// footer
 require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');
//
?>
