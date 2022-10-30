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

$mtext = array(1 => 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');

$dblink = mysql_connect($server, $user, $password);
mysql_select_db($dbname); 
mysql_query("SET NAMES 'utf8'");

$query = mysql_query("select DISTINCT YEAR(date) from $buhtable ORDER BY date ASC");
while ($data = mysql_fetch_row($query))
{
$years[] = $data[0];
}

$cur = 'BYN';

$idmode = $_GET['mode'];
$god = (int)$_GET['y'];
$m = (int)$_GET['m'];
$idts = (int)$_GET['tsid'];

switch($idmode)
{

// ====  форма для дохода======================================================

case cash:

$query = mysql_query("select * from $buhts WHERE id BETWEEN 100 AND 199 ORDER BY id");
echo '<table id="taskform" border="0" cellspacing="0"><form id="form1" name="form1" method="post" action="buhdb.php?mode=in">';
echo '<tr><td>Доходная транзакция</td><td><select name="idtsform" size = "1">';
while ($data = mysql_fetch_array($query))
{
echo '<option value="'.$data['id'].'">'.$data['transaction'].'</option>';
}
echo '</select></td></tr>';
echo '<tr><td>Сумма транзакции</td><td><input name="inform" type="text" size="10" maxlength="10" placeholder="0.00" required /></td></tr>';
echo '<tr><td colspan="2" align="center"><input type="submit" name="Submit" value="Добавить" /></form></td></tr></table></p>';

break;

// ====  показать операции за определенный год ======================================================

case year:

echo '<p align="center">'.$god.': ';
$query = mysql_query("select DISTINCT MONTH(date) from $buhview WHERE YEAR(date) = '$god' ORDER BY date ASC");
while ($data = mysql_fetch_row($query))
{
echo '<a href="buh.php?mode=month&y='.$god.'&m='.$data[0].'">'.$mtext[$data[0]].'</a> ';
}
echo '</p>';

echo '<p><table id="taskview"><tr><td colspan="2" align="center">'.$god.' год</td></tr>';
echo '<tr><td align="center">Доход</td><td align="center">Расход</td></tr>';

$stats = mysql_query("SELECT SUM(income) FROM $buhtable WHERE YEAR(date) = '$god'");
$msgnum = mysql_fetch_array($stats);
$totalingod = $msgnum[0];

$stats = mysql_query("SELECT SUM(outcome) FROM $buhtable WHERE YEAR(date) = '$god'");
$msgnum = mysql_fetch_array($stats);
$totaloutgod = $msgnum[0];

echo '<tr><td align="center">'.$totalingod.' '.$cur.'</td><td align="center">'.$totaloutgod.' '.$cur.'</td></tr>';
echo '</table></p>';

break;

// ====  Выбока по месяцу  ======================================================
case month:

echo '<p align="center">'.$god.': ';
$query = mysql_query("select DISTINCT MONTH(date) from $buhview WHERE YEAR(date) = '$god' ORDER BY date ASC");
while ($data = mysql_fetch_row($query))
{
echo '<a href="buh.php?mode=month&y='.$god.'&m='.$data[0].'">'.$mtext[$data[0]].'</a> ';
}
echo '</p>';

$query = mysql_query("SELECT * FROM $buhview WHERE YEAR(date) = '$god' AND MONTH(date) = '$m' ORDER BY date ASC");
echo '<table id="taskview">';
echo '<tr><td colspan="4" align="center">'.$mtext[$m].' '.$god.' год</td></tr>';
echo '<tr><td align="center"><b>Дата</b></td><td align="center"><b>Транзакция</b></td><td align="center"><b>Доход</b></td><td align="center"><b>Расход</b></td></tr>';
while ($data = mysql_fetch_array($query))
{
$datecreated = $data['date'];
$datecreated = date("d.m.Y", strtotime($datecreated));
echo '<tr>';
echo '<td align="center"><a href="buhdb.php?mode=del&id='.$data['id'].'" onclick="return confirm(\'Удалить транзакцию?\');">'.$datecreated.'</a></td>';
echo '<td align="left"><a href="buh.php?mode=trans&y='.$god.'&tsid='.$data['idts'].'">'.$data['transaction'].'</a></td>';
echo '<td align="center">'.$data['income'].'</td>';
echo '<td align="center">'.$data['outcome'].'</td>';
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

$query = mysql_query("SELECT * FROM $buhview WHERE idts ='$idts' AND YEAR(date) = '$god' ORDER BY date ASC");
echo '<table id="taskview">';
echo '<tr><td colspan="4" align="center">'.$god.' год</td></tr>';
echo '<tr><td align="center"><b>Дата</b></td><td align="center"><b>Транзакция</b></td><td align="center"><b>Доход</b></td><td align="center"><b>Расход</b></td></tr>';
while ($data = mysql_fetch_array($query))
{
$datecreated = $data['date'];
$datecreated = date("d.m.Y", strtotime($datecreated));
echo '<tr>';
echo '<td align="center">'.$datecreated.'</td>';
echo '<td align="left">'.$data['transaction'].'</td>';
echo '<td align="center">'.$data['income'].'</td>';
echo '<td align="center">'.$data['outcome'].'</td>';
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

echo '<p align="center">:: <a href="appsindex.php">Выход</a> : <a href="buh.php?mode=cash">Доход</a> ::</p>';
echo '<p><table id="taskview"><tr><td align="center">Год</td><td align="center">Доход</td><td align="center">Расход</td></tr>';

foreach($years as $yearnum)
{
 $stats = mysql_query("SELECT SUM(income) FROM $buhtable WHERE YEAR(date) = '$yearnum'");
 $msgnum = mysql_fetch_array($stats);
 $totalingod = $msgnum[0];

 $stats = mysql_query("SELECT SUM(outcome) FROM $buhtable WHERE YEAR(date) = '$yearnum'");
 $msgnum = mysql_fetch_array($stats);
 $totaloutgod = $msgnum[0];

 echo '<tr><td align="center"><a href="buh.php?mode=year&y='.$yearnum.'">'.$yearnum.'</a></td><td align="center">'.$totalingod.' '.$cur.'</td><td align="center">'.$totaloutgod.' '.$cur.'</td></tr>';
}
$stats = mysql_query("SELECT SUM(income) FROM $buhtable");
$msgnum = mysql_fetch_array($stats);
$totalin = $msgnum[0];

$stats = mysql_query("SELECT SUM(outcome) FROM $buhtable");
$msgnum = mysql_fetch_array($stats);
$totalout = $msgnum[0];

$stats = mysql_query("SELECT SUM(income) - SUM(outcome) FROM $buhtable");
$msgnum = mysql_fetch_array($stats);
$balance = $msgnum[0];

echo '<tr><td align="center">Сумма</td><td align="center">'.$totalin.' '.$cur.'</td><td align="center">'.$totalout.' '.$cur.'</td></tr>';
echo '<tr><td align="center">Баланс счета</td><td colspan="2" align="center">'.$balance.' '.$cur.'</td></tr>';
echo '</table></p>';

echo '<p><table id="taskview">';
echo '<tr><td colspan="2" align="center">Манибэк</td></tr>';
$stats = mysql_query("SELECT (ROUND(SUM(buhview.outcome) * 0.02, 2)) AS moneyback FROM $buhview WHERE idts = 200");
$row = mysql_fetch_array($stats);
$mbfull = $row['moneyback'];
echo '<tr><td>Расчетный по операциям с 2%</td><td align="center">'.$mbfull.' '.$cur.'</td></tr>';
$stats = mysql_query("SELECT (ROUND(SUM(buhview.outcome) * 0.005, 2)) AS moneyback FROM $buhview WHERE idts = 201");
$row = mysql_fetch_array($stats);
$mbhalf = $row['moneyback'];
echo '<tr><td>Расчетный по операциям с 0.5%</td><td align="center">'.$mbhalf.' '.$cur.'</td></tr>';
$stats = mysql_query("SELECT (ROUND(SUM(buhview.income), 2)) AS moneyback FROM $buhview WHERE idts = 101");
$row = mysql_fetch_array($stats);
$mbpay = $row['moneyback'];
echo '<tr><td>Выплаченный</td><td align="center">'.$mbpay.' '.$cur.'</td></tr>';
$mbost = $mbfull + $mbhalf - $mbpay;
$mbost = str_replace(",",".",$mbost);
echo '<tr><td>Невыплаченный</td><td align="center">'.$mbost.' '.$cur.'</td></tr>';
echo '</table></p><p>';

$query = mysql_query("SELECT * FROM $buhview WHERE DATE(date) = CURRENT_DATE ORDER BY date ASC");
echo '<table id="taskview">';
echo '<tr><td colspan="4" align="center">Транзакции на текущую дату</td></tr>';
echo '<tr><td align="center">Дата</td><td align="center">Транзакция</td><td align="center">Доход</td><td align="center">Расход</td></tr>';
while ($data = mysql_fetch_array($query))
{
$datecreated = $data['date'];
$datecreated = date("d.m.Y", strtotime($datecreated));
echo '<tr>';
echo '<td align="center">'.$datecreated.'</td>';
echo '<td align="left">'.$data['transaction'].'</td>';
echo '<td align="center">'.$data['income'].' '.$cur.'</td>';
echo '<td align="center">'.$data['outcome'].' '.$cur.'</td>';
echo '</tr>';
}
echo '</table><p>';

$query = mysql_query("select * from $buhts WHERE id BETWEEN 200 AND 299 ORDER BY id");
echo '<table id="taskform" border="0" cellspacing="0"><form id="form1" name="form1" method="post" action="buhdb.php?mode=out">';
echo '<tr><td>Расходная транзакция</td><td><select name="idtsform" size = "1">';
while ($data = mysql_fetch_array($query))
{
echo '<option value="'.$data['id'].'">'.$data['transaction'].'</option>';
}
echo '</select></td></tr>';
echo '<tr><td>Сумма транзакции</td><td><input name="outform" type="text" size="10" maxlength="10" placeholder="0.00" required /></td></tr>';
echo '<tr><td colspan="2" align="center"><input type="submit" name="Submit" value="Добавить" /></form></td></tr></table></p>';

}
mysql_close($dblink);
echo '<p align="center">&copy; fddvoron.name, 2021 - 2022</p>';
?>
</p>
</body>
</html>