<?php 


// Connect to the MySQL database  
include "storescripts/connect_to_mysql.php"; 
?>
<?php 
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
			 $min = $row["Minimum_Stock"];
			 $Selling_Price = $row["Selling_Price"];
			 
			 $product_list .= "Product ID: $id - <strong>$product_name</strong> - $$price - $category - $Manufacturer - $current -$min - $$Selling_Price &nbsp; &nbsp; &nbsp;  <a href='product.php?id=$id'>View Product Details</a> ; <br />";
			 
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
  <?php include_once("template_header.php");?>
  <div id="pageContent"><br />
    <div align="right" style="margin-right:32px;"></div>
<div align="left" style="margin-left:24px;">
      <h2>Inventory list</h2>
      <?php echo $product_list; ?>
    </div>
<br />
  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>