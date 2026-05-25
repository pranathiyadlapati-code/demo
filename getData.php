
<?php include 'connect.php'?>

<?php
$query = "SELECT 
            COUNT(*) AS totalMembers,
            SUM(CASE 
                WHEN name !='' AND mobile !='' AND course !='' AND nickname !='' AND extra !='' AND exam !='' AND year !='0' AND dob !='0000-00-00'
 AND branch !='' AND fav !='' AND company !='' AND photo_path !=''
                THEN 1 
                ELSE 0 
            END) AS fullySubmittedCount,
            SUM(CASE 
            WHEN name ='' OR mobile =''  OR course ='' OR nickname ='' OR  extra ='' OR exam ='' OR year = '0' OR dob ='0000-00-00' OR branch ='' OR fav ='' OR company ='' OR photo_path =''
                THEN 1 
                ELSE 0 
            END) AS partiallySubmittedCount
          FROM slam_studetails";

$result = mysqli_query($conn, $query); 

if ($result) {
    $data = mysqli_fetch_assoc($result);
    header('Content-Type: application/json');
    echo json_encode($data);
} 


?>