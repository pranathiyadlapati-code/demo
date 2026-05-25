<?php
// session_start();
// if (!isset($_SESSION['SESSION_ID'])) {
//   header("Location: index.php");
//   die();
// } else if ($_SESSION['SESSION_ID'] == 'admin') {
//   header("Location: reportinit.php");
// }

// // Block direct access if OTP not yet verified
// if (isset($_SESSION['OTP'])) {
//   header("Location: otp.php");
//   die();
// }
// 11-05-2026

// REPLACE WITH:
session_start();
if (!isset($_SESSION['SESSION_ID'])) {
  header("Location:index.php");   // adjust path if needed
  die();
}



$questions = array(
  "Achievement of objectives through the programme",
  "Compatibility of curriculum for National competitive exams",
  "Improvement of skills by Internships, workshops, Modular and Value added courses offered",
  "Practical exposure integrated with the curriculum",
  "Academic advising/mentoring by the faculty",
  "Ease in access of faculty out of the class",
  "Adequacy and relevance of library reading material",
  "Effectiveness in Training activities and Placement cell functions",
  "Utility of resources (Internet / Library / Labs etc.,)",
  "Measures taken to develop interest on Innovative Projects and their guidance",
  "Effectiveness of Student Counselling system in helping students to improve their quality of life (Academic, Career and Social/Emotional development)",
  "Receiving Concrete advices / guidance on further plans from Counselor",
  "Satisfaction on the practise of assigning 20 students per counselor",
  "Effectiveness of your faculty counselor",
  "Your Inclination towards recommending VFSTR to your family and friends",
  "Overall, how do you rate your experience in VFSTR?",
  "Comfort in classrooms / Labs / Seminar Halls and Canteen",
  "Canteens Maintenance : (Cleanliness and Ambience)",
  "Affordability level of Prices in Canteen",
  "Access to sports equipment and the facilities available",
  "Availability of trainers/coaches for mentoring sports activities",
  "Provision of opportunities to function in Student Bodies",
  "Execution of optional clubs and Conduction of Cultural Activities",
  "Opportunities available to the students to nurture leadership and entrepreneurial skills",
  "Opportunities provided through conduction of activities within the institute.",
  "Opportunities provided to participate in activities organised by other institutions",
  "Effectiveness of trainers in enhancing employability and life skills",
  "Enough opportunities were available in the university to serve community at large (NSS/NCC etc.)",
  "Availability of various programs to achieve an overall development.",
  "Availability of university authorities to interact with the students for their problem/ feedback/ suggestions",
  "Financial support opportunities available for the students",
  "Institutes stand on taking environment friendly measures",
  "Maintenance of Communal Harmony through un-biased nature towards any specific group",
);

$index = array(
  "spos",  "ssolving",  "slow",  "sltp",  "selectives",  "stexpectations",  "scomposition",  "lab",  "project",  "innovativeproject",  "counhelp",  "concrete",  "assign",  "facultycounselor",  "recomm",  "overallrate",  "comfort",  "canteen",  "afford",  "access",  "trainers",  "oppo",  "exe",  "nuture",  "conduction",  "otherins",  "enhance",  "community",  "overalldevelop",  "prob",  "fin",  "envi",  "unbiased"
);

// 11-04-2026 config 
  include 'connect.php';
  // 30-04-2026
  // Fetch nickname
// 11-05-2026
// exitfeedbackusers table has passing_year column, we need it for saving drafts and final submission
// 11-05-2026 we fetch passing_year later in the code when needed, no need to fetch here in the beginning id place lo regno 
$sql_nick = "SELECT nickname, name FROM slambook_reg WHERE regno='{$_SESSION['SESSION_ID']}'";
$res_nick = mysqli_query($conn, $sql_nick);
$row_nick = mysqli_fetch_assoc($res_nick);
// $nickname = !empty($row_nick['nickname']) ? $row_nick['nickname'] : $row_nick['name'];
// 11-05-2026
// $nickname = !empty($row_nick['nickname']) ? $row_nick['nickname'] : ($row_nick['name'] ?? 'Student');
$nickname = !empty($row_nick['nickname']) ? $row_nick['nickname'] : (isset($row_nick['name']) ? $row_nick['name'] : 'Student');

$draft_data = [];
$resume_step = 0;

// Define the ID here so it's available for the queries below
$id = $_SESSION['SESSION_ID']; 


// 11-05-2026

// Fetch student details from  finalyearstudents table
$finalyearstudents_res = mysqli_query($conn, "SELECT * FROM  finalyearstudents WHERE regno='$id'");

$finalyearstudents_row = ($finalyearstudents_res && mysqli_num_rows($finalyearstudents_res) > 0) 
    ? mysqli_fetch_assoc($finalyearstudents_res) 
    : array();

$finalyearstudents_name           = isset($finalyearstudents_row['name']) ? $finalyearstudents_row['name'] : '';
$finalyearstudents_mobile         = isset($finalyearstudents_row['mobile']) ? $finalyearstudents_row['mobile'] : '';
$finalyearstudents_program        = isset($finalyearstudents_row['program']) ? $finalyearstudents_row['program'] : '';
$finalyearstudents_bm             = isset($finalyearstudents_row['bm']) ? $finalyearstudents_row['bm'] : '';
$finalyearstudents_department     = isset($finalyearstudents_row['department']) ? $finalyearstudents_row['department'] : '';
$finalyearstudents_specialization = isset($finalyearstudents_row['specialization']) ? $finalyearstudents_row['specialization'] : '';

$draft_res = mysqli_query($conn, "SELECT * FROM exitfeedback_draft WHERE id='$id'");
if ($draft_res && mysqli_num_rows($draft_res) > 0) {
    $draft_data = mysqli_fetch_assoc($draft_res);
    // 1. Resuming a Step
    // $resume_step = (int)(isset($draft_data['last_step']) ? $draft_data['last_step'] : 0);


// 12-05-2026

$resume_step = (int)(isset($draft_data['last_step']) ? $draft_data['last_step'] : 0);
if ($resume_step >= 5) $resume_step = 4;

}
$draft_json = json_encode($draft_data);



$id = $_SESSION['SESSION_ID'];

// ── AJAX draft save ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_draft') {
    header('Content-Type: application/json');
    $step = (int)$_POST['step'];

