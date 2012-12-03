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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Analytics</title>
 <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["geochart"]});
      google.setOnLoadCallback(drawRegionsMap);
      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          
          <?php
          $sql2 = mysql_query(
            "SELECT sum(a.revenue) as rev, b.country FROM 

              (select * from shopPerformance) a, 
              (select * from shop) b
              where a.shopID = b.shopID
              group by country
              order by rev desc");
          $output = "['Country', 'Revenue'],";
          $productCount = mysql_num_rows($sql); // count the output amount

          if ($productCount > 0) {
            
            while( $row = mysql_fetch_array($sql2)){
              
              
                $rev = $row["rev"];
                $country = $row["country"];
                $output .= '[' .
                '\''  . $country . '\'' . ',' . 
                $rev .
                '],';
               
            }
            echo $output;
          } else{
            echo '["No Shops in Countries",     1]';  
          }
          ?>
        ]);

        var options = {
          title: 'Highest Revenue Countries'
        };
        var chart = new google.visualization.GeoChart(document.getElementById('topCountriesChart'));
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
<h2> Dynamic Strategy Charts </h2><br><br>
<h3> Geo Distribution of Revenue </h3>
 <div id="topCountriesChart" style="width: 900px; height: 500px;"></div>

  <?php include_once("../template_footer.php");?>
</div>


</body>
</html>