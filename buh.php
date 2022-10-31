<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="shortcut icon" href="buh.ico">
<title>Домашняя бухгалтерия. Карт-счет</title>
<link rel="preload" as="font" href="dos.ttf" type="font/ttf" crossorigin="crossorigin" />
<link href='buh.css' rel='stylesheet' type='text/css'>
</head>
<body>
<p align="center"><a href="buh.php">Домашняя бухгалтерия. Карт-счет</a></p>
<? 
include 'buhvar.php';
include 'buhfunctions.php';

$mtext = array(1 => 'янв', 'февр', 'март', 'апр', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'нояб', 'дек');

DB_Init($server, $user, $password, $dbname);

$years = DB_GetYears();

$cur = 'BYN';

$idmode = $_GET['mode'];
$god = (int)$_GET['y'];
$m = (int)$_GET['m'];
$idts = (int)$_GET['tsid'];

switch($idmode)
{

// ====  форма для дохода======================================================

case cash:

echo '<table id="taskform" border="0" cellspacing="0"><form id="form1" name="form1" method="post" action="buh.php">';
echo '<tr><td>Доходная транзакция</td><td><select name="idtsform" size = "1">';

$incomeTransactionTypes = DB_GetIncomeTransactionsTypes();
 foreach($incomeTransactionTypes as $trType){
    echo '<option value="'.$trType['id'].'">'.$trType['transaction'].'</option>';
  }

echo '</select></td></tr>';
echo '<tr><td>Сумма транзакции</td><td><input name="inform" type="text" size="10" maxlength="10" placeholder="0.00" required /></td></tr>';
echo '<tr><td colspan="2" align="center"><input type="submit" name="InSubmit" value="Добавить" /></form></td></tr></table></p>';


break;

// ====  манибек подробно ======================================================

case mback:

echo '<p><table id="taskview">';
echo '<tr><td colspan="2" align="center">Манибэк</td></tr>';
$stats = mysql_query("SELECT (ROUND(SUM(buhview.income), 2)) AS paid, (SELECT (ROUND(SUM(buhview.outcome) * 0.02, 2)) FROM buhview WHERE idts = 200) AS calchigh, (SELECT (ROUND(SUM(buhview.outcome) * 0.005, 2)) FROM buhview WHERE idts = 201) AS calclow FROM buhview WHERE buhview.idts = 101");
$row = mysql_fetch_array($stats);
$mbfull = $row['calchigh'];
$mbhalf = $row['calclow'];
$mbpay = $row['paid'];

$mbost = $mbfull + $mbhalf - $mbpay;
$mbost = number_format($mbost, 2, '.', '');

echo '<tr><td>Расчетный по операциям с 2%</td><td align="center">'.$mbfull.' '.$cur.'</td></tr>';
echo '<tr><td>Расчетный по операциям с 0.5%</td><td align="center">'.$mbhalf.' '.$cur.'</td></tr>';
echo '<tr><td>Выплаченный</td><td align="center">'.$mbpay.' '.$cur.'</td></tr>';
echo '<tr><td>Невыплаченный</td><td align="center">'.$mbost.' '.$cur.'</td></tr>';
echo '</table></p><p>';

break;

// ====  Выбока по месяцу  ======================================================
case month:

echo '<p align="center">'.$god.': ';

$months = DB_GetMonthsWithTransactionsByYear($god);
foreach($months as $monthId){
    echo '<a href="buh.php?mode=month&y='.$god.'&m='.$monthId.'">'.$mtext[$monthId].'</a> ';
  }  
echo '</p>';

echo '<table id="taskview">';
echo '<tr><td colspan="4" align="center">'.$mtext[$m].' '.$god.' год</td></tr>';
echo '<tr><td align="center">Дата</td><td align="center">Транзакция</td><td align="center">Доход</td><td align="center">Расход</td></tr>';

$monthTransactions = DB_GetMonthsTransactions($god, $m);
 foreach($monthTransactions as $trType){
$datecreated = $trType['date'];
$datecreated = date("d.m.Y", strtotime($datecreated));
echo '<tr>';
echo '<td align="center"><a href="buhdb.php?mode=del&id='.$trType['id'].'" onclick="return confirm(\'УДАЛИТЬ транзакцию?\');">'.$datecreated.'</a></td>';
echo '<td align="left"><a href="buh.php?mode=trans&y='.$god.'&tsid='.$trType['idts'].'">'.$trType['transaction'].'</a></td>';
echo '<td align="center">'.$trType['income'].'</td>';
echo '<td align="center">'.$trType['outcome'].'</td>';
echo '</tr>';
}

$stats = mysql_query("SELECT SUM(income) FROM $buhtable WHERE YEAR(date) = '$god' AND MONTH(date) = '$m'");
$msgnum = mysql_fetch_array($stats);
$totalin = $msgnum[0];

$stats = mysql_query("SELECT SUM(outcome) FROM $buhtable WHERE YEAR(date) = '$god' AND MONTH(date) = '$m'");
$msgnum = mysql_fetch_array($stats);
$totalout = $msgnum[0];

echo '<tr><td colspan="2" align="center">Сумма</td><td align="center">'.$totalin.'</td><td align="center">'.$totalout.'</td></tr>';

$stats = mysql_query("SELECT (SUM(buhview.income) - SUM(buhview.outcome)) AS balanse FROM $buhview WHERE YEAR(date) = '$god' AND MONTH(date) = '$m'");
$row = mysql_fetch_array($stats);
$totalgod = $row['balanse'];
echo '<tr><td colspan="2" align="center">Сальдо</td><td colspan="2" align="center">'.$totalgod.' '.$cur.'</td></tr>';
echo '</table>';

break;

// ====  Выбока по определенному типу транзакции  ======================================================
case trans:

echo '<table id="taskview">';
echo '<tr><td colspan="4" align="center">'.$god.' год</td></tr>';
echo '<tr><td align="center">Дата</td><td align="center">Транзакция</td><td align="center">Доход</td><td align="center">Расход</td></tr>';

$typeTransactions = DB_GetTypeTransactions($god, $idts);
 foreach($typeTransactions as $trType){
$datecreated = $trType['date'];
$datecreated = date("d.m.Y", strtotime($datecreated));
echo '<tr>';
echo '<td align="center">'.$datecreated.'</td>';
echo '<td align="left">'.$trType['transaction'].'</td>';
echo '<td align="center">'.$trType['income'].'</td>';
echo '<td align="center">'.$trType['outcome'].'</td>';
echo '</tr>';
}

$stats = mysql_query("SELECT ABS(SUM(buhview.income) - SUM(buhview.outcome)) AS balanse FROM $buhview WHERE idts ='$idts' AND YEAR(date) = '$god'");
$row = mysql_fetch_array($stats);
$totaltr = $row['balanse'];

$totalmb = 0;
if ($idts == 200) {
$stats = mysql_query("SELECT (ROUND(SUM(buhview.outcome) * 0.02, 2)) AS moneyback FROM $buhview WHERE idts ='$idts' AND YEAR(date) = '$god'");
$row = mysql_fetch_array($stats);
$totalmb = $row['moneyback'];
}

if ($idts == 201) {
$stats = mysql_query("SELECT (ROUND(SUM(buhview.outcome) * 0.005, 2)) AS moneyback FROM $buhview WHERE idts ='$idts' AND YEAR(date) = '$god'");
$row = mysql_fetch_array($stats);
$totalmb = $row['moneyback'];
}

echo '<tr><td colspan="4" align="right">Всего по транзакции: '.$totaltr.' '.$cur.'<br />Манибек: '.$totalmb.' '.$cur.'</td></tr>';
echo '</table>';

break;

// ==========================================================
default:

echo '<p align="center">:: <a href="appsindex.php">Выход</a> : <a href="buh.php?mode=cash">Доход</a> : <a href="buh.php?mode=mback">Манибэк</a> ::</p>';
echo '<p><table id="taskview">';

foreach($years as $god)
{
$months = DB_GetMonthsWithTransactionsByYear($god);
echo '<tr><td colspan="3" align="left">'.$god.' &gt; ';
foreach($months as $monthId){
    echo '<a href="buh.php?mode=month&y='.$god.'&m='.$monthId.'">'.$mtext[$monthId].'</a> ';
  }  
echo '</td></tr>';
}

$stats = mysql_query("SELECT SUM(income) - SUM(outcome) FROM $buhtable");
$msgnum = mysql_fetch_array($stats);
$balance = $msgnum[0];

echo '<tr><td align="center">Баланс счета</td><td colspan="2" align="center">'.$balance.' '.$cur.'</td></tr>';
echo '</table></p>';

echo '<table id="taskview">';
echo '<tr><td colspan="4" align="center">Транзакции на текущую дату</td></tr>';
echo '<tr><td align="center">Дата</td><td align="center">Транзакция</td><td align="center">Доход</td><td align="center">Расход</td></tr>';

$nowTransaction = DB_GetCurrentDateTransactions();
 foreach($nowTransaction as $trType){
$datecreated = $trType['date'];
$datecreated = date("d.m.Y", strtotime($datecreated));
echo '<tr>';
echo '<td align="center">'.$datecreated.'</td>';
echo '<td align="left">'.$trType['transaction'].'</td>';
echo '<td align="center">'.$trType['income'].' '.$cur.'</td>';
echo '<td align="center">'.$trType['outcome'].' '.$cur.'</td>';
echo '</tr>';
}
echo '</table><p>';

echo '<table id="taskform" border="0" cellspacing="0"><form id="form1" name="form1" method="post" action="buh.php">';
echo '<tr><td>Расходная транзакция</td><td><select name="idtsform" size = "1">';

$outcomeTransactionTypes = DB_GetOutcomeTransactionsTypes();
 foreach($outcomeTransactionTypes as $trType){
    echo '<option value="'.$trType['id'].'">'.$trType['transaction'].'</option>';
  }

echo '</select></td></tr>';
echo '<tr><td>Сумма транзакции</td><td><input name="outform" type="text" size="10" maxlength="10" placeholder="0.00" required /></td></tr>';
echo '<tr><td colspan="2" align="center"><input type="submit" name="OutSubmit" value="Добавить" /></form></td></tr></table></p>';

if(isset($_POST['OutSubmit']))
{
DB_InsertOutcomeTransaction();
echo '<meta http-equiv="refresh" content="0;URL=buh.php">';
}

if(isset($_POST['InSubmit']))
{
DB_InsertIncomeTransaction();
echo '<meta http-equiv="refresh" content="0;URL=buh.php">';
}


}
mysql_close($dblink);
echo '<p align="center">&copy; FDDVORON, 2022</p>';
?>
</p>
</body>
</html>