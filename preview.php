<?php 
include 'connect.php';
session_start();

if(!isset($_SESSION['reg'])){
    header('Location:index.php');
    die;
}

$user_id = $_SESSION['reg'];

// Fetch user details
$sql = "SELECT * FROM slam_studetails WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// If no data exists, redirect to fill details or show a message
if (!$data) {
    echo "<script>alert('Please fill your details first!'); window.location='details2.php';</script>";
    exit;
}

// Fetch opinions written BY OTHERS about the logged-in user
// frnd = logged-in user's reg no (they are the receiver)
$opinions_sql = "SELECT o.user_id AS author_id, o.opinion,
                        COALESCE(s.name, o.user_id) AS author_name
                 FROM slambook_opnion o
                 LEFT JOIN slam_studetails s ON s.user_id = o.user_id
                 WHERE o.frnd = '$user_id'
                 ORDER BY o.id DESC";
$opinions_result = mysqli_query($conn, $opinions_sql);
$opinions = [];
while ($row = mysqli_fetch_assoc($opinions_result)) {
    $opinions[] = $row;
}
$opinion_count = count($opinions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Preview | Vignan University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #D4A017;
            --navy: #0d1628;
            --navy-light: #142038;
            --text-muted: #8892A4;
        }

        body {
            background-color: var(--navy);
            background-image: radial-gradient(circle at top right, #1e2d50 0%, var(--navy) 100%);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            padding: 40px 20px;
        }

        /* ── Profile Card ─────────────────────────────────── */
        .profile-card {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 160, 23, 0.2);
            border-radius: 24px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }

        /* 07-04-2026  */
        /* .profile-header {
            background: linear-gradient(135deg, var(--gold), #F5C842);
            padding: 40px;
            text-align: center;
            color: var(--navy);
        }

        .profile-header h1 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 5px;
            font-size: 2.5rem;
        }

        .profile-header p {
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.9rem;
            opacity: 0.8;
        } */
.profile-header {
    background: linear-gradient(135deg, var(--gold), #F5C842);
    padding: 48px 40px;
    color: var(--navy);
    display: flex;
    align-items: center;
    gap: 36px;
}

.profile-photo-circle {
    width: 120px;
    height: 120px;
    /* border-radius: 50%; */
    border-radius: 16px;
    object-fit: cover;
    border: 4px solid rgba(13,22,40,0.2);
    box-shadow: 0 4px 20px rgba(13,22,40,0.2);
    flex-shrink: 0;
}

.profile-photo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 16px;
    background: rgba(13,22,40,0.15);
    border: 4px solid rgba(13,22,40,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    flex-shrink: 0;
}

.profile-header-text {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.profile-header p {
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-size: 0.72rem;
    opacity: 0.7;
    margin: 0;
}

.profile-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2.6rem;
    margin: 0;
    line-height: 1.1;
}

.profile-header span {
    font-size: 0.88rem;
    opacity: 0.75;
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* @media (max-width: 600px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 36px 24px;
    } */
    /* .profile-header-text { align-items: center; }
    .profile-header h1 { font-size: 2rem; }
} */



@media (max-width: 600px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 15px;
    }
    .profile-photo-circle { margin-bottom: 15px; }
}

        /* ── Info Grid ────────────────────────────────────── */
        /* .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            padding: 40px;
        } */

.info-grid {
    display: grid;
    grid-template-columns: 1fr; /* 1 Column for Mobile */
    gap: 20px;
    padding: 20px;
}

@media (min-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr 1fr; /* 2 Columns for Desktop */
        padding: 40px;
    }
}

            .info-item {
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 10px;
        }

        .info-label {
            color: var(--gold);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1.1rem;
            color: #fff;
        }

        .full-width {
            grid-column: span 2;
        }

        .exams-badge {
            display: inline-block;
            background: rgba(212, 160, 23, 0.15);
            color: var(--gold);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            margin-right: 8px;
            margin-top: 5px;
            border: 1px solid var(--gold);
        }

        /* ── Actions ──────────────────────────────────────── */
        .actions {
            text-align: center;
            padding: 0 40px 40px;
        }

        .btn-edit {
            background: transparent;
            border: 1px solid var(--gold);
            color: var(--gold);
            padding: 10px 25px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 600;
            display: inline-block;
        }

        .btn-edit:hover {
            background: var(--gold);
            color: var(--navy);
        }

        /* ── Opinions Section ─────────────────────────────── */
        .opinions-section {
            max-width: 800px;
            margin: 32px auto 0;
            padding-bottom: 60px;
        }

        .opinions-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .opinions-heading em {
            color: var(--gold);
            font-style: italic;
        }

        .opinions-heading .count-badge {
            background: rgba(212,160,23,0.15);
            border: 1px solid rgba(212,160,23,0.4);
            color: var(--gold);
            border-radius: 20px;
            padding: 2px 12px;
            font-size: 0.8rem;
            font-family: 'DM Sans', sans-serif;
            font-style: normal;
            font-weight: 600;
        }

        .divider-gold {
            width: 52px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin-bottom: 24px;
        }

        /* Opinion Cards */
        .opinion-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(212,160,23,0.15);
            border-radius: 16px;
            padding: 24px 28px;
            margin-bottom: 16px;
            position: relative;
            transition: border-color 0.25s, transform 0.2s;
        }

        .opinion-card:hover {
            border-color: rgba(212,160,23,0.45);
            transform: translateY(-2px);
        }

        /* large decorative quote mark */
        .opinion-card::before {
            content: '\201C';
            position: absolute;
            top: 10px;
            right: 22px;
            font-size: 4rem;
            line-height: 1;
            color: rgba(212,160,23,0.12);
            font-family: Georgia, serif;
        }

        .opinion-author {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), #F5C842);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            color: var(--navy);
            flex-shrink: 0;
        }

        .author-info strong {
            display: block;
            font-size: 0.92rem;
            color: #fff;
            font-weight: 600;
        }

        .author-info span {
            display: block;
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }

        .opinion-text {
            font-size: 0.97rem;
            color: #c8d0de;
            line-height: 1.7;
            font-style: italic;
        }

        /* Empty state */
        .no-opinions {
            text-align: center;
            padding: 50px 24px;
            background: rgba(255,255,255,0.02);
            border: 1px dashed rgba(212,160,23,0.2);
            border-radius: 16px;
            color: var(--text-muted);
        }

        .no-opinions .emoji { font-size: 2.5rem; display: block; margin-bottom: 12px; }
        .no-opinions p { font-size: 0.88rem; }
        .no-opinions strong { color: var(--gold); }

        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .profile-header h1 { font-size: 1.8rem; }
            .opinions-section { margin-top: 20px; }
        }


        /* 16-04-2026 */
