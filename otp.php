


<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['SESSION_ID'])) {
    header('Location: index.php');
    die;
}

$user_id = $_SESSION['SESSION_ID'];

// Fetch mobile from  finalyearstudents table
// $ finalyearstudents_result = mysqli_query($conn, "SELECT mobile FROM  finalyearstudents WHERE regno='$user_id'");
// $ finalyearstudents_row = mysqli_fetch_assoc($ finalyearstudents_result);
// $ finalyearstudents_mobile = isset($ finalyearstudents_row['mobile']) ? $ finalyearstudents_row['mobile'] : '';


$finalyearstudents_result = mysqli_query($conn, "SELECT mobile FROM finalyearstudents WHERE regno='$user_id'");
$finalyearstudents_row = mysqli_fetch_assoc($finalyearstudents_result);
$finalyearstudents_mobile = isset($finalyearstudents_row['mobile']) ? $finalyearstudents_row['mobile'] : '';

$error = '';
$otp_sent = false;
$otp_mobile = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'send_otp') {
        $mobile = trim(isset($_POST['mobile']) ? $_POST['mobile'] : '');

        // Basic validation
        if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
            $error = 'Please enter a valid 10-digit mobile number starting with 6-9.';
        } else {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Store OTP and mobile in session
            $_SESSION['otp']        = $otp;
            $_SESSION['otp_mobile'] = $mobile;
            $_SESSION['otp_time']   = time();

            // FOR NOW: print OTP in response (replace with SMS API later)
            $otp_sent   = true;
            $otp_mobile = $mobile;

            // Log to PHP error log / console equivalent
            error_log("OTP for $user_id on $mobile : $otp");
        }
    }

    if ($action === 'verify_otp') {
        $entered_otp = trim(isset($_POST['entered_otp']) ? $_POST['entered_otp'] : '');
        $stored_otp  = isset($_SESSION['otp'])      ? $_SESSION['otp']      : '';
        $otp_time    = isset($_SESSION['otp_time']) ? $_SESSION['otp_time'] : 0;

        if (empty($stored_otp)) {
            $error = 'No OTP found. Please request a new OTP.';
        } elseif (time() - $otp_time > 120) {
            // 2 minutes expiry
            $error = 'OTP has expired. Please request a new one.';
            unset($_SESSION['otp'], $_SESSION['otp_mobile'], $_SESSION['otp_time']);
        } elseif ($entered_otp == $stored_otp) {

        // OTP verified — store verified mobile in session, clear OTP session vars
    $_SESSION['verified_mobile'] = $_SESSION['otp_mobile'];
            // OTP verified — clear OTP session vars and proceed
            unset($_SESSION['otp'], $_SESSION['otp_mobile'], $_SESSION['otp_time']);
            header('Location: checkdetails.php');
            die;
        } else {
            $error = 'Incorrect OTP. Please try again.';
            $otp_sent   = true;
            $otp_mobile = isset($_SESSION['otp_mobile']) ? $_SESSION['otp_mobile'] : '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification | Vignan University</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --gold: #D4A017;
            --gold-light: #F5C842;
            --navy: #1A2744;
            --navy-dark: #0d1628;
            --navy-mid: #142038;
            --border: rgba(212,160,23,0.25);
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
            justify-content: center;
            padding: 24px;
        }

        /* Sparkles */
        .sparkles {
            position: fixed; inset: 0;
            pointer-events: none; overflow: hidden; z-index: 0;
        }
        .sparkle {
            position: absolute; width: 3px; height: 3px;
            border-radius: 50%; background: var(--gold);
            opacity: 0; animation: twinkle 3s infinite;
        }
        @keyframes twinkle {
            0%,100% { opacity:0; transform:scale(0); }
            50%      { opacity:.5; transform:scale(1); }
        }

        /* Brand top */
        .brand-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
            position: relative;
            z-index: 1;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: var(--gold); border-radius: 10px;
            display: grid; place-items: center;
            font-size: 1.2rem;
        }
        .brand-text strong {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .07em;
            text-transform: uppercase;
        }
        .brand-text span { font-size: .62rem; color: #8892A4; }

        /* Card */
        .otp-card {
            width: 100%;
            max-width: 460px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 44px 40px;
            backdrop-filter: blur(12px);
            box-shadow: 0 24px 56px rgba(0,0,0,0.45);
            position: relative;
            z-index: 1;
        }

        /* Heading */
        .card-heading {
            margin-bottom: 28px;
        }
        .card-heading small {
            display: block;
            font-size: .68rem;
            color: var(--gold);
            letter-spacing: .14em;
            text-transform: uppercase;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .card-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #fff;
            line-height: 1.15;
        }
        .card-heading h1 em {
            font-style: italic;
            color: var(--gold-light);
        }
        .gold-bar {
            width: 44px; height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin-top: 14px;
        }

        /* Info box */
        .info-box {
            background: rgba(212,160,23,0.07);
            border: 1px solid rgba(212,160,23,0.2);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: .85rem;
            color: #c8d0de;
            line-height: 1.6;
        }
        .info-box strong { color: var(--gold-light); }

        /* Label */
        .field-label {
            display: block;
            font-size: .72rem;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* Input */
        .field-input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 11px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            padding: 13px 16px;
            transition: border-color .2s, box-shadow .2s;
            letter-spacing: .5px;
        }
        .field-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,160,23,0.15);
            background: rgba(255,255,255,0.08);
        }
        .field-input::placeholder { color: rgba(255,255,255,0.25); }
        .field-input[readonly] {
            background: rgba(0,0,0,0.2);
            border-color: rgba(255,255,255,0.08);
            color: #8892A4;
            cursor: not-allowed;
        }

        /* OTP boxes */
        /* .otp-boxes {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 8px 0;
        }
        .otp-box {
            width: 52px; height: 58px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 11px;
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            text-align: center;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        } */

