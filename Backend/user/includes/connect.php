<?php


// $con=new mysqli('localhost','root','','hostel','3307');
$con=new mysqli('localhost','root','','hostel');
if(!$con){
    die(mysqli_error($con));
}
?>