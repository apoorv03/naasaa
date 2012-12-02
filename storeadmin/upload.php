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
 <form action="upload_file.php" method="post"
enctype="multipart/form-data">
   <p>
  <label for="file">Inventory:</label>
  <input type="file" name="file" id="file" />
   </p>
   <p>
     
     <input type="submit" name="submit" value="Submit" />
     <br />
  </p>
 </form>
 <form action="upload_transaction.php" method="post"
enctype="multipart/form-data">
   <p>
  <label for="file">Transaction:</label>
  <input type="file" name="file" id="file" />
   </p>
   <p>
     
     <input type="submit" name="submit" value="Submit" />
   </p>
 </form>
 <form action="upload_lcd.php" method="post"
enctype="multipart/form-data">
   <p>
  <label for="file">LCD Mapping:</label>
  <input type="file" name="file" id="file" />
   </p>
   <p>
     
     <input type="submit" name="submit" value="Submit" />
   </p>
 </form>
 <form action="upload_hq.php" method="post"
enctype="multipart/form-data">
   <p>
  <label for="file">HQ new inventory:</label>
  <input type="file" name="file" id="file" />
   </p>
   <p>
     
     <input type="submit" name="submit" value="Submit" />
   </p>
 </form>
 </div>
 <div align="center" id="mainWrapper">
  <?php include_once("../template_footer.php");?>
  </div>


</body>
</html>