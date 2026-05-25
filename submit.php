<?php include 'connect.php' ?>

<?php
session_start();
if(!isset($_SESSION['reg'])){
  header('Location:index.php');
  die;
}
$user = $_SESSION['reg'];




header("Cache-Control:no-cache,private,must-revalidate");
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// 	$selected_checkboxes = [];
// $selected_checkboxes = $_POST['selected_checkboxes'];
$selected_checkboxes = isset($_POST['selected_checkboxes']) 
    ? $_POST['selected_checkboxes'] 
    : array();

	if(!empty($_POST['selected_checkboxes'])){
		 $selectedCheckboxesString = implode(', ', $selected_checkboxes);
		
	}else{
	
		 $selectedCheckboxesString = '';
	}
 

  $name = $_POST["name"];
  $program = $_POST["program"];
  $mobile = $_POST["mobileNumber"];

  // 08-05-2026 - added location field handling

  $alt_mobile = mysqli_real_escape_string($conn, $_POST["altMobile"]);

  $course = $_POST["course"];


  // 14-05-2026 - added department field handling
  // $nickname = $_POST["nickname"];
  $nickname = isset($_POST["nickname"]) ? $_POST["nickname"] : '';
  $extra = $_POST["extraActivity"];
  // $exam = $_POST["examQualified"];
  $year = $_POST["graduationYear"];
  $dob = $_POST["dob"];
  $branch = $_POST["branch"];
  // 08-05-2026
  // $dept = mysqli_real_escape_string($conn, $_POST['dept'] ?? '');
$dept = mysqli_real_escape_string($conn, isset($_POST['dept']) ? $_POST['dept'] : '');

  $fav = $_POST["favouriteFood"];
  $company = $_POST["companyName"];

  // 08-05-2026 - added profile photo upload handling
  $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';


  // 07-04-2026 3

  // ===== ADD 1: fetch existing photo path =====
  $photo_path = '';
  $existing = mysqli_query($conn, "SELECT photo_path FROM slam_studetails WHERE user_id='$user'");
  if($existing && mysqli_num_rows($existing) > 0){
      $erow = mysqli_fetch_assoc($existing);
      $photo_path = $erow['photo_path']; // keep old photo if no new upload
  }


  // 07-04-2026 4

  // ===== ADD 2: handle new file upload if user chose one =====
  if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK){
      $upload_dir = 'uploads/profile_pics/';
      if(!is_dir($upload_dir)){
          mkdir($upload_dir, 0755, true);
      }
      $ext     = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
      // $allowed = ['jpg','jpeg','png','webp','gif'];
      // if(in_array($ext, $allowed)){

$allowed = ['jpg','jpeg','png','webp'];
$max_size = 200 * 1024; // 200 KB
if(in_array($ext, $allowed) && $_FILES['profile_photo']['size'] <= $max_size){

// // 14-05-2026 - added MIME type check and improved error handling

// $allowed_ext  = ['jpg','jpeg','png'];
// $allowed_mime = ['image/jpeg', 'image/png'];
// $max_size     = 200 * 1024; // 200 KB

// $file_mime = mime_content_type($_FILES['profile_photo']['tmp_name']);
// $file_size = $_FILES['profile_photo']['size'];

// if(!in_array($ext, $allowed_ext) || !in_array($file_mime, $allowed_mime)){
//     echo '<script>alert("❌ Only JPG and PNG files are allowed. Please upload a valid image."); window.history.back();</script>';
//     exit;
// }

// if($file_size > $max_size){
//     echo '<script>alert("❌ File size exceeds 200 KB. Please upload a smaller image."); window.history.back();</script>';
//     exit;
// }

// if(in_array($ext, $allowed_ext) && in_array($file_mime, $allowed_mime) && $file_size <= $max_size){






          // delete old photo file from server if exists
          if(!empty($photo_path) && file_exists($photo_path)){
              unlink($photo_path);
          }
          $filename   = $user . '_' . time() . '.' . $ext;
          $photo_path = $upload_dir . $filename;
          move_uploaded_file($_FILES['profile_photo']['tmp_name'], $photo_path);
      }
  }
  // ===== END ADD =====






$sql = "SELECT * FROM slam_studetails WHERE user_id = '$user'";
$result = mysqli_query($conn, $sql);

