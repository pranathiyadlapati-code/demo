<?php include 'connect.php'?>
<?php session_start();


// 11-05-2026

$user = isset($_SESSION['SESSION_ID']) ? $_SESSION['SESSION_ID'] : '';

// Check all three completion flags
// $slam_complete = false;
// $ref_complete  = false;
// $exit_complete = false;

// if (!empty($user)) {
//     $r1 = mysqli_query($conn, "SELECT is_complete FROM slam_studetails WHERE user_id='$user'");
//     if ($r1 && mysqli_num_rows($r1) > 0) {
//         $row1 = mysqli_fetch_assoc($r1);
//         $slam_complete = ($row1['is_complete'] == 1);
//     }

//     $r2 = mysqli_query($conn, "SELECT is_complete FROM slambook_reflection WHERE user_id='$user'");
//     if ($r2 && mysqli_num_rows($r2) > 0) {
//         $row2 = mysqli_fetch_assoc($r2);
//         $ref_complete = ($row2['is_complete'] == 1);
//     }

//     $r3 = mysqli_query($conn, "SELECT is_complete FROM exitfeedback_draft WHERE id='$user'");
//     if ($r3 && mysqli_num_rows($r3) > 0) {
//         $row3 = mysqli_fetch_assoc($r3);
//         $exit_complete = ($row3['is_complete'] == 1);
//     }
// }

// Check all three completion flags
$slam_complete = false;
$ref_complete  = false;
$exit_complete = false;

if (!empty($user)) {
    $r1 = mysqli_query($conn, "SELECT is_complete FROM slam_studetails WHERE user_id='$user'");
    if ($r1 && mysqli_num_rows($r1) > 0) {
        $row1 = mysqli_fetch_assoc($r1);
        $slam_complete = ($row1['is_complete'] == 1);
    }

    $r2 = mysqli_query($conn, "SELECT is_complete FROM slambook_reflection WHERE user_id='$user'");
    if ($r2 && mysqli_num_rows($r2) > 0) {
        $row2 = mysqli_fetch_assoc($r2);
        $ref_complete = ($row2['is_complete'] == 1);
    }

    $r3 = mysqli_query($conn, "SELECT is_complete FROM exitfeedback_draft WHERE id='$user'");
    if ($r3 && mysqli_num_rows($r3) > 0) {
        $row3 = mysqli_fetch_assoc($r3);
        $exit_complete = ($row3['is_complete'] == 1);
    }
}

$all_complete = $slam_complete && $ref_complete && $exit_complete;



// $all_complete = $slam_complete && $ref_complete && $exit_complete;



//11-05-2026
// Clear flag when user returns from thankyou
// if (isset($_GET['from_thankyou'])) {
//     unset($_SESSION['thankyou_shown']);
// }
// Show thank you only once per login session
// if ($all_complete && !isset($_SESSION['thankyou_shown'])) {
//     $_SESSION['thankyou_shown'] = true;
//     header("Location: thankyou.php");
//     die();
// }

if ($all_complete) {
    if (!empty($_SESSION['skip_redirect'])) {
        unset($_SESSION['skip_redirect']);
    } elseif (!isset($_SESSION['thankyou_shown'])) {
        $_SESSION['thankyou_shown'] = true;
        header("Location: thankyou.php");
        die();
    }
}


// Fetch display name for thank you page
// $display_name = '';
// if (!empty($user)) {
//     $name_res = mysqli_query($conn, "SELECT nickname, name FROM slambook_reg WHERE regno='$user'");
//     if ($name_res && mysqli_num_rows($name_res) > 0) {
//         $name_row = mysqli_fetch_assoc($name_res);
//         $display_name = !empty(trim($name_row['nickname'])) 
//                         ? trim($name_row['nickname']) 
//                         : trim($name_row['name']);
//     }
//     if (empty($display_name)) $display_name = $user;
// }


header("Cache-Control:no-cache,private,must-revalidate");


// 08-05-2026 - Fetch welcome name
$welcome_name = '';
if (isset($_SESSION['reg'])) {
    $regno_dash = $_SESSION['reg'];
    $reg_sql = "SELECT nickname, name FROM slambook_reg WHERE regno = '$regno_dash'";
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
        $welcome_name = $_SESSION['reg'];
    }
}

?>

<!-- 11-05-2026 -->




