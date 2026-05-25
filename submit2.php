<?php include 'connect.php' ?>
<?php

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $roll = $_POST["roll"];
      $frn = $_POST["frndroll"];
    
    $op = $_POST["opi"];
	$opi  = mysqli_real_escape_string($conn,$op);
   
}
      $sql = "SELECT * FROM slambook_opnion WHERE user_id = '$roll' and frnd = '$frn' ";
      $result =mysqli_query($conn, $sql);
  
      if ($result->num_rows != 0) {
    
         $sql = "UPDATE slambook_opnion  SET frnd='$frn', opinion= '$opi', updated_at=NOW()  where user_id='$roll' and frnd = '$frn' ";
         mysqli_query($conn, $sql);
    
      }
      else 
      {

      
        $sql = "INSERT INTO slambook_opnion ( user_id,frnd,opinion, inserted_at, updated_at ) VALUES ('$roll','$frn', '$opi', NOW(), NOW())";
        mysqli_query($conn, $sql);
      

    }
      
      header("Location: review.php");

?>