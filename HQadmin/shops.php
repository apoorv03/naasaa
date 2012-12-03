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
	echo 'Do you really want to delete product with ID of ' . $_GET['deleteid'] . '? <a href="shops.php?yesdelete=' . $_GET['deleteid'] . '">Yes</a> | <a href="shops.php">No</a>';
	exit();
}
if (isset($_GET['yesdelete'])) {
	// remove item from system and delete its picture
	// delete from database
	$id_to_delete = $_GET['yesdelete'];
	$sql = mysql_query("DELETE FROM shop WHERE shopID='$id_to_delete' LIMIT 1") or die (mysql_error());
	
//	ASK UNCLE SOO!
	// $sql2 = mysql_query("DELETE FROM Transactions WHERE shopID='$id_to_delete'") or die(mysql_error());
	
	
	
	// unlink the image from server
	// Remove The Pic -------------------------------------------
    //$pictodelete = ("../inventory_images/$id_to_delete.jpg");
    //if (file_exists($pictodelete)) {
      // 		    unlink($pictodelete);
    //}
  echo '<META HTTP-EQUIV="Refresh" Content="0; URL=shops.php">';

	header("location: shops.php"); 
    exit();
}
?>
<?php 
// Parse the form data and add inventory item to the system
if (isset($_POST['shopID'])) {
	
    $shopID = mysql_real_escape_string($_POST['shopID']);
	$name = mysql_real_escape_string($_POST['name']);
	$country = mysql_real_escape_string($_POST['country']);
	$address = mysql_real_escape_string($_POST['address']);
	// See if that product name is an identical match to another product in the system
	$sql = mysql_query("SELECT shopID FROM shop WHERE shopID ='$shopID' LIMIT 1");
	$productMatch = mysql_num_rows($sql); // count the output amount
    if ($productMatch > 0) {
		echo 'Sorry you tried to place a duplicate shop into the system, <a href="shops.php">click here</a>';
		exit();
	}
	// Add this product into the database now
	$sql = mysql_query("INSERT INTO shop
        VALUES('$shopID','$name','$country','$address')") or die (mysql_error());
   //  $pid = mysql_insert_id();
	// Place image in the folder 
	//$newname = "$pid.jpg";
	//move_uploaded_file( $_FILES['fileField']['tmp_name'], "../inventory_images/$newname");
	header("location: shops.php"); 
    exit();
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Shops List</title>
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
        data.addColumn('number', 'shopID');
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Country');
        data.addColumn('string', 'Address');
        data.addColumn('string', 'Edit/Delete');
      data.addRows([
        <?php

          $product_list = '';
          $sql = mysql_query("SELECT * FROM shop ");
          $productCount = mysql_num_rows($sql); // count the output amount
          if ($productCount > 0) {
            while($row = mysql_fetch_array($sql)){ 
                  $id = $row["shopID"];
                   $name = $row["name"];
                   $country = $row["country"];
                   $address = $row["address"];

                 $product_list .= '[' . 
                 $id . ',' . 
                 '\'' . $name . '\'' . ',' . 
                 '\'' . $country . '\'' . ',' . 
                 '\'' . $address . '\'' . ',' . 
                 "\"<a href='shops_edit.php?pid=$id'>edit</a>  <a href='shops.php?deleteid=$id'>delete</a>\"
                 " . '],'; 
              }
          }
          echo  $product_list ;
        ?>
      ]);

      // Instantiate and draw our table, passing in some options.
      var table = new google.visualization.Table(document.getElementById('barformat_div'));
      
  
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
    <div align="right" style="margin-right:32px;"><a href="shops.php#inventoryForm">+ Add New Shop Info</a></div>
<div align="left" style="margin-left:24px;">
      <h2>Shops list</h2>
     <div id='barformat_div'></div>
    </div>
    <hr />
    <a name="inventoryForm" id="inventoryForm"></a>
    <h3>
    &darr; Add New Shop Form &darr;
    </h3>
    <form action="shops.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Shop ID</td>
        <td width="80%"><label>
          <input name="shopID" type="text" id="shopID" size="10" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Shop Name</td>
        <td><label>
          <input name="name" type="text" id="name" size="100" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Country</td>
        <td><label>
          <input name="country" type="text" id="country" size="100" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Address</td>
        <td><label>
          <input name="address" type="text" id="address" size="100" />
        </label></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input type="submit" name="button" id="button" value="Add This Shop Now" />
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