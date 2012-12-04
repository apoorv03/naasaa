<?php
echo "in UploadTest.php file";
//set where you want to store files
//in this example we keep file in folder upload 
//$HTTP_POST_FILES['ufile']['name']; = upload file name
//for example upload file name cartoon.gif . $path will be upload/cartoon.gif

$url = "http://ec2-23-20-78-149.compute-1.amazonaws.com/uploads/";
 
$path= "/var/www/uploads/".$_FILES['file']['name'];
if($file != none)
{
	if(copy($_FILES['file']['tmp_name'], $path))
	{
		echo "Successful<BR/>";
		 
		echo "File Name :".$HTTP_POST_FILES['file']['name']."<BR/>"; 
		echo "File Size :".$HTTP_POST_FILES['file']['size']."<BR/>"; 
		echo "File Type :".$HTTP_POST_FILES['file']['type']."<BR/>";
	}
	else
	{
		echo "Error";
	}
}
?>