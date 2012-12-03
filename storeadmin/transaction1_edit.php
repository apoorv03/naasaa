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
if (isset($_POST['Barcode'])) {

$pid = mysql_real_escape_string($_POST['thisID']);
$BC1 = mysql_real_escape_string($_POST['thisBC']);
$Barcode = mysql_real_escape_string($_POST['Barcode']);
$Cashier_ID = mysql_real_escape_string($_POST['Cashier_ID']);
$Unit_Sold = mysql_real_escape_string($_POST['Unit_Sold']);
$Date = mysql_real_escape_string($_POST['Date']);

$sql1 = mysql_query("SELECT Inventory.Current_Stock,Transaction.Unit_Sold,Inventory.Barcode, Inventory.Minimum_Stock FROM Inventory,Transaction WHERE Inventory.Barcode = '$BC1' AND Transaction_ID='$pid' ");

$row=mysql_fetch_array($sql1); // count the row nums

$row[0]=$row[0]+$row[1];

while( $row[0] < $row[3])
{
$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock)
VALUES('$row[2]','$row[3]')") or die (mysql_error());

$row[0]=$row[0]+$row[3];



}


$sql3 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= '$row[2]'") or die (mysql_error());

$sql1 = mysql_query("SELECT Inventory.Current_Stock,Inventory.Barcode,Inventory.Minimum_Stock FROM Inventory,Restock WHERE Inventory.Barcode = '$Barcode' ");

$row=mysql_fetch_array($sql1); // count the row nums

$row[0]=$row[0]-$Unit_Sold;

if( $row[0] < $row[2])
{
$sql2 = mysql_query("INSERT INTO Restock (Barcode,Stock)
VALUES('$row[1]','$row[2]')") or die (mysql_error());

$row[0]=$row[0]+$row[2];

while($row[0]<$row[2])
				{
					$sql2 = mysql_query("INSERT INTO Restock 	(Barcode,Stock,Date)
				VALUES('$Barcode','$row[2]', '$Date')") or die (mysql_error());
					$row[0]=$row[0]+$row[2];
					
				}

}


$sql3 = mysql_query("UPDATE Inventory SET Current_Stock='$row[0]' WHERE Barcode= '$Barcode'") or die (mysql_error());


$sql = mysql_query("UPDATE Transaction SET Barcode='$Barcode', Cashier_ID='$Cashier_ID', Unit_Sold='$Unit_Sold', Date='$Date' WHERE Transaction_ID='$pid' and Barcode='$BC1'");

header("location: transaction1.php");
    exit();
}
?>
<?php
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['pid'])) {
$targetID = $_GET['pid'];
$BC = $_GET['BC'];
    $sql = mysql_query("SELECT * FROM Transaction WHERE Transaction_ID='$targetID' AND Barcode=$BC LIMIT 1");
    $productCount = mysql_num_rows($sql); // count the output amount
    if ($productCount > 0) {
while($row = mysql_fetch_array($sql)){
             

$Transaction_ID = $row["Transaction_ID"];
$Cashier_ID = $row["Cashier_ID"];
$Barcode = $row["Barcode"];
$Unit_Sold = $row["Unit_Sold"];
$Date = $row["Date"];

        }
    } else {
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
</div>
<hr />
<a name="inventoryForm" id="inventoryForm"></a>
<h3>
&darr; Edit Transaction Form &darr;
</h3>
<form action="transaction1_edit.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
<table width="90%" border="0" cellspacing="0" cellpadding="6">
<tr>
<td width="20%" align="right">Barcode</td>
<td width="80%"><label>
<input name="Barcode" type="text" id="Barcode" size="12" value="<?php echo $Barcode; ?>"/>
</label></td>
</tr>
<tr>
<td align="right">Transaction ID</td>
<td><?php echo $Transaction_ID; ?>
</td>
</tr>
<tr>
<td align="right">Cashier ID</td>
<td><label>
<input name="Cashier_ID" type="text" id="Cashier_ID" size="12" value="<?php echo $Cashier_ID; ?>"/>
</label></td>
</tr>
<tr>
<td align="right">Unit Sold</td>
<td><label>
<input name="Unit_Sold" type="text" id="Unit_Sold" size="12" value="<?php echo $Unit_Sold; ?>"/>
</label></td>
</tr>
<tr>
<td align="right">Date</td>
<td><label>
<input name="Date" type="text" id="Date" size="12" value="<?php echo $Date; ?>" />
</label></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><label>
<input name="thisID" type="hidden" value="<?php echo $targetID; ?>" />
<label>
<input name="thisBC" type="hidden" value="<?php echo $BC; ?>" />
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