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
if (isset($_POST['Transaction_ID'])) {

    $Transaction_ID = mysql_real_escape_string($_POST['Transaction_ID']);
$Cashier_ID = mysql_real_escape_string($_POST['Cashier_ID']);
$Product_Name = mysql_real_escape_string($_POST['Product_Name']);
$Barcode = mysql_real_escape_string($_POST['Barcode']);
$Unit_Sold = mysql_real_escape_string($_POST['Unit_Sold']);
$Date = mysql_real_escape_string($_POST['Date']);

// See if that product name is an identical match to another product in the system

// Add this product into the database now
$sql = mysql_query("INSERT INTO Transaction
VALUES('$Transaction_ID','$Cashier_ID','$Product_Name','$Barcode','$Unit_Sold','$Date')") or die (mysql_error());
   // $pid = mysql_insert_id();
// Place image in the folder
//$newname = "$pid.jpg";
//move_uploaded_file( $_FILES['fileField']['tmp_name'], "../inventory_images/$newname");
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=transaction1.php">';
header("location: transaction1.php");
    exit();
}
?>
<?php
// Delete Item Question to Admin, and Delete Product if they choose
if (isset($_GET['deleteid'])) {
echo 'Do you really want to delete product with ID of ' . $_GET['deleteid'] . '? <a href="lcd.php?yesdelete=' . $_GET['deleteid'] . '">Yes</a> | <a href="lcd.php">No</a>';
exit();
}
if (isset($_GET['yesdelete'])) {
// remove item from system and delete its picture
// delete from database
$id_to_delete = $_GET['yesdelete'];
$sql = mysql_query("UPDATE LCD SET Barcode='' WHERE LCD_ID=$id_to_delete") or die (mysql_error());
// unlink the image from server
// Remove The Pic -------------------------------------------
    //$pictodelete = ("../inventory_images/$id_to_delete.jpg");
    //if (file_exists($pictodelete)) {
      // unlink($pictodelete);
    //}
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=lcd.php">';
header("location: lcd.php");
    exit();
}
?>
<?php
// This block grabs the whole list for viewing
$product_list = "";
$product_list1 = "";
$sql = mysql_query("SELECT * FROM LCD, Inventory Where LCD.Barcode = Inventory.Barcode ORDER BY LCD.LCD_ID ASC");
$productCount = mysql_num_rows($sql); // count the output amount
if ($productCount > 0)
{

while($row = mysql_fetch_array($sql)){


             $Transaction_ID = $row["LCD_ID"];
$Cashier_ID = $row["Barcode"];
$Price = $row["Selling_Price"];
$product_list .= "LCD ID: $Transaction_ID - Barcode: $Cashier_ID Price:$ $Price &nbsp; &nbsp; &nbsp; <a href='LCD_edit.php?pid=$Transaction_ID'>edit</a> &bull; <a href='lcd.php?deleteid=$Transaction_ID'>delete</a><br />";

     }


}
 else {
$product_list = "You have no restock transaction listed in your store yet";
}
$sql = mysql_query("SELECT DISTINCT LCD.LCD_ID, LCD.Barcode FROM LCD, Inventory Where LCD.Barcode = '0'");
$productCount = mysql_num_rows($sql); // count the output amount
if ($productCount > 0)
{

while($row = mysql_fetch_array($sql)){


             $Transaction_ID = $row["LCD_ID"];
$Cashier_ID = $row["Barcode"];
$Price = 0;
$product_list1 .= "LCD ID: $Transaction_ID - Barcode: $Cashier_ID Price:$ $Price &nbsp; &nbsp; &nbsp; <a href='LCD_edit.php?pid=$Transaction_ID'>edit</a> &bull; <a href='lcd.php?deleteid=$Transaction_ID'>delete</a><br />";

     }


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
data.addColumn('number', 'LCD ID');
data.addColumn('number', 'Barcode');
data.addColumn('string', 'Product Name');
data.addColumn('number', 'Selling Price');
data.addColumn('number', 'Current Stock');
data.addColumn('string', 'Edit/Delete');
data.addRows([


<?php

          $product_list = '';
		   $sql2 = mysql_query("SELECT DISTINCT LCD.LCD_ID, LCD.Barcode FROM LCD Where LCD.Barcode = '0' ");
          $productCount = mysql_num_rows($sql2); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql2)){
				 $LCD_ID = $row["LCD_ID"];
				 $Barcode = "";
                 $product_name = "";
				 $Price = "";
                 $replace[] = ",";
                 $product_name2 = str_replace("'" , "" , $product_name);
                 $current = "";
                 

                 $product_list .= '[' .
                 $LCD_ID . ',' .
                 $Barcode . ',' .
				 '\'' . $product_name2 . '\'' . ',' .
                 $Price . ',' .
                 $current . ',' .
                 "\"<a href='LCD_edit.php?pid=$LCD_ID'>edit</a> &bull; <a href='lcd.php?deleteid=$LCD_ID'>delete</a>\"
" . '],';
              }
			  
          }
          $sql = mysql_query("SELECT * FROM LCD, Inventory Where LCD.Barcode = Inventory.Barcode ORDER BY LCD.LCD_ID ASC ");
          $productCount = mysql_num_rows($sql); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql)){
				 $LCD_ID = $row["LCD_ID"];
				 $Barcode = $row["Barcode"];
                 $product_name = $row["Product_Name"];
				 $Price = $row["Selling_Price"];
                 $replace[] = ",";
                 $product_name2 = str_replace("'" , "" , $product_name);
                 $current = $row["Current_Stock"];
                 

                 $product_list .= '[' .
                 $LCD_ID . ',' .
                 $Barcode . ',' .
				 '\'' . $product_name2 . '\'' . ',' .
                 $Price . ',' .
                 $current . ',' .
                 "\"<a href='LCD_edit.php?pid=$LCD_ID'>edit</a> &bull; <a href='lcd.php?deleteid=$LCD_ID'>delete</a>\"
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
<div align="left" style="margin-left:24px;">
<h2>Mapping of LCD</h2>
<div id='barformat_div'></div>

</div>
</div>
<?php include_once("../template_footer.php");?>
</div>
</body>
</html>