if ($result->num_rows == 0) {


  // 07-04-2026 5
  // $sql = "INSERT INTO slam_studetails ( user_id,name, mobile ,course, nickname,extra,exam,year,dob,branch,fav,company,program,photo_path, inserted_at, updated_at ) VALUES ('$user','$name', '$mobile','$course','$nickname','$extra','$selectedCheckboxesString','$year','$dob','$branch','$fav','$company','$program','$photo_path', NOW(), NOW())";
  // 08-05-2026 - added photo_path field to insert query

  // $sql = "INSERT INTO slam_studetails (user_id,name,mobile,course,nickname,extra,exam,year,dob,branch,fav,company,location,program,photo_path,inserted_at,updated_at) VALUES ('$user','$name','$mobile','$course','$nickname','$extra','$selectedCheckboxesString','$year','$dob','$branch','$fav','$company','$location','$program','$photo_path',NOW(),NOW())";
  $sql = "INSERT INTO slam_studetails (user_id,name,mobile,alt_mobile,course,nickname,extra,exam,year,dob,branch,dept,fav,company,location,program,photo_path,inserted_at,updated_at) VALUES ('$user','$name','$mobile','$alt_mobile','$course','$nickname','$extra','$selectedCheckboxesString','$year','$dob','$branch','$dept','$fav','$company','$location','$program','$photo_path',NOW(),NOW())";
	
  // mysqli_query($conn, $sql);






  // echo '<script type="text/JavaScript">';
  // echo "alert('Data inserted Successfully')";
  // echo '</script>';



  // mysqli_query($conn, $sql);
  // mysqli_query($conn, "UPDATE slam_studetails SET is_complete=1 WHERE user_id='$user'");

mysqli_query($conn, $sql);

// Only mark complete when all required fields are filled
$photo_filled   = !empty($photo_path);
$altmob_filled  = !empty($alt_mobile) && strlen($alt_mobile) == 10;
$dob_filled     = !empty($dob);
$year_filled    = !empty($year);

if ($photo_filled && $altmob_filled && $dob_filled && $year_filled) {
    mysqli_query($conn, "UPDATE slam_studetails SET is_complete=1 WHERE user_id='$user'");
} else {
    // Ensure is_complete is 0 if any required field is missing
    mysqli_query($conn, "UPDATE slam_studetails SET is_complete=0 WHERE user_id='$user'");
}

  echo '<script type="text/JavaScript">';
  echo "alert('Data inserted Successfully')";
  echo '</script>';
} else {

  // 07-04-2026 6
  // $sql = "UPDATE slam_studetails  SET name='$name', mobile= '$mobile',course='$course', nickname='$nickname',extra='$extra',exam='$selectedCheckboxesString',year='$year',dob='$dob',branch='$branch',fav='$fav',company='$company',program='$program', photo_path='$photo_path', updated_at=NOW() where user_id='$user'";
  // 08-05-2026 - added photo_path field to update query
  // $sql = "UPDATE slam_studetails SET name='$name',mobile='$mobile',course='$course',nickname='$nickname',extra='$extra',exam='$selectedCheckboxesString',year='$year',dob='$dob',branch='$branch',fav='$fav',company='$company',location='$location',program='$program',photo_path='$photo_path',updated_at=NOW() WHERE user_id='$user'";

  $sql = "UPDATE slam_studetails SET name='$name',mobile='$mobile',alt_mobile='$alt_mobile',course='$course',nickname='$nickname',extra='$extra',exam='$selectedCheckboxesString',year='$year',dob='$dob',branch='$branch',dept='$dept',fav='$fav',company='$company',location='$location',program='$program',photo_path='$photo_path',updated_at=NOW() WHERE user_id='$user'";
  
  //$conn->query($sql);
  // mysqli_query($conn, $sql);




  // echo '<script type="text/JavaScript">';
  // echo "alert('Data updated Successfully')";
  // echo '</script>';
// mysqli_query($conn, $sql);
//   mysqli_query($conn, "UPDATE slam_studetails SET is_complete=1 WHERE user_id='$user'");

//   echo '<script type="text/JavaScript">';
//   echo "alert('Data updated Successfully')";
//   echo '</script>';

// }


// mysqli_query($conn, $sql);
//   mysqli_query($conn, "UPDATE slam_studetails SET is_complete=1 WHERE user_id='$user'");

mysqli_query($conn, $sql);

// Only mark complete when all required fields are filled
$photo_filled   = !empty($photo_path);
$altmob_filled  = !empty($alt_mobile) && strlen($alt_mobile) == 10;
$dob_filled     = !empty($dob);
$year_filled    = !empty($year);

if ($photo_filled && $altmob_filled && $dob_filled && $year_filled) {
    mysqli_query($conn, "UPDATE slam_studetails SET is_complete=1 WHERE user_id='$user'");
} else {
    // Ensure is_complete is 0 if any required field is missing
    mysqli_query($conn, "UPDATE slam_studetails SET is_complete=0 WHERE user_id='$user'");
}

  echo '<script type="text/JavaScript">';
  echo "alert('Data updated Successfully')";
  echo '</script>';

}

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

// CHECK ALL 3 COMPLETE
$r1 = mysqli_query($conn, "SELECT is_complete FROM slambook_reflection WHERE user_id='$user' AND is_complete=1");
$r2 = mysqli_query($conn, "SELECT is_complete FROM slam_studetails WHERE user_id='$user' AND is_complete=1");
$r3 = mysqli_query($conn, "SELECT is_complete FROM exitfeedback_draft WHERE id='$user' AND is_complete=1");

if (mysqli_num_rows($r1) > 0 && mysqli_num_rows($r2) > 0 && mysqli_num_rows($r3) > 0) {
    echo '<script>window.location = "thankyou.php";</script>';
} else {
    echo '<script>setTimeout(function() { window.location = "checkdetails.php"; });</script>';
}
// error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
// echo '<script>setTimeout(function() { window.location = "checkdetails.php"; });</script>';
//header("Location: index.php");

}
?>