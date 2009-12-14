<?php
 if (!defined('_INCLUDE')) define('_INCLUDE', true);

	$delete_id = '';
	if (isset($_REQUEST['delete_id'])) $delete_id = $_REQUEST['delete_id'];
	$delete_coll = '';
	if (isset($_REQUEST['delete_coll'])) $delete_coll = $_REQUEST['delete_coll'];
	if (($delete_id != '') || ($delete_coll != '')) {

// header
 require_once(str_replace('//','/',dirname(__FILE__).'/').'header.php');

 $connection = dctl_sql_connect(DCTL_DB_NAME);
    if ($connection) {
//


 echo "<h2>Elimina Nome";


	$ok = FALSE;
 if ($delete_coll != '') {
		$query = "DELETE FROM tNAME WHERE tNAME.collector='".$delete_coll."' OR tNAME.id='".$delete_coll."'";
 } else {
		$query = "DELETE FROM tNAME WHERE tNAME.id='".$delete_id."'";
 };
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
		header('Location: http://'.WWW_HOST.':'.WWW_PORT.$uri);
		exit();
	};
?>
