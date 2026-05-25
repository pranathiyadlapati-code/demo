
<?php include 'connect.php' ?>
<?php
session_start();
$regno = $_SESSION['reg'];
header("Cache-Control:no-cache,private,must-revalidate");
?>
<?php 

$selectedCheckboxes=[];
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
  
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $regno;
    $user= $regno;


    // 08-05-2026 - Welcome name: nickname from slambook_reg, fallback to name
$welcome_name = '';
$reg_sql = "SELECT nickname, name FROM slambook_reg WHERE regno = '$regno'";
$reg_result = mysqli_query($conn, $reg_sql);
if ($reg_result && mysqli_num_rows($reg_result) > 0) {
    $reg_row = mysqli_fetch_assoc($reg_result);
    if (!empty(trim($reg_row['nickname']))) {
        $welcome_name = trim($reg_row['nickname']);
    } else {
        $welcome_name = trim($reg_row['name']);
    }
}
if (empty($welcome_name)) {
    $welcome_name = $regno;
}
   
// 08-05-2026 2

// Fetch from  finalyearstudents table for this logged-in user
$finalyearstudents_data = [];
$finalyearstudents_result = mysqli_query($conn, "SELECT * FROM  finalyearstudents WHERE regno='$user_id'");
if ($finalyearstudents_result && mysqli_num_rows($finalyearstudents_result) > 0) {
    $finalyearstudents_data = mysqli_fetch_assoc($finalyearstudents_result);
}
// $ finalyearstudents_program       = $ finalyearstudents_data['program']        ?? '';
// $ finalyearstudents_department    = $ finalyearstudents_data['department']     ?? '';
// $ finalyearstudents_specialization= $ finalyearstudents_data['specialization'] ?? '';

$finalyearstudents_program        = isset($finalyearstudents_data['program'])        ? $finalyearstudents_data['program']        : '';
$finalyearstudents_department     = isset($finalyearstudents_data['department'])     ? $finalyearstudents_data['department']     : '';
$finalyearstudents_specialization = isset($finalyearstudents_data['specialization']) ? $finalyearstudents_data['specialization'] : '';

// 11-05-2026
$finalyearstudents_bm = isset($finalyearstudents_data['bm']) ? $finalyearstudents_data['bm'] : '';

// 08-05-2026
$finalyearstudents_name = isset($finalyearstudents_data['name']) ? $finalyearstudents_data['name'] : '';
$verified_mobile = isset($_SESSION['verified_mobile']) 
    ? $_SESSION['verified_mobile'] 
    : '';


    // SQL query to retrieve user information based on user ID
    $sql = "SELECT * FROM slam_studetails WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        // User information found
        $row = $result->fetch_assoc();
        
        $name = $row["name"];
        
        $mobile = $row["mobile"];
        $course = $row["course"];
        $nickname = $row["nickname"];
        $extra = $row["extra"];
        $exam = $row["exam"];
        $year = $row["year"];
        $dob = $row["dob"];
        $branch = $row["branch"];
// 08-05-2026 2
// $dept = mysqli_real_escape_string($conn, $_POST['dept'] ?? '');
$dept = mysqli_real_escape_string($conn, isset($_POST['dept']) ? $_POST['dept'] : '');

        $fav = $row["fav"];
// 08-05-2026 2
// $dept_val = $row['dept'] ?? '';
$dept_val = isset($row['dept']) ? $row['dept'] : '';

        $company = $row["company"];
        $program=$row["program"];

        // 07-04-2026 9
        //  $saved_photo = $row["photo_path"] ?? ''; // ← ADD THIS


// 15-04-2026
         $saved_photo = isset($row["photo_path"]) ? $row["photo_path"] : '';
        
    } else {
        // User information not found, set empty values
        
        $name = '';
        
        $mobile = '';
        $course = '';
        $nickname = '';
        $course = '';
        $extra = '';
        $exam = '';
        $year = '';
        $dob = '';
        $branch = '';
        $fav = '';
        $company = '';
        $program='';
    }

    // Close the database connection
    // $conn->close();
