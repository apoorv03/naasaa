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
// Delete Item Question to Admin, and Delete Product if they choose
if (isset($_GET['deleteid'])) {
echo 'Do you really want to delete product with ID of ' . $_GET['deleteid'] . '? <a href="inventory_list.php?yesdelete=' . $_GET['deleteid'] . '">Yes</a> | <a href="inventory_list.php">No</a>';
exit();
}
if (isset($_GET['yesdelete'])) {
// remove item from system and delete its picture
// delete from database
$id_to_delete = $_GET['yesdelete'];
$sql1 = mysql_query("UPDATE LCD SET Barcode='0' WHERE Barcode='$id_to_delete'");
$sql = mysql_query("DELETE FROM Inventory WHERE Barcode='$id_to_delete' LIMIT 1") or die (mysql_error());
// unlink the image from server
// Remove The Pic -------------------------------------------
    //$pictodelete = ("../inventory_images/$id_to_delete.jpg");
    //if (file_exists($pictodelete)) {
      // unlink($pictodelete);
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
$Selling_Price = mysql_real_escape_string($_POST['Selling_Price']);
// See if that product name is an identical match to another product in the system
$sql = mysql_query("SELECT Barcode FROM Inventory WHERE Barcode ='$Barcode' LIMIT 1");
$productMatch = mysql_num_rows($sql); // count the output amount
    if ($productMatch > 0) {
echo 'Sorry you tried to place a duplicate inventory into the system, <a href="inventory_list.php">click here</a>';
exit();
}
// Add this product into the database now
$sql = mysql_query("INSERT INTO Inventory
VALUES('$product_name','$Category','$Manufacturer','$Barcode','$price','$stock','$min', '$Selling_Price')") or die (mysql_error());
$sql1 = mysql_query("INSERT INTO LCD (Barcode)
VALUES('$Barcode')") or die (mysql_error());
$sql = mysql_query("INSERT INTO Logbook
VALUES('$product_name','$Category','$Manufacturer','$Barcode','$price','$stock','$min', '$Selling_Price')") or die (mysql_error());
   // $pid = mysql_insert_id();
// Place image in the folder
//$newname = "$pid.jpg";
//move_uploaded_file( $_FILES['fileField']['tmp_name'], "../inventory_images/$newname");
header("location: inventory_list.php");
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inventory List</title>
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<!--<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />-->

<!--
<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />
--><!--Load the AJAX API-->
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
data.addColumn('number', 'Barcode');
data.addColumn('string', 'Name');
data.addColumn('number', 'Cost Price');
data.addColumn('number', 'Selling Price');
data.addColumn('string', 'Category');
data.addColumn('string', 'Manufacturer');
data.addColumn('number', 'quantity');
data.addColumn('string', 'Expiry');
data.addRows([
<?php

          $product_list = '';
          $sql = mysql_query("SELECT * FROM Inventory,expiry WHERE Inventory.Barcode= expiry.Barcode ");
          $productCount = mysql_num_rows($sql); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql)){
                 $id = $row["Barcode"];
                 $product_name = $row["Product_Name"];
                 $replace[] = ",";
                 $product_name2 = str_replace("'" , "" , $product_name);
                 $category = $row["Category"];
                 $Manufacturer = $row["Manufacturer"];
                 $Cprice = $row["Cost_Price"];
				 $Sprice = $row["Selling_Price"];
                 $current = $row["Current_Stock"];
				 $expiry = $row["Expiry_Date"];

                 $product_list .= '[' .
                 $id . ',' .
                 '\'' . $product_name2 . '\'' . ',' .
                 $Cprice . ',' .
				$Sprice . ',' .
                 '\'' . $category . '\'' . ',' .
                 '\'' . $Manufacturer . '\'' . ',' .
                 $current . ',' .
                 "\"$expiry\"
" . '],';
              }
          }
          echo $product_list ;
        ?>
]);

// Instantiate and draw our table, passing in some options.
var table = new google.visualization.Table(document.getElementById('barformat_div'));
var formatter = new google.visualization.ArrowFormat({width: 30, base:2000});
formatter.format(data, 5); // Apply formatter to sixth column
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
<div align="right" style="margin-right:32px;"><a href="inventory_list.php#inventoryForm">+ Add New Inventory Item</a></div>
<div align="left" style="margin-left:24px;">
<h2>Expiry list</h2>
<div id='barformat_div'></div>
</div>
<hr />
<a name="inventoryForm" id="inventoryForm"></a>

<br />
<br />
</div>
<?php include_once("../template_footer.php");?>
</div>
</body>
</html>