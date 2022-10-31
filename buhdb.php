<? 
include 'buhvar.php';

$idmode = $_GET['mode'];
$id = (int)$_GET['id'];

switch($idmode)
{

// ====  удаление транзакции  ======================================================

case del:

$dblink = mysql_connect($server, $user, $password);
mysql_select_db($dbname); 
mysql_query("SET NAMES 'utf8'");

mysql_query("DELETE FROM $buhtable WHERE id = '$id'");

mysql_close($dblink);

echo '<meta http-equiv="refresh" content="0;URL=buh.php">';

break;

// ==========================================================
default:

echo '<meta http-equiv="refresh" content="0;URL=buh.php">';

}
?>