// 11-05-2026

    // $sql_p = "SELECT passing_year FROM exitfeedbackusers WHERE id='$id'";
    // $res_p = mysqli_query($conn, $sql_p);
    // $row_p = mysqli_fetch_assoc($res_p);
    // $passingYear = $row_p["passing_year"];

    $passingYear = date('Y');

    // Map: completed step index => fields that belong to that step
    // saveDraft(currentStep + 1) is called on Next, so completed step = $step - 1
    // saveDraft(currentStep - 1) is called on Back, so completed step = $step + 1
    // We only save fields for the step the user just ANSWERED (completed going forward).
    // On Back we just update last_step, no field changes needed.
    $step_fields = [
        0 => [], // Welcome - no questions
        1 => [
            'curr_industry','curr_conceptual','curr_electives','curr_projects','curr_innovation',
            'curr_eval_fair','curr_improve','curr_assess','curr_mentoring','curr_support',
            'fac_knowledge','fac_methods','fac_approach'
        ],
        2 => [
            'skill_train','skill_place','res_encourage','res_events','res_adequate',
            'infra_labs','infra_lib','infra_satisfy'
        ],
        3 => [
            'adm_smooth','adm_grievance','dig_moodle','dig_online',
            'out_technical','out_comm','cam_growth','cam_satisfy'
        ],
        4 => [
            'ov_quality','ov_career','ov_personal','ov_recomm','ov_exp'
        ],
    ];

    $text_fields = ['txt_strength','txt_improve','txt_placements','txt_academic','txt_comments'];

    // The step the user just completed = the step BEFORE the new step
    // (goNext calls saveDraft(currentStep + 1), goBack calls saveDraft(currentStep - 1))
    // We detect direction: if new $step > current last_step in db, user went forward
    // Simpler: always save fields of ($step - 1) when going forward (step increased).
    // On back, $step decreases - we just update last_step only.
    $completed_step = $step - 1; // step user just left going forward
    $fields_to_save = (isset($step_fields[$completed_step]) && $step > 0) ? $step_fields[$completed_step] : [];

    // Text fields only saved when leaving the final section (step 4)
    $save_texts = ($completed_step === 4);

    $set_parts = ["last_step=$step", "passing_year='$passingYear'"];

    foreach ($fields_to_save as $f) {
        if (isset($_POST[$f]) && $_POST[$f] !== '') {
            $set_parts[] = "`$f`=" . (int)$_POST[$f];
        }
    }

    if ($save_texts) {
        foreach ($text_fields as $tf) {
            if (isset($_POST[$tf])) {
                $v = mysqli_real_escape_string($conn, substr(trim(isset($_POST[$tf]) ? $_POST[$tf] : ''), 0, 255));
                $set_parts[] = "`$tf`='$v'";
            }
        }
    }

    $set_sql = implode(',', $set_parts);

    $exists = mysqli_query($conn, "SELECT id FROM exitfeedback_draft WHERE id='$id'");
    if (mysqli_num_rows($exists) > 0) {
        $r = mysqli_query($conn, "UPDATE exitfeedback_draft SET $set_sql WHERE id='$id'");
    } else {
        $cols = ['id','passing_year','last_step'];
        $vals = ["'$id'","'$passingYear'",$step];
        foreach ($fields_to_save as $f) {
            if (isset($_POST[$f]) && $_POST[$f] !== '') {
                $cols[] = "`$f`";
                $vals[] = (int)$_POST[$f];
            }
        }
        if ($save_texts) {
            foreach ($text_fields as $tf) {
                if (isset($_POST[$tf])) {
                    $v = mysqli_real_escape_string($conn, substr(trim(isset($_POST[$tf]) ? $_POST[$tf] : ''), 0, 255));
                    $cols[] = "`$tf`"; $vals[] = "'$v'";
                }
            }
        }
        $r = mysqli_query($conn, "INSERT INTO exitfeedback_draft (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")");
    }
    echo json_encode(['ok' => (bool)$r]);
    exit;
}

