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
echo 'Do you really want to delete product with ID of ' . $_GET['deleteid'] . '? <a href="restock1.php?yesdelete=' . $_GET['deleteid'] . '">Yes</a> | <a href="restock1.php">No</a>';
exit();
}
if (isset($_GET['yesdelete'])) {
// remove item from system and delete its picture
// delete from database
$id_to_delete = $_GET['yesdelete'];
$sql1 = mysql_query("SELECT Inventory.Current_Stock,Restock.Stock,Inventory.Barcode,Inventory.Minimum_Stock,Restock.Date FROM Inventory,Restock WHERE Inventory.Barcode = Restock.Barcode AND Restock_ID='$id_to_delete' ");

$row=mysql_fetch_array($sql1); // count the row nums

$row[0]=$row[0]-$row[1];
if( $row[0] < $row[3])
{
$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock,Date)
VALUES('$row[2]','$row[3]','$row[4]')") or die (mysql_error());

$row[0]=$row[0]+$row[3];

$sql3 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= $row[2]") or die (mysql_error());

}

$sql3 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= '$row[2]'") or die (mysql_error());

$sql = mysql_query("DELETE FROM Restock WHERE Restock_ID='$id_to_delete' LIMIT 1") or die (mysql_error());
// unlink the image from server
// Remove The Pic -------------------------------------------
    //$pictodelete = ("../inventory_images/$id_to_delete.jpg");
    //if (file_exists($pictodelete)) {
      // unlink($pictodelete);
    //}
header("location: restock1.php");
    exit();
}
?>
<?php
// Parse the form data and add inventory item to the system
if (isset($_POST['Barcode'])) {

    $Barcode = mysql_real_escape_string($_POST['Barcode']);
$Stock = mysql_real_escape_string($_POST['Stock']);
$Date = mysql_real_escape_string($_POST['Date']);

$sql1 = mysql_query("SELECT Current_Stock FROM Inventory WHERE Barcode = '$Barcode' ");

$row=mysql_fetch_array($sql1); // count the row nums

$row[0]=$row[0]+$Stock;


$sql3 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= $Barcode") or die (mysql_error());



// Add this product into the database now
$sql = mysql_query("INSERT INTO Restock ( Barcode, Stock, Date)
VALUES('$Barcode','$Stock','$Date')") or die (mysql_error());
   // $pid = mysql_insert_id();
// Place image in the folder
//$newname = "$pid.jpg";
//move_uploaded_file( $_FILES['fileField']['tmp_name'], "../inventory_images/$newname");
header("location: restock1.php");
    exit();
}
?>
<?php
// This block grabs the whole list for viewing
$product_list = "";
$sql = mysql_query("SELECT * FROM Restock ");
$productCount = mysql_num_rows($sql); // count the output amount
if ($productCount > 0) {
while($row = mysql_fetch_array($sql)){
             $id = $row["Restock_ID"];
$product_name = $row["Barcode"];
$category = $row["Stock"];
$Manufacturer = $row["Date"];


$product_list .= "Restock ID: $id - $product_name - $category - $Manufacturer &nbsp; &nbsp; &nbsp; <a href='restock_edit.php?pid=$id'>edit</a> &bull; <a href='restock1.php?deleteid=$id'>delete</a><br />";
    }
} else {
$product_list = "You have no restock information listed in your store yet";
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
data.addColumn('number', 'Restock_ID');
data.addColumn('number', 'Barcode');
data.addColumn('string', 'Name');
data.addColumn('number', 'Cost Price');
data.addColumn('number', 'Quantity');
data.addColumn('string', 'Date');

data.addRows([
<?php

          $product_list = '';
          $sql = mysql_query("SELECT * FROM Inventory,Restock WHERE Inventory.Barcode= Restock.Barcode ");
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
				 $Restock_ID = $row["Restock_ID"];
                 $Stock = $row["Stock"];
				 $Date = $row["Date"];

                 $product_list .= '[' .
                 $Restock_ID . ',' .
                 
                 $id . ',' .
				 '\'' . $product_name2 . '\'' . ',' .
				$Cprice . ',' .
                 $Stock . ',' .
                 "\"$Date\"
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
<div align="right" style="margin-right:32px;"><a href="restock1.php#inventoryForm">+ Add New Restock Item</a></div>
<div align="left" style="margin-left:24px;">
<h2>Restock list</h2>
<div id='barformat_div'></div>
</div>
<hr />
<a name="inventoryForm" id="inventoryForm"></a>
<h3>
&darr; Add Restock Form &darr;
</h3>
<form action="restock1.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
<table width="90%" border="0" cellspacing="0" cellpadding="6">
<tr>
<td width="20%" align="right">Barcode</td>
<td width="80%"><label>
<input name="Barcode" type="text" id="Barcode" size="12" />
</label></td>
</tr>
<tr>
<td align="right">Stock</td>
<td><label>
<input name="Stock" type="text" id="Stock" size="12" />
</label></td>
</tr>
<tr>
<td align="right">Date</td>
<td><label>
<input name="Date" type="text" id="Date" size="12" />
</label></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><label>
<input type="submit" name="button" id="button" value="Add This Item Now" />
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