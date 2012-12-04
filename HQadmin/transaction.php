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
// Delete Item Question to Admin, and Delete Product if they choose
if (isset($_GET['deleteid'])) {
	echo 'Do you really want to delete product with ID of ' . $_GET['deleteid'] . '? <a href="inventory_list.php?yesdelete=' . $_GET['deleteid'] . '">Yes</a> | <a href="inventory_list.php">No</a>';
	exit();
}
if (isset($_GET['yesdelete'])) {
	// remove item from system and delete its picture
	// delete from database
	$id_to_delete = $_GET['yesdelete'];
	$sql = mysql_query("DELETE FROM Inventory WHERE Barcode='$id_to_delete' LIMIT 1") or die (mysql_error());
	// unlink the image from server
	// Remove The Pic -------------------------------------------
    //$pictodelete = ("../inventory_images/$id_to_delete.jpg");
    //if (file_exists($pictodelete)) {
      // 		    unlink($pictodelete);
    //}
	header("location: inventory_list.php"); 
    exit();
}
?>
<?php 
// Parse the form data and add inventory item to the system
if (isset($_POST['product_name'])) {
	
    $product_name = mysql_real_escape_string($_POST['product_name']);
	$price = mysql_real_escape_string($_POST['price']);
	$Barcode = mysql_real_escape_string($_POST['Barcode']);
	$Category = mysql_real_escape_string($_POST['Category']);
	$Manufacturer = mysql_real_escape_string($_POST['Manufacturer']);
	$stock = mysql_real_escape_string($_POST['stock']);
	$min = mysql_real_escape_string($_POST['min']);
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("SELECT Barcode FROM Inventory WHERE Barcode ='$Barcode' LIMIT 1");
	$productMatch = mysql_num_rows($sql); // count the output amount
    if ($productMatch > 0) {
		echo 'Sorry you tried to place a duplicate inventory into the system, <a href="inventory_list.php">click here</a>';
		exit();
	}
	// Add this product into the database now
	$sql = mysql_query("INSERT INTO Inventory
        VALUES('$product_name','$Category','$Manufacturer','$Barcode','$price','$stock','$min')") or die (mysql_error());
   //  $pid = mysql_insert_id();
	// Place image in the folder 
	//$newname = "$pid.jpg";
	//move_uploaded_file( $_FILES['fileField']['tmp_name'], "../inventory_images/$newname");
	header("location: inventory_list.php"); 
    exit();
}
?>
<?php 
// This block grabs the whole list for viewing
$product_list = "";
$sql = mysql_query("SELECT * FROM Transactions ");
$productCount = mysql_num_rows($sql); // count the output amount

if ($productCount > 0) {
	
	while($row = mysql_fetch_array($sql)){ 
             $shopID = $row["shopID"];
			 $Barcode = $row["Barcode"];
			 $Unit_Sold = $row["Quantity"];
			 $Month = $row["Month"];
			 $Cost = $row["Cost"];
			 $Price = $row["Price"];
			 
			 
			 $product_list .= "Shop ID: $shopID - Month: - $Month - 
			 $Barcode - $Price - $Unit_Sold  <br />";
    }
} else {
	$product_list = "You have no transaction listed in your store yet";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Transaction</title>
 <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1', {'packages':['table']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawTable);


      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawTable() {

      // Create the data table.
      var data = new google.visualization.DataTable();
        data.addColumn('number', 'shopID');
        data.addColumn('number', 'Month');
        data.addColumn('number', 'Barcode');
        data.addColumn('number', 'Units Sold');
        data.addColumn('number', 'Price');
      data.addRows([
        <?php

          $product_list = '';
          $sql = mysql_query("SELECT * FROM Transactions ");
          $productCount = mysql_num_rows($sql); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql)){ 
                 $barcode = $row["Barcode"];
                 $shopID = $row["shopID"];
                 $units = $row["Quantity"];
                 $month = $row["Month"];
                 $price = $row["Price"];

                 
                 

                 $product_list .= '[' . 
                 $shopID . ',' .
                 $month . ',' .
                 $barcode . ',' . 
                 $units . ',' . 
                 $price . ',' .
                 '],'; 
              }
          }
          echo  $product_list ;
        ?>
      ]);

      // Instantiate and draw our table, passing in some options.
      var table = new google.visualization.Table(document.getElementById('barformat_div'));
       // Apply formatter to sixth column
  
  table.draw(data, {allowHtml: true, showRowNumber: false});

       // set the width of the column with the title "Name" to 100px
     var title = "Name";
     var width = "200px";
     $('.google-visualization-table-th:contains(' + title + ')').css('width', width);
    }
    </script>
</head>

<body>
<div align="center" id="mainWrapper">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
  <?php include_once("../template_header.php");?>
  <div id="pageContent"><br />
    
<div align="left" style="margin-left:24px;">
      <h2>Transactions</h2>
       <div id='barformat_div'></div>
    </div>
    <hr />
    
  
    
    <br />
  <br />
  </div>
  <?php include_once("../template_footer.php");?>
</div>
</body>
</html>