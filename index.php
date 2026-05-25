<?php
session_start();
// if (isset($_SESSION['SESSION_ID'])) {
//     header("Location: feedback.php");
//     die();
// }

include 'connect.php';
$msg = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $id = mysqli_real_escape_string($conn, $_POST['id']);
    // $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $id = $_POST['id'];
    $password = $_POST['password'];
    // echo $password;
    $_SESSION['reg'] = $id;

    $sql = "SELECT * FROM slambook_reg WHERE regno='{$id}' AND pass='{$password}'";
    $result = mysqli_query($conn, $sql);


    if (mysqli_num_rows($result) === 1) {
        $_SESSION['SESSION_ID'] = $id;
        // 08-05-2026
        header("Location: otp.php");
    } else {
        echo '<script>alert("Id and Password not matched")</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- added 26-03-2026-->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>

<!-- commented 26-03-2026-->
    <!-- <link rel="stylesheet" href="login-style.css">
    <link rel="stylesheet" href="boxicons/css/boxicons.css">
    <link rel="stylesheet" href="css/text.css"> -->
    <title>Vignan University::Vadlamudi</title>
    <style>
        #reg {
            text-decoration: none;
            text-align: center;
        }
        
      /* 16-04-2026 */
      /* ================= MOBILE VIEW ================= */
/* ================= MOBILE VIEW OPTIMIZED ================= */
      @media (max-width: 768px) {
          .left-panel {
              display: none !important;
          }
          body {
              padding: 0 !important;
              background: #1A2744; /* Ensures no white gaps */
          }
          .box {
              width: 90% !important;
              max-width: 400px !important;
              margin: 40px auto !important; /* Centers the card on the screen */
              border-radius: 20px !important;
              padding: 30px 20px !important;
              box-shadow: 0 20px 40px rgba(0,0,0,0.4);
          }
          .brand-row {
              justify-content: center;
              text-align: center;
          }
          .login-heading {
              text-align: center !important;
          }
          .login-heading h1 {
              font-size: 2rem !important;
          }
          .divider-line {
              margin: 12px auto !important; /* Forces gold line to center */
          }
          .input {
              width: 100% !important;
              font-size: 16px !important; /* Prevents auto-zoom on iPhones */
              padding: 12px;
          }
          .submit {
              width: 100%;
              padding: 14px;
              font-size: 16px;
          }
          .bottom {
              flex-direction: row !important; /* Keeps links side-by-side */
              justify-content: space-between;
              margin-top: 15px;
          }
      }
    </style>
    <!-- ↓ NEW: index-specific stylesheet -->

     <!-- added 26-03-2026 -->
    <link rel="stylesheet" href="styles2.css">
</head>

<body>
    <div class="heading">
        <!-- commented 26-03-2026 -->
        <!-- <img id="logo" src="logo.svg" alt="" >
        <img id="accl" src="accloads.png" alt=""> -->

    </div>
      <div class="left-panel">
                <!-- ↓ NEW: photo layer + caption (mirrors login.php left panel) -->
                 <!-- added 26-03-2026-->
            <div class="photo-area">
            <!-- 16-04-2026 -->
            <img src="backgroundlogi.webp" alt="Graduation" class="photo-placeholder"/>
            </div>
            <div class="left-caption">
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
                <h1>Slam <em>Book</em></h1>
                </div>
                <div class="divider-line"></div>
            <!-- ↑ END NEW -->
        <div class="avt">
            <img style="border-radius: 50%;" src="avt.png" alt="">
        </div>
        <div class="container">
            <div class="top-header">
                <span>Have an account?</span>
                <header><b>Login</b></header>
            </div>
            <form action="" method="post">
                <div class="input-field">
                    <!-- added 26-03-2026 -->
                     <label class="input-label">Registration No.</label>   <!-- ADD THIS LINE -->

                    <input type="text" style="background-color:white;" class="input id" name="id" placeholder="e.g. 221FA04252"
                        required>
                </div>
                <div class="input-field">
                     <!-- added 26-03-2026 -->
                    <label class="input-label">Password</label>

                    <input type="password" style="background-color:white;" class="input password" name="password"
                        placeholder="Password" required>
                </div>
                <div class="input-field">
                    <input type="submit" class="submit" id="submit" value="Submit">
                </div>
            </form>

            <div class="bottom">
                <div class="left">
                    <label><a href="register.php">Register here</a></label>
                </div>
                <div class="right">
                    <label><a href="forgot-password.php">Forgot password?</a></label>
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

