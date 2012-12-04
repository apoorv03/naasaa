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
if(isset($_GET['Logbook']))
{
	header("Content-Type: application/force-download");
	header('Content-type: application/txt');
	header('Content-Disposition: attachment; filename="Logbook.txt"');
	$sql = mysql_query("SELECT * FROM Logbook");
	
    while($row=mysql_fetch_array($sql)){
		
		
		echo $row[0].":".$row[1].":".$row[2].":".$row[3].":".$row[4].":".$row[5]."\n";
	}
	exit(1);
	
}
if(isset($_GET['download']))
{
	header("Content-Type: application/force-download");
	header('Content-type: application/txt');
	header('Content-Disposition: attachment; filename="transaction.txt"');
	$sql = mysql_query("SELECT Transaction.Barcode, Inventory.Cost_Price, Inventory.Selling_Price, Transaction.Unit_Sold, Transaction.Date FROM Transaction, Inventory WHERE Inventory.Barcode= Transaction.Barcode");
	echo "123456 \n";
    while($row=mysql_fetch_array($sql)){
		$month = explode('-',$row[4]);
		
		echo $row[0].":".$row[1].":".$row[2].":".$row[3].":".$month[1]."\n";
	}
	$sql1 = mysql_query("SELECT Restock.Barcode, Inventory.Cost_Price, Inventory.Selling_Price, Restock.Stock, Restock.Date FROM Restock, Inventory WHERE Inventory.Barcode= Restock.Barcode");
	
    while($row=mysql_fetch_array($sql1)){
		$month = explode('-',$row[4]);
		
		echo $row[0].":".$row[1].":".$row[2].":".-$row[3].":".$month[1]."\n";
	}
	
	$upload_url = "http://ec2-23-20-78-149.compute-1.amazonaws.com/index.php";
	print_r(post_files($upload_url, "transaction.txt"));
}
if(isset($_GET['autoupload']))
{
	
    $ourFileName = "transaction.txt";
    $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
    //fwrite($fh, $stringData);
	$sql = mysql_query("SELECT Transaction.Barcode, Inventory.Cost_Price, Inventory.Selling_Price, Transaction.Unit_Sold, Transaction.Date FROM Transaction, Inventory WHERE Inventory.Barcode= Transaction.Barcode");
	// echo "123456 \n";
	fwrite($ourFileHandle, "123456 \n");
    while($row=mysql_fetch_array($sql)){
		$month = explode('-',$row[4]);
		
		// echo $row[0].":".$row[1].":".$row[2].":".$row[3].":".$month[1]."\n";
		$stringData = $row[0].":".$row[1].":".$row[2].":".$row[3].":".$month[1]."\n";
        fwrite($ourFileHandle, $stringData);
	}

	$sql1 = mysql_query("SELECT Restock.Barcode, Inventory.Cost_Price, Inventory.Selling_Price, Restock.Stock, Restock.Date FROM Restock, Inventory WHERE Inventory.Barcode= Restock.Barcode");
	
    while($row=mysql_fetch_array($sql1)){
		$month = explode('-',$row[4]);
		
		// echo $row[0].":".$row[1].":".$row[2].":".-$row[3].":".$month[1]."\n";
		$stringData = $row[0].":".$row[1].":".$row[2].":".-$row[3].":".$month[1]."\n";
        fwrite($ourFileHandle, $stringData);
	}
	fclose($ourFileHandle); 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://ec2-23-20-78-149.compute-1.amazonaws.com/HQadmin/upload_transaction.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
 
$data = array(
    "upload" => "@" . $ourFileName 
);
 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
}


function post_files($url,$file) {
    //Post 1-n files, each element of $files array assumed to be absolute
    // path to a file.  $files can be array (multiple) or string (one file).
    // Data will be posted in a series of POST vars named $file0, $file1...
    // $fileN
    $data=array();
    $data['uploadedfile']="@".$file;
    
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    $response = curl_exec($ch);
    return $response;
}
	