//}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vignan University::Vadlamudi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


    <!-- 30-03-2026 added styling -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">


    <style>
        :root {
            --gold: #D4A017;
            --navy: #1A2744;
            --glass-bg: rgba(255, 255, 255, 0.05);
        }

        body {
            background-color: var(--navy);
            background-image: radial-gradient(circle at 20% 30%, #1e2d50 0%, #10182b 100%);
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            min-height: 100vh;
            padding: 40px 0;
        }

        .container-fluid {
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 160, 23, 0.2);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        /* Styling the header */
        h2.bg-primary {
            background: transparent !important;
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            letter-spacing: 1px;
            color: var(--gold) !important;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        hr {
            border-top: 1px solid var(--gold);
            opacity: 0.3;
            margin-bottom: 40px;
        }

        .form-label {
            color: var(--gold);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        /* Styling Inputs and Selects */
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.07) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--gold) !important;
            box-shadow: 0 0 10px rgba(212, 160, 23, 0.3);
            color: #fff !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Checkbox/Switch styling */
        .form-check-input {
            background-color: var(--navy);
            border-color: var(--gold);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #e0e0e0;
        }

        /* Buttons styling */
        .btn-primary {
            background: linear-gradient(135deg, #D4A017 0%, #f5c842 100%) !important;
            border: none !important;
            color: var(--navy) !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 30px !important;
            border-radius: 10px !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 160, 23, 0.3) !important;
            background: linear-gradient(135deg, #f5c842 0%, #D4A017 100%) !important;
        }

        /* Error message */
        #mobileError {
            font-size: 0.75rem;
            font-weight: 400;
            display: block;
            margin-top: 5px;
            color: #ff6b6b !important;
        }

        /* Readonly input (Reg No) */
        input[readonly] {
            background-color: rgba(0, 0, 0, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #8892A4 !important;
        }

        /* This targets the actual dropdown list items */
select option {
    background-color: var(--navy) !important;
    color: #ffffff !important;
}

/* Specific fix for some browsers/mobile devices */
.form-select option {
    background-color: #1A2744; /* Solid navy color */
    color: white;
}

/* This ensures the dropdown stays dark when active */
.form-select:focus {
    color: #ffffff;
}


/* 16-04-2026
 */
/* Mobile View */
@media (max-width: 768px) {
    
    .button-row {
        flex-direction: column;   /* stack buttons */
        gap: 15px;                /* spacing */
    }

    .btn-wrap {
        width: 100%;
    }

    .btn-wrap .btn {
        width: 100%;              /* full width buttons */
        padding: 12px;
        font-size: 16px;
    }
}
    </style>


    <script>
//       var subjectObject = {
//           "UG": {
//             "B.TECH": [ "ACSE","AGRI","AIML","AME","BIOTECH","BME","Chem.Eng", "CIVIL", "CS", "CSBS", "CSE","DS","ECE","EEE","FT","IT","ME","RE","TT" ],
//             "B.SC": ["Stats",  "Maths", "Computers"],
//             "B.PHARMA": ["B.PHARMA"],
//             "BCA": ["BCA"],
// 			"BBA": ["BBA"],
// 			"BBALLB": ["BBALLB"],
//             "BALLB": ["BALLB"],
//           },
//           "PG": {
//             "M.TECH": ["AI","BIGDATA","BIOTECH","CSE","Embedded Systems","Farming Machinery",
//  "Food Processing","Machine Design","Power Electronics & Drives","Structural Engineering","VLSI Design"],
//             "M.SC": ["PHYSICS", "CHEMISTRY","ORGANIC CHEMISTRY" ,"MATHEMATICS"],
//             "MCA": ["MCA"],
// 			"MBA": ["MBA"],
// 			 "MA Eng": ["MA Eng"]
//           },
//           "PHD": {
//             "PHD": ["PHD"]
//           },
//           "DIPLOMA": {
//             "DIPLOMA": ["CSE","ECE","EEE","MECH"]
//           }
//         }
//         window.onload = function() {
//           var subjectSel = document.getElementById("subject");
//           var topicSel = document.getElementById("topic");
//           var chapterSel = document.getElementById("chapter");
//           for (var x in subjectObject) {
//             subjectSel.options[subjectSel.options.length] = new Option(x, x);
//           }
//           subjectSel.onchange = function() {
//             //empty Chapters- and Topics- dropdowns
//             chapterSel.length = 1;
//             topicSel.length = 1;

//             // new
// // 04-04-2026
// // 2. NEW: Force the text display to reset to the default "placeholder"
//     topicSel.innerHTML = '<option value="">Select Course</option>';
//     chapterSel.innerHTML = '<option value="">Select Department</option>';


//             //display correct values
//             for (var y in subjectObject[this.value]) {
//               topicSel.options[topicSel.options.length] = new Option(y, y);
//             }
//           }
//           topicSel.onchange = function() {
//             //empty Chapters dropdown
//             chapterSel.length = 1;

//             // new
// // 04-04-2026
// // 2. NEW: Force the text display to reset to the default "placeholder"
//     chapterSel.innerHTML = '<option value="">Select Department</option>';



//             //display correct values
//             var z = subjectObject[subjectSel.value][this.value];
//             for (var i = 0; i < z.length; i++) {
//               chapterSel.options[chapterSel.options.length] = new Option(z[i], z[i]);
//             }
//           }
//         }

// 08-05-2026 2


  </script>
  </head>
  <body>
    <?php 
    
    $selected_checkboxes = [];
    
    
    
     $sql = "SELECT exam FROM slam_studetails WHERE user_id = '$user'";
     $result = mysqli_query($conn, $sql);
 
     $selectedCheckboxes = [];
     if ($result && mysqli_num_rows($result) > 0) {

    //  07-04-2026 8
        //  $row = mysqli_fetch_assoc($result);
        //  $selectedCheckboxes = explode(', ', $row['exam']);
        $examRow = mysqli_fetch_assoc($result);              // ← was $row, now $examRow
    $selectedCheckboxes = explode(', ', $examRow['exam']); // ← was $row['exam']
     }
    ?>

  <!-- 07-04-2026 1 -->
  <form action="submit.php" method="post" enctype="multipart/form-data">
     <div class="container-fluid">
      
        <div class="row">

            <div class="col-md-12">
                <div class="mb-3 text-center">
                    <h2 class="bg-primary" style="color:white;">Student Details</h2>
                  </div>
            </div>  
        </div>
        <hr>
        
        <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Registration no</label>
                    <input type="text" class="form-control" id="user" name="user" value="<?php echo $user_id; ?>" readonly>
                  </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label"><span style="color: #ff6b6b;">*</span>Name</label>
                    <!-- <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required > -->
<!-- 08-05-2026 -->
<input type="text" class="form-control" id="name" name="name"
           value="<?php echo htmlspecialchars($finalyearstudents_name ?: $name); ?>"
           readonly>
    <small style="color:#8892A4; font-size:.72rem;">
        Auto-filled from your university record
    </small>



                  </div>
            </div>
            <div class="col-md-5">
               <div class="mb-3">
                 <!-- <label class="form-label">Profile Photo</label> -->

                 <label class="form-label"><span style="color:#ff6b6b;">*</span> Profile Photo <span style="font-size:.7rem; color:#ff6b6b; font-weight:400;">(Required)</span></label>
                 <div style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">

                <!-- preview box -->
                  <div id="imgPreviewWrap" style="
                    width:120px; height:120px;
                    border-radius:14px;
                    border:2px solid rgba(212,160,23,0.4);
                    overflow:hidden;
                    background:rgba(255,255,255,0.05);
                    display:flex; align-items:center; justify-content:center;
                    flex-shrink:0;">
                    <?php  if(!empty($saved_photo)):?>
                        <img id="imgPreview"
                             src="<?php echo htmlspecialchars($saved_photo); ?>"
                             style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <img id="imgPreview" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                        <span id="imgPlaceholder" style="font-size:2.2rem;">📷</span>
                    <?php endif; ?>
                </div>

                <!-- file input styled as button -->
                <div>
                    <label for="profile_photo" style="
                        display:inline-block;
                        padding:10px 22px;
                        border-radius:10px;
                        border:1px solid rgba(212,160,23,0.5);
                        color:#D4A017;
                        font-size:.85rem;
                        font-weight:600;
                        cursor:pointer;
                        background:rgba(212,160,23,0.07);
                        transition:all .2s;">
                        📁 Choose Photo
                    </label>
                    <input type="file" id="profile_photo" name="profile_photo"
                          accept="image/jpeg,image/jpg,image/png"
                           style="display:none;">
                    <!-- <p style="font-size:.72rem;color:#8892A4;margin-top:8px;">
                        JPG, PNG, WEBP — max 2 MB
                    </p> -->
<p id="photoError" style="color:#ff6b6b; font-size:.78rem; margin-top:6px; font-weight:600;"></p>
<p style="font-size:.72rem;color:#8892A4;margin-top:8px;">
    JPG, JPEG, PNG only — max 200 KB
</p>

                </div>
            </div>
        </div>
    </div>
            <!-- add /div -->
        </div>




          <!-- added row justtify-content-evenly  -->
        <!-- <div class="row justify-content-evenly">
            
            <div class="col-md-5">
           
            </div>
           
        </div> -->

        
        <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label"><span style="color: #ff6b6b;">*</span>Mobile</label>
                    
                    <!-- 08-05-2026 -->
                    <input type="tel" class="form-control" id="mobileNumber" name="mobileNumber" value="<?php echo htmlspecialchars($verified_mobile ?: $mobile); ?>" required readonly>
                    <span id="mobileError" style="color: red;"></span>

<script>
  
  document.getElementById("mobileNumber").addEventListener("input", function () {
    var mobileNumber = this.value;
    var errorSpan = document.getElementById("mobileError");
    var a=mobileNumber.substring(0,1);
   //alert(a);
    if (mobileNumber.length != 10 || a<6) {
      errorSpan.textContent = "write correct format and length should not exceed 10 characters.";
    } else {
      errorSpan.textContent = ""; 
    }
  });
</script>
                  </div>
            </div>

            <div class="col-md-5">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="exampleFormControlInput1" class="form-label"><span style="color: #ff6b6b;">*</span>DOB</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $dob; ?>" required>
                       </div>
                       <div class="col-md-5">

                       <!-- 08-05-2026 2  -->
                <!-- <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label"><span style="color: #ff6b6b;">*</span>Year</label> -->

                    <!-- 16-04-2026 -->
                    <!-- <select class="form-select" aria-label="Default select example" name="graduationYear" id="graduationYears" required>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                       
						 <option value="2026">2026</option>
                        
                     </select> -->


                    <!-- 18-04-2026 -->
                <!-- <input type="text" class="form-control" name="graduationYear" id="graduationYears" value="2026" readonly> -->
                 <?php
// $currentYear = date("Y");
// $prevYear = $currentYear - 1;
// $nextYear = $currentYear + 1;

// $option1 = $prevYear . "-" . $currentYear; // previous-current
// $option2 = $currentYear . "-" . $nextYear; // current-next
?>

<!-- <select class="form-select" name="graduationYear" id="graduationYears" required>
    <option value="">Select Year</option>

    <option value="<?php echo $option1; ?>" 
        <?php if($year == $option1) echo "selected"; ?>>
        <?php echo $option1; ?>
    </option>

    <option value="<?php echo $option2; ?>" 
        <?php if($year == $option2) echo "selected"; ?>>
        <?php echo $option2; ?> -->
    <!-- </option>
</select>



                
                  </div> -->
            </div>
                    </div>
                  </div>
            </div>
        </div>



                <div class="row justify-content-evenly">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="altMobile" class="form-label"><span style="color: #ff6b6b;">*</span>Alternate / Parent's Number</label>
                                    <input type="tel" class="form-control" id="altMobile" name="altMobile"
                                        value="<?php echo htmlspecialchars(isset($row['alt_mobile']) ? $row['alt_mobile'] : ''); ?>"
                                        placeholder="10-digit alternate number" required maxlength="10">
                                    <span id="altMobileError" style="color: red;"></span>
                <script>
                document.getElementById("altMobile").addEventListener("input", function () {
                    var val = this.value;
                    var err = document.getElementById("altMobileError");
                    if (val.length != 10 || val.charAt(0) < '6') {
                    err.textContent = "Enter a valid 10-digit mobile number.";
                    } else {
                    err.textContent = "";
                    }
                });
                </script>
                                </div>
                            </div>
                            <div class="col-md-5"></div>
                        </div>




        
        <hr style="border-top: 2px solid rgba(212, 160, 23, 0.5); margin: 30px 0;">
        
        <!-- <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nickname</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" value="<?php echo $nickname; ?>">
                  </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Favourite Food</label>
                    <input type="text" class="form-control" id="favouriteFood" name="favouriteFood" value="<?php echo $fav; ?>">
                  </div>
            </div>
        </div>
        <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Extra Activity</label>
                    <input type="text" class="form-control" id="extraActivity" name="extraActivity" value="<?php echo $extra; ?>">
                  </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Got Placed in Company</label>
                <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo $company; ?>">  
            </div>

08-05-2026 2

        </div> -->
        <!-- <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label" name="program"  id="Program"><span style="color: #ff6b6b;">*</span>Program</label>
                    <select name="program" id="subject" class="form-select" aria-label="Default select example" required>
                        <option value="<?php echo $program; ?>" ><?php echo $program; ?></option> 
                    </select>
                </div>
            </div> -->
            <!-- <div class="col-md-5"> -->
                    <!-- <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label" >Course</label>
                        <select name="course" id="topic" class="form-select"  aria-label="Default select example" >
                            <option   ><?php echo $course; ?></option> 
                        </select>
                    </div> -->
                    <!-- <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label" ><span style="color: #ff6b6b;">*</span>Department</label>
                        <select name="branch" id="chapter" class="form-select"  aria-label="Default select example" required>
                            <option value="<?php echo $branch; ?>"><?php echo $branch; ?></option> 
                        </select>
                    </div>
                
            </div> -->
            <!-- <div class="col-md-5"> -->
                
                    <!-- <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label" >Department</label>
                        <select name="branch" id="chapter" class="form-select"  aria-label="Default select example" >
                            <option value="<?php echo $branch; ?>"><?php echo $branch; ?></option> 
                        </select>
                    </div> -->
                            <!-- <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label" ><span style="color: #ff6b6b;">*</span>Course</label>
                        <select name="course" id="topic" class="form-select"  aria-label="Default select example" required>
                            <option   ><?php echo $course; ?></option> 
                        </select>
                    </div>
                
            </div> -->
            <!-- <div class="col-md-5">
                <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label" >Exam Qualified:</label><br>
                        <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="TOEFL" name="selected_checkboxes[]" value="TOEFL" <?php if(in_array('TOEFL',$selectedCheckboxes)) echo "checked";?>>
                        <label for="TOEFL" class="form-check-label"> TOEFL</label><br>
                        <input type="checkbox" class="form-check-input" id="GRE" name="selected_checkboxes[]" value="GRE" <?php if(in_array('GRE',$selectedCheckboxes)) echo "checked";?>>
                        <label for="GRE"class="form-check-label">GRE</label><br>
                        <input type="checkbox" class="form-check-input" id="GATE" name="selected_checkboxes[]" value="GATE" <?php if(in_array('GATE',$selectedCheckboxes)) echo "checked";?>>
                        <label for="GATE" class="form-check-label">GATE</label><br>
                        <input type="checkbox" class="form-check-input" id="ILETS" name="selected_checkboxes[]" value="ILETS" <?php if(in_array('ILETS',$selectedCheckboxes)) echo "checked";?>>
                        <label for="GATE" class="form-check-label">ILETS</label><br>
						<input type="checkbox" class="form-check-input" id="ECET" name="selected_checkboxes[]" value="ECET" <?php if(in_array('ECET',$selectedCheckboxes)) echo "checked";?>>
                        <label for="ECET" class="form-check-label">ECET</label><br>
                        </div>
                      </div>
                </div>
            </div> -->
<div class="row justify-content-evenly">

    <!-- NEW: PHD/PG/UG/Diploma dropdown (maps to program column) -->
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">
                <span style="color:#ff6b6b;">*</span> PHD / PG / UG / Diploma
            </label>
            <!-- <select class="form-select" name="program" id="levelSelect" required>
                <option value="">Select Level</option>
                <option value="UG"      <?php if($program == 'UG')      echo 'selected'; ?>>UG</option>
                <option value="PG"      <?php if($program == 'PG')      echo 'selected'; ?>>PG</option>
                <option value="PHD"     <?php if($program == 'PHD')     echo 'selected'; ?>>PHD</option>
                <option value="Diploma" <?php if($program == 'Diploma') echo 'selected'; ?>>Diploma</option>
            </select> -->

<!-- 11-05-2026 -->
 <input type="text" class="form-control" name="program" id="levelSelect"
       value="<?php echo htmlspecialchars($finalyearstudents_bm ?: $program); ?>"
       readonly>
<small style="color:#8892A4; font-size:.72rem;">
    Auto-filled from your university record
</small>

        </div>
    </div>

    <!-- NEW: Program (read-only, from  finalyearstudents table → stored in course column) -->
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Program</label>
            <input type="text" class="form-control" name="course"
                   value="<?php echo htmlspecialchars($finalyearstudents_program ?: $course); ?>"
                   readonly>
            <small style="color:#8892A4; font-size:.72rem;">
                Auto-filled from your university record
            </small>
        </div>
    </div>

    <!-- NEW: Department (read-only, from  finalyearstudents table → stored in new dept column) -->
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Department</label>
            <!-- <input type="text" class="form-control" name="dept"
                 value="<?php echo htmlspecialchars($finalyearstudents_department ? $finalyearstudents_department : (isset($data['dept']) ? $data['dept'] : '')); ?>"
                   readonly> -->

<!-- 08-05-2026 -->

<input type="text" class="form-control" name="dept"

                  value="<?php echo htmlspecialchars($finalyearstudents_department ? $finalyearstudents_department : (isset($data['dept']) ? $data['dept'] : '')); ?>"
                   readonly>
    

            <small style="color:#8892A4; font-size:.72rem;">
                Auto-filled from your university record
            </small>
        </div>
    </div>

    <!-- NEW: Branch/Specialization (read-only, from  finalyearstudents table → stored in branch column) -->
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Branch / Specialization</label>
            <input type="text" class="form-control" name="branch"
                   value="<?php echo htmlspecialchars($finalyearstudents_specialization ?: $branch); ?>"
                   readonly>
            <small style="color:#8892A4; font-size:.72rem;">
                Auto-filled from your university record
            </small>
        </div>
    </div>


    <!-- 08-05-2026 year position -->
    <div class="col-md-5">

                    <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label"><span style="color: #ff6b6b;">*</span>Graduation Year</label>

                    <!-- 16-04-2026 -->
                    <!-- <select class="form-select" aria-label="Default select example" name="graduationYear" id="graduationYears" required>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                       
						 <option value="2026">2026</option>
                        
                     </select> -->


                    <!-- 18-04-2026 -->
                <!-- <input type="text" class="form-control" name="graduationYear" id="graduationYears" value="2026" readonly> -->
                 <?php
$currentYear = date("Y");
$prevYear = $currentYear - 1;
$nextYear = $currentYear + 1;

$option1 = $prevYear . "-" . $currentYear; // previous-current
$option2 = $currentYear . "-" . $nextYear; // current-next
?>

<select class="form-select" name="graduationYear" id="graduationYears" required>
    <option value="">Select Year</option>

    <option value="<?php echo $option1; ?>" 
        <?php if($year == $option1) echo "selected"; ?>>
        <?php echo $option1; ?>
    </option>

    <option value="<?php echo $option2; ?>" 
        <?php if($year == $option2) echo "selected"; ?>>
        <?php echo $option2; ?>
    </option>
</select>



                
                  </div>

</div>

    <!-- Exam Qualified checkboxes remain here (unchanged) -->
    <div class="col-md-5">


        
        <div class="mb-3">
            <label class="form-label">Exam Qualified:</label><br>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="TOEFL" name="selected_checkboxes[]" value="TOEFL" <?php if(in_array('TOEFL',$selectedCheckboxes)) echo "checked";?>>
                <label for="TOEFL" class="form-check-label">TOEFL</label><br>
                <input type="checkbox" class="form-check-input" id="GRE" name="selected_checkboxes[]" value="GRE" <?php if(in_array('GRE',$selectedCheckboxes)) echo "checked";?>>
                <label for="GRE" class="form-check-label">GRE</label><br>
                <input type="checkbox" class="form-check-input" id="GATE" name="selected_checkboxes[]" value="GATE" <?php if(in_array('GATE',$selectedCheckboxes)) echo "checked";?>>
                <label for="GATE" class="form-check-label">GATE</label><br>
                <input type="checkbox" class="form-check-input" id="ILETS" name="selected_checkboxes[]" value="ILETS" <?php if(in_array('ILETS',$selectedCheckboxes)) echo "checked";?>>
                <label for="ILETS" class="form-check-label">ILETS</label><br>
                <input type="checkbox" class="form-check-input" id="ECET" name="selected_checkboxes[]" value="ECET" <?php if(in_array('ECET',$selectedCheckboxes)) echo "checked";?>>
                <label for="ECET" class="form-check-label">ECET</label><br>
            </div>
        </div>
    </div>

</div>


<hr style="border-top: 2px solid rgba(212, 160, 23, 0.5); margin: 30px 0;">
            <!-- new -->
                    
        <!-- <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nickname</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" value="<?php echo $nickname; ?>">
                  </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Favourite Food</label>
                    <input type="text" class="form-control" id="favouriteFood" name="favouriteFood" value="<?php echo $fav; ?>">
                  </div>
            </div>
        </div>
        <div class="row justify-content-evenly">
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Extra Activity</label>
                    <input type="text" class="form-control" id="extraActivity" name="extraActivity" value="<?php echo $extra; ?>">
                  </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Companies you placed in</label>
                <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo $company; ?>">  
            </div>
        </div> -->


<div class="row justify-content-evenly">
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Favourite Food</label>
            <input type="text" class="form-control" id="favouriteFood" name="favouriteFood" value="<?php echo htmlspecialchars($fav); ?>">
        </div>
    </div>
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Extra Activity</label>
            <input type="text" class="form-control" id="extraActivity" name="extraActivity" value="<?php echo htmlspecialchars($extra); ?>">
        </div>
    </div>
</div>
<div class="row justify-content-evenly">
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Companies you placed in</label>
            <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo htmlspecialchars($company); ?>">
        </div>
    </div>
    <div class="col-md-5">
        <div class="mb-3">
            <label class="form-label">Location of Company</label>
            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars(isset($row['location']) ? $row['location'] : ''); ?>" placeholder="City / State">
        </div>
    </div>
</div>

        </div>
       
<!-- 07-04-2026 2 -->

<!-- IMAGE UPLOAD SECTION - added -->
<!-- <div class="row justify-content-evenly" style="margin-bottom: 20px;">
    
</div> -->

<script>
// document.getElementById('profile_photo').addEventListener('change', function(){
//     var file = this.files[0];
//     if(!file) return;
//     if(file.size > 2 * 1024 * 1024){
//         alert('File too large. Please choose an image under 2 MB.');
//         this.value = '';
//         return;
//     }
//     var reader = new FileReader();
//     reader.onload = function(e){
//         var img = document.getElementById('imgPreview');
//         var ph  = document.getElementById('imgPlaceholder');
//         img.src = e.target.result;
//         img.style.display = 'block';
//         if(ph) ph.style.display = 'none';
//     };
//     reader.readAsDataURL(file);
// });

document.getElementById('profile_photo').addEventListener('change', function(){
    var file = this.files[0];
    var err = document.getElementById('photoError');

    if(!file) return;

    // Type check
    var allowed = ['image/jpeg', 'image/jpg', 'image/png'];
    if(allowed.indexOf(file.type) === -1){
        alert('❌ Invalid file format!\nOnly JPG, JPEG, and PNG files are allowed.');
        this.value = '';
        return;
    }

    // Size check: 200 KB
    if(file.size > 200 * 1024){
       alert('❌ File too large!\nMaximum allowed size is 200 KB.');
        this.value = '';
        return;
    }

    // Clear any previous error
    if(err) err.textContent = '';

    var reader = new FileReader();
    reader.onload = function(e){
        var img = document.getElementById('imgPreview');
        var ph  = document.getElementById('imgPlaceholder');
        img.src = e.target.result;
        img.style.display = 'block';
        if(ph) ph.style.display = 'none';
    };
    reader.readAsDataURL(file);
});


// After the existing profile_photo change listener, add this:

document.querySelector('form').addEventListener('submit', function(e) {
    var photoInput = document.getElementById('profile_photo');
    var savedPhoto = <?php echo !empty($saved_photo) ? 'true' : 'false'; ?>;
    
    // Check if there's no saved photo AND no new photo selected
    if (!savedPhoto && (!photoInput.files || photoInput.files.length === 0)) {
        e.preventDefault();
        // Scroll to photo field
        document.getElementById('imgPreviewWrap').scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Show error
        var err = document.getElementById('photoError');
        if (!err) {
            err = document.createElement('p');
            err.id = 'photoError';
            err.style.cssText = 'color:#ff6b6b; font-size:.78rem; margin-top:6px; font-weight:600;';
            document.getElementById('imgPreviewWrap').parentElement.appendChild(err);
        }
        err.textContent = '⚠️ Profile photo is required.';
        return false;
    }

    // Validate alt mobile
    var alt = document.getElementById('altMobile').value;
    if (alt.length !== 10 || alt.charAt(0) < '6') {
        e.preventDefault();
        document.getElementById('altMobile').scrollIntoView({ behavior: 'smooth', block: 'center' });
        document.getElementById('altMobileError').textContent = 'Enter a valid 10-digit mobile number.';
        return false;
    }

    // Validate DOB
    if (!document.getElementById('dob').value) {
        e.preventDefault();
        document.getElementById('dob').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }

    // Validate graduation year
    if (!document.getElementById('graduationYears').value) {
        e.preventDefault();
        document.getElementById('graduationYears').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
});

// Clear photo error when user picks a file
document.getElementById('profile_photo').addEventListener('change', function() {
    var err = document.getElementById('photoError');
    if (err) err.textContent = '';
});

</script>

<!-- end -->

<!-- button-row,btn-wrap added 16-04-2026 -->
        <div class="row button-row" style="margin-top : 50px;">
          <div class="col-md-3"></div>
          <div class="col-md-3 btn-wrap">
          <div class="col-sm-2">
                        <button type="button" class="btn btn-primary btn-sm-12" id="submit" onclick="window.location.href='checkdetails.php'">Back </button>
                <!-- <button type="submit" class="btn btn-primary btn-sm-12" id="submit">Submit</button> -->
            </div>
          </div>
          <div class="col-md-3 btn-wrap">
          <div class="col-sm-2">
            <!-- <button type="button" class="btn btn-primary btn-sm-12" id="submit" onclick="window.location.href='checkdetails.php'">Back </button> -->
            <button type="submit" class="btn btn-primary btn-sm-12" id="submit">Submit</button>
			  <br><br>
          </div>
          </div>
          <div class="col-md-3"></div>
        </div>
     </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">

    </script>
  </form>
  <?php




 
  ?>



  </body>
</html>
