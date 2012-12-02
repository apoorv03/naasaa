<?php 

session_start();
if (!isset($_SESSION["manager"])) {
    header("location: admin_login.php"); 
    exit();
}
// Be sure to check that this manager SESSION value is in fact in the database
$managerID = preg_replace('#[^0-9]#i', '', $_SESSION["id"]); // filter everything but numbers and letters
$manager = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["manager"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database  
include "../storescripts/connect_to_mysql_HQ.php"; 
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
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("UPDATE Inventory SET Product_Name='$product_name', Cost_Price='$price', Category='$Category', Manufacturer='$Manufacturer', Current_Stock='$stock' WHERE Barcode='$pid'");
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
    $sql = mysql_query("SELECT * FROM Inventory WHERE Barcode='$targetID' LIMIT 1");
    $productCount = mysql_num_rows($sql); // count the output amount
    if ($productCount > 0) {
	    while($row = mysql_fetch_array($sql)){ 
             
			 
			 
			 $product_name = $row["Product_Name"];
			 $category = $row["Category"];
			 $Manufacturer = $row["Manufacturer"];
			 $price = $row["Cost_Price"];
			 $current = $row["Current_Stock"];	 
			 
        }
    } else {
	    echo "Sorry dude that crap does not exist.";
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
      
</div>
    <hr />
    <a name="inventoryForm" id="inventoryForm"></a>
    <h3>
    &darr; Edit Inventory Item Form &darr;
    </h3>
    <form action="inventory_edit.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Product Name</td>
        <td width="80%"><label>
         <input name="product_name" type="text" id="product_name" size="64" value="<?php echo $product_name; ?>"/>
        </label></td>
      </tr>
      <tr>
        <td align="right">Barcode</td>
        <td><?php echo $targetID; ?>
        </td>
      </tr>
      <tr>
        <td align="right">Product Price</td>
        <td><label>
          $
          <input name="price" type="text" id="price" size="12"  value="<?php echo $price; ?>"/>
        </label></td>
      </tr>
      <tr>
        <td align="right">Category</td>
        <td><label>
          <input name="Category" type="text" id="Category" size="12" value="<?php echo $category; ?>"/>
        </label></td>
      </tr>
        <tr>
        <td align="right">Manufacturer</td>
        <td><label>
          <input name="Manufacturer" type="text" id="Manufacturer" size="12" value="<?php echo $Manufacturer; ?>" />
        </label></td>
      </tr>
        <tr>
        <td align="right">Current Stock</td>
        <td><label>
          <input name="stock" type="text" id="stock" size="12" value="<?php echo $current; ?>" />
        </label></td>
      </tr>
           
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input name="thisID" type="hidden" value="<?php echo $targetID; ?>" />
          <input type="submit" name="button" id="button" value="Make Changes" />
        </label></td>
      </tr>
    </table>
    </form>
    <br />
  <br />
  </div>
  <?php include_once("../template_footer.php");?>
</div>
</body>
</html>