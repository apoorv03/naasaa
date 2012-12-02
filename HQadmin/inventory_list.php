<?php 

session_start();
if (!isset($_SESSION["manager"])) {
    header("location: admin_login.php"); 
    exit();
}


include "../storescripts/connect_to_mysql_HQ.php";

if(isset($_GET['download'])){
	header("Content-Type: application/force-download");
	header('Content-type: application/txt');
	header('Content-Disposition: attachment; filename="ch.txt"');
	$sql = mysql_query("SELECT * FROM Inventory ");
    while($row=mysql_fetch_array($sql)){
		echo $row[0].":".$row[1].":".$row[2].":".$row[3].":".$row[4].":".$row[5].":0"."\n";
	}
	exit(1);
}



// Be sure to check that this manager SESSION value is in fact in the database
$managerID = preg_replace('#[^0-9]#i', '', $_SESSION["id"]); // filter everything but numbers and letters
$manager = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["manager"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database  
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
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("SELECT Barcode FROM Inventory WHERE Barcode ='$Barcode' LIMIT 1");
	$productMatch = mysql_num_rows($sql); // count the output amount
    if ($productMatch > 0) {
		echo 'Sorry you tried to place a duplicate inventory into the system, <a href="inventory_list.php">click here</a>';
		exit();
	}

	// Add this product into the database now
	$sql = mysql_query("INSERT INTO Inventory
        VALUES('$product_name','$Category','$Manufacturer','$Barcode','$price','$stock')") or die (mysql_error());
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
$sql = mysql_query("SELECT * FROM Inventory ");
$productCount = mysql_num_rows($sql); // count the output amount
if ($productCount > 0) {
	while($row = mysql_fetch_array($sql)){ 
             $id = $row["Barcode"];
			 $product_name = $row["Product_Name"];
			 $category = $row["Category"];
			 $Manufacturer = $row["Manufacturer"];
			 $price = $row["Cost_Price"];
			 $current = $row["Current_Stock"];
			 $product_list .= "Product ID: $id - <strong>$product_name</strong> - $$price - $category - $Manufacturer -$current &nbsp; &nbsp; &nbsp; <a href='inventory_edit.php?pid=$id'>edit</a> &bull; <a href='inventory_list.php?deleteid=$id'>delete</a><br />";
    }
} else {
	$product_list = "You have no products listed in your store yet";
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
    <div align="right" style="margin-right:32px;"><a href="inventory_list.php#inventoryForm">+ Add New Inventory Item</a></div>
<div align="left" style="margin-left:24px;">
      <h2>Inventory list</h2>
      <?php echo $product_list; ?>
    </div>
  	<form><input type="button" value="Download This Inventory" onclick="window.location='?download';"></form> 
	<?php
  function generateFile(){
		$filename = "inventory.txt";
    $product_list = "";
    $sql = mysql_query("SELECT * FROM Inventory ");
    $productCount = mysql_num_rows($sql); // count the output amount
    if ($productCount > 0) {
    while($row = mysql_fetch_array($sql)){ 
        $id = $row["Barcode"];
        $product_name = $row["Product_Name"];
        $category = $row["Category"];
        $Manufacturer = $row["Manufacturer"];
        $price = $row["Cost_Price"];
        $current = $row["Current_Stock"];
        $product_list .= "$product_name:$category:$Manufacturer:$id:$price:$current:0 \n";
    }
    } 
    $f = fopen($filename,'w');
    fwrite($f,$product_list);
    fclose($f);
    echo "File has been generated";
  	}
	?>

    <hr />
    <a name="inventoryForm" id="inventoryForm"></a>
    <h3>
    &darr; Add New Inventory Item Form &darr;
    </h3>
    <form action="inventory_list.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Product Name</td>
        <td width="80%"><label>
          <input name="product_name" type="text" id="product_name" size="64" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Barcode</td>
        <td><label>
          <input name="Barcode" type="text" id="Barcode" size="12" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Product Price</td>
        <td><label>
          $
          <input name="price" type="text" id="price" size="12" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Category</td>
        <td><label>
          <input name="Category" type="text" id="Category" size="12" />
        </label></td>
      </tr>
        <tr>
        <td align="right">Manufacturer</td>
        <td><label>
          <input name="Manufacturer" type="text" id="Manufacturer" size="12" />
        </label></td>
      </tr>
        <tr>
        <td align="right">Current Stock</td>
        <td><label>
          <input name="stock" type="text" id="stock" size="12" />
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