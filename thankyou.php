<?php
// include 'connect.php';
// session_start();

// unset($_SESSION['thankyou_shown']);

// if (!isset($_SESSION['SESSION_ID'])) {
//     header('Location: index.php');
//     die;
// }

// // Clear the flag immediately so dashboard works on next visit
// unset($_SESSION['thankyou_shown']);

session_start();
include 'connect.php';

if (!isset($_SESSION['SESSION_ID'])) {
    header('Location: index.php');
    die;
}

$user = $_SESSION['SESSION_ID'];

// Fetch display name
$display_name = '';
$name_res = mysqli_query($conn, "SELECT nickname, name FROM slambook_reg WHERE regno='$user'");
if ($name_res && mysqli_num_rows($name_res) > 0) {
    $name_row = mysqli_fetch_assoc($name_res);
    $display_name = !empty(trim($name_row['nickname']))
                    ? trim($name_row['nickname'])
                    : trim($name_row['name']);
}
if (empty($display_name)) $display_name = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Vignan University</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{min-height:100vh;background:#1A2744;display:flex;align-items:center;justify-content:center;font-family:'DM Sans',sans-serif;padding:20px;}
        .sparkle{position:fixed;width:3px;height:3px;border-radius:50%;background:#D4A017;opacity:0;animation:twinkle 3s infinite;pointer-events:none;}
        @keyframes twinkle{0%,100%{opacity:0;transform:scale(0)}50%{opacity:.5;transform:scale(1)}}
        .card{max-width:560px;width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(212,160,23,0.25);border-radius:24px;padding:60px 48px;text-align:center;box-shadow:0 30px 80px rgba(0,0,0,0.5);backdrop-filter:blur(10px);position:relative;overflow:hidden;animation:fadeUp 0.8s cubic-bezier(0.16,1,0.3,1) both;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:none}}
        .card::before{content:'';position:absolute;top:-80px;left:-80px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(212,160,23,0.08),transparent 70%);pointer-events:none;}
        .icon-wrap{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#D4A017,#F5C842);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 28px;box-shadow:0 8px 30px rgba(212,160,23,0.3);}
        .eyebrow{font-size:0.68rem;letter-spacing:0.2em;text-transform:uppercase;color:#D4A017;font-weight:600;margin-bottom:14px;}
        h1{font-family:'Playfair Display',serif;font-size:2.4rem;color:#fff;line-height:1.2;margin-bottom:8px;}
        h1 em{font-style:italic;color:#F5C842;}
        .divider{width:48px;height:3px;background:#D4A017;border-radius:2px;margin:20px auto;}
        .message{font-size:0.95rem;color:#8892A4;line-height:1.8;margin:0 auto 36px;max-width:400px;}
        .message strong{color:#F5C842;}
        .checklist{display:flex;flex-direction:column;gap:10px;margin-bottom:36px;text-align:left;}
        .check-item{display:flex;align-items:center;gap:12px;background:rgba(212,160,23,0.06);border:1px solid rgba(212,160,23,0.15);border-radius:10px;padding:10px 16px;font-size:0.85rem;color:#c8d0de;}
        .check-icon{width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#D4A017,#F5C842);display:flex;align-items:center;justify-content:center;font-size:0.75rem;flex-shrink:0;color:#1A2744;font-weight:700;}
        /* .btn-dashboard{display:inline-block;background:linear-gradient(135deg,#D4A017,#F5C842);color:#1A2744;font-weight:700;font-size:0.85rem;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;padding:14px 40px;border-radius:12px;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 8px 24px rgba(212,160,23,0.25);margin-bottom:14px;display:block;}
        .btn-dashboard:hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(212,160,23,0.35);}
        .btn-edit{display:inline-block;background:transparent;border:1px solid rgba(212,160,23,0.4);color:#D4A017;font-weight:600;font-size:0.82rem;letter-spacing:0.06em;text-transform:uppercase;text-decoration:none;padding:11px 32px;border-radius:12px;transition:all 0.2s;margin-top:10px;display:block;}
        .btn-edit:hover{background:rgba(212,160,23,0.08);border-color:#D4A017;} */

.btn-dashboard{background:linear-gradient(135deg,#D4A017,#F5C842);color:#1A2744;font-weight:700;font-size:0.85rem;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;padding:14px 40px;border-radius:12px;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 8px 24px rgba(212,160,23,0.25);text-align:center;flex:1;}
.btn-dashboard:hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(212,160,23,0.35);}
.btn-edit{background:transparent;border:1px solid rgba(212,160,23,0.4);color:#D4A017;font-weight:600;font-size:0.82rem;letter-spacing:0.06em;text-transform:uppercase;text-decoration:none;padding:11px 32px;border-radius:12px;transition:all 0.2s;text-align:center;flex:1;}
.btn-edit:hover{background:rgba(212,160,23,0.08);border-color:#D4A017;}

        /* .btn-group{display:flex;flex-direction:column;gap:10px;align-items:center;} */

        .btn-group {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 15px;
}
@media (min-width: 768px) {
    .btn-group {
        flex-direction: row; /* Side by side on desktop */
        justify-content: center;
    }
}
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
    <div class="eyebrow">Vignan's University</div>
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

    <!-- <div class="btn-group"> -->
        <!-- <a href="checkdetails.php" class="btn-dashboard">← Go Back to Dashboard</a> -->

        <!-- <a href="checkdetails.php?from_thankyou=1" class="btn-dashboard">← Go Back to Dashboard</a>
        <a href="feedback.php" class="btn-edit">✏️ Edit Exit Feedback</a>
        <a href="details2.php" class="btn-edit">✏️ Edit Student Details</a>
        <a href="reflections.php" class="btn-edit">✏️ Edit Reflections</a>
    </div> -->


<div class="btn-group">
        <a href="thankyou_back.php" class="btn-dashboard">🏠 Go to Dashboard</a>
        <a href="logout.php" class="btn-edit">🚪 Logout</a>
    </div>


    <div class="badges">
        <div class="badge"><strong>NAAC</strong>A+ Grade</div>
        <div class="badge"><strong>NIRF</strong>70th Rank</div>
        <div class="badge"><strong>UGC</strong>Deemed University</div>
        <div class="badge"><strong>ISO</strong>Certified</div>
    </div>
</div>

</body>
</html>