@media (max-width: 600px) {

    .actions {
        display: flex;
        flex-direction: column;   /* stack buttons */
        align-items: center;
        gap: 12px;                /* 🔥 space between buttons */
    }

    .actions .btn-edit {
        width: 100%;              /* full width buttons */
        text-align: center;
    }

}
    </style>
</head>
<body>

<!-- ══════════════════════════════════════════════
     PROFILE CARD
══════════════════════════════════════════════ -->
<div class="profile-card">
    <!-- <div class="profile-header">
        <p>Student Slam Book Profile</p>
        <h1><?php echo htmlspecialchars($data['name']); ?></h1>
        <span>Reg No: <?php echo htmlspecialchars($data['user_id']); ?></span>
    </div> -->

<!-- 07-04-2026 7 -->
<div class="profile-header">

    <?php if(!empty($data['photo_path'])): ?>
        <img src="<?php echo htmlspecialchars($data['photo_path']); ?>"
             alt="Profile Photo"
             class="profile-photo-circle">
    <?php else: ?>
        <div class="profile-photo-placeholder">👤</div>
    <?php endif; ?>

    <div class="profile-header-text">
        <p>Student Slam Book Profile</p>
        <h1><?php echo htmlspecialchars($data['name']); ?></h1>
        <span>Reg No: <?php echo htmlspecialchars($data['user_id']); ?></span>
    </div>

</div>
    

    <div class="info-grid">

    <!-- 16-04-2026 commented mobile pasted above nickname 
     rechanged the order  -->
        <!-- <div class="info-item">
            <span class="info-label">Mobile</span>
            <span class="info-value"><?php echo htmlspecialchars($data['mobile'] ?: 'N/A'); ?></span>
        </div> -->

        <!-- 08-05-2026 - added alt mobile number display -->

        <div class="info-item">
            <span class="info-label">Mobile</span>
            <span class="info-value"><?php echo htmlspecialchars($data['mobile'] ?: 'N/A'); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Alternate / Parent's Number</span>
            <span class="info-value"><?php echo htmlspecialchars($data['alt_mobile'] ?: 'N/A'); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Date of Birth</span>
            <span class="info-value"><?php echo htmlspecialchars($data['dob']); ?></span>
        </div>
         <div class="info-item">
            <span class="info-label">Graduation Year</span>
            <span class="info-value"><?php echo htmlspecialchars($data['year']); ?></span>
        </div>
          <div class="info-item">
            <span class="info-label">Program & Course</span>
            <span class="info-value"><?php echo htmlspecialchars($data['program'] . " — " . $data['course']); ?></span>
        </div>
        <!-- <div class="info-item">
            <span class="info-label">Department</span>
            <span class="info-value"><?php echo htmlspecialchars($data['branch']); ?></span>
        </div> -->



