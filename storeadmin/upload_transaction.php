

<?php
include "../storescripts/connect_to_mysql.php"; 
$table="Transaction";
$sql = "TRUNCATE TABLE `$table`";
mysql_query($sql);


$filename= mysql_real_escape_string($_FILES["file"]["tmp_name"]);

$lines = file($filename); // slurp file and split into an array by lines

foreach($lines as $line) {
    $parts = explode(':', $line); // decompose a line into individual sections
    $Transaction_ID = mysql_real_escape_string(trim($parts[0])); // prepare sections for SQL
    $Cashier_ID = mysql_real_escape_string(trim($parts[1]));
    $Product_Name = mysql_real_escape_string(trim($parts[2]));
	$Barcode = mysql_real_escape_string(trim($parts[3]));
	$Unit_Sold = mysql_real_escape_string(trim($parts[4]));
	$Date = mysql_real_escape_string(trim($parts[5]));
	
	
    $date = explode('/', $Date);
    if (strlen($date[0]) == 1)
        $date[0] = '0' . $date[0];
    if (strlen($date[1]) == 1)
        $date[1] = '0' . $date[1];
    $new = $date[2] . '/' . $date[1] . '/' . $date[0];
  $Date= $new;

	

    $sql = "INSERT INTO Transaction (Transaction_ID, Cashier_ID, Product_Name, Barcode, Unit_Sold, Date) VALUES ('$Transaction_ID', '$Cashier_ID', '$Product_Name', '$Barcode', '$Unit_Sold', '$Date')";
    mysql_query($sql) or die(mysql_error());
}
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=transaction1.php">';    
    exit;    
?>