.otp-boxes {
    display: flex;
    gap: 8px;
    justify-content: center;
    width: 100%;
}
.otp-box {
    width: 14%; /* Scales with screen width */
    max-width: 50px;
    height: 55px;
    font-size: 1.2rem;
}

        .otp-box:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,160,23,0.18);
            background: rgba(255,255,255,0.09);
        }

        /* Hidden full OTP input (submitted with form) */
        #entered_otp { display: none; }

        /* Buttons */
        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            border: none;
            color: var(--navy);
            font-family: 'DM Sans', sans-serif;
            font-weight: 700;
            font-size: .9rem;
            letter-spacing: .07em;
            text-transform: uppercase;
            padding: 14px;
            border-radius: 11px;
            cursor: pointer;
            transition: transform .18s, box-shadow .18s;
            margin-top: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(212,160,23,0.35);
        }
        .btn-secondary {
            width: 100%;
            background: transparent;
            border: 1px solid var(--border);
            color: #8892A4;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: .85rem;
            padding: 12px;
            border-radius: 11px;
            cursor: pointer;
            transition: border-color .2s, color .2s;
            margin-top: 10px;
        }
        .btn-secondary:hover { border-color: var(--gold); color: var(--gold); }

        /* Toggle link */
        .toggle-link {
            display: inline-block;
            font-size: .78rem;
            color: var(--gold);
            cursor: pointer;
            text-decoration: underline;
            margin-top: 10px;
            background: none;
            border: none;
            padding: 0;
            font-family: 'DM Sans', sans-serif;
        }
        .toggle-link:hover { color: var(--gold-light); }

        /* Error / success banners */
        .banner {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: .85rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .banner-error {
            background: rgba(220,50,50,0.12);
            border: 1px solid rgba(220,50,50,0.35);
            color: #ff8a80;
        }
        .banner-success {
            background: rgba(50,180,100,0.12);
            border: 1px solid rgba(50,180,100,0.35);
            color: #6ee7a0;
        }

        /* OTP display box (dev mode) */
        .dev-otp-box {
            background: rgba(245,200,66,0.08);
            border: 1px dashed rgba(245,200,66,0.4);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: .82rem;
            color: var(--gold-light);
            margin-bottom: 20px;
            text-align: center;
        }
        .dev-otp-box strong { font-size: 1.5rem; letter-spacing: .15em; display: block; margin-top: 4px; }

        /* Sending mobile display */
        .mobile-display {
            font-size: .85rem;
            color: #8892A4;
            margin-bottom: 20px;
            text-align: center;
        }
        .mobile-display strong { color: #fff; letter-spacing: .08em; }

        /* Timer */
        .timer-text {
            text-align: center;
            font-size: .75rem;
            color: #8892A4;
            margin-top: 14px;
        }
        .timer-text span { color: var(--gold-light); font-weight: 600; }

        /* Field group spacing */
        .field-group { margin-bottom: 20px; }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 24px 0;
        }

        /* Logout link */
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: .75rem;
            color: #8892A4;
            text-decoration: none;
            position: relative;
            z-index: 1;
        }
        .logout-link:hover { color: #ff8a80; }

        @media (max-width: 480px) {
            /* .otp-card { padding: 32px 20px; }
             */

            .otp-card {
    width: 95%; /* Take most of the screen on mobile */
    max-width: 460px; /* Stay normal on desktop */
    padding: 30px 20px;
}
            .otp-box { width: 42px; height: 50px; font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<div class="sparkles" id="sparkles"></div>

<!-- Brand -->
<div class="brand-top">
    <div class="brand-icon">🎓</div>
    <div class="brand-text">
        <strong>Vignan's University</strong>
        <span>Foundation for Science, Technology &amp; Research</span>
    </div>
</div>

<div class="otp-card">

    <div class="card-heading">
        <small>Security Check</small>
        <h1>Verify <em>Your Number</em></h1>
        <div class="gold-bar"></div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="banner banner-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- ══ STEP 1: Mobile confirmation + Send OTP ══ -->
    <?php if (!$otp_sent): ?>

        <div class="info-box">
            We found the mobile number linked to your account. You can continue with it or enter a different number to receive your OTP.
        </div>

        <form method="POST" action="otp.php" id="sendForm">
            <input type="hidden" name="action" value="send_otp">

            <div class="field-group">
                <label class="field-label">Mobile Number</label>
                <input type="tel" class="field-input" name="mobile" id="mobileInput"
                       value="<?php echo htmlspecialchars($finalyearstudents_mobile); ?>"
                       maxlength="10" placeholder="10-digit mobile number"
                       <?php echo !empty($finalyearstudents_mobile) ? 'readonly' : ''; ?>>
            </div>

            <?php if (!empty($finalyearstudents_mobile)): ?>
                <button type="button" class="toggle-link" id="changeBtn"
                        onclick="enableEdit()">
                    ✏️ Use a different number
                </button>
                <br><br>
            <?php endif; ?>

            <button type="submit" class="btn-primary">📲 Send OTP</button>
        </form>

    <?php else: ?>

    <!-- ══ STEP 2: Enter OTP ══ -->

        <!-- DEV MODE: Show OTP on screen -->
        <div class="dev-otp-box">
            <!-- 🔧 Dev Mode — OTP sent to <?php echo htmlspecialchars('******' . substr($otp_mobile, -4));?> -->
            <strong><?php echo isset($_SESSION['otp']) ? $_SESSION['otp'] : '------'; ?></strong>
            <!-- <small style="display:block; margin-top:4px; color:#8892A4;">(Remove this box before going live)</small> -->
        </div>
        <?php $masked_mobile = '******' . substr($otp_mobile, -4); ?>
        <div class="mobile-display">
            OTP sent to <strong><?php echo htmlspecialchars($masked_mobile); ?></strong>
        </div>

        <form method="POST" action="otp.php" id="verifyForm">
            <input type="hidden" name="action" value="verify_otp">
            <input type="hidden" name="entered_otp" id="entered_otp">

            <div class="field-group">
                <label class="field-label" style="text-align:center; display:block;">Enter 6-digit OTP</label>
                <div class="otp-boxes">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]">
                </div>
            </div>

            <button type="submit" class="btn-primary">✅ Verify OTP</button>

            <div class="timer-text">
                OTP expires in <span id="countdown">02:00</span>
            </div>
        </form>

        <hr class="divider">

        <!-- Resend -->
        <!-- <form method="POST" action="otp.php">
            <input type="hidden" name="action" value="send_otp">
            <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($otp_mobile); ?>">
            <button type="submit" class="btn-secondary">🔄 Resend OTP</button>
        </form> -->


<!-- Resend -->
<form method="POST" action="otp.php" class="resend-form">
    <input type="hidden" name="action" value="send_otp">
    <input type="hidden" name="mobile" value="<?php echo htmlspecialchars($otp_mobile); ?>">
    <button type="submit" class="btn-secondary">🔄 Resend OTP</button>
</form>

    <?php endif; ?>

</div>

<a href="logout.php" class="logout-link">← Back to Login</a>

<script>
// Sparkles
const sp = document.getElementById('sparkles');
for (let i = 0; i < 18; i++) {
    const s = document.createElement('div');
    s.className = 'sparkle';
    s.style.left = Math.random() * 100 + '%';
    s.style.top  = Math.random() * 100 + '%';
    s.style.animationDelay    = (Math.random() * 4) + 's';
    s.style.animationDuration = (2.5 + Math.random() * 2) + 's';
    sp.appendChild(s);
}

// Enable editing mobile number
function enableEdit() {
    const input = document.getElementById('mobileInput');
    const btn   = document.getElementById('changeBtn');
    if (!input) return;
    input.removeAttribute('readonly');
    input.style.borderColor = '#D4A017';
    input.style.background  = 'rgba(255,255,255,0.08)';
    input.style.color       = '#fff';
    input.style.cursor      = 'text';
    input.focus();
    input.select();
    if (btn) btn.style.display = 'none';
}

// OTP box auto-advance + backspace
const otpBoxes = document.querySelectorAll('.otp-box');
otpBoxes.forEach(function(box, idx) {
    box.addEventListener('input', function() {
        // Only allow digits
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 1 && idx < otpBoxes.length - 1) {
            otpBoxes[idx + 1].focus();
        }
        collectOtp();
    });
    box.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && idx > 0) {
            otpBoxes[idx - 1].focus();
        }
    });
    // Allow paste on first box
    box.addEventListener('paste', function(e) {
        if (idx !== 0) return;
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g,'');
        otpBoxes.forEach(function(b, i) {
            b.value = pasted[i] || '';
        });
        collectOtp();
        // Focus last filled box
        const last = Math.min(pasted.length, otpBoxes.length) - 1;
        if (last >= 0) otpBoxes[last].focus();
    });
});

