<?php
include "../storescripts/connect_to_mysql_HQ.php"; 
mysql_query($sql);
mysql_query($sql2);
mysql_query($sql3);

$filename= mysql_real_escape_string($_FILES["file"]["tmp_name"]);

$lines = file($filename); // slurp file and split into an array by lines
$counter = 0;
foreach($lines as $line) {
	if ($counter<1){
		$shopID = $line;
	}
	else{
   	 	$parts = explode(':', $line); // decompose a line into individual sections
		$Barcode = mysql_real_escape_string(trim($parts[0]));
		$Cost_Price = mysql_real_escape_string(trim($parts[1]));
		$Selling_Price = mysql_real_escape_string(trim($parts[2]));
		$Quantity = mysql_real_escape_string(trim($parts[3]));
		$Month = mysql_real_escape_string(trim($parts[4]));

		/*$hash['name'] = "Vishesh";
		$hash['barcode'] = 'JSHKJS';
		$hash2['one']= $hash;
		$hash2['two'] = "Vish21";
		print_r($hash2['one']['name']);
		foreach($hash2 as $key=>$value){
			1st loop
			key = one
			$value['name'];
			$value[]
		}
		$hash2[] = "Vishesh";*/

		$revenue += $Selling_Price*$Quantity;
		$cost += $Cost_Price*$Quantity;
		$unitsSold += $Quantity;
		
		$sql2 = "INSERT INTO Transactions (shopID, Barcode, Cost, Price, Quantity, Month) 
		VALUES ('$shopID', '$Barcode', '$Cost_Price', '$Selling_Price', '$Quantity', '$Month')";
		
		mysql_query($sql2) or die(mysql_error());
		
		$sql3 = mysql_query("SELECT Inventory.Current_Stock FROM Inventory where Barcode = '$Barcode'");
		echo $sql3;
		$curUnits = 0;
		while($row=mysql_fetch_array($sql3)){
			$curUnits = $row[0];
		}
		$curUnits = $curUnits - $Quantity;
		$sql4 = mysql_query("UPDATE Inventory SET Current_Stock='$curUnits' WHERE Barcode='$Barcode'");
	}	
	$counter++;
}
$profits = $revenue - $cost;
$sql = "INSERT INTO shopPerformance (month, shopID, revenue, profits, cost, unitsSold) 
VALUES ('$Month', '$shopID', '$revenue', '$profits', '$cost', '$unitsSold')";
mysql_query($sql) or die(mysql_error());

echo '<META HTTP-EQUIV="Refresh" Content="0; URL=transaction.php">';    
    exit;    
?>