// ── Final submit ─────────────────────────────────────────────────────────────
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'final_submit') {
 
// // 11-05-2026

// // $sql_p = "SELECT passing_year FROM exitfeedbackusers WHERE id='$id'";
//     // $res_p = mysqli_query($conn, $sql_p);
//     // $row_p = mysqli_fetch_assoc($res_p);
//     // $passingYear = $row_p["passing_year"];

// $passingYear = date('Y');

//     $fields = [
//         'curr_industry','curr_conceptual','curr_electives','curr_projects','curr_innovation',
//         'curr_eval_fair','curr_improve','curr_assess','curr_mentoring','curr_support',
//         'fac_knowledge','fac_methods','fac_approach','skill_train','skill_place',
//         'res_encourage','res_events','res_adequate','infra_labs','infra_lib',
//         'infra_satisfy','adm_smooth','adm_grievance','dig_moodle','dig_online',
//         'out_technical','out_comm','cam_growth','cam_satisfy','ov_quality',
//         'ov_career','ov_personal','ov_recomm','ov_exp'
//     ];
//     $txt_fields = ['txt_strength','txt_improve','txt_placements','txt_academic','txt_comments'];

//     $check = mysqli_query($conn, "SELECT id FROM exitfeedback WHERE id='$id'");
//     if (mysqli_num_rows($check) > 0) {
//         echo "<script>alert('Already Submitted!');</script>";
//     } else {
//         $vals = ["'$id'", "'$passingYear'"];

//         foreach ($fields as $f) { 
//             $vals[] = isset($_POST[$f]) && $_POST[$f] !== '' ? (int)$_POST[$f] : 'NULL'; 
//         }

//         foreach ($txt_fields as $tf) {
//             $temp_val = isset($_POST[$tf]) ? $_POST[$tf] : '';
//             $words = preg_split('/\s+/', trim($temp_val), -1, PREG_SPLIT_NO_EMPTY);
//             if (count($words) > 255) $words = array_slice($words, 0, 255);
//             $vals[] = "'" . mysqli_real_escape_string($conn, implode(' ', $words)) . "'";
//         }
//         // $sql3 = "INSERT INTO exitfeedback VALUES(" . implode(",", $vals) . ")";
//         //  $sql3 = "INSERT INTO exitfeedback_draft VALUES(" . implode(",", $vals) . ")";
//         // if (mysqli_query($conn, $sql3)) {
//         //     mysqli_query($conn, "DELETE FROM exitfeedback_draft WHERE id='$id'");
//         //     echo "<script>if(confirm('Feedback Submitted Successfully!')){document.location.href='logout.php'};</script>";
//         // } else {
//         //     echo "<script>alert('Please Try Again!');</script>";
//         // }

// // modified now
//         $all_fields = array_merge(['passing_year'], $fields, $txt_fields);
// $all_vals   = array_merge(["'$passingYear'"], array_slice($vals, 2));

// $set_parts = [];
// foreach ($all_fields as $i => $f) {
//     $set_parts[] = "`$f`=" . $all_vals[$i];
// }
// $set_parts[] = "last_step=5";

// $sql3 = "UPDATE exitfeedback_draft SET " . implode(",", $set_parts) . " WHERE id='$id'";
// // 11-05-2026
// if (mysqli_query($conn, $sql3)) {

// // // 11-05-2026 logout
//     echo "<script>if(confirm('Feedback Submitted Successfully!')){document.location.href='checkdetails.php'};</script>";
// } else {
//     echo "<script>alert('Please Try Again!');</script>";
// }



// if (mysqli_query($conn, $sql3)) {
//     // Check all required fields answered
//     $required_fields = [
//         'curr_industry','curr_conceptual','curr_electives','curr_projects','curr_innovation',
//         'curr_eval_fair','curr_improve','curr_assess','curr_mentoring','curr_support',
//         'fac_knowledge','fac_methods','fac_approach','skill_train','skill_place',
//         'res_encourage','res_events','res_adequate','infra_labs','infra_lib',
//         'infra_satisfy','adm_smooth','adm_grievance','dig_moodle','dig_online',
//         'out_technical','out_comm','cam_growth','cam_satisfy','ov_quality',
//         'ov_career','ov_personal','ov_recomm','ov_exp'
//     ];
//     $all_answered = true;
//     foreach ($required_fields as $f) {
//         if (empty($_POST[$f])) { $all_answered = false; break; }
//     }
//     if ($all_answered) {
//         mysqli_query($conn, "UPDATE exitfeedback_draft SET is_complete=1 WHERE id='$id'");
//     }
//     echo "<script>alert('Feedback Submitted Successfully!'); document.location.href='checkdetails.php';</script>";
// } else {
//     echo "<script>alert('Please Try Again!');</script>";
// }



//     }
// }

// 12-05-2026 - updated final submit logic to handle both first time submission and updates, also added check to mark is_complete only when all required fields are answered, and removed deletion of draft to keep record of all submissions/updates in draft table

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'final_submit') {

    $passingYear = date('Y');

    $fields = [
        'curr_industry','curr_conceptual','curr_electives','curr_projects','curr_innovation',
        'curr_eval_fair','curr_improve','curr_assess','curr_mentoring','curr_support',
        'fac_knowledge','fac_methods','fac_approach','skill_train','skill_place',
        'res_encourage','res_events','res_adequate','infra_labs','infra_lib',
        'infra_satisfy','adm_smooth','adm_grievance','dig_moodle','dig_online',
        'out_technical','out_comm','cam_growth','cam_satisfy','ov_quality',
        'ov_career','ov_personal','ov_recomm','ov_exp'
    ];
    $txt_fields = ['txt_strength','txt_improve','txt_placements','txt_academic','txt_comments'];

    $set_parts = ["passing_year='$passingYear'", "last_step=5", "is_complete=1"];

    foreach ($fields as $f) {
        $set_parts[] = "`$f`=" . (isset($_POST[$f]) && $_POST[$f] !== '' ? (int)$_POST[$f] : 'NULL');
    }
    foreach ($txt_fields as $tf) {
        $temp_val = isset($_POST[$tf]) ? $_POST[$tf] : '';
        $words = preg_split('/\s+/', trim($temp_val), -1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > 255) $words = array_slice($words, 0, 255);
        $set_parts[] = "`$tf`='" . mysqli_real_escape_string($conn, implode(' ', $words)) . "'";
    }

    $set_sql = implode(',', $set_parts);

    $check = mysqli_query($conn, "SELECT id FROM exitfeedback_draft WHERE id='$id'");
    if (mysqli_num_rows($check) > 0) {
        $r = mysqli_query($conn, "UPDATE exitfeedback_draft SET $set_sql WHERE id='$id'");
    } else {
        $cols = ['id', 'passing_year', 'last_step', 'is_complete'];
        $vals = ["'$id'", "'$passingYear'", 5, 1];
        foreach ($fields as $f) {
            $cols[] = "`$f`";
            $vals[] = isset($_POST[$f]) && $_POST[$f] !== '' ? (int)$_POST[$f] : 'NULL';
        }
        foreach ($txt_fields as $tf) {
            $temp_val = isset($_POST[$tf]) ? $_POST[$tf] : '';
            $words = preg_split('/\s+/', trim($temp_val), -1, PREG_SPLIT_NO_EMPTY);
            if (count($words) > 255) $words = array_slice($words, 0, 255);
            $cols[] = "`$tf`";
            $vals[] = "'" . mysqli_real_escape_string($conn, implode(' ', $words)) . "'";
        }
        $r = mysqli_query($conn, "INSERT INTO exitfeedback_draft (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")");
    }

    // if ($r) {
    //     echo "<script>if(confirm('Feedback Submitted Successfully!')){document.location.href='checkdetails.php'};</script>";
    // } else {
    //     echo "<script>alert('Please Try Again!');</script>";
    // }


if ($r) {
        // CHECK ALL 3 COMPLETE
        $r1 = mysqli_query($conn, "SELECT is_complete FROM slambook_reflection WHERE user_id='$id' AND is_complete=1");
        $r2 = mysqli_query($conn, "SELECT is_complete FROM slam_studetails WHERE user_id='$id' AND is_complete=1");
        $r3 = mysqli_query($conn, "SELECT is_complete FROM exitfeedback_draft WHERE id='$id' AND is_complete=1");

        if (mysqli_num_rows($r1) > 0 && mysqli_num_rows($r2) > 0 && mysqli_num_rows($r3) > 0) {
            echo "<script>document.location.href='thankyou.php';</script>";
            exit;
        }

        echo "<script>if(confirm('Feedback Submitted Successfully!')){document.location.href='checkdetails.php'};</script>";
    } else {
        echo "<script>alert('Please Try Again!');</script>";
    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>vignan feedback</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Karla:wght@300;400;500;600&family=Cinzel:wght@400;600&display=swap" rel="stylesheet">
  <style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--burg:#5c1f2a;--burg2:#7a2e3a;--rose:#e8a0a0;--rose-dk:#c4706e;--blush:#fff0f0;--cream:#faf6f3;--gold:#c9a060;--border:rgba(184,140,140,0.22);--muted:#a07878;--text:#2a0f14}
body{background:var(--blush);font-family:'Karla',sans-serif;color:var(--text)}
.shell{max-width:740px;margin:0 auto;padding:16px}
.progress-bar{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:24px}
.step-dot{display:flex;flex-direction:column;align-items:center;gap:4px}
.step-circle{width:36px;height:36px;border-radius:50%;border:2px solid var(--border);background:var(--cream);display:flex;align-items:center;justify-content:center;font-family:'Cinzel',serif;font-size:12px;color:var(--muted);transition:all .3s}
.step-circle.active{border-color:var(--burg);background:var(--burg);color:#fff}
.step-circle.done{border-color:var(--rose-dk);background:var(--rose-dk);color:#fff}
.step-label{font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);font-family:'Cinzel',serif;text-align:center;max-width:70px}
.step-line{width:60px;height:1px;background:var(--border);margin:0 4px;margin-bottom:18px;transition:background .3s}
.step-line.done{background:var(--rose-dk)}
.card{background:var(--cream);border-radius:16px;box-shadow:0 4px 32px rgba(92,31,42,0.10);overflow:hidden}
.card-header{background:linear-gradient(135deg,var(--burg) 0%,var(--burg2) 60%,#9a4050 100%);padding:20px 28px}
.card-eyebrow{font-family:'Cinzel',serif;font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px}
.card-title{font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:600;color:#fff}
.card-body{padding:20px 28px}
.rating-scale{display:flex;justify-content:center;gap:6px;flex-wrap:wrap;margin-bottom:20px;padding:10px 0;border-bottom:1px solid var(--border)}
.scale-item{display:flex;align-items:center;gap:4px;font-size:11px;color:var(--muted)}
.scale-emoji{font-size:16px}
.q-block{margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:20px}
.q-block:last-child{border-bottom:none;margin-bottom:0}
.q-text{font-size:13.5px;font-weight:500;text-align:left;line-height:1.5;color:var(--text);flex:1;min-width:0}
.emoji-row{display:flex;justify-content:center;gap:4px;flex-shrink:0}
.emoji-btn{display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer;padding:6px 8px;border-radius:10px;border:1.5px solid transparent;background:none;transition:all .18s;min-width:44px}
.emoji-btn:hover{background:rgba(92,31,42,0.06);border-color:var(--border)}
.emoji-btn.selected{border-color:var(--rose-dk);background:rgba(196,112,110,0.1)}
.emoji-face{font-size:24px;line-height:1}
.emoji-label{font-size:9px;letter-spacing:0.5px;color:var(--muted);text-align:center;font-family:'Cinzel',serif}
.nav-row{display:flex;justify-content:space-between;align-items:center;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)}
.btn-nav{font-family:'Cinzel',serif;font-size:10px;letter-spacing:2px;text-transform:uppercase;padding:10px 24px;border-radius:10px;cursor:pointer;border:1.5px solid var(--border);background:var(--cream);color:var(--burg);transition:all .2s}
.btn-nav:hover{background:rgba(92,31,42,0.06)}
/* .btn-primary{background:linear-gradient(135deg,var(--burg) 0%,var(--burg2) 60%,#9a4050 100%);color:#fff;border:none;box-shadow:0 4px 16px rgba(92,31,42,0.22)} */
/* .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(92,31,42,0.28)} */


.btn-primary {
    background: linear-gradient(135deg, var(--burg) 0%, var(--burg2) 60%, #9a4050 100%) !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 4px 16px rgba(92,31,42,0.22);
    outline: none !important;
    transition: all 0.2s ease;
}

/* This keeps the color identical for hover, click (active), and focus */
.btn-primary:hover, 
.btn-primary:active, 
.btn-primary:focus {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(92,31,42,0.28);
    background: linear-gradient(135deg, var(--burg) 0%, var(--burg2) 60%, #9a4050 100%) !important;
    color: #fff !important;
}

.warn{font-size:11px;color:#c44040;font-family:'Cormorant Garamond',serif;font-style:italic;text-align:center;margin-top:6px;min-height:16px}
.success-box{text-align:center;padding:32px 24px}
.success-icon{font-size:52px;margin-bottom:16px}
.success-title{font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:600;color:var(--burg);margin-bottom:8px}
.success-sub{font-size:14px;color:var(--muted);line-height:1.7;font-style:italic}
.q-num{font-family:'Cinzel',serif;font-size:9px;letter-spacing:2px;color:var(--muted);text-align:left;margin-bottom:4px}

/* 30-04-2026 */
.top-bar { position: absolute; top: 0; right: 0; width: 100%; display: flex; justify-content: flex-end; padding: 16px 24px; pointer-events: none; }
.logout-btn { pointer-events: auto; font-family: 'Cinzel', serif; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; padding: 8px 16px; border-radius: 8px; cursor: pointer; border: 1.5px solid var(--border); background: var(--cream); color: var(--burg); text-decoration: none; transition: all .2s; box-shadow: 0 2px 10px rgba(92,31,42,0.05); }
.logout-btn:hover { background: rgba(92,31,42,0.06); }

/* 11-05-2026  padding top */ 
/* .shell{max-width:740px;margin:0 auto;padding:16px; padding-top: 80px;} */

.shell{max-width:740px;margin:0 auto;padding:16px; padding-top: 110px;}


/* Topbar nav */
.topbar-nav {
    display: flex;
    gap: 8px;
    align-items: center;
}

.nav-link {
    font-size: .78rem !important;
    font-weight: 800 !important;
    padding: 8px 14px;
    border-radius: 10px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    border: 2px solid rgba(212, 160, 23, 0.4);
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.05);
}

.nav-link.active {
    background-color: #D4A017 !important;
    color: #1A2744 !important;
    border-color: #D4A017;
    box-shadow: 0 4px 15px rgba(212, 160, 23, 0.3);
}

.nav-link:hover {
    background-color: #f5c842 !important;
    color: #1A2744 !important;
    transform: translateY(-1px);
}
@media (max-width: 768px) {
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
}
  .shell {
    padding-top: 150px !important;
  }

  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@300;400;500;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
</head>

<body>

<!-- 30-04-2026 sign out -->
<!-- <div class="top-bar"> -->
    <!-- 11-05-2026 logout -->
  <!-- <a href="checkdetails.php" class="logout-btn">Signing Off</a>
</div> -->
<!-- <div class="top-bar" style="
    background: #142038;
    border-bottom: 1px solid rgba(212,160,23,0.2);
    padding: 14px 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    pointer-events: auto;
"> -->
    <!-- Brand -->
    <!-- <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px;height:36px;background:#D4A017;border-radius:9px;display:grid;place-items:center;font-size:1.1rem;">🎓</div>
        <div>
            <strong style="display:block;font-size:.72rem;font-weight:600;color:#fff;letter-spacing:.07em;text-transform:uppercase;font-family:'Karla',sans-serif;">Vignan's University</strong>
            <span style="font-size:.62rem;color:#8892A4;font-family:'Karla',sans-serif;">Foundation for Science, Technology &amp; Research</span>
        </div>
    </div> -->


    <div style="
    background: #142038;
    border-bottom: 1px solid rgba(212,160,23,0.2);
    padding: 10px 16px;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    gap: 8px;
">
    <!-- Row 1: Brand + Nav -->
    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
        <!-- Brand -->
        <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
            <div style="width:32px;height:32px;background:#D4A017;border-radius:8px;display:grid;place-items:center;font-size:1rem;">🎓</div>
            <strong style="font-size:.65rem;font-weight:800;color:#fff;letter-spacing:.07em;text-transform:uppercase;font-family:'Karla',sans-serif;">Vignan's University</strong>
        </div>
        <!-- Nav buttons -->
        <div class="topbar-nav">
            <a href="checkdetails.php" class="nav-link">📖 Slam Book</a>
            <a href="feedback.php" class="nav-link active">📝 Exit Feedback</a>
        </div>
    </div>
    <!-- Row 2: Logged in + Logout -->
    <div style="display:flex; align-items:center; gap:8px; font-size:.72rem; color:#8892A4; font-family:'Karla',sans-serif;">
        Logged in as <strong style="color:#F5C842;"><?php echo htmlspecialchars(isset($_SESSION['SESSION_ID']) ? $_SESSION['SESSION_ID'] : ''); ?></strong>
        <a href="index.php" style="background:rgba(160,60,40,0.5);border:1px solid rgba(255,100,80,0.4);color:#fff;padding:4px 10px;border-radius:8px;font-size:.7rem;font-weight:600;text-decoration:none;" onmouseover="this.style.background='rgba(160,60,40,0.8)'" onmouseout="this.style.background='rgba(160,60,40,0.5)'">🚪 Logout</a>
    </div>
</div>

    <!-- Nav links -->
    <!-- <div style="display:flex; gap:8px; align-items:center;">
        <a href="checkdetails.php" style="
            color:#8892A4; text-decoration:none; font-size:.78rem; font-weight:600;
            letter-spacing:.06em; padding:7px 16px; border-radius:8px;
            border:1px solid transparent; transition:all .2s;
            font-family:'Karla',sans-serif;
        " onmouseover="this.style.color='#D4A017';this.style.borderColor='rgba(212,160,23,0.4)';this.style.background='rgba(212,160,23,0.07)'"
           onmouseout="this.style.color='#8892A4';this.style.borderColor='transparent';this.style.background='none'">
            📖 Slam Book
        </a>
        <a href="feedback.php" style="
            color:#D4A017; text-decoration:none; font-size:.78rem; font-weight:600;
            letter-spacing:.06em; padding:7px 16px; border-radius:8px;
            border:1px solid rgba(212,160,23,0.5); background:rgba(212,160,23,0.1);
            font-family:'Karla',sans-serif;
        ">
            📝 Exit Feedback
        </a>
    </div> -->


<?php
// Get the current page name (e.g., 'checkdetails.php' or 'feedback.php')
// $current_page = basename($_SERVER['PHP_SELF']);

// // Define styles for reuse
// $active_style = "color: #1A2744; background: #D4A017; border: 1px solid #D4A017; font-size: 1.05rem; font-weight: 800; letter-spacing: .06em; padding: 10px 22px; border-radius: 10px; text-decoration: none; font-family: 'Poppins', sans-serif; display: inline-flex; align-items: center; gap: 8px; transition: all .2s;";
// $normal_style = "color: #ffffff; background: rgba(255, 255, 255, 0.1); border: 2px solid rgba(255, 255, 255, 0.2); font-size: 1.05rem; font-weight: 800; letter-spacing: .06em; padding: 10px 22px; border-radius: 10px; text-decoration: none; font-family: 'Poppins', sans-serif; display: inline-flex; align-items: center; gap: 8px; transition: all .2s;";
?>

<!-- <div style="display:flex; gap:12px; align-items:center;">
    
    <a href="checkdetails.php" 
       style="<?php echo ($current_page == 'checkdetails.php') ? $active_style : $normal_style; ?>"
       onmouseover="this.style.transform='translateY(-2px)';"
       onmouseout="this.style.transform='translateY(0)';"
    >
        📖 Slam Book
    </a>

    <a href="feedback.php" 
       style="<?php echo ($current_page == 'feedback.php') ? $active_style : $normal_style; ?>"
       onmouseover="this.style.transform='translateY(-2px)';"
       onmouseout="this.style.transform='translateY(0)';"
    >
        📝 Exit Feedback
    </a>

</div>
     -->

     <div class="topbar-nav">
    <a href="checkdetails.php" class="nav-link">📖 Slam Book</a>
    <a href="feedback.php" class="nav-link active">📝 Exit Feedback</a>
</div>

    <!-- Logged in + back button -->
    <div style="display:flex; align-items:center; gap:10px; font-size:.75rem; color:#8892A4; font-family:'Karla',sans-serif;">
        Logged in as <span style="color:#F5C842; font-weight:500;"><?php echo htmlspecialchars(isset($_SESSION['SESSION_ID']) ? $_SESSION['SESSION_ID'] : ''); ?></span>
        <a href="index.php" style="
            background:rgba(160,60,40,0.5);
            border:1px solid rgba(255,100,80,0.4);
            color:#fff; padding:6px 16px; border-radius:8px;
            font-size:.75rem; font-weight:600; text-decoration:none;
            letter-spacing:.05em; transition:background .2s;
            font-family:'Karla',sans-serif;
        " onmouseover="this.style.background='rgba(160,60,40,0.8)'"
           onmouseout="this.style.background='rgba(160,60,40,0.5)'">
            ← Logout
        </a>
    </div>
</div>

<div class="shell">
  <div class="progress-bar" id="progressBar"></div>
  <div class="card" id="mainCard"></div>
</div>

<script>

const STUDENT_NICKNAME = "<?php echo addslashes(htmlspecialchars($nickname)); ?>";
// 11-05-2026
const STUDENT_DETAILS = {
    name:           "<?php echo addslashes(htmlspecialchars($finalyearstudents_name)); ?>",
    regno:          "<?php echo addslashes(htmlspecialchars($id)); ?>",
    mobile:         "<?php echo addslashes(htmlspecialchars($finalyearstudents_mobile)); ?>",
    program:        "<?php echo addslashes(htmlspecialchars($finalyearstudents_program)); ?>",
    bm:             "<?php echo addslashes(htmlspecialchars($finalyearstudents_bm)); ?>",
    department:     "<?php echo addslashes(htmlspecialchars($finalyearstudents_department)); ?>",
    specialization: "<?php echo addslashes(htmlspecialchars($finalyearstudents_specialization)); ?>"
};


// const RESUME_STEP = <?php echo $resume_step; ?>;
// const DRAFT = <?php echo $draft_json; ?>;
// const RESUME_STEP = <?php echo $resume_step; ?>;
// const ALREADY_SUBMITTED = <?php echo (isset($draft_data['last_step']) && (int)$draft_data['last_step'] >= 5) ? 'true' : 'false'; ?>;

// 12-05-2026

const RESUME_STEP = <?php echo $resume_step; ?>;
const DRAFT = <?php echo $draft_json; ?>;
const ALREADY_SUBMITTED = <?php echo (isset($draft_data['last_step']) && (int)$draft_data['last_step'] >= 5) ? 'true' : 'false'; ?>;


// 1. Updated Section Structure
const sections = [
    { key: 'welcome', title: 'Welcome', eyebrow: 'I', questions: [], isWelcome: true },
    { key: 'group1', title: 'Curriculum & Faculty', eyebrow: 'II', subSections: [
        { head: 'Curriculum & Assessment', questions: [
            {name:'curr_industry', text:'Curriculum is industry-relevant and up-to-date'},
            {name:'curr_conceptual', text:'Courses improved conceptual and practical understanding'},
            {name:'curr_electives', text:'Electives and flexibility supported my interests'},
            {name:'curr_projects', text:'Projects and internships enhanced learning'},
            {name:'curr_innovation', text:'Curriculum encouraged innovation and creativity'},
            {name:'curr_eval_fair', text:'Evaluation methods were fair and transparent'},
            {name:'curr_improve', text:'Feedback helped improve performance'},
            {name:'curr_assess', text:'Continuous assessment supported learning'},
            {name:'curr_mentoring', text:'Mentoring and guidance were effective'},
            {name:'curr_support', text:'Doubts and academic support were addressed timely'}
        ]},
        { head: 'Faculty & Teaching Quality', questions: [
            {name:'fac_knowledge', text:'Faculty demonstrated strong knowledge and clarity'},
            {name:'fac_methods', text:'Teaching methods were effective and engaging'},
            {name:'fac_approach', text:'Faculty were approachable and supportive'}
        ]}
    ]},
    { key: 'group2', title: 'Research & Infrastructure', eyebrow: 'III', subSections: [
        { head: 'Skill Development & Employability', questions: [
            {name:'skill_train', text:'Training programs improved employability skills'},
            {name:'skill_place', text:'Placement support and career guidance were effective'}
        ]},
        { head: 'Research & Innovation', questions: [
            {name:'res_encourage', text:'Research and innovation activities were encouraged'},
            {name:'res_events', text:'Exposure through events/hackathons was useful'},
            {name:'res_adequate', text:'Facilities and guidance for projects were adequate'}
        ]},
        { head: 'Infrastructure & Campus Facilities', questions: [
            {name:'infra_labs', text:'Laboratories and classrooms were adequate'},
            {name:'infra_lib', text:'Library and digital resources were useful'},
            {name:'infra_satisfy', text:'Campus facilities and internet were satisfactory'}
        ]}
    ]},
    { key: 'group3', title: 'Admin & Digital', eyebrow: 'IV', subSections: [
        { head: 'Administration', questions: [
            {name:'adm_smooth', text:'Administrative processes were smooth and transparent'},
            {name:'adm_grievance', text:'Student support and grievance handling were effective'}
        ]},
        { head: 'Digital Learning', questions: [
            {name:'dig_moodle', text:'VuMoodle supported learning'},
            {name:'dig_online', text:'Online resources enhanced self-learning'}
        ]},
        { head: 'Program Outcomes', questions: [
            {name:'out_technical', text:'Program improved technical and problem-solving skills'},
            {name:'out_comm', text:'Program enhanced communication and teamwork'}
        ]},
        { head: 'Campus Life', questions: [
            {name:'cam_growth', text:'Campus environment supported personal growth'},
            {name:'cam_satisfy', text:'Overall campus experience was satisfying'}
        ]}
    ]},
    { key: 'final', title: 'Overall & Comments', eyebrow: 'V', isFinal: true, subSections: [
        { head: 'Overall Satisfaction', questions: [
            {name:'ov_quality', text:'I am satisfied with the quality of education'},
            {name:'ov_career', text:'The university supported my career development'},
            {name:'ov_personal', text:'The institution supported my personal growth'},
            {name:'ov_recomm', text:'I would recommend this university to others'},
            {name:'ov_exp', text:'Overall, I am satisfied with my university experience'}
        ]}
    ]}
];

const emojis = [
    {v:1, face:'😞', label:'Strongly\nDisagree'},
    {v:2, face:'😕', label:'Disagree'},
    {v:3, face:'😐', label:'Neutral'},
    {v:4, face:'🙂', label:'Agree'},
    {v:5, face:'😄', label:'Strongly\nAgree'},
];

let currentStep = RESUME_STEP;
let textAnswers = {};
let answers = {};
let warnEl;

// ── DEFAULT ALL TO 5 FOR UI DISPLAY ONLY (not saved to DB until user clicks Next) ──
sections.forEach(sec => {
    if (sec.subSections) {
        sec.subSections.forEach(sub => {
            sub.questions.forEach(q => { answers[q.name] = 5; });
        });
    }
});

// ── Load saved draft answers (overrides defaults where user actually saved) ──
if (DRAFT && typeof DRAFT === 'object') {
  Object.keys(DRAFT).forEach(k => {
    if (DRAFT[k] !== null && DRAFT[k] !== '') {
      if (['txt_strength','txt_improve','txt_placements','txt_academic','txt_comments'].includes(k))
        textAnswers[k] = DRAFT[k];
      else if (!isNaN(DRAFT[k]))
        answers[k] = parseInt(DRAFT[k]);
    }
  });
}


function buildProgress() {
    const pb = document.getElementById('progressBar');
    if(!pb) return;
    pb.innerHTML = '';
    sections.forEach((s, i) => {
        const dot = document.createElement('div');
        dot.className = 'step-dot';
        const circ = document.createElement('div');
        circ.className = 'step-circle' + (i === currentStep ? ' active' : i < currentStep ? ' done' : '');
        circ.textContent = i < currentStep ? '✓' : (i + 1);
        const lbl = document.createElement('div');
        lbl.className = 'step-label';
        lbl.textContent = s.title;
        dot.appendChild(circ);
        dot.appendChild(lbl);
        pb.appendChild(dot);
        if (i < sections.length - 1) {
            const line = document.createElement('div');
            line.className = 'step-line' + (i < currentStep ? ' done' : '');
            pb.appendChild(line);
        }
    });
}

function buildSection(idx) {
    const sec = sections[idx];
    const card = document.getElementById('mainCard');
    if(!card) return;

    let html = `<div class="card-header">
        <div class="card-eyebrow">Section ${sec.eyebrow} of V</div>
        <div class="card-title">${sec.title}</div>
    </div><div class="card-body">`;
// 11-05-2026
    // if (sec.isWelcome) {
    //     html += `<div style="text-align:center;padding:24px 0;">
    //         <div style="font-size:52px;margin-bottom:18px;">🎓</div>
    //         <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:600;color:var(--burg);">Dear ${STUDENT_NICKNAME},</div>
    //         <p style="font-style:italic;color:var(--muted);">Welcome to the Exit Feedback Survey.</p>
    //     </div>;





if (sec.isWelcome) {
        // 1. ADDED: Big Personalized Greeting Header
        html += `
        <div style="text-align:center; padding: 10px 0 30px 0;">
            <div style="font-size:48px; margin-bottom:10px;">👋</div>
            <div style="font-family:'Cormorant Garamond', serif; font-size:34px; font-weight:600; color:var(--burg); line-height:1.2;">
                Hello, ${STUDENT_NICKNAME}!
            </div>
            <p style="font-size:16px; color:var(--muted); font-style:italic; margin-top:8px;">
                Welcome to your Exit Feedback. Please verify your details below.
            </p>
        </div>`;

        // 2. Your existing Student Details list logic
        html += `
        <div style="max-width:480px; margin:0 auto; background: rgba(92,31,42,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
            <div style="font-family:'Cinzel',serif; font-size:11px; letter-spacing:3px; text-transform:uppercase; color:maroon; margin-bottom:16px; text-align:center; padding-bottom:8px; font-weight: bold; border-bottom:1px solid var(--border);">
                University Record Profile
            </div>

            ${[
                {label:'Name', val:STUDENT_DETAILS.name, icon:'👤'},
                {label:'Reg No', val:STUDENT_DETAILS.regno, icon:'🎓'},
                {label:'Mobile', val:STUDENT_DETAILS.mobile, icon:'📱'},
                {label:'Program', val:STUDENT_DETAILS.program, icon:'📚'},
                {label:'UG / PG / PHD', val:STUDENT_DETAILS.bm, icon:'🏫'},
                {label:'Department', val:STUDENT_DETAILS.department, icon:'🏛️'},
                {label:'Specialization', val:STUDENT_DETAILS.specialization, icon:'⚡'},
            ].filter(f => f.val).map((f,i,arr) => `
                <div style="display:flex; align-items:flex-start; gap:14px; padding:12px 4px; border-bottom:${i < arr.length-1 ? '1px solid var(--border)' : 'none'};">
                    <span style="font-size:18px; flex-shrink:0; margin-top:2px;">${f.icon}</span>
                    <div style="flex:1; min-width:0;">
                        <div style="font-family:'Cinzel',serif; font-size:10px; letter-spacing:2px; text-transform:uppercase; color:maroon; font-weight: bold; margin-bottom:3px;">${f.label}</div>
                        <div style="font-size:13.5px; font-weight:600; color:var(--burg); word-break:break-word; line-height:1.5;">${f.val}</div>
                    </div>
                </div>
            `).join('')}
        </div>`;


// if (sec.isWelcome) {
// html += `
// <div style="
//     max-width:480px;
//     margin:0 auto;
//     background:var(--cream);
//     border-radius:14px;
//     border:1px solid var(--border);
//     overflow:hidden;
//     box-shadow:0 2px 16px rgba(92,31,42,0.07);
// ">
//     <div style="background:linear-gradient(135deg,var(--burg),var(--burg2));padding:12px 20px;">
//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,0.8);">Your Details</span>
//     </div>

//     ${[
//         {label:'Name', val:STUDENT_DETAILS.name, icon:'👤'},
//         {label:'Reg No', val:STUDENT_DETAILS.regno, icon:'🎓'},
//         {label:'Mobile', val:STUDENT_DETAILS.mobile, icon:'📱'},
//         {label:'Program', val:STUDENT_DETAILS.program, icon:'📚'},
//         {label:'UG / PG / PHD', val:STUDENT_DETAILS.bm, icon:'🏫'},
//         {label:'Department', val:STUDENT_DETAILS.department, icon:'🏛️'},
//         {label:'Specialization', val:STUDENT_DETAILS.specialization, icon:'⚡'},
//     ].filter(f => f.val).map((f,i,arr) => `
//         <div style="
//             display:flex;align-items:flex-start;gap:14px;
//             padding:13px 20px;
//             border-bottom:${i < arr.length-1 ? '1px solid var(--border)' : 'none'};
//             border-left:3px solid ${i%2===0 ? 'var(--burg)' : 'var(--rose-dk)'};
//         ">
//             <span style="font-size:16px;flex-shrink:0;margin-top:1px;">${f.icon}</span>
//             <div style="flex:1;min-width:0;">
//                 <div style="font-family:'Cinzel',serif;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:black;margin-bottom:3px;font-weight:600;">${f.label}</div>
//                 <div style="font-size:13.5px;font-weight:600;color:var(--text);word-break:break-word;line-height:1.5;">${f.val}</div>
//             </div>
//         </div>
//     `).join('')}
// </div>`;



// html += `
// <div style="max-width:480px;margin:0 auto;">
//     <div style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--burg);margin-bottom:16px;text-align:center;padding-bottom:8px;border-bottom:1px solid var(--border);">
//         Your Details
//     </div>

//     ${[
//         {label:'Name', val:STUDENT_DETAILS.name, icon:'👤'},
//         {label:'Reg No', val:STUDENT_DETAILS.regno, icon:'🎓'},
//         {label:'Mobile', val:STUDENT_DETAILS.mobile, icon:'📱'},
//         {label:'Program', val:STUDENT_DETAILS.program, icon:'📚'},
//         {label:'UG / PG / PHD', val:STUDENT_DETAILS.bm, icon:'🏫'},
//         {label:'Department', val:STUDENT_DETAILS.department, icon:'🏛️'},
//         {label:'Specialization', val:STUDENT_DETAILS.specialization, icon:'⚡'},
//     ].filter(f => f.val).map((f,i,arr) => `
//         <div style="
//             display:flex;
//             align-items:flex-start;
//             gap:14px;
//             padding:12px 4px;
//             border-bottom:${i < arr.length-1 ? '1px solid var(--border)' : 'none'};
//         ">
//             <span style="font-size:18px;flex-shrink:0;margin-top:2px;">${f.icon}</span>
//             <div style="flex:1;min-width:0;">
//                 <div style="font-family:'Cinzel',serif;font-size:8px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:3px;">${f.label}</div>
//                 <div style="font-size:13.5px;font-weight:600;color:var(--burg);word-break:break-word;line-height:1.5;">${f.val}</div>
//             </div>
//         </div>
//     `).join('')}
// </div>`;


// html += `
// <div style="
//     background:rgba(92,31,42,0.04);
//     border:1px solid var(--border);
//     border-radius:12px;
//     padding:20px 24px;
//     max-width:480px;
//     margin:0 auto;
//     text-align:left;
// ">
//     <div style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--burg);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);">
//         Your Details
//     </div>
//     <div style="display:grid;grid-template-columns:140px 1fr;row-gap:12px;align-items:start;">

//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">Name</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);word-break:break-word;">${STUDENT_DETAILS.name}</span>

//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">Reg No</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);">${STUDENT_DETAILS.regno}</span>

//         ${STUDENT_DETAILS.mobile ? `
//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">Mobile</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);">${STUDENT_DETAILS.mobile}</span>` : ''}

//         ${STUDENT_DETAILS.program ? `
//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">Program</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);">${STUDENT_DETAILS.program}</span>` : ''}

//         ${STUDENT_DETAILS.bm ? `
//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">UG / PG / PHD</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);">${STUDENT_DETAILS.bm}</span>` : ''}

//         ${STUDENT_DETAILS.department ? `
//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">Department</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);word-break:break-word;line-height:1.5;">${STUDENT_DETAILS.department}</span>` : ''}

//         ${STUDENT_DETAILS.specialization ? `
//         <span style="font-family:'Cinzel',serif;font-size:9px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding-top:2px;">Specialization</span>
//         <span style="font-size:13px;font-weight:600;color:var(--text);">${STUDENT_DETAILS.specialization}</span>` : ''}

//     </div>
// </div>`;


    } else {
        sec.subSections.forEach(sub => {
            html += `<div style="font-family:'Cinzel'; font-size:14px; color:var(--burg); margin-top:20px; border-bottom:1px solid var(--gold); padding-bottom:5px; font-weight: bold;">${sub.head}</div>`;
            sub.questions.forEach((q) => {
                const val = answers[q.name] || 0;
                html += `<div class="q-block" style="display:block; border-bottom:1px solid var(--border); padding:15px 0;">
                    <div class="q-text" style="margin-bottom:10px;">${q.text}</div>
                    <div class="emoji-row" id="row_${q.name}" style="display:flex; justify-content: space-between;">`;
                emojis.forEach(e => {
                    const sel = val === e.v ? 'selected' : '';
                    html += `<button type="button" class="emoji-btn ${sel}" onclick="pick('${q.name}',${e.v})">
                        <span class="emoji-face">${e.face}</span>
                        <span class="emoji-label" style="font-size:8px; font-weight: bold; display:block;">${e.label}</span>
                    </button>`;
                });
                html += `</div></div>`;
            });
        });

//         if (sec.isFinal) {
// html += `<div style="margin-top:30px;">
//     <div style="font-family:'Cinzel'; font-size:14px; color:var(--burg); margin-top:20px; border-bottom:1px solid var(--gold); padding-bottom:5px; font-weight: bold;">Open-Ended Feedback (max 255 characters each)</div>
// <div style="margin-bottom:15px;">
//                     <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">1. Major strengths of the university?</label>
//                     <textarea id="txt_strength" placeholder="e.g. Infrastructure, Faculty support, Campus life..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_strength')">${textAnswers['txt_strength'] || ''}</textarea>
//                     <div id="wc_txt_strength" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
//                 </div>

//                 <div style="margin-bottom:15px;">
//                     <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">2. Highest improvement area?</label>
//                     <textarea id="txt_improve" placeholder="e.g. Canteen variety, Lab equipment, Sports facilities..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_improve')">${textAnswers['txt_improve'] || ''}</textarea>
//                     <div id="wc_txt_improve" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
//                 </div>

//                 <div style="margin-bottom:15px;">
//                     <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">3. Suggestions for placements?</label>
//                     <textarea id="txt_placements" placeholder="e.g. More mock interviews, core company drives..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_placements')">${textAnswers['txt_placements'] || ''}</textarea>
//                     <div id="wc_txt_placements" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
//                 </div>

//                 <div style="margin-bottom:15px;">
//                     <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">4. Suggestions for academic quality?</label>
//                     <textarea id="txt_academic" placeholder="e.g. Interactive teaching, updated syllabus..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_academic')">${textAnswers['txt_academic'] || ''}</textarea>
//                     <div id="wc_txt_academic" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
//                 </div>

//                 <div style="margin-bottom:15px;">
//                     <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">5. Additional comments?</label>
//                     <textarea id="txt_comments" placeholder="Any other feedback you would like to share..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_comments')">${textAnswers['txt_comments'] || ''}</textarea>
//                     <div id="wc_txt_comments" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
//                 </div>
//             </div>`;
//         }


if (sec.isFinal) {
            html += `<div style="margin-top:30px;">
                <div style="font-family:'Cinzel'; font-size:14px; color:var(--burg); margin-top:20px; border-bottom:1px solid var(--gold); padding-bottom:10px; margin-bottom: 25px; font-weight: bold;">
                    Open-Ended Feedback (max 255 characters each)
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">1. Major strengths of the university?</label>
                    <textarea id="txt_strength" placeholder="e.g. Infrastructure, Faculty support, Campus life..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_strength')">${textAnswers['txt_strength'] || ''}</textarea>
                    <div id="wc_txt_strength" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">2. Highest improvement area?</label>
                    <textarea id="txt_improve" placeholder="e.g. Canteen variety, Lab equipment, Sports facilities..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_improve')">${textAnswers['txt_improve'] || ''}</textarea>
                    <div id="wc_txt_improve" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">3. Suggestions for placements?</label>
                    <textarea id="txt_placements" placeholder="e.g. More mock interviews, core company drives..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_placements')">${textAnswers['txt_placements'] || ''}</textarea>
                    <div id="wc_txt_placements" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">4. Suggestions for academic quality?</label>
                    <textarea id="txt_academic" placeholder="e.g. Interactive teaching, updated syllabus..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_academic')">${textAnswers['txt_academic'] || ''}</textarea>
                    <div id="wc_txt_academic" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:8px;">5. Additional comments?</label>
                    <textarea id="txt_comments" placeholder="Any other feedback you would like to share..." style="width:100%; height:70px; margin-bottom:4px; padding:10px; border-radius:8px; border:1px solid var(--border); font-family:inherit;" oninput="enforceWordLimit(this,'wc_txt_comments')">${textAnswers['txt_comments'] || ''}</textarea>
                    <div id="wc_txt_comments" style="font-size:10px;color:var(--muted);text-align:right;">0 / 255 characters</div>
                </div>
            </div>`;
        }


    }
// 11-05-2026
    html += `<div class="warn" id="warnMsg" style="color:red; font-size:12px; margin:10px 0; min-height:15px;"></div>
        <div class="nav-row" style="display:flex; justify-content:space-between; margin-top:20px;">
        ${idx > 0 ? `<button type="button" class="btn-nav" onclick="goBack()">← Back</button>` : `<div></div>`}
        <button type="button" class="btn-nav btn-primary" onclick="${idx === sections.length - 1 ? 'submitAll()' : 'goNext()'}">
          
        


        
        ${idx === sections.length - 1 ? (ALREADY_SUBMITTED ? '✓ Update Feedback' : 'Submit Feedback') : 'Next →'}
        
        
        
        
        
        
        
        
        </button>
    </div></div>`;

    card.innerHTML = html;
    warnEl = document.getElementById('warnMsg');
}

function pick(name, val) {
    answers[name] = val;
    const row = document.getElementById('row_' + name);
    if(row) {
        row.querySelectorAll('.emoji-btn').forEach((btn, i) => {
            btn.classList.toggle('selected', emojis[i].v === val);
        });
    }
    if (warnEl) warnEl.textContent = '';
}

function validate() {
    const sec = sections[currentStep];
    if (sec.isWelcome) return true;
    if (sec.subSections) {
        for (const sub of sec.subSections) {
            for (const q of sub.questions) {
                if (!answers[q.name]) {
                    if (warnEl) warnEl.textContent = 'Please answer all questions.';
                    return false;
                }
            }
        }
    }
    return true;
}

function goNext() {
    if (!validate()) return;
    collectTexts();
    // saveDraft with next step index — PHP will save fields of currentStep (completed step)
    saveDraft(currentStep + 1);
    currentStep++;
    buildProgress();
    buildSection(currentStep);
    window.scrollTo(0, 0);
}

function goBack() {
    collectTexts();
    // saveDraft with previous step index — PHP only updates last_step (no field changes on back)
    saveDraft(currentStep - 1);
    currentStep--;
    buildProgress();
    buildSection(currentStep);
    window.scrollTo(0, 0);
}

function submitAll() {
    if (!validate()) return;
    collectTexts();
    const form = document.createElement('form');
    form.method = 'POST'; form.action = 'feedback.php';
    const addH = (n,v) => { const i=document.createElement('input'); i.type='hidden'; i.name=n; i.value=v; form.appendChild(i); };
    addH('action','final_submit');
    Object.entries(answers).forEach(([k,v]) => addH(k,v));
    Object.entries(textAnswers).forEach(([k,v]) => addH(k,v));
    document.body.appendChild(form);
    form.submit();
}

function collectTexts() {
    ['txt_strength','txt_improve','txt_placements','txt_academic','txt_comments'].forEach(id => {
        const el = document.getElementById(id);
        if (el) textAnswers[id] = el.value;
    });
}

function enforceWordLimit(el, counterId) {
    if (el.value.length > 255) el.value = el.value.slice(0, 255);
    const c = document.getElementById(counterId);
    if (c) c.textContent = el.value.length + ' / 255 characters';
}

// ── KEY FIX: saveDraft only sends fields for the completed step ──
// PHP-side step_fields map decides which columns to write.
// JS sends ALL answers but PHP ignores anything not in that step's field list.
function saveDraft(step) {
    collectTexts();
    const body = new URLSearchParams();
    body.set('action', 'save_draft');
    body.set('step', step);
    Object.entries(answers).forEach(([k,v]) => body.set(k,v));
    Object.entries(textAnswers).forEach(([k,v]) => body.set(k,v));
    fetch('feedback.php', { method:'POST', body });
}

// Initial Run
buildProgress();
buildSection(currentStep);
</script>

</body>

</html>