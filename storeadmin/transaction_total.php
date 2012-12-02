<?php 

session_start();
if (!isset($_SESSION["manager"])) {
    header("location: admin_login.php"); 
    exit();
}
$list = "";
$total="";
// Be sure to check that this manager SESSION value is in fact in the database
$managerID = preg_replace('#[^0-9]#i', '', $_SESSION["id"]); // filter everything but numbers and letters
$manager = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["manager"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database  
include "../storescripts/connect_to_mysql.php"; 
$sql = mysql_query("SELECT * FROM admin WHERE id='$managerID' AND username='$manager' AND password='$password' LIMIT 1"); // query the person
// ------- MAKE SURE PERSON EXISTS IN DATABASE ---------
$existCount = mysql_num_rows($sql); // count the row nums
if ($existCount == 0) { // evaluate the count
	 echo "Your login session data is not on record in the database.";
     exit();
}
?>
<?php 
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php 
// Parse the form data and add inventory item to the system
if (isset($_POST['product_name'])) {
	
	$pid = mysql_real_escape_string($_POST['thisID']);
	$product_name = mysql_real_escape_string($_POST['product_name']);
	$price = mysql_real_escape_string($_POST['price']);
	$Category = mysql_real_escape_string($_POST['Category']);
	$Manufacturer = mysql_real_escape_string($_POST['Manufacturer']);
	$stock = mysql_real_escape_string($_POST['stock']);
	$min = mysql_real_escape_string($_POST['min']);
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("UPDATE Inventory SET Product_Name='$product_name', Cost_Price='$price', Category='$Category', Manufacturer='$Manufacturer', Current_Stock='$stock', Minimum_Stock='$min' WHERE Barcode='$pid'");
	//if ($_FILES['fileField']['tmp_name'] != "") {
	    // Place image in the folder 
	  //  $newname = "$pid.jpg";
	   // move_uploaded_file($_FILES['fileField']['tmp_name'], "../inventory_images/$newname");
	//}
	header("location: inventory_list.php"); 
    exit();
}
?>
<?php 
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['pid'])) {
	$targetID = $_GET['pid'];
    $sql = mysql_query("SELECT Transaction.Transaction_ID,Transaction.Cashier_ID,Transaction.Product_Name, Transaction.Barcode,Transaction.Unit_Sold,Transaction.Date , Inventory.Selling_Price FROM Transaction, Inventory WHERE Transaction.Transaction_ID='$targetID' and Transaction.Barcode = Inventory.Barcode ");
    $productCount = mysql_num_rows($sql); // count the output amount
	if ($productCount > 0) {
		$total=0;
	while($row = mysql_fetch_array($sql)){ 
             $Transaction_ID = $row["Transaction_ID"];
			 $Cashier_ID = $row["Cashier_ID"];
			 $Product_Name = $row["Product_Name"];
			 $Barcode = $row["Barcode"];
			 $Unit_Sold = $row["Unit_Sold"];
			 $Date = $row["Date"];
			 $price = $row["Selling_Price"];
			 $subtotal=$price*$Unit_Sold;
			 $total=$total+$subtotal;
			 
			 $list.= "Transaction ID : $Transaction_ID - <strong>$Product_Name</strong> - $Barcode - $$price - $Unit_Sold ---------$$subtotal  <br />";
    }
}
     else {
	    echo "Sorry dude that crap dont exist.";
		exit();
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inventory List</title>
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<!--
<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />
-->
</head>

<body>
<div align="center" id="mainWrapper">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
  <?php include_once("../template_header.php");?>
  <div id="pageContent"><br />
    
<div align="left" style="margin-left:24px;">
   <?php echo $list;?>
</div>
<div align="right" style="margin-left:24px;">
   <?php echo "Grand Total : $ $total";?>
</div>

 
  </div>
  <?php include_once("../template_footer.php");?>
</div>
</body>
</html>