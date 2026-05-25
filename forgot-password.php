<?php

session_start();
// if (isset($_SESSION['SESSION_ID'])) {
//     header("Location: feedback.php");
//     die();
// }

include 'connect.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regno = $_POST['regno'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $result = mysqli_query($conn, "SELECT * FROM slambook_reg WHERE regno='{$regno}'");
    if (mysqli_num_rows($result) > 0) {
        if ($password == $password2) {
            $query = mysqli_query($conn, "UPDATE slambook_reg SET pass='{$password}' WHERE regno='{$regno}'");
            echo '<script>alert("Password changed successfully.")</script>';
        } else {
            echo '<script>alert("Passwords not matched.")</script>';
        }
    } else {
        echo '<script>alert("Please Enter Correct Registration Number")</script></div>';
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
        #log {
            text-decoration: none;
            text-align: center;
        }

        /* 16-04-2026 */
        /* ================= MOBILE FIX ================= */
@media (max-width: 768px) {

    /* ❌ Completely remove left panel */
    .left-panel {
        display: none !important;
        position: absolute !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
    }

    .photo-area,
    .photo-placeholder {
        display: none !important;
    }

    /* Remove spacing */
    body {
        padding: 0 !important;
    }

    /* ✅ Make login/register box full screen */
    .box {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 25px 20px !important;
        border-radius: 0 !important;
    }

    /* Center brand */
    .brand-row {
        justify-content: center;
        text-align: center;
    }

    .brand-text {
        text-align: center;
    }

    /* ✅ Heading same as login */
    .login-heading {
        text-align: left;
        margin-top: 10px;
    }

    .login-heading small {
        font-size: 0.7rem;
        letter-spacing: 2px;
        margin-bottom: 5px;
        display: block;
    }

    .login-heading h1 {
        font-size: 2.2rem;
        line-height: 1.2;
    }

    /* ✅ GOLD LINE (LEFT ALIGNED like login) */
    .divider-line {
        width: 45px;
        height: 3px;
        margin: 10px 0 20px 0;
        display: block;
    }

    /* Inputs */
    .input {
        width: 100% !important;
        padding: 12px;
        font-size: 14px;
    }

    /* Button */
    .submit {
        width: 100%;
        padding: 12px;
        font-size: 15px;
        border-radius: 10px;
    }

    /* Bottom */
    .bottom {
        text-align: center;
        justify-content: center !important;
    }

}
    </style>
     <!-- added 26-03-2026 -->
    <link rel="stylesheet" href="styles2.css">
</head>

<body>
    <div class="heading">
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
                <div class="login-heading">
                <small>Welcome back</small>
                <h1>Reset <em>Password</em></h1>
                </div>
                <div class="divider-line"></div>
            <!-- ↑ END NEW -->


        <div class="avt">
            <img style="border-radius: 50%;" src="avt.png" alt="">
        </div>
        <div class="container">
            <div class="top-header">
                <header><b>Reset Password</b></header>
            </div>

            <form action="" method="post">
                <div class="input-field">
                    <!-- added 26-03-2026 -->
                     <label class="input-label">Registration No.</label>   <!-- ADD THIS LINE -->
                    <input style="background-color:white;" type="text" class="input email" name="regno"
                        placeholder="Registration No" required>
                </div>
                <div class="input-field">
                        <!-- added 26-03-2026 -->
                        <label class="input-label">New Password</label>   <!-- ADD THIS LINE -->
                    <input style="background-color:white;" type="password" class="input password" name="password"
                        placeholder="Enter New Password" required>
                </div>
                <div class="input-field">
                        <!-- added 26-03-2026 -->
                        <label class="input-label">Confirm New Password</label>   <!-- ADD THIS LINE -->
                    <input style="background-color:white;" type="password" class="input password" name="password2"
                        placeholder="Re-Enter Password" required>
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
</body>
</html>