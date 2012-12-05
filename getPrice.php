<?php 

session_start(); // Start session first thing in script
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Connect to the MySQL database  
include "storescripts/connect_to_mysql.php"; 
?>

<?php
	function get_price()
	{
		$product = '';
		if(!isset($_POST['barcode']))
			$product = $_GET['barcode'];
		else 
			$product = $_POST['barcode'];
		$lower = $product * 100;
		$upper = ($product * 100) + 99;
		$q = mysql_query("SELECT * FROM Inventory WHERE Barcode LIKE '%$product%' LIMIT 1");
		if(mysql_num_rows($q) < 1){
				die (mysql_error());
				return -1;
			}
		else {$row=mysql_fetch_array($q);
			$price = $row["Selling_Price"];
			$name = $row["Product_Name"];
			if(!isset($_POST['barcode'])) echo $name . ",";
			mysql_close();
			return $price;
		}
	}
	echo get_price();
?>
