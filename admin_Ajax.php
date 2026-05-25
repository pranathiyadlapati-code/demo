<?php
/**
 * admin_Ajax101.php
 * AJAX backend for adminbcd.php
 */

session_start();
include 'connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// $action = trim($_GET['action'] ?? '');
$action = trim(isset($_GET['action']) ? $_GET['action'] : '');

function esc_f($conn, $v) {
    // return mysqli_real_escape_string($conn, trim($v ?? ''));
    return mysqli_real_escape_string($conn, trim(isset($v) ? $v : ''));
}

// $f_year   = esc_f($conn, $_GET['year']       ?? '');
// $f_bm     = esc_f($conn, $_GET['bm']         ?? '');
// $f_prog   = esc_f($conn, $_GET['program']    ?? '');
// $f_dept   = esc_f($conn, $_GET['department'] ?? '');
// $f_branch = esc_f($conn, $_GET['branch']     ?? '');
$f_year   = esc_f($conn, isset($_GET['year']) ? $_GET['year'] : '');
$f_bm     = esc_f($conn, isset($_GET['bm']) ? $_GET['bm'] : '');
$f_prog   = esc_f($conn, isset($_GET['program']) ? $_GET['program'] : '');
$f_dept   = esc_f($conn, isset($_GET['department']) ? $_GET['department'] : '');
$f_branch = esc_f($conn, isset($_GET['branch']) ? $_GET['branch'] : '');