function collectOtp() {
    let val = '';
    otpBoxes.forEach(function(b) { val += b.value; });
    const hidden = document.getElementById('entered_otp');
    if (hidden) hidden.value = val;
}

// Verify form — ensure OTP collected before submit
const verifyForm = document.getElementById('verifyForm');
if (verifyForm) {
    verifyForm.addEventListener('submit', function(e) {
        collectOtp();
        const otp = document.getElementById('entered_otp').value;
        if (otp.length < 6) {
            e.preventDefault();
            alert('Please enter the complete 6-digit OTP.');
        }
    });
}

// Countdown timer (5 minutes)
// <?php if ($otp_sent): ?>
// const otpTime = <?php echo isset($_SESSION['otp_time']) ? $_SESSION['otp_time'] : time(); ?>;
   
// function updateCountdown() {
//     const elapsed = Math.floor(Date.now() / 1000) - otpTime;
//     const remaining = Math.max(300 - elapsed, 0);
//     const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
//     const secs = String(remaining % 60).padStart(2, '0');
//     const el = document.getElementById('countdown');
//     if (el) {
//         el.textContent = mins + ':' + secs;
//         if (remaining === 0) el.style.color = '#FF5733';
//     }
// }
// updateCountdown();
// setInterval(updateCountdown, 1000);
// <?php endif; ?>

