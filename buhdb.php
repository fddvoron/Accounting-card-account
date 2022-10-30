<? 
include 'buhvar.php';

$idmode = $_GET['mode'];
$id = (int)$_GET['id'];

switch($idmode)
{

// ====  добавление в базу  расхода ======================================================

case out:

$dblink = mysql_connect($server, $user, $password);
mysql_select_db($dbname); 
mysql_query("SET NAMES 'utf8'");

$trans = (int)$_POST['idtsform'];

$outnew = $_POST['outform'];
$outnew= str_replace(",",".",$outnew);

mysql_query("INSERT INTO $buhtable (idts, income, outcome) VALUES ('$trans', 0, '$outnew')");

mysql_close($dblink);

echo '<meta http-equiv="refresh" content="0;URL=buh.php">';

break;


// ====  добавление в базу дохода  ======================================================

case in:

$dblink = mysql_connect($server, $user, $password);
mysql_select_db($dbname); 
mysql_query("SET NAMES 'utf8'");

$trans = (int)$_POST['idtsform'];

$innew = $_POST['inform'];
$innew= str_replace(",",".",$innew);

mysql_query("INSERT INTO $buhtable (idts, income, outcome) VALUES ('$trans', '$innew', 0)");

mysql_close($dblink);

echo '<meta http-equiv="refresh" content="0;URL=buh.php">';

break;

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