<!-- THANK YOU PAGE -->
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Vignan University</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{min-height:100vh;background:#1A2744;display:flex;align-items:center;justify-content:center;font-family:'DM Sans',sans-serif;padding:20px;overflow:hidden;}
        .sparkle{position:fixed;width:3px;height:3px;border-radius:50%;background:#D4A017;opacity:0;animation:twinkle 3s infinite;pointer-events:none;}
        @keyframes twinkle{0%,100%{opacity:0;transform:scale(0)}50%{opacity:.5;transform:scale(1)}}
        .card{max-width:560px;width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(212,160,23,0.25);border-radius:24px;padding:60px 48px;text-align:center;box-shadow:0 30px 80px rgba(0,0,0,0.5);backdrop-filter:blur(10px);position:relative;overflow:hidden;animation:fadeUp 0.8s cubic-bezier(0.16,1,0.3,1) both;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:none}}
        .card::before{content:'';position:absolute;top:-80px;left:-80px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(212,160,23,0.08),transparent 70%);pointer-events:none;}
        .icon-wrap{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#D4A017,#F5C842);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 28px;box-shadow:0 8px 30px rgba(212,160,23,0.3);}
        .eyebrow{font-size:0.68rem;letter-spacing:0.2em;text-transform:uppercase;color:#D4A017;font-weight:600;margin-bottom:14px;}
        h1{font-family:'Playfair Display',serif;font-size:2.6rem;color:#fff;line-height:1.2;margin-bottom:8px;}
        h1 em{font-style:italic;color:#F5C842;}
        .divider{width:48px;height:3px;background:#D4A017;border-radius:2px;margin:20px auto;}
        .message{font-size:0.95rem;color:#8892A4;line-height:1.8;margin:0 auto 36px;max-width:400px;}
        .message strong{color:#F5C842;}
        .checklist{display:flex;flex-direction:column;gap:10px;margin-bottom:36px;text-align:left;}
        .check-item{display:flex;align-items:center;gap:12px;background:rgba(212,160,23,0.06);border:1px solid rgba(212,160,23,0.15);border-radius:10px;padding:10px 16px;font-size:0.85rem;color:#c8d0de;}
        .check-icon{width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#D4A017,#F5C842);display:flex;align-items:center;justify-content:center;font-size:0.75rem;flex-shrink:0;color:#1A2744;font-weight:700;}
        .btn-dashboard{display:inline-block;background:linear-gradient(135deg,#D4A017,#F5C842);color:#1A2744;font-weight:700;font-size:0.85rem;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;padding:14px 40px;border-radius:12px;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 8px 24px rgba(212,160,23,0.25);}
        .btn-dashboard:hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(212,160,23,0.35);}
        .badges{display:flex;justify-content:center;gap:10px;margin-top:32px;flex-wrap:wrap;}
        .badge{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:6px 12px;font-size:0.58rem;color:#8892A4;letter-spacing:0.05em;text-align:center;line-height:1.5;}
        .badge strong{color:#D4A017;display:block;font-size:0.65rem;}
        @media(max-width:500px){.card{padding:40px 24px;}h1{font-size:2rem;}}
    </style>
</head>
<body>

<script>
for(let i=0;i<20;i++){
    const s=document.createElement('div');
    s.className='sparkle';
    s.style.left=Math.random()*100+'%';
    s.style.top=Math.random()*100+'%';
    s.style.animationDelay=(Math.random()*4)+'s';
    s.style.animationDuration=(2.5+Math.random()*2)+'s';
    document.body.appendChild(s);
}
</script>

<div class="card">
    <div class="icon-wrap">🎓</div>
    <div class="eyebrow">Vignan's University</div> -->
    <!-- <h1>Thank <em>You!</em></h1> -->
     <!-- 11-05-2026 -->
<!-- 
<h1>Thank You, <em><?php echo htmlspecialchars($display_name); ?>!</em></h1>

    <div class="divider"></div>
    <p class="message">
        You have successfully completed all required steps.<br>
        <strong>Your journey is now fully recorded.</strong><br>
        We wish you all the best ahead!
    </p>
    <div class="checklist">
        <div class="check-item"><div class="check-icon">✓</div>Student Details Filled</div>
        <div class="check-item"><div class="check-icon">✓</div>Reflections Submitted</div>
        <div class="check-item"><div class="check-icon">✓</div>Exit Feedback Completed</div>
    </div>
    <a href="checkdetails.php" class="btn-dashboard">Dashboard</a>
    <div class="badges">
        <div class="badge"><strong>NAAC</strong>A+ Grade</div>
        <div class="badge"><strong>NIRF</strong>70th Rank</div>
        <div class="badge"><strong>UGC</strong>Deemed University</div>
        <div class="badge"><strong>ISO</strong>Certified</div>
    </div>
</div>

</body>
</html> -->





<!DOCTYPE html>
<html>
<head>
    <title>Vignan University::Vadlamudi</title>


<!-- commented 26-03-2026 -->
<!-- </head>
<body> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

<!-- hold -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<!-- added 26-03-2026 -->
 <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">



<style>
    /* commented 26-03-2026 */
    /* .stylish-text {
    font-size: 48px; 
    color: #ff6600; 
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background-color: lightcyan;
    padding: 10px;
    border-radius: 10px; 
    width: 160vb;
    margin-left: 12%;
    justify-content: center;
    animation: pulse 2s infinite; 
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2); 
    }
    100% {
        transform: scale(1);
    }
}
    #pieChart{
        margin-left: 25%;
    margin-top: 3%;
    }
    h3{
        text-shadow: #FFFF33;
        size: 60vh;
        
    }
    body {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
            animation: joy 8s linear infinite;
           
            background-image: linear-gradient(to left,#FFFF33, lightblue );
            background-size: 200% 200%;
        }

        @keyframes joy {
            0% {
                background-position: 0% 20%;
            }
            50% {
                background-position: 50% 25%;
            }
            100%{
                background-position: 100% 50%;
            }
        }
    .box {
          
            top: 35%; 
            margin-left: 25%;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: lightsteelblue;
            border-radius: 5px;
            box-shadow: 15px 10px 10px lightseagreen;
            width: 40%;
            height: 5%;
            

        }
        
        @media  (max-width: 500px) {
            #give   {

            margin-left: 20%;
            margin-top: 5%;
            width:30vb;
            height: 25%;

          }
          .stylish-text{
            width: 40vb;
            height: 10vh;
            font-size: 36px;
          }


   .pie1{
    justify-content: center;
    
    height: 200px;
    width: 40px;

  } 
  h3{
    width: 53vb;
  }

  
}


#give{
    margin-left: 30%;
    margin-top: 2%;
    margin-right: 30%;

}
#image{
    margin-left: 30%;
    margin-top: 2%;
    margin-right: 30%;
}
#pieChart{
    position: relative;
    left:10%;

    height: 50vh;
    width: 50vb;
}
.logout{
    position: absolute;
     text-decoration:none; 

     top:10px;
      right:10px;
} */


      /* added 26-03-2026 */
        /* ── NEW STYLES (same class/id names) ── */

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            background: #1A2744;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stylish-text {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: #fff;
            text-shadow: none;
            background-color: transparent;
            padding: 0;
            border-radius: 0;
            width: 100%;
            margin: 0;
            animation: none;
            text-align: center;
        }

        .stylish-text em {
            font-style: italic;
            color: #F5C842;
        }

        h3 {
            width: 100%;
            text-align: center;
            padding: 52px 48px 8px;
        }

        /* subtitle above heading */
        h3::before {
            content: 'Dashboard';
            display: block;
            font-size: .7rem;
            color: #D4A017;
            letter-spacing: .14em;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
        }

        /* gold divider below heading */
        h3::after {
            content: '';
            display: block;
            width: 48px;
            height: 3px;
            background: #D4A017;
            border-radius: 2px;
            margin: 16px auto 0;
        }

        /* #give {
            width: 100%;
            max-width: 244px;
            margin: 0;
            /* padding: 20px 24px; */
            /* border-radius: 14px !important;
            border: 1px solid rgba(212,160,23,0.2) !important;
            background: rgba(255,255,255,0.06) !important;
            color: #fff !important;
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            font-weight: 600;
            transition: transform .15s, border-color .2s, box-shadow .2s;
            display: inline-flex !important;
            flex-direction: column;
            align-items: center; */
            /* gap: 6px; */
            /* changed */
/* padding: 28px 20px 24px;   was: 20px 24px */
/* gap: 14px;                 was: 6px */
/* text-align: center;        add this */
/* min-height: 180px; / */
        /* } */

        #give {
    width: 100%;
    max-width: 240px;
    margin: 0 !important; /* Forces removal of any weird spacing */
    border-radius: 16px !important;
    border: 1px solid rgba(212,160,23,0.2) !important;
    background: rgba(255,255,255,0.06) !important;
    color: #fff !important;
    padding: 24px 20px;
    display: flex !important;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 8px;
    transition: transform 0.2s, border-color 0.2s;
    min-height: 180px;
}

        #give:hover {
            transform: translateY(-3px);
            border-color: rgba(212,160,23,0.6) !important;
            box-shadow: 0 10px 32px rgba(212,160,23,0.15);
        }

        /* wrapper to center the 3 buttons in a row */
        /* .btn-row {
            display: flex;
            gap: 16px;
            justify-content: center;
            width: 100%;
            /* max-width: 760px; */
            /* max-width: 900px;  /* was 760px */
            /* padding: 0 48px; */
            /* margin-top: 36px;
            margin-left: 10px;
        /* } */ 


        .btn-row {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;      /* Allows buttons to wrap to next line if screen is small */
    gap: 20px;            /* This sets exactly 20px between every button */
    justify-content: center;
    width: 100%;
    max-width: 1100px;    /* Widened to fit all 4 in one row on desktop */
    padding: 0 40px;
    margin: 40px auto;    /* Centers the whole row and gives top/bottom space */
}

        .pie1 {
            width: 100%;
            max-width: 760px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212,160,23,0.15);
            border-radius: 16px;
            padding: 28px 32px;
            margin-top: 36px;
        }
    .pie-wrap {
        position: relative;
        height: 300px;
        width: 100%;
    }

        #pieChart {
            position: static;
            left: auto;
            height: 260px !important;
            width: 100% !important;
            margin: 0;
        }

        .logout {
            position: static;
            text-decoration: none;
        }

        /* sparkles */
        .sparkles {
            position: fixed; inset: 0;
            pointer-events: none; overflow: hidden; z-index: 0;
        }
        .sparkle {
            position: absolute; width: 3px; height: 3px;
            border-radius: 50%; background: #D4A017;
            opacity: 0; animation: twinkle 3s infinite;
        }
        @keyframes twinkle {
            0%,100% { opacity:0; transform:scale(0); }
            50%      { opacity:.5; transform:scale(1); }
        }

        /* badges */
        .badges-row {
            display: flex; gap: 10px;
            justify-content: center;
            margin: 28px 0 48px;
            flex-wrap: wrap;
        }
        .badge-item {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 8px;
            padding: 7px 14px;
            font-size: .6rem; color: #8892A4;
            letter-spacing: .06em; text-align: center; line-height: 1.5;
        }
        .badge-item strong { color: #D4A017; display: block; font-size: .68rem; }

        /* topbar */
        .topbar {
            width: 100%;
            background: #142038;
            border-bottom: 1px solid rgba(212,160,23,0.2);
            padding: 14px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-icon {
            width: 36px; height: 36px;
            background: #D4A017; border-radius: 9px;
            display: grid; place-items: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .brand-text strong {
            display: block; font-size: .72rem;
            font-weight: 600; color: #fff;
            letter-spacing: .07em; text-transform: uppercase;
        }
        .brand-text span { font-size: .62rem; color: #8892A4; }
        .topbar-right { font-size: .75rem; color: #8892A4; }
        .topbar-right span { color: #F5C842; font-weight: 500; }

        /* chart header */
        .chart-header {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .chart-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem; color: #fff;
        }
        .chart-legend { display: flex; gap: 16px; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: .7rem; color: #8892A4; }
        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .dot-partial { background: #FF5733; }
        .dot-full    { background: #36A2EB; }

        @media (max-width: 600px) {
            .btn-row { flex-direction: column; align-items: center; padding: 0 24px; }
            #give { max-width: 100%; width: 100%; }
            .topbar { padding: 14px 24px; }
            h3 { padding: 36px 24px 8px; }
            .pie1 { margin: 24px 24px 0; }
        }

        /* added */
.btn-icon {
    width: 52px; height: 52px;
    border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.btn-icon-gold { background: linear-gradient(135deg,#D4A017,#F5C842); }
.btn-icon-dark { background: rgba(255,255,255,0.10); }
.btn-icon-red  { background: rgba(160,60,40,0.45); }
.btn-desc {
    font-size: .72rem; font-weight: 400;
    color: #8892A4; margin-top: -6px; line-height: 1.5;
}

/* 16-04-2026 */

.chart-counts {
  margin-top: 12px;
  text-align: center;
  font-size: 13px;
  display: flex;
  justify-content: center;
  gap: 16px;
}

.chart-counts span {
  font-weight: 500;
}

.chart-counts .red { color: #FF5733; }
.chart-counts .blue { color: #36A2EB; }

/* 11-05-2026 */

 /* ── Navbar nav links ── */
.topbar-nav {
    display: flex;
    gap: 8px;
    align-items: center;
}

.nav-link {
    color: #8892A4;
    text-decoration: none;
    font-size: .78rem;
    font-weight: 600;
    letter-spacing: .06em;
    padding: 7px 16px;
    border-radius: 8px;
    border: 1px solid transparent;
    transition: all .2s;
}

.nav-link:hover {
    color: #D4A017;
    border-color: rgba(212,160,23,0.4);
    background: rgba(212,160,23,0.07);
}

.nav-link.active {
    color: #D4A017;
    border-color: rgba(212,160,23,0.5);
    background: rgba(212,160,23,0.1);
}


    /* ======================================================
       DESKTOP & BASE STYLES (Applies to all)
       ====================================================== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        min-height: 100vh;
        font-family: 'DM Sans', sans-serif;
        background: #1A2744;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* TOP BAR CONTAINER */
    .topbar {
        width: 100%;
        background: #142038;
        border-bottom: 1px solid rgba(212,160,23,0.2);
        padding: 14px 48px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    /* CENTER NAV BUTTONS - DESKTOP VIEW */
    .topbar-nav {
        display: flex;
        gap: 20px;
        align-items: center;
        justify-content: center;
        flex-grow: 1;
    }

    /* THE BUTTONS (Desktop Visibility Fix) */
    .nav-link {
        font-size: 1.1rem !important;
        font-weight: 800 !important;
        padding: 12px 35px;
        border-radius: 12px;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        border: 2px solid rgba(212, 160, 23, 0.4);
        min-width: 220px; /* Ensures buttons are large on desktop */
        color: #ffffff !important;
    }

    /* Active vs Inactive Colors */
    .nav-link.active {
        background-color: #D4A017 !important;
        color: #1A2744 !important;
        box-shadow: 0 4px 15px rgba(212, 160, 23, 0.3);
        border-color: #D4A017;
    }

    .nav-link:not(.active) {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    .nav-link:hover {
        transform: translateY(-2px);
        background-color: #f5c842 !important;
        color: #1A2744 !important;
    }

    /* TOPBAR LOGOUT & USER INFO */
    .topbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: .85rem;
        color: #8892A4;
    }
    
    .topbar-right span { color: #F5C842; font-weight: 600; }

    /* ======================================================
       MOBILE RESPONSIVENESS (The "Up and Down" Fix)
       ====================================================== */
  @media (max-width: 768px) {

    /* ── Topbar: stay in one row, wrap if needed ── */
    .topbar {
        padding: 10px 12px !important;
        flex-wrap: wrap !important;
        flex-direction: row !important;
        gap: 8px !important;
        align-items: center !important;
    }

    /* ── Brand: shrink text ── */
    .brand-text strong { font-size: .6rem !important; }
    .brand-text span { display: none; }

    /* ── Nav buttons: side by side, small ── */
    .topbar-nav {
        flex-direction: row !important;
        gap: 6px !important;
        flex-grow: 0 !important;
    }
    .nav-link {
        font-size: .68rem !important;
        font-weight: 700 !important;
        padding: 6px 10px !important;
        min-width: unset !important;
        border-radius: 8px !important;
    }

    /* ── Logged in: single line ── */
    .topbar-right {
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
        font-size: .68rem !important;
        gap: 6px !important;
        width: auto !important;
        border-top: none !important;
        padding-top: 0 !important;
        justify-content: flex-end !important;
    }

    /* ── 4 buttons: 2x2 grid ── */
    .btn-row {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
        padding: 0 12px !important;
        margin: 20px auto !important;
    }
    #give {
        max-width: 100% !important;
        width: 100% !important;
        flex-direction: column !important;
        text-align: center !important;
        min-height: 130px !important;
        padding: 14px 8px !important;
        gap: 6px !important;
    }
    .btn-icon {
        width: 38px !important;
        height: 38px !important;
        font-size: 1rem !important;
    }
    .btn-desc { display: none !important; }

    .pie1 {
        width: calc(100% - 24px) !important;
        margin: 16px 12px 0 !important;
    }
}
 
        </style>
</head>
<body>

     <!-- added 26-03-2026 -->
          <div class="sparkles" id="sparkles"></div>

            <!-- NEW topbar -->
            <div class="topbar">
                <div class="brand">
                    <div class="brand-icon">🎓</div>
                    <div class="brand-text">
                        <strong>Vignan's University</strong>
                        <span>Foundation for Science, Technology &amp; Research</span>
                    </div>
                </div>
                <!-- <div class="topbar-right">Logged in as <span><?php echo isset($_SESSION['SESSION_ID']) ? htmlspecialchars($_SESSION['SESSION_ID']) : ''; ?></span></div> -->
                 <!-- 08-05-2026 1  -->

<!-- 11-05-2026 -->
<!-- NEW: nav links -->
    <!-- <div class="topbar-nav">
        <a href="checkdetails.php" class="nav-link active">📖 Slam Book</a>
        <a href="feedback.php" class="nav-link">🚪 Exit Feedback</a>
    </div>
    -->

<div class="topbar-nav">
    <a href="checkdetails.php" class="nav-link active">📖 Sla Book</a>
    <a href="feedback.php" class="nav-link">📝 Exit Feedback</a>
</div>




                 <!-- <div class="topbar-right" style="display:flex; align-items:center; gap:16px;">
        Logged in as <span><?php echo isset($_SESSION['SESSION_ID']) ? htmlspecialchars($_SESSION['SESSION_ID']) : ''; ?></span>
        <a href="logout.php" style="
            background: rgba(160,60,40,0.5);
            border: 1px solid rgba(255,100,80,0.4);
            color: #fff;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: .75rem;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: .05em;
            transition: background .2s;
        " onmouseover="this.style.background='rgba(160,60,40,0.8)'" onmouseout="this.style.background='rgba(160,60,40,0.5)'">
            🚪 Logout
        </a>
    </div> -->


<div class="topbar-right" style="display:flex; align-items:center; gap:8px; white-space:nowrap; font-size:.75rem; color:#8892A4;">
    Logged in as <strong style="color:#F5C842;"><?php echo isset($_SESSION['SESSION_ID']) ? htmlspecialchars($_SESSION['SESSION_ID']) : ''; ?></strong>
    <a href="logout.php" style="background:rgba(160,60,40,0.5);border:1px solid rgba(255,100,80,0.4);color:#fff;padding:5px 10px;border-radius:8px;font-size:.72rem;font-weight:600;text-decoration:none;white-space:nowrap;" onmouseover="this.style.background='rgba(160,60,40,0.8)'" onmouseout="this.style.background='rgba(160,60,40,0.5)'">🚪 Logout</a>
</div>

            </div>


   <!-- changed 26-03-2026 -->
    <h3 class="text-center mt-5 stylish-text">Explore <em>Here</em></h3>
   
<!-- 08-05-2026 Welcome message -->
<div style="
    text-align: center;
    padding: 10px 20px 0;
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    color: #fff;
">
    Welcome, <em style="color: #D4A017; font-style: italic;">
        <?php echo htmlspecialchars($welcome_name); ?>
    </em> 👋
</div>
<br>

  <div style="
        font-family: 'DM Sans', sans-serif;
        font-size: 0.88rem;
        color: #8892A4;
        max-width: 520px;
        margin: 0 auto;
        line-height: 1.8;
        border-top: 1px solid rgba(212,160,23,0.15);
        border-bottom: 1px solid rgba(212,160,23,0.15);
        padding: 14px 20px;
    ">
        ✨ &nbsp; <em style="color:#D4A017;">"College memories are the chapters that never get old."</em>
        <!-- <br>
        📸 &nbsp; <em style="color:#D4A017;">"Fill your slam book, share your story, and leave a little piece of yourself behind."</em>
         -->
    </div>

</div>
      
    <!-- added 26-03-2026 -->
    <!-- NEW wrapper div around ORIGINAL buttons -->
    <!-- <div class="btn-row"> -->
<!-- 
    <button type="button" onclick="window.location.href='details2.php'" class="btn btn-primary"  value="give opinion" id="give" style="background-color: orange;"><b>Fill your Details</b></button>
    <button type="button" onclick="window.location.href='review.php'" class="btn btn-white" value="give opinion" id="give" style="background-color: white;color:blue"><div style="color:blue;"><b>Add Attribute</b></div></button>
    <button type="button" onclick="window.location.href='logout.php'" class="btn bg-success" value="give opinion" id="give" style="background-color: white;color:white"><div style="color:white;"><b>Logout</b></div></button><br> -->

    <!-- modified 26-03-2026 -->
            <!-- <button type="button" onclick="window.location.href='details2.php'" class="btn btn-primary" value="give opinion" id="give" style="background-color: orange;">
            <div class="btn-icon btn-icon-gold">📝</div>
            <b>Fill your Details</b>
            <span class="btn-desc">Complete your slam book profile</span>
            <b>Step 1</b>
        </button>
        <button type="button" onclick="window.location.href='review.php'" class="btn btn-white" value="give opinion" id="give" style="background-color: white;color:blue">
            <div class="btn-icon btn-icon-dark">✨</div>
            <b>Give Opinion</b>
            <span class="btn-desc">Share your thoughts about your friends</span>
            <b>Step 2</b>
        </button>
<button type="button" onclick="window.location.href='preview.php'" class="btn btn-white" id="give" style="background-color: white; color: darkblue;">
    <div class="btn-icon btn-icon-dark">👤</div>
    <b>View Profile</b>
    <span class="btn-desc">Preview your slam book profile page</span>
    <b>Step 3</b>
</button> -->
        <!-- <button type="button" onclick="window.location.href='logout.php'" class="btn bg-success" value="give opinion" id="give" style="background-color: white;color:white"> -->
           
            <!-- <div class="btn-icon btn-icon-red">🚪</div>
            <b>Logout</b>
            <span class="btn-desc">Sign out of your account after completing all steps</span>
            <b>Step 4</b>
         </button> -->
       


         <!-- 08-05-2026 -->  1
          <!-- <button type="button" onclick="window.location.href='reflections.php'" class="btn" id="give" style="background-color: white; color:white"> -->

<!-- 
<button type="button" onclick="window.location.href='reflections.php'" class="btn btn-white" id="give" style="background-color: white; color:white; margin-left: -20px;">
    <div class="btn-icon" style="background: linear-gradient(135deg,#7B4FD4,#A97FF2);">🪞</div>
    <b>Reflections</b>
    <span class="btn-desc">Answer heartfelt questions</span>
    <b>Step 4</b>
</button> -->




    <!-- <form>
    <div class="bg-primary justify-content-center ml-120px">
                                <button type="button" class="btn btn-primary justify-content-center">Logout</button>
    </div>
</form> -->

    <!-- <button type="button" onclick="window.location.href='form2.php'" class="btn btn-success" value="upload" id="image" >Upload Photo</button> -->

    <!-- </div> -->


<!-- <div class="btn-row">
    <button type="button" onclick="window.location.href='details2.php'" class="btn" id="give">
        <div class="btn-icon btn-icon-gold">📝</div>
        <b>Fill your Details</b>
        <span class="btn-desc">Complete your slam book profile</span>
        <b>Step 1</b>
    </button>

    <button type="button" onclick="window.location.href='review.php'" class="btn" id="give">
        <div class="btn-icon btn-icon-dark">✨</div>
        <b>Give Opinion</b>
        <span class="btn-desc">Share thoughts about your friends</span>
        <b>Step 2</b>
    </button>

    <button type="button" onclick="window.location.href='preview.php'" class="btn" id="give">
        <div class="btn-icon btn-icon-dark">👤</div>
        <b>View Profile</b>
        <span class="btn-desc">Preview your slam book page</span>
        <b>Step 3</b>
    </button>

    <button type="button" onclick="window.location.href='reflections.php'" class="btn" id="give">
        <div class="btn-icon" style="background: linear-gradient(135deg,#7B4FD4,#A97FF2);">🪞</div>
        <b>Reflections</b>
        <span class="btn-desc">Answer heartfelt questions</span>
        <b>Step 4</b>
    </button>
</div> -->

<div class="btn-row">
    <button type="button" onclick="window.location.href='details2.php'" class="btn" id="give">
        <div class="btn-icon btn-icon-gold">📝</div>
        <b style="font-size:.85rem;line-height:1.3;">Fill your Details</b>
        <span style="font-size:.68rem;color:#D4A017;font-weight:700;background:rgba(212,160,23,0.12);padding:2px 8px;border-radius:20px;">Step 1</span>
    </button>

    <button type="button" onclick="window.location.href='review.php'" class="btn" id="give">
        <div class="btn-icon btn-icon-dark">✨</div>
        <b style="font-size:.85rem;line-height:1.3;">Give Opinion</b>
        <span style="font-size:.68rem;color:#D4A017;font-weight:700;background:rgba(212,160,23,0.12);padding:2px 8px;border-radius:20px;">Step 2</span>
    </button>

    <button type="button" onclick="window.location.href='preview.php'" class="btn" id="give">
        <div class="btn-icon btn-icon-dark">👤</div>
        <b style="font-size:.85rem;line-height:1.3;">View Profile</b>
        <span style="font-size:.68rem;color:#D4A017;font-weight:700;background:rgba(212,160,23,0.12);padding:2px 8px;border-radius:20px;">Step 3</span>
    </button>

    <button type="button" onclick="window.location.href='reflections.php'" class="btn" id="give">
        <div class="btn-icon" style="background:linear-gradient(135deg,#7B4FD4,#A97FF2);">🪞</div>
        <b style="font-size:.85rem;line-height:1.3;">Reflections</b>
        <span style="font-size:.68rem;color:#D4A017;font-weight:700;background:rgba(212,160,23,0.12);padding:2px 8px;border-radius:20px;">Step 4</span>
    </button>
</div>

    <br>


    <div class="pie1">

         <!-- added 26-03-2026 -->
            <div class="chart-header">
                <h2>Submission Overview</h2>
                <div class="chart-legend">
                    <div class="legend-item"><div class="dot dot-partial"></div>Partial</div>
                    <div class="legend-item"><div class="dot dot-full"></div>Full</div>
                </div>
            </div>

         <canvas  id="pieChart"></canvas>
         <br>
         <!-- 16-04-2026 -->
<!-- <div id="submissionCounts" style="text-align:center; margin-top:10px;">
    
    <span style="color:#ff5722; font-size:18px; margin-right:20px;">
        Partial Submissions: <span id="partialCount">0</span>
    </span>

    <span style="color:#3498db; font-size:18px;">
        Full Submissions: <span id="fullCount">0</span>
    </span>

</div> -->


<!-- 16-04-2026 -->
<div class="chart-counts">
  <span class="red">
    ● Partial Submissions: <span id="partialCount">0</span>
  </span>

  <span class="blue">
    ● Full Submissions: <span id="fullCount">0</span>
  </span>
</div>


    </div>
        <br><br>

     <!--added 26-03-2026 -->
     <!-- NEW badges -->
    <div class="badges-row">
        <div class="badge-item"><strong>NAAC</strong>A+ Grade</div>
        <div class="badge-item"><strong>NIRF</strong>70th Rank</div>
        <div class="badge-item"><strong>UGC</strong>Deemed University</div>
        <div class="badge-item"><strong>ISO</strong>Certified</div>
    </div>


    <script>
        
        
document.addEventListener("DOMContentLoaded", function () {

    fetch('getData.php')
        .then(response => response.json())
        .then(data => {
           
            const partiallySubmittedCount = data.partiallySubmittedCount;
            const fullySubmittedCount = data.fullySubmittedCount;

        //    16-04-2026
                document.getElementById("partialCount").innerText = partiallySubmittedCount;
                document.getElementById("fullCount").innerText = fullySubmittedCount;


            const ctx = document.getElementById('pieChart').getContext('2d');
            
            const pieChart = new Chart(ctx, {
                // type: 'pie',
                type: 'doughnut',
                data: {
                    labels: ['Partial Submissions', 'Full Submissions'],
                    datasets: [{
                        data: [partiallySubmittedCount, fullySubmittedCount],
                        backgroundColor: ['#FF5733', '#36A2EB'], // You can customize the colors

                        borderColor: '#1A2744',
                        borderWidth: 4,
                        hoverOffset: 8,
                    }],
                },
                // options: {
                //     // pieChart.style.right="300px"
                //     responsive: false,
                // },
                
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#142038',
                            titleColor: '#F5C842',
                            bodyColor: '#fff',
                            borderColor: 'rgba(212,160,23,0.3)',
                            borderWidth: 1,
                            padding: 10,
                        }
                    }
                },
            });
        })
        .catch(error => console.error('Error:', error));
});



</script>

<!-- added 26-03-2026 -->
     <script>
        const sp = document.getElementById('sparkles');
        for(let i = 0; i < 18; i++){
            const s = document.createElement('div');
            s.className = 'sparkle';
            s.style.left = Math.random()*100+'%';
            s.style.top  = Math.random()*100+'%';
            s.style.animationDelay = (Math.random()*4)+'s';
            s.style.animationDuration = (2.5+Math.random()*2)+'s';
            sp.appendChild(s);
        }
    </script>

</body>
</html>


