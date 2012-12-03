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


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Search Result</title>
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<!--
<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />
-->

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
    <?php
    if (isset($_POST['button'])) {
    ?>
      // Load the Visualization API and the piechart package.
      google.load('visualization', '1', {'packages':['table']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawTable);
      
    <?php 
        }
    ?>

      // Callback that creates and populates a data table, 
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawTable() {

      // Create the data table.
      var data = new google.visualization.DataTable();
        data.addColumn('number', 'Barcode');
        data.addColumn('string', 'Name');
        data.addColumn('number', 'Price');
        data.addColumn('string', 'Category');
        data.addColumn('string', 'Manufacturer');
        data.addColumn('number', 'quantity');
      data.addRows([
        <?php
            $barcode_list="";
              $product_list="";
              $price_list="";
              $category_list="";
              $manufacturer_list="";
              $product_name = mysql_real_escape_string($_POST['product_name']);
              $Barcode = mysql_real_escape_string($_POST['Barcode']);
              $Category = mysql_real_escape_string($_POST['Category']);
              $Manufacturer = mysql_real_escape_string($_POST['Manufacturer']);
              
              $query = "SELECT * FROM Inventory WHERE Barcode > 0 ";
              if (strlen($product_name) > 0){
                $query .= " AND Product_Name like '%$product_name%' ";
              }
              if (strlen($Barcode) > 0){
                $query .= " AND Barcode = $Barcode ";
              }
              if (strlen($Category) > 0){
                $query .= " AND Category like '%$Category%' ";
              }
              if (strlen($Manufacturer) > 0){
                $query .= " AND Manufacturer like '%$Manufacturer%' ";
              }

              $sql = mysql_query($query);
              $productMatch = mysql_num_rows($sql); // count the output amount
              if ($productMatch > 0) {
                while($row = mysql_fetch_array($sql)){ 
                  $id1 = $row["Barcode"];
                   $product_name1 = $row["Product_Name"];
                   $product_name1 = str_replace("'", "", $product_name1);
                   $category1 = $row["Category"];
                   $Manufacturer1 = $row["Manufacturer"];
                   $price1 = $row["Cost_Price"];
                   $current1 = $row["Current_Stock"];
                   
                   $product_list .= '[' . 
                   $id1 . ',' . 
                  '\' <b> '  . $product_name1 . ' </b>\'' . ',' . 
                  $price1 . ',' . 
                  '\'' . $category1 . '\'' . ',' . 
                  '\'' . $Manufacturer1 . '\'' . ',' . 
                  $current1 . ',' .
                  '],'; 
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
<script src="../bootstrap/js/bootstrap.min.js"></script>
  <?php include_once("../template_header.php");?>
  <div id="pageContent"><br />
    
<div align="left" style="margin-left:24px;">
      <h2>Search list</h2>
      <div id='barformat_div'></div>
      <?php 
      // echo $barcode_list; 
      ?>
    </div>
    <hr />
    <a name="inventoryForm" id="inventoryForm"></a>
    <h3>
    &darr; Search Item Form &darr;
    </h3>
    <form action="search.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td align="right">Barcode</td>
        <td><label>
          <input name="Barcode" type="text" id="Barcode" size="12" />
        </label></td>
      </tr>
      <tr>
        <td width="20%" align="right">Product Name</td>
        <td width="80%"><label>
          <input name="product_name" type="text" id="product_name" size="64" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Category</td>
        <td><label>
          <input name="Category" type="text" id="Category" size="64" />
        </label></td>
      </tr>
        <tr>
        <td align="right">Manufacturer</td>
        <td><label>
          <input name="Manufacturer" type="text" id="Manufacturer" size="64" />
        </label></td>
      </tr>
  
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input type="submit" name="button" id="button" value="Search This Item Now" />
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