<!-- 08-05-2026 2 -->

<div class="info-item">
    <span class="info-label">Department</span>
    <span class="info-value"><?php echo htmlspecialchars($data['dept'] ?: 'N/A'); ?></span>
</div>

<div class="info-item">
    <span class="info-label">Branch / Specialization</span>
    <span class="info-value"><?php echo htmlspecialchars($data['branch'] ?: 'N/A'); ?></span>
</div>

 

        <div class="info-item ">
            <span class="info-label">Exams Qualified</span>
            <div class="mt-2">
                <?php 
                $exams = explode(', ', $data['exam']);
                $printed = false;
                foreach($exams as $e) {
                    if(!empty(trim($e))) {
                        echo "<span class='exams-badge'>" . htmlspecialchars(trim($e)) . "</span>";
                        $printed = true;
                    }
                }
                if(!$printed) echo "<span class='info-value' style='color:#8892A4;'>None</span>";
                ?>
            </div>
        </div>
        <div class="info-item ">
            <span class="info-label">Nickname</span>
            <span class="info-value"><?php echo htmlspecialchars($data['nickname'] ?: 'N/A'); ?></span>
        </div>
        <!-- <div class="info-item">
            <span class="info-label">Mobile</span>
            <span class="info-value"><?php echo htmlspecialchars($data['mobile'] ?: 'N/A'); ?></span>
        </div> -->
      
   
        <!-- <div class="info-item">
            <span class="info-label">Date of Birth</span>
            <span class="info-value"><?php echo htmlspecialchars($data['dob']); ?></span>
        </div> -->
       
        <div class="info-item ">
            <span class="info-label">Favorite Food</span>
            <span class="info-value"><?php echo htmlspecialchars($data['fav'] ?: 'N/A'); ?></span>
        </div>
        <div class="info-item ">
            <span class="info-label">Extra Activities</span>
            <span class="info-value"><?php echo htmlspecialchars($data['extra'] ?: 'N/A'); ?></span>
        </div>

        <!-- <div class="info-item ">
            <span class="info-label">Placed At</span>
            <span class="info-value"><?php echo htmlspecialchars($data['company'] ?: 'Looking for opportunities'); ?></span>
        </div> -->

<!-- 08-05-2026 -->

<div class="info-item">
    <span class="info-label">Placed At</span>
    <span class="info-value"><?php echo htmlspecialchars($data['company'] ?: 'Looking for opportunities'); ?></span>
</div>
<div class="info-item">
    <span class="info-label">Company Location</span>
    <span class="info-value"><?php echo htmlspecialchars(isset($data['location']) && $data['location'] ? $data['location'] : 'N/A'); ?></span>
</div>


    </div>

    <br>
    <div class="actions">
        <a href="details2.php" class="btn-edit">✏️ Edit Profile</a>
        <!-- 16-04-2026 removed style marginright -->
        <a href="checkdetails.php" class="btn-edit" >← Back to Dashboard</a>
    </div>
</div>


<!-- ══════════════════════════════════════════════
     OPINIONS SECTION
     Opinions written BY OTHERS about this user
     (slambook_opnion.frnd = this user's reg no)
══════════════════════════════════════════════ -->
<div class="opinions-section">

    <div class="opinions-heading">
        What Friends Say <em>About You</em>
        <span class="count-badge"><?php echo $opinion_count; ?> opinion<?php echo $opinion_count !== 1 ? 's' : ''; ?></span>
    </div>
    <div class="divider-gold"></div>

    <?php if ($opinion_count > 0): ?>
        <?php foreach ($opinions as $op): 
            // First letter of author name for avatar
            $initials = !empty($op['author_name']) ? strtoupper(substr($op['author_name'], 0, 1)) : '?';
        ?>
        <div class="opinion-card">
            <div class="opinion-author">
                <div class="author-avatar"><?php echo htmlspecialchars($initials); ?></div>
                <div class="author-info">
                    <strong><?php echo htmlspecialchars($op['author_name']); ?></strong>
                    <span><?php echo htmlspecialchars($op['author_id']); ?></span>
                </div>
            </div>
            <div class="opinion-text">
                <?php echo nl2br(htmlspecialchars($op['opinion'])); ?>
            </div>
        </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="no-opinions">
            <span class="emoji">💬</span>
            <strong>No opinions yet</strong>
            <p>Share your profile with friends and ask them to leave their thoughts about you!</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>