// ══════════════════════════════════════════════════════════════════════════════
// ACTION: student_detail
// ══════════════════════════════════════════════════════════════════════════════
if ($action === 'student_detail') {
    // $regno = esc_f($conn, $_GET['regno'] ?? '');
    $regno = esc_f($conn, isset($_GET['regno']) ? $_GET['regno'] : '');

    if (!$regno) { echo json_encode(['error'=>'No regno']); exit; }

    $sd = [];
    $res = mysqli_query($conn, "SELECT * FROM slam_studetails WHERE user_id='$regno' LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) $sd = mysqli_fetch_assoc($res);

    $t = [];
    $res2 = mysqli_query($conn, "SELECT * FROM finalyearstudents WHERE regno='$regno' LIMIT 1");
    if ($res2 && mysqli_num_rows($res2) > 0) $t = mysqli_fetch_assoc($res2);

    $reg = [];
    $res3 = mysqli_query($conn, "SELECT name, nickname FROM slambook_reg WHERE regno='$regno' LIMIT 1");
    if ($res3 && mysqli_num_rows($res3) > 0) $reg = mysqli_fetch_assoc($res3);

    $opinions = [];
    $oq = mysqli_query($conn,
        "SELECT o.user_id AS author_id, o.opinion,
                COALESCE(s.name, o.user_id) AS author_name
         FROM slambook_opnion o
         LEFT JOIN slam_studetails s ON s.user_id = o.user_id
         WHERE o.frnd = '$regno'
         ORDER BY o.id ASC"
    );
    if ($oq) {
        while ($orow = mysqli_fetch_assoc($oq)) {
            $opinions[] = [
                'author_id'   => $orow['author_id'],
                'author_name' => $orow['author_name'],
                'opinion'     => $orow['opinion'],
            ];
        }
    }

    $out = [
        // 'regno'      => $regno,
        // 'name'       => $sd['name']       ?? $t['name']   ?? $reg['name'] ?? '',
        // 'nickname'   => $sd['nickname']   ?? $reg['nickname'] ?? '',
        // 'mobile'     => $sd['mobile']     ?? $t['mobile'] ?? '',
        // 'alt_mobile' => $sd['alt_mobile'] ?? '',
        // 'dob'        => $sd['dob']        ?? '',
        // 'year'       => $sd['year']       ?? '',
        // 'bm'         => $t['bm']          ?? '',
        // 'program'    => $t['program']     ?? $sd['course'] ?? '',
        // 'department' => $t['department']  ?? $sd['dept']   ?? '',
        // 'branch'     => $t['specialization'] ?? $sd['branch'] ?? '',
        // 'fav'        => $sd['fav']        ?? '',
        // 'extra'      => $sd['extra']      ?? '',
        // 'exam'       => $sd['exam']       ?? '',
        // 'company'    => $sd['company']    ?? '',
        // 'location'   => $sd['location']   ?? '',
        // 'photo_path' => $sd['photo_path'] ?? '',
        // 'opinions'   => $opinions,
        'regno'      => $regno,
'name'       => isset($sd['name']) ? $sd['name'] : (isset($t['name']) ? $t['name'] : (isset($reg['name']) ? $reg['name'] : '')),
'nickname'   => isset($sd['nickname']) ? $sd['nickname'] : (isset($reg['nickname']) ? $reg['nickname'] : ''),
'mobile'     => isset($sd['mobile']) ? $sd['mobile'] : (isset($t['mobile']) ? $t['mobile'] : ''),
'alt_mobile' => isset($sd['alt_mobile']) ? $sd['alt_mobile'] : '',
'dob'        => isset($sd['dob']) ? $sd['dob'] : '',
'year'       => isset($sd['year']) ? $sd['year'] : '',
'bm'         => isset($t['bm']) ? $t['bm'] : '',
'program'    => isset($t['program']) ? $t['program'] : (isset($sd['course']) ? $sd['course'] : ''),
'department' => isset($t['department']) ? $t['department'] : (isset($sd['dept']) ? $sd['dept'] : ''),
'branch'     => isset($t['specialization']) ? $t['specialization'] : (isset($sd['branch']) ? $sd['branch'] : ''),
'fav'        => isset($sd['fav']) ? $sd['fav'] : '',
'extra'      => isset($sd['extra']) ? $sd['extra'] : '',
'exam'       => isset($sd['exam']) ? $sd['exam'] : '',
'company'    => isset($sd['company']) ? $sd['company'] : '',
'location'   => isset($sd['location']) ? $sd['location'] : '',
'photo_path' => isset($sd['photo_path']) ? $sd['photo_path'] : '',
'opinions'   => $opinions,
    ];

    echo json_encode($out);
    exit;
}


// ─────────────────────────────────────────────────────────────────────────────
// Resolve regnos from finalyearstudents
// ─────────────────────────────────────────────────────────────────────────────
function getTestRegnos($conn, $f_bm, $f_prog, $f_dept, $f_branch) {
    if (!$f_bm && !$f_prog && !$f_dept && !$f_branch) return null;

    $where = [];
    if ($f_bm)     $where[] = "bm             = '$f_bm'";
    if ($f_prog)   $where[] = "program        = '$f_prog'";
    if ($f_dept)   $where[] = "department     = '$f_dept'";
    if ($f_branch) $where[] = "specialization = '$f_branch'";

    $sql = "SELECT DISTINCT regno FROM finalyearstudents WHERE " . implode(' AND ', $where);
    $res = mysqli_query($conn, $sql);
    if (!$res) return [];
    $regnos = [];
    while ($row = mysqli_fetch_row($res)) $regnos[] = $row[0];
    return $regnos;
}

// function inClause_f($regnos, $col, $prefix = 'AND') {
//     if ($regnos === null) return '';
//     if (empty($regnos))   return "$prefix 1=0";
//     $list = implode(',', array_map(fn($r) => "'" . $r . "'", $regnos));
//     return "$prefix $col IN ($list)";
// }


function inClause_f($regnos, $col, $prefix = 'AND') {
    if ($regnos === null) return '';
    if (empty($regnos))   return "$prefix 1=0";
    $list = implode(',', array_map(function($r) { return "'" . $r . "'"; }, $regnos));
    return "$prefix $col IN ($list)";
}

$test_regnos = getTestRegnos($conn, $f_bm, $f_prog, $f_dept, $f_branch);

$in_reg_regno  = inClause_f($test_regnos, 'r.regno');
$in_sd_user    = inClause_f($test_regnos, 'sd.user_id');
$in_ref_user   = inClause_f($test_regnos, 'ref.user_id');
$in_ef_id      = inClause_f($test_regnos, 'ef.id');

$sd_year_cond = $f_year ? "AND sd.year = '$f_year'" : '';

// $ef_year_cond = '';
// $ef_end_year  = 0;
// if ($f_year) {
//     $parts       = explode('-', $f_year);
//     // $ef_end_year = (int)($parts[1] ?? 0);
//     $ef_end_year = (int)(isset($parts[1]) ? $parts[1] : 0);
//     if ($ef_end_year) $ef_year_cond = "AND ef.passing_year = $ef_end_year";
// }


$ef_year_cond = '';
$ef_end_year  = 0;
if (!empty($f_year)) {
    $parts       = explode('-', $f_year);
    $ef_end_year = (int)(isset($parts[1]) ? $parts[1] : 0);
    if ($ef_end_year) {
        $ef_year_cond = "AND ef.passing_year = $ef_end_year";
    }
}


// ══════════════════════════════════════════════════════════════════════════════
// ACTION: stats
// ══════════════════════════════════════════════════════════════════════════════
if ($action === 'stats') {

    // 1. Total students
    if ($test_regnos === null) {
        $r = mysqli_query($conn, "SELECT COUNT(DISTINCT regno) AS c FROM finalyearstudents");
        $total_students = (int)mysqli_fetch_assoc($r)['c'];
    } else {
        $total_students = count($test_regnos);
    }

    // 2. Total registrations
    $sql = "SELECT COUNT(DISTINCT r.regno) AS c FROM slambook_reg r WHERE 1=1 $in_reg_regno";
    $total_reg = (int)mysqli_fetch_assoc(mysqli_query($conn, $sql))['c'];

    // 3. Details complete
    $sql = "SELECT COUNT(*) AS c FROM slam_studetails sd WHERE sd.is_complete=1 $sd_year_cond $in_sd_user";
    $details_complete = (int)mysqli_fetch_assoc(mysqli_query($conn, $sql))['c'];

    // 4. Reflection complete
    if ($f_year) {
        $sql = "SELECT COUNT(DISTINCT ref.user_id) AS c
                FROM slambook_reflection ref
                INNER JOIN slam_studetails sd ON sd.user_id=ref.user_id
                WHERE ref.is_complete=1 $sd_year_cond $in_ref_user";
    } else {
        $sql = "SELECT COUNT(*) AS c FROM slambook_reflection ref WHERE ref.is_complete=1 $in_ref_user";
    }
    $reflection_complete = (int)mysqli_fetch_assoc(mysqli_query($conn, $sql))['c'];

    // 5. Exit Feedback complete
    $sql = "SELECT COUNT(*) AS c FROM exitfeedback_draft ef WHERE ef.is_complete=1 $ef_year_cond $in_ef_id";
    $feedback_complete = (int)mysqli_fetch_assoc(mysqli_query($conn, $sql))['c'];

    // 6. Fully filled
    $sql = "SELECT COUNT(DISTINCT r.regno) AS c
            FROM slambook_reg r
            INNER JOIN slam_studetails sd ON sd.user_id=r.regno AND sd.is_complete=1 $sd_year_cond
            INNER JOIN slambook_reflection ref ON ref.user_id=r.regno AND ref.is_complete=1
            INNER JOIN exitfeedback_draft ef ON ef.id=r.regno AND ef.is_complete=1 $ef_year_cond
            WHERE 1=1 $in_reg_regno";
    $fully_filled = (int)mysqli_fetch_assoc(mysqli_query($conn, $sql))['c'];

    // 7. Partially filled
    $partially_filled = max(0, $total_reg - $fully_filled);

    // ── 8. Section-wise completion counts (how many users filled each group) ──
    $fb_group_fields = [
        'Curriculum & Faculty'      => [
            'curr_industry','curr_conceptual','curr_electives','curr_projects',
            'curr_innovation','curr_eval_fair','curr_improve','curr_assess',
            'curr_mentoring','curr_support','fac_knowledge','fac_methods','fac_approach'
        ],
        'Research & Infrastructure' => [
            'skill_train','skill_place','res_encourage','res_events','res_adequate',
            'infra_labs','infra_lib','infra_satisfy'
        ],
        'Admin & Digital'           => [
            'adm_smooth','adm_grievance','dig_moodle','dig_online',
            'out_technical','out_comm','cam_growth','cam_satisfy'
        ],
        'Overall & Comments'        => [
            'ov_quality','ov_career','ov_personal','ov_recomm','ov_exp'
        ],
    ];

    // $section_avgs = [];
    // foreach ($fb_group_fields as $label => $cols) {
    //     $not_null_check = implode(' AND ', array_map(fn($c) => "ef.$c IS NOT NULL", $cols));
    //     $sql_cnt = "
    //         SELECT COUNT(*) AS cnt
    //         FROM exitfeedback_draft ef
    //         WHERE ($not_null_check)
    //           $ef_year_cond
    //           $in_ef_id
    //     ";


$section_avgs = [];
    foreach ($fb_group_fields as $label => $cols) {
        $not_null_check = implode(' AND ', array_map(function($c) { return "ef." . $c . " IS NOT NULL"; }, $cols));
        $sql_cnt = "
            SELECT COUNT(*) AS cnt
            FROM exitfeedback_draft ef
            WHERE ($not_null_check)
              $ef_year_cond
              $in_ef_id
        ";

        $r_cnt = mysqli_query($conn, $sql_cnt);
        // $val   = $r_cnt ? (int)(mysqli_fetch_assoc($r_cnt)['cnt'] ?? 0) : 0;
        $tmp = mysqli_fetch_assoc($r_cnt);
$val = $r_cnt ? (int)(isset($tmp['cnt']) ? $tmp['cnt'] : 0) : 0;

        $section_avgs[$label] = $val;
    }

    echo json_encode([
        'total_students'      => $total_students,
        'total_reg'           => $total_reg,
        'fully_filled'        => $fully_filled,
        'partially_filled'    => $partially_filled,
        'details_complete'    => $details_complete,
        'reflection_complete' => $reflection_complete,
        'feedback_complete'   => $feedback_complete,
        'section_avgs'        => $section_avgs,
    ]);
    exit;
}


// ══════════════════════════════════════════════════════════════════════════════
// ACTION: fully
// ══════════════════════════════════════════════════════════════════════════════
if ($action === 'fully') {

    $sql = "
        SELECT
            r.regno,
            COALESCE(sd.name, t.name, '')       AS name,
            COALESCE(t.bm, '')                  AS bm,
            COALESCE(t.program, '')             AS program,
            COALESCE(t.department, '')          AS department,
            COALESCE(t.specialization, '')      AS branch,
            COALESCE(sd.year, '')               AS year,
            COALESCE(sd.mobile, '')             AS mobile,
            COALESCE(sd.alt_mobile, '')         AS alt_mobile,
            COALESCE(sd.dob, '')                AS dob,
            COALESCE(sd.fav, '')                AS fav,
            COALESCE(sd.extra, '')              AS extra,
            COALESCE(sd.exam, '')               AS exam,
            COALESCE(sd.company, '')            AS company,
            COALESCE(sd.location, '')           AS location,
            COALESCE(sd.photo_path, '')         AS photo_path,
            '1'                                 AS details_done,
            '1'                                 AS reflect_done,
            '1'                                 AS feedback_done
        FROM slambook_reg r
        LEFT JOIN finalyearstudents t ON t.regno = r.regno
        INNER JOIN slam_studetails sd
            ON sd.user_id = r.regno AND sd.is_complete = 1 $sd_year_cond
        INNER JOIN slambook_reflection ref
            ON ref.user_id = r.regno AND ref.is_complete = 1
        INNER JOIN exitfeedback_draft ef
            ON ef.id = r.regno AND ef.is_complete = 1 $ef_year_cond
        WHERE 1=1 $in_reg_regno
        ORDER BY t.department, t.specialization, r.regno
    ";

    $res  = mysqli_query($conn, $sql);
    $rows = [];
    if ($res) while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;

    echo json_encode($rows);
    exit;
}


// ══════════════════════════════════════════════════════════════════════════════
// ACTION: partial
// ══════════════════════════════════════════════════════════════════════════════
if ($action === 'partial') {

    if ($f_year) {
        $sql = "
            SELECT
                r.regno,
                COALESCE(t.name, '')            AS name,
                COALESCE(t.bm, '')              AS bm,
                COALESCE(t.program, '')         AS program,
                COALESCE(t.department, '')      AS department,
                COALESCE(t.specialization, '')  AS branch,
                COALESCE(sd_y.year, '')         AS year,
                COALESCE(sd_y.is_complete, 0)   AS details_done,
                COALESCE(ref.is_complete,  0)   AS reflect_done,
                COALESCE(ef.is_complete,   0)   AS feedback_done
            FROM slambook_reg r
            LEFT JOIN finalyearstudents t ON t.regno = r.regno
            LEFT JOIN slam_studetails sd_y ON sd_y.user_id=r.regno AND sd_y.year='$f_year'
            LEFT JOIN slambook_reflection ref ON ref.user_id=r.regno
            LEFT JOIN exitfeedback_draft ef ON ef.id=r.regno AND ef.passing_year=" . (int)$ef_end_year . "
            WHERE 1=1 $in_reg_regno
            AND NOT (
                COALESCE(sd_y.is_complete,0)=1
                AND COALESCE(ref.is_complete,0)=1
                AND COALESCE(ef.is_complete,0)=1
            )
            ORDER BY t.department, t.specialization, r.regno
        ";
    } else {
        $sql = "
            SELECT
                r.regno,
                COALESCE(sd.name, t.name, '')   AS name,
                COALESCE(t.bm, '')              AS bm,
                COALESCE(t.program, '')         AS program,
                COALESCE(t.department, '')      AS department,
                COALESCE(t.specialization, '')  AS branch,
                COALESCE(sd.year, '')           AS year,
                COALESCE(sd.is_complete,  0)    AS details_done,
                COALESCE(ref.is_complete, 0)    AS reflect_done,
                COALESCE(ef.is_complete,  0)    AS feedback_done
            FROM slambook_reg r
            LEFT JOIN finalyearstudents t  ON t.regno = r.regno
            LEFT JOIN slam_studetails sd  ON sd.user_id = r.regno
            LEFT JOIN slambook_reflection ref ON ref.user_id = r.regno
            LEFT JOIN exitfeedback_draft ef   ON ef.id = r.regno
            WHERE 1=1 $in_reg_regno
            AND NOT (
                COALESCE(sd.is_complete,0)=1
                AND COALESCE(ref.is_complete,0)=1
                AND COALESCE(ef.is_complete,0)=1
            )
            ORDER BY t.department, t.specialization, r.regno
        ";
    }

    $res  = mysqli_query($conn, $sql);
    $rows = [];
    if ($res) while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;

    echo json_encode($rows);
    exit;
}

echo json_encode(['error' => 'Unknown action: ' . htmlspecialchars($action)]);