// Countdown timer (2 minutes) + Resend lock
<?php if ($otp_sent): ?>

 const otpTime = <?php echo isset($_SESSION['otp_time']) ? $_SESSION['otp_time'] : time(); ?>;
const EXPIRY  = 120; // 2 minutes in seconds

// Disable resend button on load
const resendBtn = document.querySelector('.resend-form button');
if (resendBtn) {
    resendBtn.disabled = true;
    resendBtn.style.opacity  = '0.4';
    resendBtn.style.cursor   = 'not-allowed';
    resendBtn.title          = 'Available after OTP expires';
}

function updateCountdown() {
    const elapsed   = Math.floor(Date.now() / 1000) - otpTime;
    const remaining = Math.max(EXPIRY - elapsed, 0);
    const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
    const secs = String(remaining % 60).padStart(2, '0');
    const el = document.getElementById('countdown');

    if (el) {
        if (remaining > 0) {
            el.textContent = mins + ':' + secs;
            el.style.color = '';
        } else {
            el.textContent = 'Expired';
            el.style.color = '#FF5733';

            // Enable resend button once expired
            if (resendBtn) {
                resendBtn.disabled             = false;
                resendBtn.style.opacity        = '1';
                resendBtn.style.cursor         = 'pointer';
                resendBtn.title                = '';
                resendBtn.style.borderColor    = '#D4A017';
                resendBtn.style.color          = '#D4A017';
            }
        }
    }
}
updateCountdown();
setInterval(updateCountdown, 1000);
<?php endif; ?>


</script>

</body>
</html>