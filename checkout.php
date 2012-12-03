<?php 

session_start(); // Start session first thing in script
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Connect to the MySQL database  
include "storescripts/connect_to_mysql.php"; 
?>

<?php
	//Call by barcode and quantity and get price
	//Input is 2D array declared as : $arr = array(array('123456', 2), array('234567', 3))
	function getitem()
	{
		$total_price = 0;
		$checkout_list = array(array('a', 'b'), array('c', 'd'));
	
		foreach ($checkout_list as $product)
			echo $product[0] . " . " $product[1];

		foreach ($checkout_list as $product) 
		{
			//if (!isset($product[1]) || $product[1] < 1)
				//die (mysql_error());
			$q = mysql_query("SELECT * FROM Inventory WHERE Barcode = '$product[0]' ");
			if(mysql_num_rows($q) < 1)
				{
					echo "Barcode not found";
					die (mysql_error());
				}
			$row=mysql_fetch_array($q);
			$prod_name = $row['Product_Name'];
			$qty = $row['Current_Stock'];
			$price = $row['Selling_Price'];
			$total_price = $total_price + ($qty * $price);
							
			$getDate = new DateTime(null, new DateTimeZone('Asia/Singapore'));
            $Date = $getDate->format('Y-m-d');
			
			$ins = mysql_query("INSERT INTO Transaction VALUES('123456','1234','$prod_name,'$product[0]','$product[1]','$Date')");
			if(mysql_affected_rows($ins) == 0)
				echo "Value not inserted in Transaction";
				
				
			if($qty < $product[1])
				{
					$diff = $product[1] - $qty;
					$restk = mysql_query("INSERT INTO Restock (Barcode, Stock, Date) VALUES('$product[0]','$diff', '$Date')");
					$new_qty = 0;
				}
			else
					$new_qty = $qty - $product[1];
					
			$upd = mysql_query("UPDATE Inventory SET Current_Stock = '$new_qty' WHERE Barcode = '$product[0]'");
			if(mysql_affected_rows($upd) == 0)
				echo "Value not updated in Inventory";
				
			return $total_price;
		}
		
		echo getitem();
		/*if($new_qty > 0)
			$q_update = mysql_query("INSERT INTO Transaction VALUES quantity = '$new_qty' WHERE Barcode='$product[0]'");
		else
			$q_update = mysql_query("DELETE FROM Transaction WHERE Barcode='$product[0]'");*/
		//$price = $row["Selling_Price"];
		//$sum = $sum + $price;

		/*$product_name = $row["Product_Name"];
			$price = $row["Selling_Price"];
			$category = $row["Category"];
			$Manufacturer=$row["Manufacturer"];
			$Date="2012-09-30";
			$Unit_Sold=$each_item['quantity'];*/
	
?>



