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
 <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
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
        data.addColumn('number', 'Barcode');
        data.addColumn('string', 'Name');
     
        data.addColumn('string', 'Category');
        data.addColumn('string', 'Manufacturer');
        data.addColumn('number', 'quantity');
        data.addColumn('string', 'View Details');
      data.addRows([
        <?php

          $product_list = '';
          $sql = mysql_query("SELECT * FROM Inventory ");
          $productCount = mysql_num_rows($sql); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql)){ 
                 $id = $row["Barcode"];
                 $product_name = $row["Product_Name"];
                 $replace[] = ",";
                 $product_name2 = str_replace("'" , "" , $product_name);
                 $category = $row["Category"];
                 $Manufacturer = $row["Manufacturer"];
                 $price = $row["Cost_Price"];
                 $current = $row["Current_Stock"];
                 $sellingPrice = $row["Selling_Price"];

                 $product_list .= '[' . 
                 $id . ',' . 
                 '\''  . $product_name2 . '\'' . ',' . 
                 
                 '\'' . $category . '\'' . ',' . 
                 '\'' . $Manufacturer . '\'' . ',' . 
                 $current . ',' .
                 "\"<a href='product.php?id=$id'>View Product Details</a>\"
                 " . '],'; 
              }
          }
          echo  $product_list ;
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
<script src="bootstrap/js/bootstrap.min.js"></script>
  <?php include_once("template_header.php");?>
  <div id="pageContent"><br />
    <div align="right" style="margin-right:32px;"></div>
<div align="left" style="margin-left:24px;">
      <h2>Inventory list</h2>
      <div id='barformat_div'></div>
     
    </div>
<br />
  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>