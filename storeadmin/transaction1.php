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
// Parse the form data and add inventory item to the system
if (isset($_POST['Transaction_ID']))
{

    $Transaction_ID = mysql_real_escape_string($_POST['Transaction_ID']);
$Cashier_ID = mysql_real_escape_string($_POST['Cashier_ID']);

$Barcode = mysql_real_escape_string($_POST['Barcode']);
$Unit_Sold = mysql_real_escape_string($_POST['Unit_Sold']);
$Date = mysql_real_escape_string($_POST['Date']);

// See if that product name is an identical match to another product in the system

$sql1 = mysql_query("SELECT Current_Stock, Minimum_Stock, Product_Name FROM Inventory WHERE Barcode = '$Barcode' ");
$row=mysql_fetch_array($sql1); // count the row nums

if (mysql_num_rows($sql1) < 1){
echo "barcode does not exist.";

echo '<META HTTP-EQUIV="Refresh" Content="0; URL=transaction1.php">';
// header("location: transaction1.php");
     exit(1);
}

$row[0]=$row[0]-$Unit_Sold;

if( $row[0]< $row[1])
{
$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock,Date)
VALUES('$Barcode','$row[1]', '$Date')") or die (mysql_error());

$row[0]=$row[0] +$row[1];

while($row[0]<$row[1])
{
$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock,Date)
VALUES('$Barcode','$row[1]', '$Date')") or die (mysql_error());
$row[0]=$row[0]+$row[1];

}

}


$sql4 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= '$Barcode'") or die (mysql_error());

// Add this product into the database now
$sql = mysql_query("INSERT INTO Transaction
VALUES('$Transaction_ID','$Cashier_ID','$row[2]','$Barcode','$Unit_Sold','$Date')") or die (mysql_error());
 
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=transaction1.php">';
header("location: transaction1.php");
    exit();

}



?>
<?php
// Delete Item Question to Admin, and Delete Product if they choose
if (isset($_GET['deleteid'])) {
echo 'Do you really want to delete product with ID of ' . $_GET['deleteid'] . '? <a href="transaction1.php?yesdelete=' . $_GET['deleteid'] . '">Yes</a> | <a href="transaction1.php">No</a>';
exit();
}
if (isset($_GET['yesdelete'])) {
// remove item from system and delete its picture
// delete from database
$id_to_delete = $_GET['yesdelete'];

$sql1 = mysql_query("SELECT Inventory.Current_Stock,SUM(Transaction.Unit_Sold),Inventory.Barcode FROM Inventory,Transaction WHERE Inventory.Barcode = Transaction.Barcode AND Transaction_ID='$id_to_delete' GROUP BY Inventory.Barcode ");
$productCount = mysql_num_rows($sql1); // count the output amount
if ($productCount > 0) {
while($row = mysql_fetch_array($sql1)){
// count the row nums

$row[0]=$row[0]+$row[1];


$sql3 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= '$row[2]'") or die (mysql_error());

$sql = mysql_query("DELETE FROM Transaction WHERE Transaction_ID='$id_to_delete' ") or die (mysql_error());
}
}





// unlink the image from server
// Remove The Pic -------------------------------------------
    //$pictodelete = ("../inventory_images/$id_to_delete.jpg");
    //if (file_exists($pictodelete)) {
      // unlink($pictodelete);
    //}
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=transaction1.php">';
header("location: transaction1.php");
    exit();
}
?>
<?php
// This block grabs the whole list for viewing
$product_list = "";
$sql = mysql_query("SELECT Transaction.Transaction_ID, Transaction.Cashier_ID, Inventory.Product_Name, Inventory.Barcode,Transaction.Unit_Sold,Transaction.Date FROM Transaction, Inventory WHERE Transaction.Barcode=Inventory.Barcode");
$productCount = mysql_num_rows($sql); // count the output amount

if ($productCount > 0) {

while($row = mysql_fetch_array($sql)){
             $Transaction_ID = $row[0];
$Cashier_ID = $row[1];
$Product_Name = $row[2];
$Barcode = $row[3];
$Unit_Sold = $row[4];
$Date = $row[5];


$product_list .= "Transaction ID: $Transaction_ID - $Cashier_ID - $Product_Name - $Barcode - $Unit_Sold - $Date &nbsp; &nbsp; &nbsp; <a href='transaction1_edit.php?pid=$Transaction_ID& BC=$Barcode'>edit</a> &bull;<a href='transaction1.php?deleteid=$Transaction_ID'>delete</a> &bull;<a href='transaction_total.php?pid=$Transaction_ID'>total</a> <br />";
    }
} else {
$product_list = "You have no transaction listed in your store yet";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Transaction List</title>
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<!--
<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />
-->
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
data.addColumn('number', 'Transaction ID');
data.addColumn('number', 'Cashier ID');
data.addColumn('number', 'Barcode');
data.addColumn('string', 'Name');
data.addColumn('number', 'Unit Sold');
data.addColumn('string', 'Date');
data.addColumn('string', 'Edit/Delete/Total');
data.addRows([
<?php

          $product_list = '';
          $sql = mysql_query("SELECT Transaction.Transaction_ID, Transaction.Cashier_ID, Inventory.Product_Name, Inventory.Barcode,Transaction.Unit_Sold,Transaction.Date FROM Transaction, Inventory WHERE Transaction.Barcode=Inventory.Barcode ");
          $productCount = mysql_num_rows($sql); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql)){
                 $Transaction_ID = $row[0];
				$Cashier_ID = $row[1];
				$Product_Name = $row[2];
				$replace[] = ",";
                $product_name2 = str_replace("'" , "" , $Product_Name);
				$Barcode = $row[3];
				$Unit_Sold = $row[4];
				$Date = $row[5];

                 $product_list .= '[' .
                 $Transaction_ID . ',' .
				  $Cashier_ID . ',' .
				  $Barcode . ',' .
                 '\'' . $product_name2 . '\'' . ',' .
                 $Unit_Sold. ',' .
				 '\'' . $Date . '\'' . ',' .
                 "\"<a href='transaction1_edit.php?pid=$Transaction_ID& BC=$Barcode'>edit</a> &bull;<a href='transaction1.php?deleteid=$Transaction_ID'>delete</a> &bull;<a href='transaction_total.php?pid=$Transaction_ID'>total</a>\"
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
<div align="right" style="margin-right:32px;"><a href="transaction1.php#inventoryForm">+ Add New Transaction</a></div>
<div align="left" style="margin-left:24px;">
<h2>Transaction list</h2>
<div id='barformat_div'></div>
</div>
<hr />
<a name="inventoryForm" id="inventoryForm"></a>
<h3>
&darr; Add New Transaction Form &darr;
</h3>
<form action="transaction1.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
<table width="90%" border="0" cellspacing="0" cellpadding="6">
<tr>
<td width="20%" align="right">Transaction ID</td>
<td width="80%"><label>
<input name="Transaction_ID" type="text" id="Transaction_ID" size="12" />
</label></td>
</tr>
<tr>
<td align="right">Cashier ID</td>
<td><label>
<input name="Cashier_ID" type="text" id="Cashier_ID" size="12" />
</label></td>
</tr>
<tr>
<td align="right">Barcode</td>
<td><label>
<input name="Barcode" type="text" id="Barcode" size="12" />
</label></td>
</tr>
<tr>
<td align="right">Unit Sold</td>
<td><label>
<input name="Unit_Sold" type="text" id="Unit_Sold" size="12" />
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
<input type="submit" name="button" id="button" value="Add This Transaction" />
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