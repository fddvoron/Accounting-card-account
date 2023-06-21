<? 

function DB_Init($server, $user, $password, $dbname){
  $dblink = mysql_connect($server, $user, $password);
  mysql_select_db($dbname); 
  mysql_query("SET NAMES 'utf8'");
}

function DB_GetYears(){
  global $buhtable;
  $query = mysql_query("select DISTINCT YEAR(date) from $buhtable ORDER BY date ASC");
  while ($data = mysql_fetch_row($query))
  {
  	$result[] = $data[0];
  }
  return $result;
}

function DB_GetMonthsWithTransactionsByYear($year){
  global $buhview;
  $query = mysql_query("select DISTINCT MONTH(date) from $buhview WHERE YEAR(date) = '$year' ORDER BY date ASC");
  while ($data = mysql_fetch_row($query))
  {
  	$result[] = $data[0];
  }
  return $result;
}

function DB_GetIncomeTransactionsTypes(){
  global $buhts;
  $query = mysql_query("select * from $buhts WHERE id BETWEEN 100 AND 199 ORDER BY id");
	while ($data = mysql_fetch_assoc($query))
  {
  	$result[] = $data;
  }
  return $result;
}

function DB_GetOutcomeTransactionsTypes(){
  global $buhts;
  $query = mysql_query("select * from $buhts WHERE id BETWEEN 200 AND 299 ORDER BY transaction");
	while ($data = mysql_fetch_assoc($query))
  {
  	$result[] = $data;
  }
  return $result;
}

function DB_GetMonthsTransactions($god, $m){
  global $buhview;
 $query = mysql_query("SELECT * FROM $buhview WHERE YEAR(date) = '$god' AND MONTH(date) = '$m' ORDER BY date ASC");
  while ($data = mysql_fetch_assoc($query))
  {
  	$result[] = $data;
  }
  return $result;
}

function DB_GetTypeTransactions($god, $idts){
  global $buhview;
  $query = mysql_query("SELECT * FROM $buhview WHERE idts ='$idts' AND YEAR(date) = '$god' ORDER BY date ASC");
  while ($data = mysql_fetch_assoc($query))
  {
  	$result[] = $data;
  }
  return $result;
}

function DB_GetCurrentDateTransactions(){
  global $buhview;
$query = mysql_query("SELECT * FROM $buhview WHERE DATE(date) = CURRENT_DATE ORDER BY date ASC");
  while ($data = mysql_fetch_assoc($query))
  {
  	$result[] = $data;
  }
  return $result;
}

function DB_InsertOutcomeTransaction($accountid){
  global $buhtable;
  $trans = (int)$_POST['idtsform'];
  $outnew = $_POST['outform'];
  $outnew= str_replace(",",".",$outnew);
  mysql_query("INSERT INTO $buhtable (account, idts, income, outcome) VALUES ('$accountid', '$trans', 0, '$outnew')");
}

function DB_InsertIncomeTransaction($accountid){
  global $buhtable;
  $trans = (int)$_POST['idtsform'];
  $innew = $_POST['inform'];
  $innew= str_replace(",",".",$innew);
  mysql_query("INSERT INTO $buhtable (account, idts, income, outcome) VALUES ('$accountid', '$trans', '$innew', 0)");
}



?>