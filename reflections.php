<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['reg'])) {
    header('Location:index.php');
    die;
}

$user_id = $_SESSION['reg'];
header("Cache-Control:no-cache,private,must-revalidate");

// Fetch existing reflections if any
$existing = mysqli_query($conn, "SELECT * FROM slambook_reflection WHERE user_id='$user_id'");
$ref = mysqli_fetch_assoc($existing);

$saved = false;
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $q1 = mysqli_real_escape_string($conn, substr($_POST['q1'] ?? '', 0, 250));
    // $q2 = mysqli_real_escape_string($conn, substr($_POST['q2'] ?? '', 0, 250));
    // $q3 = mysqli_real_escape_string($conn, substr($_POST['q3'] ?? '', 0, 250));
    // $q4 = mysqli_real_escape_string($conn, substr($_POST['q4'] ?? '', 0, 250));
    // $q5 = mysqli_real_escape_string($conn, substr($_POST['q5'] ?? '', 0, 250));

$q1 = mysqli_real_escape_string($conn, substr(isset($_POST['q1']) ? $_POST['q1'] : '', 0, 250));
$q2 = mysqli_real_escape_string($conn, substr(isset($_POST['q2']) ? $_POST['q2'] : '', 0, 250));
$q3 = mysqli_real_escape_string($conn, substr(isset($_POST['q3']) ? $_POST['q3'] : '', 0, 250));
$q4 = mysqli_real_escape_string($conn, substr(isset($_POST['q4']) ? $_POST['q4'] : '', 0, 250));
$q5 = mysqli_real_escape_string($conn, substr(isset($_POST['q5']) ? $_POST['q5'] : '', 0, 250));


    // Check if already exists
    $check = mysqli_query($conn, "SELECT id FROM slambook_reflection WHERE user_id='$user_id'");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE slambook_reflection SET q1='$q1',q2='$q2',q3='$q3',q4='$q4',q5='$q5', updated_at=NOW() WHERE user_id='$user_id'";
    } else {
        $sql = "INSERT INTO slambook_reflection (user_id,q1,q2,q3,q4,q5,inserted_at,updated_at) VALUES ('$user_id','$q1','$q2','$q3','$q4','$q5',NOW(),NOW())";
    }

    if (mysqli_query($conn, $sql)) {


// 11-05-2026

if (!empty($q1) && !empty($q2)) {
        mysqli_query($conn, "UPDATE slambook_reflection SET is_complete=1 WHERE user_id='$user_id'");
    }

        // Re-fetch updated data
        $existing = mysqli_query($conn, "SELECT * FROM slambook_reflection WHERE user_id='$user_id'");
        $ref = mysqli_fetch_assoc($existing);
    //     $saved = true;
    // } else {
    //     $msg = 'Something went wrong. Please try again.';
    // }

    $saved = true;

    // CHECK ALL 3 COMPLETE
    $r1 = mysqli_query($conn, "SELECT is_complete FROM slambook_reflection WHERE user_id='$user_id' AND is_complete=1");
    $r2 = mysqli_query($conn, "SELECT is_complete FROM slam_studetails WHERE user_id='$user_id' AND is_complete=1");
    $r3 = mysqli_query($conn, "SELECT is_complete FROM exitfeedback_draft WHERE id='$user_id' AND is_complete=1");

    if (mysqli_num_rows($r1) > 0 && mysqli_num_rows($r2) > 0 && mysqli_num_rows($r3) > 0) {
        header('Location: thankyou.php');
        die;
    }

    } else {
        $msg = 'Something went wrong. Please try again.';
    }
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reflections | Vignan University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --gold: #D4A017;
            --gold-light: #F5C842;
            --navy: #1A2744;
            --navy-dark: #0d1628;
            --navy-mid: #142038;
            --border: rgba(212,160,23,0.25);
            --purple: #7B4FD4;
            --purple-light: #A97FF2;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            background: var(--navy-dark);
            background-image: radial-gradient(circle at 20% 30%, #1e2d50 0%, var(--navy-dark) 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Sparkles ── */
        .sparkles {
            position: fixed; inset: 0;
            pointer-events: none; overflow: hidden; z-index: 0;
        }
        .sparkle {
            position: absolute; width: 3px; height: 3px;
                        border-radius: 50%; 
            /* background: rgba(212,160,23,0.8); */
             background: radial-gradient(circle, #FFF8CC 0%, #FFD700 45%, #D4A017 100%);

            opacity: 0; animation: twinkle 3s infinite;
        }
        @keyframes twinkle {
            0%,100% { opacity:0; transform:scale(0); }
            50%      { opacity:.45; transform:scale(1); }
        }

        /* ── Topbar ── */
        .topbar {
            width: 100%;
            background: var(--navy-mid);
            border-bottom: 1px solid var(--border);
            padding: 14px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-icon {
            width: 36px; height: 36px;
            background: var(--gold); border-radius: 9px;
            display: grid; place-items: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .brand-text strong {
            display: block; font-size: .72rem;
            font-weight: 600; color: #fff;
            letter-spacing: .07em; text-transform: uppercase;
        }
        .brand-text span { font-size: .62rem; color: #8892A4; }
        .topbar-right {
            display: flex; align-items: center; gap: 16px;
            font-size: .75rem; color: #8892A4;
        }
        .topbar-right span { color: var(--gold-light); font-weight: 500; }
        .logout-btn {
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
        }
        .logout-btn:hover { background: rgba(160,60,40,0.85); color:#fff; }

        /* ── Page heading ── */
        .page-heading {
            text-align: center;
            padding: 52px 24px 8px;
            position: relative; z-index: 1;
        }
        .page-heading small {
            display: block;
            font-size: .7rem;
            /* color: var(--purple-light); */
                        color: whitesmoke;

            letter-spacing: .14em;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 500;
        }
        .page-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: #fff;
            line-height: 1.15;
        }
        .page-heading h1 em {
            font-style: italic;
            color: var(--gold-light);
        }
        .page-heading .gold-bar {
            width: 52px; height: 3px;
            /* background: linear-gradient(90deg, var(--purple), var(--gold)); */
                        /* background: linear-gradient(90deg, var(--purple), var(--gold)); */
            background: linear-gradient(90deg, #E6C200, #e1d89f);

            border-radius: 2px;
            margin: 18px auto 0;
        }

        /* ── Form card ── */
        .ref-card {
            width: 100%;
            max-width: 720px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 44px 48px;
            margin: 36px 24px 60px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            position: relative; z-index: 1;
        }

        /* ── Question blocks ── */
        .q-block {
            margin-bottom: -4px;
        }
        .q-label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }
        .q-num {
            width: 28px; height: 28px; flex-shrink: 0;
            border-radius: 50%;
            /* background: linear-gradient(135deg, var(--purple), var(--purple-light)); */
            background:#D4A017;


            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; color: #fff;
        }
        .q-num.optional {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .q-text {
            font-size: .92rem;
            font-weight: 600;
            color: #e8eaf0;
            line-height: 1.4;
            padding-top: 3px;
        }
        .q-text .badge-required {
            display: inline-block;
            background: rgba(212,160,23,0.15);
            border: 1px solid var(--gold);
            color: var(--gold);
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: 4px;
            padding: 2px 7px;
            margin-left: 8px;
            vertical-align: middle;
        }
        .q-text .badge-optional {
            display: inline-block;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            color: #8892A4;
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: 4px;
            padding: 2px 7px;
            margin-left: 8px;
            vertical-align: middle;
        }

        textarea.ref-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: .92rem;
            padding: 14px 16px;
            resize: vertical;
            min-height: 90px;
            max-height: 200px;
            transition: border-color .2s, box-shadow .2s;
            line-height: 1.6;
        }
        textarea.ref-input:focus {
            outline: none;
            border-color: var(--purple-light);
            box-shadow: 0 0 0 3px rgba(123,79,212,0.15);
            background: rgba(255,255,255,0.07);
        }
        textarea.ref-input::placeholder { color: rgba(255,255,255,0.25); }

        .char-counter {
            text-align: right;
            font-size: .68rem;
            color: #8892A4;
            margin-top: 5px;
        }
        .char-counter.warn { color: #F5C842; }
        .char-counter.over { color: #FF5733; }

        /* divider between q blocks */
        .q-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 0 0 32px;
        }

        /* ── Action buttons ── */
        .actions-row {
            display: flex;
            gap: 14px;
            justify-content: flex-end;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .btn-submit {
            /* background: linear-gradient(135deg, var(--purple) 0%, var(--purple-light) 100%); */
            background: #D4A017;


            border: none;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-weight: 700;
            font-size: .88rem;
            letter-spacing: .07em;
            text-transform: uppercase;
            padding: 12px 32px;
            border-radius: 11px;
            cursor: pointer;
            transition: transform .18s, box-shadow .18s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            /* box-shadow: 0 8px 24px rgba(123,79,212,0.35); */

            box-shadow: 0 8px 24px rgba(222, 179, 48, 0.79);


        }
        .btn-back {
            background: transparent;
            border: 1px solid var(--border);
            color: #8892A4;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: .88rem;
            padding: 12px 24px;
            border-radius: 11px;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .2s, color .2s;
            display: inline-flex; align-items: center;
        }
        .btn-back:hover { border-color: var(--gold); color: var(--gold); }

        /* ── Success toast ── */
        .toast-saved {
            display: none;
            align-items: center;
            gap: 10px;
            background: rgba(50,180,100,0.15);
            border: 1px solid rgba(50,180,100,0.4);
            color: #6ee7a0;
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 28px;
            font-size: .88rem;
            font-weight: 500;
        }
        .toast-saved.show { display: flex; }

        /* ── Error toast ── */
        .toast-error {
            display: none;
            align-items: center;
            gap: 10px;
            background: rgba(220,50,50,0.12);
            border: 1px solid rgba(220,50,50,0.35);
            color: #ff8a80;
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 28px;
            font-size: .88rem;
            font-weight: 500;
        }
        .toast-error.show { display: flex; }

        /* ── Badge row ── */
        .badges-row {
            display: flex; gap: 10px;
            justify-content: center;
            margin: 0 0 48px;
            flex-wrap: wrap;
            position: relative; z-index: 1;
        }
        .badge-item {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 8px;
            padding: 7px 14px;
            font-size: .6rem; color: #8892A4;
            letter-spacing: .06em; text-align: center; line-height: 1.5;
        }
        .badge-item strong { color: var(--gold); display: block; font-size: .68rem; }

        /* ── Mobile ── */
        @media (max-width: 600px) {
            .topbar { padding: 14px 20px; }
            .ref-card { padding: 28px 20px; margin: 24px 12px 48px; }
            .page-heading h1 { font-size: 2rem; }
            .actions-row { flex-direction: column; }
            .btn-submit, .btn-back { width: 100%; text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>

<!-- Sparkles -->
<div class="sparkles" id="sparkles"></div>

<!-- Topbar -->
<div class="topbar">
    <div class="brand">
        <div class="brand-icon">🎓</div>
        <div class="brand-text">
            <strong>Vignan's University</strong>
            <span>Foundation for Science, Technology &amp; Research</span>
        </div>
    </div>
    <div class="topbar-right">
        Logged in as <span><?php echo htmlspecialchars($user_id); ?></span>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>
</div>

<!-- Page heading -->
<div class="page-heading">
    <small>Look Back &amp; Cherish</small>
    <h1>My <em>Reflections</em></h1>
    <div class="gold-bar"></div>
</div>

<!-- Form card -->
<div class="ref-card">

    <!-- Success / error messages -->
    <div class="toast-saved <?php echo $saved ? 'show' : ''; ?>">
        ✅ Your reflections have been saved successfully!
    </div>
    <div class="toast-error <?php echo !empty($msg) ? 'show' : ''; ?>">
        ⚠️ <?php echo htmlspecialchars($msg); ?>
    </div>

    <form action="reflections.php" method="POST" id="refForm">

        <!-- Q1 -->
        <div class="q-block">
            <div class="q-label">
                <div class="q-num">1</div>
                <div class="q-text">
                    How would you describe your overall journey at Vignan University?
                    <span class="badge-required">Required</span>
                </div>
            </div>
            <textarea class="ref-input" name="q1" id="q1" maxlength="250"
                placeholder="Share your story in a few words…"
               required><?php echo htmlspecialchars(isset($ref['q1']) ? $ref['q1'] : ''); ?></textarea>
            <div class="char-counter" id="cnt-q1">0 / 250</div>
        </div>
        <hr class="q-divider">

        <!-- Q2 -->
        <div class="q-block">
            <div class="q-label">
                <div class="q-num">2</div>
                <div class="q-text">
                    What makes your department special to you?
                    <span class="badge-required">Required</span>
                </div>
            </div>
            <textarea class="ref-input" name="q2" id="q2" maxlength="250"
                placeholder="The people, the culture, the labs…"
           required><?php echo htmlspecialchars(isset($ref['q2']) ? $ref['q2'] : ''); ?></textarea>
            <div class="char-counter" id="cnt-q2">0 / 250</div>
        </div>
        <hr class="q-divider">

        <!-- Q3 -->
        <div class="q-block">
            <div class="q-label">
                <div class="q-num optional">3</div>
                <div class="q-text">
                    Who is your favourite faculty member and what makes them special?
                    <span class="badge-optional">Optional</span>
                </div>
            </div>
            <textarea class="ref-input" name="q3" id="q3" maxlength="250"
                placeholder="A mentor who made a difference…"><?php echo htmlspecialchars(isset($ref['q3']) ? $ref['q3'] : ''); ?></textarea>

            <div class="char-counter" id="cnt-q3">0 / 250</div>
        </div>
        <hr class="q-divider">

        <!-- Q4 -->
        <div class="q-block">
            <div class="q-label">
                <div class="q-num optional">4</div>
                <div class="q-text">
                    What will you miss the most after graduation?
                    <span class="badge-optional">Optional</span>
                </div>
            </div>
            <textarea class="ref-input" name="q4" id="q4" maxlength="250"
                placeholder="The canteen, the friends, the chaos…"><?php echo htmlspecialchars(isset($ref['q4']) ? $ref['q4'] : ''); ?></textarea>
            <div class="char-counter" id="cnt-q4">0 / 250</div>
        </div>
        <hr class="q-divider">

        <!-- Q5 -->
        <div class="q-block" style="margin-bottom:0;">
            <div class="q-label">
                <div class="q-num optional">5</div>
                <div class="q-text">
                    One thing you wish you had done more during college?
                    <span class="badge-optional">Optional</span>
                </div>
            </div>
            <textarea class="ref-input" name="q5" id="q5" maxlength="250"
placeholder="Attended more fests, started that club…"><?php echo htmlspecialchars(isset($ref['q5']) ? $ref['q5'] : ''); ?></textarea>
            <div class="char-counter" id="cnt-q5">0 / 250</div>
        </div>

        <!-- Actions -->
        <div class="actions-row" style="margin-top:36px;">
            <a href="checkdetails.php" class="btn-back">← Dashboard</a>
            <button type="submit" class="btn-submit">
                <?php echo $ref ? '💾 Update Reflections' : '✨ Save Reflections'; ?>
            </button>
        </div>

    </form>
</div>

<!-- Badges -->
<div class="badges-row">
    <div class="badge-item"><strong>NAAC</strong>A+ Grade</div>
    <div class="badge-item"><strong>NIRF</strong>70th Rank</div>
    <div class="badge-item"><strong>UGC</strong>Deemed University</div>
    <div class="badge-item"><strong>ISO</strong>Certified</div>
</div>

<script>
// Sparkles
const sp = document.getElementById('sparkles');
for (let i = 0; i < 20; i++) {
    const s = document.createElement('div');
    s.className = 'sparkle';
    s.style.left = Math.random() * 100 + '%';
    s.style.top  = Math.random() * 100 + '%';
    s.style.animationDelay    = (Math.random() * 4) + 's';
    s.style.animationDuration = (2.5 + Math.random() * 2) + 's';
    sp.appendChild(s);
}

// Character counters
const questions = ['q1','q2','q3','q4','q5'];
questions.forEach(function(id) {
    const ta  = document.getElementById(id);
    const cnt = document.getElementById('cnt-' + id);
    if (!ta || !cnt) return;

    function update() {
        const len = ta.value.length;
        cnt.textContent = len + ' / 250';
        cnt.classList.remove('warn','over');
        if (len >= 250)       cnt.classList.add('over');
        else if (len >= 200)  cnt.classList.add('warn');
    }

    ta.addEventListener('input', update);
    update(); // init on page load (for pre-filled values)
});
</script>

</body>
</html>