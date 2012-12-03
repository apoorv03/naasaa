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
		$product = $_POST['Barcode'];
		$q = mysql_query("SELECT * FROM Inventory WHERE Barcode = '$product[0]' ");
		if(mysql_num_rows($q) < 1)
				die (mysql_error());
		$row=mysql_fetch_array($q);
		$price = $row["Selling_Price"];

		mysql_close();
		return $price;
	}

	echo get_price();

?>
