<?php

    session_start();
    // if (isset($_SESSION['SESSION_ID'])) {
    //     header("Location: index1.php");
    //     die();
    // }

    include 'connect.php';
    $msg = "";



    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];
        // $passing_year = $_POST['passing_year'];

    // 18-04-2026
        // CHECK 1: Is this regno in the finalyearstudents table?
    $check_student = mysqli_query($conn, "SELECT * FROM finalyearstudents WHERE regno='{$id}'");
    if (mysqli_num_rows($check_student) === 0) {
        echo '<script>alert("You are not authorized to register. Your Registration Number is not found in our records.")</script>';
    }
    // CHECK 2: Already registered in slambook?



       else if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM slambook_reg WHERE email='{$email}' OR regno='{$id}'")) > 0) {
            echo '<script>alert("User already exists.")</script>';
        } else {
            if ($password === $confirm_password) {
                // $sql = "INSERT INTO slambook_reg  VALUES ('{$id}','{$name}', '{$email}', '{$password}', NOW(), NOW())";

// 08-05-2026 - added nickname field

$nickname_val = isset($_POST['nickname']) ? mysqli_real_escape_string($conn, trim($_POST['nickname'])) : '';
$sql = "INSERT INTO slambook_reg (regno, name, nickname, email, pass, inserted_at, updated_at) VALUES ('{$id}','{$name}','{$nickname_val}','{$email}','{$password}', NOW(), NOW())";

                $result = mysqli_query($conn, $sql);

                if ($result) {
                    echo "<div style='display: none;'>";
                    
                    echo "</div>";
                    echo '<script>alert("Registration done successfully.")</script>';
                } else {
                    echo '<script>alert("Something went wrong.")</script>';
                }
            } else {
                echo '<script>alert("Passwords not matched.")</script>';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- commented 26-03-2026-->
    <!-- <link rel="stylesheet" href="login-style.css">
    <link rel="stylesheet" href="boxicons/css/boxicons.css">
    <link rel="stylesheet" href="css/text.css"> -->
    <!-- added 26-03-2026-->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
    <title>Vignan University::Vadlamudi</title>
    <style>
        /* #log{
            text-decoration: none;
            text-align: center;
        } */
        #pyear{
            margin-left: 20px;
            width: 60px;
            text-align: center;
        }
        .select{
            display: flex;
        }

        /* 16-04-2026  */
        /* ================= MOBILE VIEW ================= */
@media (max-width: 768px) {
          .left-panel { display: none !important; }
          body { background: #1A2744; }
          .box {
              width: 100% !important;
              margin: 0 !important;
              padding: 30px 20px !important;
              border-radius: 0 !important;
              min-height: 100vh;
          }
          .container { margin-top: 10px; }
          .brand-row { justify-content: center; margin-bottom: 20px; }
          .input-field { margin-bottom: 15px; }
          .input { 
              font-size: 16px !important; 
              padding: 12px !important; 
          }
          .bottom {
              justify-content: center !important;
              padding-bottom: 30px;
          }
      }
    </style>
       <!-- added 26-03-2026 -->
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="heading">
         <!-- commented 26-03-2026 -->
        <!-- <img id="logo" src="logo.png" alt="">
        <img id="accl" src="accloads.png" alt=""> -->

    </div>
          <div class="left-panel">
                <!-- ↓ NEW: photo layer + caption (mirrors login.php left panel) -->
                 <!-- added 26-03-2026-->
            <div class="photo-area">
            <img src="backgroundlogin.png" alt="Graduation" class="photo-placeholder"/>
            </div>
            <div class="left-caption" style="bottom: 40px;">
            <span class="tag">Vignan's University</span>
            <h2>Memories that<br/>last forever.</h2>
            <p>Celebrate your journey, connect with us,<br/>and relive the golden days of campus life.</p>
            </div>
            <!-- ↑ END NEW -->
      </div>


    <div class="welcome">
    <h1 class="stylish-text" style="font-size:50px;">Slam Book</h1>
    </div>
    <div class="box">
                      <!-- ↓ NEW: sparkles + brand row + heading + divider (mirrors login.php right panel top) -->
               <!-- added 26-03-2026 -->
                <div class="sparkles" id="sparkles"></div>
                <div class="brand-row">
                <div class="brand-icon">🎓</div>
                <div class="brand-text">
                    <strong>Vignan's University</strong>
                    <span>Foundation for Science, Technology &amp; Research</span>
                </div>
                </div>
                <!-- <div class="login-heading">
                <small>Welcome back</small>
                <h1>Slam <em>Book</em></h1>
                </div> -->
                <!-- <div class="divider-line"></div> -->
               <!-- ↑ END NEW -->
        <!-- <div class="avt">
            <img style="border-radius: 50%;" src="avt.png" alt="">
        </div> -->
        <div class="container">
            <div class="top-header">
                <header><b>Register here</b></header>
            </div>
            <form action="" method="post">
            <div class="input-field">
                <!-- added 26-03-2026 -->
                <label class="input-label">Registration No.</label>   <!-- ADD THIS LINE -->
                <input style="background-color:white;" type="text" class="input id" name="id" placeholder="Registration No" required>
            </div>
            <div class="input-field">
                <label class="input-label">Name</label>
                <input style="background-color:white;" type="text" class="input name" name="name" placeholder="Enter your Name" required>
            </div>
            <div class="input-field">
                <label class="input-label">Email</label>
                <input style="background-color:white;" type="text" class="input email" name="email" placeholder="Enter Email" required>
            </div>

<!-- 08-05-2026 nickname optional -->
<div class="input-field">
    <label class="input-label">Nickname <span style="color:#aaa; font-size:0.75rem;">(Optional)</span></label>
    <input style="background-color:white;" type="text" class="input" name="nickname" placeholder="What would you like to be called?">
</div>





            <div class="input-field">
                <label class="input-label">Password</label>
                <input style="background-color:white;" type="password" class="input password" name="password" placeholder="Enter New Password" required>
            </div>
            <div class="input-field">
                <label class="input-label">Confirm Password</label>

                <input style="background-color:white;" type="password" class="input confirm-password" name="confirm-password" placeholder="Re-Enter Password" required>
            </div>
            
            <div class="input-field">
                <input type="submit" class="submit" id="submit" value="Submit">
            </div>
            </form>

            <div class="bottom" style="justify-content: end; margin-bottom: 10px;">
                <div class="right">
                <label><a href="index.php">Back to Login</a></label>
                </div>
            </div>


        </div>

                <!-- added 26-03-2026 -->
                <!-- ↓ NEW: badges row (mirrors login.php bottom badges) -->
        <!-- <div class="badges-row">
          <div class="badge"><strong>NAAC</strong>A+ Grade</div>
          <div class="badge"><strong>NIRF</strong>2022</div>
          <div class="badge"><strong>UGC</strong>Deemed University</div>
          <div class="badge"><strong>ISO</strong>Certified</div>
        </div> -->
        <!-- ↑ END NEW -->
    </div>
                <!-- added 26-03-2026 -->
            <!-- ↓ NEW: sparkle script -->
            <script src="script2.js"></script>
</body>
</html>