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
<title>Analytics</title>
 <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart2);
      function drawChart2() {
        var data = google.visualization.arrayToDataTable([
          ['Day Of Month', 'Units Sold'],
          <?php 
          $sql = mysql_query("SELECT SUM(Unit_Sold) as s, Date 
          FROM Transaction
          Group by Date 
          Order by s desc ");
          $output = "";
          $productCount = mysql_num_rows($sql); // count the output amount
          while( $row = mysql_fetch_array($sql)){
                $units = $row["s"];
                $name = $row["Date"];
                $output .= '[' .
                '\''  . $name . '\'' . ',' . 
                $units . ',' .
                '],';
              }
            echo $output;
          ?>
        ]);

        var options = {
          title: 'Store Performance By Day For the Month',
          hAxis: {title: 'Day', titleTextStyle: {color: 'blue'}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('productsByDay'));
        chart.draw(data, options);
      }
    </script>

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Product', 'Total Sales'],
          <?php 
          $sql = mysql_query("SELECT SUM(Unit_Sold) as s, Product_Name 
          FROM Transaction
          Group by Product_Name 
          Order by s desc ");
          $output = "";
          $productCount = mysql_num_rows($sql); // count the output amount

          if ($productCount > 0) {
            $count = 0;
            $residual = 0;
            while( $row = mysql_fetch_array($sql)){
              $count++;
              if ($count < 8){
                $units = $row["s"];
                $name = $row["Product_Name"];
                if (strlen($name) < 2){
                  $name = "Ravintsara";
                }
                $output .= '[' .
                '\''  . $name . '\'' . ',' . 
                $units .
                '],';
              } else {
                $residual += $row["s"];
                if ($count == $productCount){
                  $output .= '[' .
                  '\' Others \'' . ',' . 
                  $units .
                  '],';
                }
              }
            }
            echo $output;
          } else{
            echo '["No Sales",     1]';  
          }
          ?>
        ]);

        var options = {
          title: 'Product Statistics'
        };
        var chart = new google.visualization.PieChart(document.getElementById('topProductChart'));
        chart.draw(data, options);
      }
    </script>


    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart3);
      function drawChart3() {
        var data = google.visualization.arrayToDataTable([
          
          <?php
          $sql2 = mysql_query(" SELECT a1.Manufacturer as man, SUM( a2.s ) AS finalUnits 
            FROM (
            SELECT Barcode, Manufacturer
            FROM Inventory
            ) AS a1, (
            SELECT SUM( Unit_Sold ) AS s, Barcode
            FROM Transaction
            GROUP BY Barcode
            ) AS a2
            WHERE a1.Barcode = a2.Barcode
            GROUP BY a1.Manufacturer
            ORDER BY finalUnits DESC");
          $output = "['Manufacturer', 'Total Sales'],";
          $productCount = mysql_num_rows($sql); // count the output amount

          if ($productCount > 0) {
            $count = 0;
            $residual = 0;
            while( $row = mysql_fetch_array($sql2)){
              $count++;
              if ($count < 9){
                $units = $row["finalUnits"];
                $name = $row["man"];
                $output .= '[' .
                '\''  . $name . '\'' . ',' . 
                $units .
                '],';
              } 
            }
            echo $output;
          } else{
            echo '["No Manufacturers",     1]';  
          }
          ?>
        ]);

        var options = {
          title: ' Manufacturer Statistics'
        };
        var chart = new google.visualization.PieChart(document.getElementById('topManufacturerChart'));
        chart.draw(data, options);
      }
    </script>
</html>
</head>


<body>
<div align="center" id="mainWrapper">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <?php include_once("../template_header.php");?>
  <div id="pageContent"><br>
  <div align="left" style="margin-left:24px;">
  </div>
  <div align="center" id="mainWrapper">
<h2> Dynamic Strategy Charts </h2>
  <div id="topProductChart" style="width: 900px; height: 500px;"></div>  
  <div id="topManufacturerChart" style="width: 900px; height: 500px;"></div>  
  <div id="productsByDay" style="width: 900px; height: 500px;"></div>
  <?php include_once("../template_footer.php");?>
</div>


</body>
</html>