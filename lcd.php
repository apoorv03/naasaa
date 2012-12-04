<?php 

session_start(); // Start session first thing in script
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Connect to the MySQL database  
include "storescripts/connect_to_mysql.php"; 
?>

<?php
	function get_info()
	{	//$LCD_ID=3;
		$LCD_ID = $_POST['ID'];
		$LCD_ID=str_pad( $LCD_ID, 6, '0', STR_PAD_LEFT );
		//echo"$LCD_ID <br />";
		$q = mysql_query("SELECT * FROM Inventory,LCD WHERE LCD.LCD_ID = '$LCD_ID' AND Inventory.Barcode= LCD.Barcode LIMIT 1 ");
		if(mysql_num_rows($q) < 1)
				die (mysql_error());
		$row=mysql_fetch_array($q);
		$price = $row["Selling_Price"];
		$qty=$row["Current_Stock"];
		$prod_name=$row["Product_Name"];
		$Barcode=$row["Barcode"];

		mysql_close();
		//String Format
		$info = " $prod_name,$qty,$price";
		//Array Format
		//$info = array($prod_name,$qty, $price);

		//echo "$info<br />";
		//print_r($info);

		return $info;
	}

	echo get_info();

?>

