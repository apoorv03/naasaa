




	




<?php
include "../storescripts/connect_to_mysql_HQ.php"; 
$table="Inventory";
$sql = "TRUNCATE TABLE `$table`";
mysql_query($sql);


$filename= mysql_real_escape_string($_FILES["file"]["tmp_name"]);

$lines = file($filename); // slurp file and split into an array by lines

foreach($lines as $line) {
    $parts = explode(':', $line); // decompose a line into individual sections
    $Product_Name = mysql_real_escape_string(trim($parts[0])); // prepare sections for SQL
    $Category = mysql_real_escape_string(trim($parts[1]));
    $Manufacturer = mysql_real_escape_string(trim($parts[2]));
	$Barcode = mysql_real_escape_string(trim($parts[3]));
	$Cost_Price = mysql_real_escape_string(trim($parts[4]));
	$Current_Stock = mysql_real_escape_string(trim($parts[5]));

    $sql = "INSERT INTO Inventory (Product_Name, Category, Manufacturer, Barcode, Cost_Price, Current_Stock) VALUES ('$Product_Name', '$Category', '$Manufacturer', '$Barcode', '$Cost_Price', '$Current_Stock')";
    mysql_query($sql) or die(mysql_error());
}
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=inventory_list.php">';    
    exit;    
?>