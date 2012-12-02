<?php 
 include "../storescripts/connect_to_mysql.php"; 
$filename =$_FILES['datatxtfile'];

$file = fopen( $filename, "r" ) or die ("Error in opening File");
$filesize = filesize( $filename );
$filetext = fread( $file, $filesize );
fclose( $file );
$content= explode("\n", $filetext);
foreach($content as $line)
{
list($name, $category, $manuf, $barcode, $costprice, $currentStock, $minStock) = explode(":", $line);
$query = "insert into product values ('".$name."', '".$category."','".$manuf."','".$barcode."','".$costprice."','".$currentStock."','".$minStock."')";
$result = mysql_query($query);
} 

?>