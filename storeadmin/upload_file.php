




	




<?php
include "../storescripts/connect_to_mysql.php"; 
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
	$Current_Stock=$Current_Stock;
	$Minimum_Stock = mysql_real_escape_string(trim($parts[6]));
	$Minimum_Stock=$Minimum_Stock;
	$Selling_Price= $Cost_Price*1.5;
	$Selling_Price=number_format($Selling_Price, 2, '.', ''); 
    $sql = "INSERT INTO Inventory (Product_Name, Category, Manufacturer, Barcode, Cost_Price, Current_Stock, Minimum_Stock, Selling_Price) VALUES ('$Product_Name', '$Category', '$Manufacturer', '$Barcode', '$Cost_Price', '$Current_Stock', '$Minimum_Stock', '$Selling_Price')";
    mysql_query($sql) or die(mysql_error());
}
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=inventory_list.php">';    
    exit;    
?>