if(isset($_GET['Clear']))
{
		$sql = "TRUNCATE TABLE Restock";
		mysql_query($sql);
		$sql = "TRUNCATE TABLE Transaction";
		mysql_query($sql);
		$sql = "TRUNCATE TABLE Inventory";
		mysql_query($sql);
		$sql = "TRUNCATE TABLE LCD";
		mysql_query($sql);
		$sql = "TRUNCATE TABLE Logbook";
		mysql_query($sql);
}
if(isset($_GET['Demo']))
{
		header("Content-Type: application/force-download");
	header('Content-type: application/txt');
	header('Content-Disposition: attachment; filename="Inventory_HQ.txt"');
	$sql = mysql_query("SELECT * FROM Inventory");
	
    while($row=mysql_fetch_array($sql)){
		
		$row[5]=10000;
		
		
		echo $row[0].":".$row[1].":".$row[2].":".$row[3].":".$row[4].":".$row[5].":".$row[6].":".$row[7]."\n";
	}
}
if(isset($_GET['download']))
{
	$getDate = new DateTime(null, new DateTimeZone('Asia/Singapore'));
       $Today_Date = $getDate->format('Y-m-d');
$sql = mysql_query(" SELECT Inventory.Barcode, Inventory.Cost_Price, Inventory.Current_Stock, Inventory.Minimum_Stock FROM Inventory");
	
while($row=mysql_fetch_array($sql))
{
	
//This is active pricing	
if($row[2] < $row[3]* 1.1)
{
$row[1]=$row[1]*1.7;
$row[1]=number_format($row[1], 2, '.', '');
}
else
{
	if($row[2] < $row[3]* 1.2)
	{
	$row[1]=$row[1]*1.6;
	$row[1]=number_format($row[1], 2, '.', '');
	}
	else
	{
	$row[1]=$row[1]*1.5;
	$row[1]=number_format($row[1], 2, '.', '');
	}


}

$sql1 = mysql_query("UPDATE Inventory SET Selling_Price='$row[1]' WHERE Barcode='$row[0]'");
}

$sql5 = mysql_query("SELECT Inventory.Barcode, Inventory.Cost_Price, Inventory.Current_Stock, Inventory.Minimum_Stock,DATEDIFF (Expiry.Expiry_Date,'$Today_Date') as Duration,Expiry.Expiry_Date FROM Inventory,Expiry WHERE Inventory.Barcode = Expiry.Barcode");
	
	
	

while($row=mysql_fetch_array($sql5))
{
 
if($row['Duration'] < 10 )
{
	$row[1]=$row[1] * 1;
	$row[1]=number_format($row[1], 2, '.', '');
}
else if($row['Duration'] < 20 )
	{
	$row[1]=$row[1]*1.2;
	$row[1]=number_format($row[1], 2, '.', '');
	}
else if($row['Duration']<30)
		{
			$row[1]=$row[1]*1.3;
			$row[1]=number_format($row[1], 2, '.', '');
		}

$sql1 = mysql_query("UPDATE Inventory SET Selling_Price='$row[1]' WHERE Barcode='$row[0]'");


}




$row[0].":".$row[1].":".$row[2].":".$row[3].":".$row[4]."\n";

exit(1);
}
$sql = mysql_query("SELECT * FROM admin WHERE id='$managerID' AND username='$manager' AND password='$password' LIMIT 1"); // query the person
// ------- MAKE SURE PERSON EXISTS IN DATABASE ---------
$existCount = mysql_num_rows($sql); // count the row nums
if ($existCount == 0) { // evaluate the count
	 echo "Your login session data is not on record in the database.";
     exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Store Admin Area</title>

 <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
 
<!-- <link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />-->
</head>
<body>
 <script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<div align="center" id="mainWrapper">
  </div><?php include_once("../template_header.php");?>
  <div id="pageContent"><br />
    <div align="left" style="margin-left:24px;">
      <h2>Hello store manager, what would you like to do today?</h2>
      <p><a href="inventory_list.php">Manage Inventory</a><br />
      <a href="restock1.php">Manage Restock </a>
      <br />
      <a href="transaction1.php">Check Transaction </a>
      <br />
      <a href="upload.php">Updates </a>
         <br />
      <a href="search.php">Search </a>
      <br />
       <a href="LCD.php">Mapping of LCD </a>
       <br>
       <a href="analytics.php">Analytics</a>
       <br>
       <a href="expiry.php">List of Expiring Products</a>
      </p>
      
<form><input type="button" value="End Of Day" onclick="window.location='?autoupload';"></form> 
<form><input type="button" value="Clear " onclick="window.location='?Clear';"></form> 
<form><input type="button" value="Logbook " onclick="window.location='?Logbook';"></form> 


    </div>
    <br />
  <br />
  <br />
  </div>
 <?php include_once("../template_footer.php");?>
</div>
</body>
</html>