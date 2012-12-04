<?php

// Start session first thing in script
// Script Error Reporting
session_start();

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
//$checkout_list = array(array('100364', '3000'), array('101857', '1000'));

$checkout_list =$_POST['arr'];
$checkout_list1 = explode('|', $checkout_list);
$i = 0;
$checkout_list = array();
foreach($checkout_list1 as $checkout_l)
$checkout_list[] = explode(',', $checkout_l);
//print_r($checkout_list);

$sql2 = mysql_query("SELECT MAX(Transaction_ID) FROM Transaction ") or die (mysql_error());
$row = mysql_fetch_array($sql2);
$Transaction_ID=$row[0]+ 1;

//foreach ($checkout_list as $product)
// echo "$product[0] . " . " $product[1] <br/> ";

foreach ($checkout_list as $product)
{
//if (!isset($product[1]) || $product[1] < 1)
//die (mysql_error());
$q = mysql_query("SELECT * FROM Inventory WHERE Barcode like '$product[0]%' LIMIT 1");
if(mysql_num_rows($q) < 1)
{
echo "Barcode not found";
die (mysql_error());
}

$row=mysql_fetch_array($q);
$Barcode = $row['Barcode'];
$prod_name = $row['Product_Name'];
$qty = $row['Current_Stock'];
$price = $row['Selling_Price'];
$min= $row['Minimum_Stock'];
$total_price = $total_price + ($product[1] * $price);	
$getDate = new DateTime(null, new DateTimeZone('Asia/Singapore'));
            $Date = $getDate->format('Y-m-d');

$sql1 = mysql_query("INSERT INTO Transaction
VALUES('$Transaction_ID','1234','$prod_name','$Barcode','$product[1]','$Date')") or die (mysql_error());


//echo "$Transaction_ID','1234','$prod_name,'$Barcode','$product[1]','$Date <br/> ";



//Check that after sale, if current stock< minimum stock
// A restock will be done
$qty=$qty - $product[1];
if($qty< $min)
{

$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock,Date)
VALUES('$Barcode','$min', '$Date')") or die (mysql_error());

$qty=$qty +$min;
while($qty<$min)
{
$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock,Date)
VALUES('$Barcode','$min', '$Date')") or die (mysql_error());
$qty=$qty+$min;

}


}

$sql4 = mysql_query("UPDATE Inventory SET Current_Stock='$qty' WHERE Barcode=$Barcode") or die (mysql_error());
//echo "'$Barcode','$qty','$min' <br/> ";


}

return $total_price;
}

echo getitem();


?>
