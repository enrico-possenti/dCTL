<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

	$delete_id = '';
	if (isset($_REQUEST['delete_id'])) $delete_id = $_REQUEST['delete_id'];
	if ($delete_id != '') {

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

 $connection = dctl_sql_connect(DCTL_DB_ICONCLASS);
    if ($connection) {
//


 echo "<h2>Elimina";


	$ok = FALSE;
	$query = "DELETE FROM tNAME WHERE tNAME.id='".$delete_id."'";
	$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());

 $ok = $result;


if ($ok) {
 echo "<h3>";
 echo "Eliminazione effettuata con successo";
 echo "</h3>";
	echo "<br />";
	echo "<br />";
	} else {
 echo "<h3>";
 echo "Impossibile eliminare l'elemento... (errore)";
 echo "</h3>";
	};
	echo "<br />";
	echo "<br />";
	echo '<a href="searchByName.php">Prosegui</a>';

// footer
mysql_close($connection);
//

};
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'footer.php');

	} else {
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/searchByName.php';
		header('Location: '.WWW_NAME.$uri);
		exit();
	};
?>
