<?php
session_start();
include 'connect.php';

$ADMIN_USER = 'admin';
$ADMIN_PASS = 'admin123';


if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
        if ($_POST['admin_user'] === $ADMIN_USER && $_POST['admin_pass'] === $ADMIN_PASS) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $login_error = true;
        }
    }
    if (!isset($_SESSION['admin_logged_in'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — Vignan Slam Book</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;background:#0d1628;display:flex;align-items:center;justify-content:center;font-family:'DM Sans',sans-serif;}
.card{background:rgba(255,255,255,.04);border:1px solid rgba(212,160,23,.25);border-radius:20px;padding:48px 40px;width:360px;box-shadow:0 24px 60px rgba(0,0,0,.5);}
.icon{width:52px;height:52px;background:#D4A017;border-radius:14px;display:grid;place-items:center;font-size:1.5rem;margin:0 auto 22px;}
h1{font-family:'Playfair Display',serif;color:#fff;font-size:1.7rem;text-align:center;margin-bottom:6px;}
small{display:block;color:#8892A4;font-size:.72rem;text-align:center;margin-bottom:28px;letter-spacing:.05em;}
label{display:block;font-size:.68rem;color:#D4A017;text-transform:uppercase;letter-spacing:.1em;font-weight:600;margin-bottom:6px;}
input[type=text],input[type=password]{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:10px;padding:12px 14px;font-size:.88rem;font-family:'DM Sans',sans-serif;margin-bottom:18px;transition:border-color .2s;}
input:focus{outline:none;border-color:#D4A017;box-shadow:0 0 0 3px rgba(212,160,23,.15);}
.btn{width:100%;padding:13px;background:linear-gradient(135deg,#D4A017,#F5C842);border:none;border-radius:10px;color:#0d1628;font-weight:700;font-size:.9rem;letter-spacing:.07em;text-transform:uppercase;cursor:pointer;transition:transform .15s,box-shadow .15s;}
.btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(212,160,23,.3);}
.error{background:rgba(255,80,60,.1);border:1px solid rgba(255,80,60,.3);color:#ff6b6b;border-radius:8px;padding:10px 14px;font-size:.78rem;margin-bottom:16px;text-align:center;}
</style>
</head>
<body>
<div class="card">
  <div class="icon">&#128737;</div>
  <h1>Admin Portal</h1>
  <small>VIGNAN'S UNIVERSITY &mdash; SLAM BOOK</small>
  <?php if (!empty($login_error)): ?>
  <div class="error">Invalid credentials. Please try again.</div>
  <?php endif; ?>
  <form method="post">
    <label>Username</label>
    <input type="text" name="admin_user" placeholder="admin" required>
    <label>Password</label>
    <input type="password" name="admin_pass" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
    <button class="btn" name="admin_login">Sign In &rarr;</button>
  </form>
</div>
</body>
</html>
<?php
        exit;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: adminc.php');
    exit;
}


$bm_options = array();
$r = mysqli_query($conn, "SELECT DISTINCT bm FROM finalyearstudents WHERE bm IS NOT NULL AND bm != '' ORDER BY bm");
while ($row = mysqli_fetch_row($r)) $bm_options[] = $row[0];

$year_options = array();
$r = mysqli_query($conn, "SELECT DISTINCT year FROM slam_studetails WHERE year IS NOT NULL AND year != '' AND year != '0' ORDER BY year DESC");
while ($row = mysqli_fetch_row($r)) $year_options[] = $row[0];

$r = mysqli_query($conn, "SELECT DISTINCT bm, program, department, specialization FROM finalyearstudents WHERE bm != '' ORDER BY bm, program, department, specialization");
$cascade_raw = array();
while ($row = mysqli_fetch_assoc($r)) {
    $bm   = $row['bm'];
    $prog = $row['program'];
    $dept = $row['department'];
    $spec = $row['specialization'];
    if (!isset($cascade_raw[$bm])) $cascade_raw[$bm] = array();
    if (!isset($cascade_raw[$bm][$prog])) $cascade_raw[$bm][$prog] = array();
    if (!isset($cascade_raw[$bm][$prog][$dept])) $cascade_raw[$bm][$prog][$dept] = array();
    if ($spec && !in_array($spec, $cascade_raw[$bm][$prog][$dept])) {
        $cascade_raw[$bm][$prog][$dept][] = $spec;
    }
}

$currentYear = (int)date('Y');
$defaultYear = ($currentYear - 1) . '-' . $currentYear;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard — Vignan Slam Book</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Space+Grotesk:wght@300;400;500;700&family=Bebas+Neue&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --gold:#D4A017;--gold-l:#F5C842;--navy:#0d1628;--navy-mid:#142038;
  --navy-light:#1A2744;--navy-card:rgba(255,255,255,.04);--border:rgba(212,160,23,.2);
  --text:#e0e7f0;--muted:#8892A4;--green:#22c55e;--red:#FF5733;--blue:#36A2EB;
}
body{font-family:'DM Sans',sans-serif;background:var(--navy);color:var(--text);min-height:100vh;}

#sparkles{position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0}
.sparkle{position:absolute;width:2px;height:2px;border-radius:50%;background:var(--gold);opacity:0;animation:twinkle var(--d,3s) var(--dl,0s) infinite;}
@keyframes twinkle{0%,100%{opacity:0;transform:scale(0)}50%{opacity:.45;transform:scale(1)}}

.page{position:relative;z-index:1;display:grid;grid-template-columns:240px 1fr;min-height:100vh;}

.sidebar{background:var(--navy-mid);border-right:1px solid var(--border);display:flex;flex-direction:column;position:sticky;top:0;height:100vh;overflow-y:auto;}
.sidebar-brand{padding:28px 24px 22px;border-bottom:1px solid var(--border);}
.sidebar-brand .icon{width:40px;height:40px;background:var(--gold);border-radius:11px;display:grid;place-items:center;font-size:1.2rem;margin-bottom:10px;}
.sidebar-brand strong{display:block;font-size:.72rem;color:#fff;letter-spacing:.08em;text-transform:uppercase;font-weight:700;}
.sidebar-nav{flex:1;padding:16px 12px;display:flex;flex-direction:column;gap:4px;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;font-size:.8rem;color:var(--muted);text-decoration:none;transition:all .2s;cursor:pointer;background:none;border:none;width:100%;text-align:left;}
.nav-item:hover,.nav-item.active{background:rgba(212,160,23,.1);color:var(--gold);}
.nav-sub{padding-left:28px;font-size:.75rem;}

.main{display:flex;flex-direction:column;}
.topbar{background:var(--navy-mid);border-bottom:1px solid var(--border);padding:16px 36px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.topbar-title{font-family:'Playfair Display',serif;font-size:1.25rem;color:#fff;}
.topbar-title em{color:var(--gold);font-style:italic;}
.admin-pill{background:rgba(212,160,23,.12);border:1px solid var(--border);border-radius:20px;padding:6px 14px;font-size:.7rem;color:var(--gold);font-weight:600;}
.logout-btn{font-size:.7rem;color:var(--muted);text-decoration:none;padding:6px 12px;border-radius:8px;border:1px solid rgba(255,255,255,.1);}

#loading-bar{display:none;position:fixed;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--gold),var(--gold-l),var(--gold));background-size:200% 100%;animation:loading-slide 1s linear infinite;z-index:9999;}
@keyframes loading-slide{0%{background-position:200% 0}100%{background-position:-200% 0}}

.content{padding:28px 36px;display:flex;flex-direction:column;gap:24px;}
.section-title{font-family:'Playfair Display',serif;font-size:1.1rem;color:#fff;display:flex;align-items:center;gap:10px;margin-bottom:16px;}
.section-title::after{content:'';flex:1;height:1px;background:linear-gradient(to right,var(--border),transparent);}

.global-filter{background:var(--navy-card);border:1px solid rgba(212,160,23,.35);border-radius:16px;padding:22px 28px;}
.global-filter-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:8px;}
.global-filter-top h3{font-family:'Playfair Display',serif;font-size:1rem;color:#fff;display:flex;align-items:center;gap:8px;}
.global-filter-top h3 em{color:var(--gold);font-style:italic;}
#filter-label{font-size:.72rem;color:var(--gold);background:rgba(212,160,23,.1);border:1px solid rgba(212,160,23,.3);border-radius:20px;padding:4px 14px;font-weight:600;letter-spacing:.04em;max-width:500px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:none;}
.filter-controls{display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:6px;}
.filter-group label{font-size:.62rem;color:var(--gold);text-transform:uppercase;letter-spacing:.12em;font-weight:600;}
.filter-group select{background:rgba(255,255,255,.07);border:1px solid var(--border);color:#fff;border-radius:10px;padding:9px 14px;font-size:.82rem;min-width:160px;cursor:pointer;transition:border-color .2s;font-family:'DM Sans',sans-serif;}
.filter-group select:focus{outline:none;border-color:var(--gold);}
.filter-group select:disabled{opacity:.4;cursor:not-allowed;}
.filter-group select option{background:#142038;color:#fff;}
.filter-year select{min-width:130px;}
.filter-actions{display:flex;gap:8px;padding-bottom:1px;}

.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.stat-card{background:var(--navy-card);border:1px solid var(--border);border-radius:16px;padding:24px 22px;position:relative;overflow:hidden;transition:transform .2s;}
.stat-card:hover{transform:translateY(-3px);}
.stat-label{font-size:.65rem;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);font-weight:600;margin-bottom:10px;}
.stat-num{font-family:'Playfair Display',serif;font-size:2.6rem;color:#fff;line-height:1;margin-bottom:6px;}
.stat-sub{font-size:.7rem;color:var(--muted);}
.stat-icon{position:absolute;top:18px;right:18px;font-size:1.5rem;opacity:.25;}

.charts-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
.chart-card{background:var(--navy-card);border:1px solid var(--border);border-radius:16px;padding:24px;}
.chart-card h3{font-size:.8rem;color:var(--gold);text-transform:uppercase;letter-spacing:.1em;margin-bottom:18px;}
.chart-wrap{position:relative;height:220px;}
.chart-counts{margin-top:12px;text-align:center;font-size:.75rem;display:flex;flex-wrap:wrap;justify-content:center;gap:10px;}
.chart-counts span{font-weight:500;}
.chart-counts .red{color:var(--red);}
.chart-counts .gold{color:var(--gold);}
.chart-counts .blue{color:var(--blue);}
.chart-counts .green{color:var(--green);}

.section-legend{margin-top:10px;display:flex;flex-wrap:wrap;justify-content:center;gap:6px;}
.section-legend .pill{font-size:.62rem;padding:2px 9px;border-radius:20px;font-weight:600;
    background:rgba(212,160,23,.1);border:1px solid rgba(212,160,23,.25);color:var(--gold);}
.section-legend .pill.top{background:rgba(34,197,94,.12);border-color:rgba(34,197,94,.35);color:#22c55e;}

.details-section{display:none;}
.details-card{background:var(--navy-card);border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:20px;}
.details-card h3{font-size:.8rem;color:var(--gold);text-transform:uppercase;letter-spacing:.1em;margin-bottom:20px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}

.search-wrap{max-width:300px;margin-bottom:20px;}
.search-wrap label{font-size:.62rem;color:var(--gold);text-transform:uppercase;letter-spacing:.12em;font-weight:600;display:block;margin-bottom:8px;}
.search-wrap input{width:100%;background:rgba(255,255,255,.05);border:1px solid var(--border);color:#fff;border-radius:8px;padding:10px 14px;font-size:.8rem;outline:none;font-family:'DM Sans',sans-serif;}
.search-wrap input:focus{border-color:var(--gold);}

.result-info{display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap;}
.table-scroll{overflow-x:auto;}
.data-table{width:100%;border-collapse:separate;border-spacing:0;font-size:.8rem;}
.data-table th{background:rgba(212,160,23,.1);color:var(--gold);text-transform:uppercase;letter-spacing:.08em;font-size:.63rem;padding:12px 16px;text-align:left;border-bottom:1px solid var(--border);font-weight:600;white-space:nowrap;}
.data-table td{padding:11px 16px;border-bottom:1px solid rgba(255,255,255,.04);color:#e0e7f0;vertical-align:middle;}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:rgba(212,160,23,.03);}
.data-table .mono{font-family:monospace;letter-spacing:.03em;font-size:.82rem;}
.data-table .num{color:var(--muted);}
.empty-msg{color:var(--muted);font-size:.82rem;padding:20px 0;text-align:center;}

.row-checkbox{width:16px;height:16px;accent-color:var(--gold);cursor:pointer;border-radius:4px;}
.data-table tr.row-unchecked td{opacity:.45;}
.data-table tr.row-unchecked{background:rgba(255,87,51,.03);}

.bulk-bar{
  display:flex;align-items:center;gap:14px;padding:14px 20px;
  background:rgba(212,160,23,.06);border:1px solid rgba(212,160,23,.25);
  border-radius:12px;margin-bottom:16px;flex-wrap:wrap;
}
.bulk-bar-left{display:flex;align-items:center;gap:14px;flex:1;flex-wrap:wrap;}
.bulk-sel-label{display:flex;align-items:center;gap:8px;font-size:.82rem;color:var(--text);cursor:pointer;user-select:none;font-weight:500;}
.bulk-sel-label input[type=checkbox]{width:17px;height:17px;accent-color:var(--gold);cursor:pointer;flex-shrink:0;}
.bulk-count{font-size:.75rem;color:var(--muted);padding:4px 12px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:20px;}
.bulk-count strong{color:var(--gold);}
.btn-bulk-zip{
  display:flex;align-items:center;gap:8px;
  background:linear-gradient(135deg,#D4A017,#F5C842);border:none;border-radius:10px;
  color:#0d1628;font-weight:700;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;
  padding:10px 20px;cursor:pointer;transition:transform .15s,box-shadow .15s,opacity .2s;
  white-space:nowrap;
}
.btn-bulk-zip:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 6px 20px rgba(212,160,23,.35);}
.btn-bulk-zip:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-bulk-zip .zip-icon{font-size:1rem;}
.btn-bulk-excel{
  display:flex;align-items:center;gap:8px;
  background:linear-gradient(135deg,#1a7a4a,#22c55e);border:none;border-radius:10px;
  color:#fff;font-weight:700;font-size:.78rem;letter-spacing:.06em;text-transform:uppercase;
  padding:10px 20px;cursor:pointer;transition:transform .15s,box-shadow .15s,opacity .2s;
  white-space:nowrap;
}
.btn-bulk-excel:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 6px 20px rgba(34,197,94,.35);}
.btn-bulk-excel:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-bulk-excel .xl-icon{font-size:1rem;}

.bulk-progress-overlay{
  display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);
  z-index:1500;align-items:center;justify-content:center;padding:20px;
}
.bulk-progress-overlay.open{display:flex;}
.bulk-progress-box{
  background:#0d1628;border:1px solid rgba(212,160,23,.3);
  border-radius:20px;width:100%;max-width:440px;padding:36px 32px;
  box-shadow:0 30px 80px rgba(0,0,0,.7);text-align:center;
}
.bp-icon{font-size:2.5rem;margin-bottom:14px;display:block;}
.bp-title{font-family:'Playfair Display',serif;font-size:1.2rem;color:#fff;margin-bottom:6px;}
.bp-sub{font-size:.78rem;color:var(--muted);margin-bottom:22px;}
.bp-bar-wrap{background:rgba(255,255,255,.07);border-radius:8px;height:8px;overflow:hidden;margin-bottom:12px;}
.bp-bar-fill{height:100%;background:linear-gradient(90deg,#D4A017,#F5C842);border-radius:8px;transition:width .4s ease;width:0%;}
.bp-progress-text{font-size:.75rem;color:var(--gold);font-weight:600;letter-spacing:.04em;}
.bp-student-name{font-size:.72rem;color:var(--muted);margin-top:8px;min-height:1.2em;font-style:italic;}
.bp-cancel{margin-top:20px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:var(--muted);border-radius:8px;padding:8px 20px;cursor:pointer;font-size:.78rem;transition:all .2s;font-family:'DM Sans',sans-serif;}
.bp-cancel:hover{background:rgba(255,80,60,.15);color:#ff6b6b;border-color:rgba(255,80,60,.3);}

.section-badges{display:flex;flex-direction:column;gap:5px;align-items:flex-start;}
.section-badge{display:inline-flex;align-items:center;gap:5px;border-radius:6px;padding:3px 10px;font-size:.7rem;font-weight:600;white-space:nowrap;min-width:110px;}
.section-badge.ok{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.35);color:#22c55e;}
.section-badge.no{background:rgba(255,87,51,.15);border:1px solid rgba(255,87,51,.35);color:#FF5733;}

.action-btns{display:flex;gap:6px;align-items:center;}
.btn-view{background:rgba(54,162,235,.15);border:1px solid rgba(54,162,235,.4);color:#36A2EB;border-radius:7px;padding:5px 12px;font-size:.72rem;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap;}
.btn-view:hover{background:rgba(54,162,235,.3);}
.btn-dl{background:rgba(212,160,23,.15);border:1px solid rgba(212,160,23,.4);color:var(--gold);border-radius:7px;padding:5px 12px;font-size:.72rem;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap;}
.btn-dl:hover{background:rgba(212,160,23,.3);}
.btn-dl:disabled,.btn-view:disabled{opacity:.5;cursor:not-allowed;}

.badge-blue{background:rgba(54,162,235,.15);border:1px solid rgba(54,162,235,.35);color:#36A2EB;border-radius:20px;padding:3px 12px;font-size:.7rem;font-weight:600;}
.badge-red{background:rgba(255,87,51,.15);border:1px solid rgba(255,87,51,.35);color:#FF5733;border-radius:20px;padding:3px 12px;font-size:.7rem;font-weight:600;}
.badge-gold{background:rgba(212,160,23,.15);border:1px solid rgba(212,160,23,.35);color:var(--gold);border-radius:20px;padding:3px 12px;font-size:.7rem;font-weight:600;}
.badge-green{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.35);color:#22c55e;border-radius:20px;padding:3px 12px;font-size:.7rem;font-weight:600;}
.badge-branch{background:rgba(54,162,235,.1);border:1px solid rgba(54,162,235,.3);color:#36A2EB;border-radius:6px;padding:3px 10px;font-size:.72rem;}
.badge-course{background:rgba(212,160,23,.1);border:1px solid rgba(212,160,23,.3);color:var(--gold);border-radius:6px;padding:3px 10px;font-size:.72rem;}

.pagination-bar{display:flex;align-items:center;justify-content:space-between;margin-top:14px;flex-wrap:wrap;gap:10px;}
.page-info{font-size:.75rem;color:var(--muted);}
.page-btns{display:flex;gap:8px;}

.btn-gold{background:linear-gradient(135deg,#D4A017,#F5C842);border:none;border-radius:10px;color:#0d1628;font-weight:700;font-size:.78rem;letter-spacing:.07em;text-transform:uppercase;padding:10px 22px;cursor:pointer;transition:transform .15s,box-shadow .15s;}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(212,160,23,.3);}
.btn-ghost{background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:10px;color:var(--muted);font-size:.78rem;padding:10px 18px;cursor:pointer;transition:background .2s;font-family:'DM Sans',sans-serif;}
.btn-ghost:hover{background:rgba(255,255,255,.1);}
.btn-ghost:disabled{opacity:.35;cursor:not-allowed;}

.sub-divider{border:none;border-top:1px solid var(--border);margin:4px 0 20px;}

.badges-row{display:flex;gap:10px;justify-content:center;margin:20px 0 40px;flex-wrap:wrap;}
.badge-item{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:7px 14px;font-size:.6rem;color:#8892A4;letter-spacing:.06em;text-align:center;line-height:1.5;}
.badge-item strong{color:var(--gold);display:block;font-size:.68rem;}

.dl-spinner{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:2000;align-items:center;justify-content:center;flex-direction:column;gap:16px;}
.dl-spinner.show{display:flex;}
.spinner-ring{width:48px;height:48px;border:4px solid rgba(212,160,23,.2);border-top-color:#D4A017;border-radius:50%;animation:spin .8s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}
.dl-spinner p{color:#fff;font-size:.85rem;}

.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:1000;align-items:center;justify-content:center;padding:20px;}
.modal-overlay.open{display:flex;}
.modal-box{background:#0d1628;border:1px solid rgba(212,160,23,.3);border-radius:20px;width:100%;max-width:820px;max-height:90vh;overflow-y:auto;box-shadow:0 30px 80px rgba(0,0,0,.7);}
.modal-header{display:flex;align-items:center;justify-content:space-between;padding:20px 28px;border-bottom:1px solid rgba(212,160,23,.15);}
.modal-header h2{font-family:'Playfair Display',serif;font-size:1.15rem;color:#fff;}
.modal-header h2 em{color:var(--gold);font-style:italic;}
.modal-close{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:var(--muted);border-radius:8px;padding:6px 12px;cursor:pointer;font-size:.8rem;transition:all .2s;}
.modal-close:hover{background:rgba(255,80,60,.2);color:#ff6b6b;border-color:rgba(255,80,60,.3);}
.modal-body{padding:0;}

/* ── BROADSHEET PROFILE CARD ─────────────────────────────────────────────── */
#profile-print-area{ background:#fff; position:relative; }

.bs-card{
  width:794px; min-height:1123px;
  background:url('slambook_template.jpg') no-repeat center top / 100% 100%;
  position:relative; display:flex; flex-direction:column;
  font-family:'Space Grotesk',sans-serif;
}
.bs-inner{
  padding:148px 50px 120px; flex:1;
  display:flex; flex-direction:column; gap:0;
}
/* stamp */
.bs-stamp{
  position:absolute; top:145px; right:56px;
  width:68px; height:68px; border-radius:50%;
  border:3px solid #D4820A; background:rgba(245,240,230,.9);
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  color:#D4820A; text-align:center; transform:rotate(-15deg);
  font-size:.42rem; letter-spacing:.1em; text-transform:uppercase;
  font-weight:700; line-height:1.4; z-index:10;
}
.bs-stamp-year{ font-size:.78rem; font-weight:700; }
/* masthead */
.bs-masthead{
  text-align:center; border-bottom:3px solid #111;
  padding-bottom:10px; margin-bottom:0;
}
.bs-mast-pre{ font-size:.52rem; letter-spacing:.3em; text-transform:uppercase; color:#6A5F50; margin-bottom:4px; }
.bs-mast-title{ font-family:'Bebas Neue',sans-serif; font-size:3.6rem; color:#111; letter-spacing:.12em; line-height:1; }
.bs-mast-sub{
  display:flex; justify-content:center; align-items:center;
  margin-top:6px; padding-top:6px; border-top:1px solid rgba(17,17,17,.15);
  font-size:.72rem; letter-spacing:.1em; color:#6A5F50; text-transform:uppercase;
}
.bs-mast-sub strong{ color:#D4820A; font-size:.9rem; letter-spacing:.12em; }
/* main grid */
.bs-grid{ display:grid; grid-template-columns:1fr 195px; flex:1; border-bottom:2px solid #111; }
.bs-col-left{ padding:18px 22px 18px 0; border-right:2px solid #111; }
.bs-col-right{ padding:18px 0 18px 16px; }
/* name block */
.bs-name-block{ margin-bottom:13px; padding-bottom:13px; border-bottom:2px solid #111; }
.bs-nb-byline{ font-size:.5rem; letter-spacing:.2em; text-transform:uppercase; color:#6A5F50; margin-bottom:4px; }
.bs-nb-name{ font-family:'Bebas Neue',sans-serif; font-size:2.5rem; color:#111; letter-spacing:.06em; line-height:1; }
.bs-nb-nick{ font-family:'Libre Baskerville',serif; font-style:italic; font-size:.95rem; color:#D4820A; margin-top:3px; margin-bottom:9px; }
.bs-tag-row{ display:flex; flex-wrap:wrap; gap:6px; }
.bs-tag{
  font-size:.65rem; letter-spacing:.1em; text-transform:uppercase; font-weight:700;
  padding:4px 12px; border:1.5px solid #111; color:#111;
}
.bs-tag-filled{ background:#111; color:#F5F0E8; }
/* info table */
.bs-info-table{ width:100%; border-collapse:collapse; margin-bottom:13px; }
.bs-info-table tr{ border-bottom:1px solid rgba(17,17,17,.15); }
.bs-info-table tr:last-child{ border-bottom:none; }
.bs-info-table td{ padding:7px 0; vertical-align:top; }
.bs-it-lbl{ font-size:.56rem; letter-spacing:.15em; text-transform:uppercase; color:black; font-weight:1000; width:100px; padding-right:90px; padding-top:7px; }
.bs-it-val{ font-size:.85rem; color:#2A2520; line-height:1.5; text-transform:uppercase; }
.bs-ep{ display:inline-block; font-size:.55rem; padding:1px 7px; margin:1px 2px 1px 0; border:1.5px solid #D4820A; color:#D4820A; font-weight:700; }
/* opinions */
.bs-op-head{ display:flex; align-items:baseline; gap:10px; margin-bottom:6px; border-top:2px solid #111; padding-top:11px; }
.bs-oh-title{ font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:#111; letter-spacing:.08em; }
.bs-oh-subtitle{ font-family:'Libre Baskerville',serif; font-style:italic; font-size:.8rem; color:#D4820A; }
.bs-oh-count{ margin-left:auto; font-size:.5rem; letter-spacing:.1em; text-transform:uppercase; background:#111; color:#F5F0E8; padding:2px 8px; font-weight:700; }
.bs-op-rule{ height:3px; background:repeating-linear-gradient(90deg,#111 0,#111 6px,transparent 6px,transparent 10px); margin-bottom:9px; }
.bs-op-card{ margin-bottom:9px; padding-bottom:9px; border-bottom:1px solid rgba(17,17,17,.15); }
.bs-op-card:last-child{ border-bottom:none; margin-bottom:0; }
.bs-oc-author-row{ display:flex; align-items:center; gap:7px; margin-bottom:5px; }
.bs-oc-ava{ width:24px; height:24px; border-radius:50%; font-size:.56rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:#111; color:#F5F0E8; }
.bs-oc-name{ font-size:.68rem; font-weight:700; color:#111; display:block; }
.bs-oc-id{ font-size:.52rem; color:#6A5F50; font-family:monospace; display:block; }
.bs-oc-text{ font-family:'Libre Baskerville',serif; font-style:italic; font-size:.77rem; color:#2A2520; line-height:1.65; }
.bs-op-empty{ text-align:center; padding:20px 12px; color:#6A5F50; font-size:.75rem; border:1px dashed rgba(17,17,17,.2); border-radius:4px; }
/* right sidebar */
.bs-rs-photo{ width:100%; height:225px; background:rgba(200,190,172,.3); border:1.5px solid #111; display:flex; align-items:center; justify-content:center; font-size:3.2rem; margin-bottom:11px; overflow:hidden; }
.bs-rs-photo img{ width:100%; height:100%; object-fit:cover; }
.bs-rs-section{ margin-bottom:11px; padding-bottom:11px; border-bottom:1px solid rgba(17,17,17,.15); }
.bs-rs-section:last-child{ border-bottom:none; }
.bs-rs-lbl{ font-size:.46rem; letter-spacing:.2em; text-transform:uppercase; color:#6A5F50; font-weight:700; margin-bottom:3px; }
.bs-rs-val{ font-size:.71rem; color:#2A2520; line-height:1.5; }
.bs-rs-batch{ margin-top:14px; padding:10px; background:#111; }
.bs-rs-batch-lbl{ font-size:.44rem; letter-spacing:.18em; text-transform:uppercase; color:rgba(255,255,255,.5); margin-bottom:4px; }
.bs-rs-batch-val{ font-family:'Bebas Neue',sans-serif; font-size:1.5rem; color:#F5F0E8; letter-spacing:.06em; line-height:1; }
/* footer */
.bs-footer{ padding:9px 0 0; display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(17,17,17,.15); }
.bs-ft{ font-size:.49rem; letter-spacing:.15em; text-transform:uppercase; color:#6A5F50; }
.bs-ft-ochre{ color:#D4820A; font-weight:700; }

.pc-modal-btns{padding:16px 28px;border-top:1px solid rgba(212,160,23,.15);display:flex;gap:10px;justify-content:flex-end;}
</style>
</head>
<body>

<div id="loading-bar"></div>
<div id="sparkles"></div>

<div class="dl-spinner" id="dl-spinner">
  <div class="spinner-ring"></div>
  <p id="dl-spinner-msg">Generating PDF&hellip;</p>
</div>

<div class="bulk-progress-overlay" id="bulk-progress-overlay">
  <div class="bulk-progress-box">
    <span class="bp-icon">&#128230;</span>
    <div class="bp-title">Generating Bulk ZIP</div>
    <div class="bp-sub" id="bp-sub">Preparing student PDFs&hellip;</div>
    <div class="bp-bar-wrap">
      <div class="bp-bar-fill" id="bp-bar-fill"></div>
    </div>
    <div class="bp-progress-text" id="bp-progress-text">0 / 0</div>
    <div class="bp-student-name" id="bp-student-name"></div>
    <button class="bp-cancel" id="bp-cancel-btn" onclick="cancelBulkDownload()">&#10005; Cancel</button>
  </div>
</div>

<div class="modal-overlay" id="profile-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h2>Student <em>Profile</em></h2>
      <button class="modal-close" onclick="closeModal()">&#10005; Close</button>
    </div>
    <div class="modal-body">
      <div id="profile-print-area"></div>
    </div>
    <div class="pc-modal-btns">
      <button class="btn-dl" id="modal-dl-btn" onclick="downloadCurrentModalPDF()">&#8595; Download PDF</button>
    </div>
  </div>
</div>

<div id="pdf-render-holder" style="position:fixed;left:-9999px;top:0;width:794px;z-index:-999;pointer-events:none;background:#fff;"></div>

<div class="page">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="icon">&#128737;</div>
      <strong>Admin Portal</strong>
    </div>
    <nav class="sidebar-nav">
      <a href="#filter-section"  class="nav-item active" onclick="setActive(this)">&#128269; Filter</a>
      <a href="#overview"        class="nav-item"        onclick="setActive(this)">&#128202; Overview</a>
      <a href="#charts"          class="nav-item"        onclick="setActive(this)">&#128200; Charts</a>
      <a href="#details-section" class="nav-item"        onclick="setActive(this)">&#128203; Student Details</a>
      <a href="#sec-fully"       class="nav-item nav-sub" onclick="setActive(this)">&#9989; Completely Filled</a>
      <a href="#sec-partial"     class="nav-item nav-sub" onclick="setActive(this)">&#9203; Partially Filled</a>
    </nav>
  </aside>

  <div class="main">

    <div class="topbar">
      <div class="topbar-title">Slam Book <em>Dashboard</em></div>
      <div style="display:flex;gap:10px;align-items:center;">
        <div class="admin-pill">&#128737; Admin</div>
        <a href="?logout=1" class="logout-btn">Sign Out &rarr;</a>
      </div>
    </div>

    <div class="content">

      <!-- FILTER -->
      <div id="filter-section" class="global-filter">
        <div class="global-filter-top">
          <h3>&#128269; Filter <em>Dashboard</em></h3>
          <span id="filter-label"></span>
        </div>
        <div class="filter-controls">
          <div class="filter-group filter-year">
            <label>Batch Year</label>
            <select id="gf-year">
              <option value="">&mdash; All Years &mdash;</option>
              <?php foreach ($year_options as $yo): ?>
              <option value="<?php echo htmlspecialchars($yo); ?>" <?php echo $yo===$defaultYear?'selected':''; ?>><?php echo htmlspecialchars($yo); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>PHD / PG / UG / Diploma</label>
            <select id="gf-bm">
              <option value="">&mdash; All &mdash;</option>
              <?php foreach ($bm_options as $bm): ?>
              <option value="<?php echo htmlspecialchars($bm); ?>"><?php echo htmlspecialchars($bm); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>Program</label>
            <select id="gf-program" disabled><option value="">&mdash; Select Level First &mdash;</option></select>
          </div>
          <div class="filter-group">
            <label>Department</label>
            <select id="gf-department" disabled><option value="">&mdash; Select Program First &mdash;</option></select>
          </div>
          <div class="filter-group">
            <label>Branch / Specialization</label>
            <select id="gf-branch" disabled><option value="">&mdash; Select Dept First &mdash;</option></select>
          </div>
          <div class="filter-actions">
            <button class="btn-gold"  onclick="applyFilter()">Apply Filter</button>
            <button class="btn-ghost" onclick="resetFilter()">Reset</button>
          </div>
        </div>
      </div>

      <!-- OVERVIEW -->
      <div id="overview">
        <div class="section-title">Overview</div>
        <div class="stats-grid">
          <div class="stat-card" style="border-color:rgba(212,160,23,.5)">
            <div class="stat-icon">&#127891;</div>
            <div class="stat-label">Total Students</div>
            <div class="stat-num" id="n-total-students">&mdash;</div>
            <div class="stat-sub">University records</div>
          </div>
          <div class="stat-card" style="border-color:rgba(139,92,246,.4)">
            <div class="stat-icon">&#128221;</div>
            <div class="stat-label">Total Registrations</div>
            <div class="stat-num" id="n-total-reg">&mdash;</div>
            <div class="stat-sub">Signed up on slam book</div>
          </div>
          <div class="stat-card" style="border-color:rgba(54,162,235,.4)">
            <div class="stat-icon">&#9989;</div>
            <div class="stat-label">Completely Filled</div>
            <div class="stat-num" id="n-fully">&mdash;</div>
            <div class="stat-sub" id="n-fully-sub">All 3 sections complete</div>
          </div>
          <div class="stat-card" style="border-color:rgba(255,87,51,.4)">
            <div class="stat-icon">&#9203;</div>
            <div class="stat-label">Partially Filled</div>
            <div class="stat-num" id="n-partial">&mdash;</div>
            <div class="stat-sub">One or more sections incomplete</div>
          </div>
        </div>
      </div>

      <!-- CHARTS -->
      <div id="charts">
        <div class="section-title">Visual Analytics</div>
        <div class="charts-row">

          <div class="chart-card">
            <h3>Slam Book Completion</h3>
            <div class="chart-wrap"><canvas id="slambookChart"></canvas></div>
            <div class="chart-counts">
              <span class="blue">&#9679; Details: <span id="cc-details">&mdash;</span></span>
              <span class="gold">&#9679; Reflections: <span id="cc-reflect">&mdash;</span></span>
              <span class="red">&#9679; Incomplete: <span id="cc-slambook-partial">&mdash;</span></span>
            </div>
          </div>

          <div class="chart-card">
            <h3>Exit Feedback Completion</h3>
            <div class="chart-wrap"><canvas id="feedbackChart"></canvas></div>
            <div class="chart-counts">
              <span class="green">&#9679; Completed: <span id="cc-feedback">&mdash;</span></span>
              <span class="red">&#9679; Pending: <span id="cc-feedback-partial">&mdash;</span></span>
            </div>
          </div>

          <div class="chart-card">
            <h3>Section Completion &mdash; No. of Students</h3>
            <div class="chart-wrap"><canvas id="sectionFeedbackChart"></canvas></div>
            <div class="section-legend" id="section-legend-pills"></div>
          </div>

        </div>
      </div>

      <!-- DETAILS -->
      <div id="details-section" class="details-section">

        <div class="section-title">Student Details</div>

        <div id="sec-fully" class="details-card">
          <h3>
            &#9989; Completely Filled
            <span class="badge-blue" id="fully-count-badge" style="font-family:'DM Sans',sans-serif;">&mdash;</span>
            <span style="font-size:.7rem;color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">(all 3 sections is_complete = 1)</span>
          </h3>
          <div class="search-wrap">
            <label>Quick Search</label>
            <input type="text" id="fully-search" placeholder="Reg No. or Name&hellip;">
          </div>
          <div id="fully-result"><p class="empty-msg">Apply a filter to load data.</p></div>
        </div>

        <hr class="sub-divider">

        <div id="sec-partial" class="details-card">
          <h3>
            &#9203; Partially Filled
            <span class="badge-red" id="partial-count-badge" style="font-family:'DM Sans',sans-serif;">&mdash;</span>
            <span style="font-size:.7rem;color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">(at least one section not complete)</span>
          </h3>
          <div class="search-wrap">
            <label>Quick Search</label>
            <input type="text" id="partial-search" placeholder="Reg No. or Name&hellip;">
          </div>
          <div id="partial-result"><p class="empty-msg">Apply a filter to load data.</p></div>
        </div>

      </div>

      <div class="badges-row">
        <div class="badge-item"><strong>NAAC</strong>A+ Grade</div>
        <div class="badge-item"><strong>NIRF</strong>70th Rank</div>
        <div class="badge-item"><strong>UGC</strong>Deemed University</div>
        <div class="badge-item"><strong>ISO</strong>Certified</div>
      </div>

    </div>
  </div>
</div>

<script>
var CASCADE_DATA = <?php echo json_encode($cascade_raw); ?>;
var PAGE_SIZE    = 10;
var AJAX_FILE    = 'admin_Ajax101.php';

var slambookChart, feedbackChart, sectionFeedbackChart;
var fullyData   = [], partialData   = [];
var fullyPage   = 0,   partialPage  = 0;
var fullySearch = '',  partialSearch= '';
var fullySpg    = 0,   partialSpg   = 0;

var bulkCancelled = false;
var _currentModalData = null;

// ── CHANGE 1: persistent selection sets ──────────────────────────────────────
var fullySelectedSet   = null;
var partialSelectedSet = null;
// ─────────────────────────────────────────────────────────────────────────────

var SECTION_LABELS = [
    'Curriculum & Faculty',
    'Research & Infrastructure',
    'Admin & Digital',
    'Overall & Comments'
];

var SECTION_COLORS = ['#36A2EB', '#22c55e', '#F5C842', '#FF5733'];

document.addEventListener('DOMContentLoaded', function() {
    initSparkles();
    initCharts();
    setupCascade();

    document.getElementById('fully-search').addEventListener('input', function() {
        fullySearch = this.value.toLowerCase().trim();
        fullySpg = 0; fullyPage = 0;
        renderFullyTable();
    });
    document.getElementById('partial-search').addEventListener('input', function() {
        partialSearch = this.value.toLowerCase().trim();
        partialSpg = 0; partialPage = 0;
        renderPartialTable();
    });

    loadStats({});
});

function initCharts() {
    Chart.defaults.color = '#8892A4';
    var GOLD='#D4A017', BLUE='#36A2EB', RED='#FF5733', GREEN='#22c55e';
    var dOpts = {
        responsive:true, maintainAspectRatio:false, cutout:'65%',
        plugins:{legend:{display:true,position:'bottom',labels:{boxWidth:10,font:{size:10}}}}
    };

    slambookChart = new Chart(document.getElementById('slambookChart'), {
        type:'bar',
        data:{
            labels:['Details Filled','Reflections Filled','Both Incomplete'],
            datasets:[{
                label:'Students',
                data:[0,0,0],
                backgroundColor:[BLUE, GOLD, RED],
                borderRadius:6
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{
                y:{beginAtZero:true, grid:{color:'rgba(255,255,255,.05)'}, ticks:{color:'#8892A4'}},
                x:{grid:{display:false}, ticks:{color:'#8892A4'}}
            }
        }
    });

    feedbackChart = new Chart(document.getElementById('feedbackChart'), {
        type:'doughnut',
        data:{
            labels:['Feedback Complete','Not Completed'],
            datasets:[{data:[0,0], backgroundColor:[GREEN,RED], borderColor:'#0d1628', borderWidth:3}]
        },
        options:dOpts
    });

    sectionFeedbackChart = new Chart(document.getElementById('sectionFeedbackChart'), {
        type: 'bar',
        data: {
            labels: SECTION_LABELS,
            datasets: [{
                label: 'Students who filled',
                data: [0,0,0,0],
                backgroundColor: SECTION_COLORS,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.parsed.x + ' student' + (ctx.parsed.x !== 1 ? 's' : '') + ' filled';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,.05)' },
                    ticks: { color: '#8892A4', stepSize: 1, precision: 0 }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#c8a84b', font: { size: 11 } }
                }
            }
        }
    });
}

function updateCharts(d) {
    var reg      = d.total_reg || 0;
    var details  = d.details_complete || 0;
    var reflect  = d.reflection_complete || 0;
    var feedback = d.feedback_complete || 0;
    var slambookPartial = Math.max(0, reg - Math.max(details, reflect));

    slambookChart.data.datasets[0].data = [details, reflect, slambookPartial];
    slambookChart.update();

    feedbackChart.data.datasets[0].data = [feedback, Math.max(0, reg - feedback)];
    feedbackChart.update();

    if (d.section_avgs) {
        var vals = [];
        for (var i = 0; i < SECTION_LABELS.length; i++) {
            vals.push(d.section_avgs[SECTION_LABELS[i]] || 0);
        }
        sectionFeedbackChart.data.datasets[0].data = vals;
        sectionFeedbackChart.update();

        var maxVal = Math.max.apply(null, vals);
        var pillsHtml = '';
        for (var j = 0; j < SECTION_LABELS.length; j++) {
            var v = vals[j];
            var isTop = (v === maxVal && v > 0);
            pillsHtml += '<span class="pill ' + (isTop ? 'top' : '') + '" style="border-color:' + SECTION_COLORS[j] + '33;color:' + SECTION_COLORS[j] + ';">' +
                SECTION_LABELS[j] + ': <strong>' + v + '</strong></span>';
        }
        document.getElementById('section-legend-pills').innerHTML = pillsHtml;
    }
}

function updateStats(d) {
    var reg = d.total_reg || 0;
    document.getElementById('n-total-students').textContent = d.total_students;
    document.getElementById('n-total-reg').textContent      = d.total_reg;
    document.getElementById('n-fully').textContent          = d.fully_filled;
    document.getElementById('n-partial').textContent        = d.partially_filled;

    var pct = reg > 0 ? Math.round(d.fully_filled / reg * 100) : 0;
    document.getElementById('n-fully-sub').textContent = pct + '% of registered completed all 3 sections';

    document.getElementById('cc-details').textContent          = d.details_complete;
    document.getElementById('cc-reflect').textContent          = d.reflection_complete;
    document.getElementById('cc-feedback').textContent         = d.feedback_complete || 0;
    document.getElementById('cc-slambook-partial').textContent = Math.max(0, reg - Math.max(d.details_complete||0, d.reflection_complete||0));
    document.getElementById('cc-feedback-partial').textContent = Math.max(0, reg - (d.feedback_complete||0));
}

function loadStats(f) {
    setLoading(true);
    var qs = '';
    var parts = [];
    for (var k in f) {
        if (f.hasOwnProperty(k)) parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(f[k]));
    }
    qs = parts.join('&');
    fetch(AJAX_FILE + '?action=stats' + (qs ? '&' + qs : ''))
        .then(function(r){ return r.json(); })
        .then(function(d){ updateStats(d); updateCharts(d); setLoading(false); })
        .catch(function(e){ console.error('Stats error:',e); setLoading(false); });
}

function loadDetails(f) {
    var parts = [];
    for (var k in f) {
        if (f.hasOwnProperty(k)) parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(f[k]));
    }
    var qs = parts.join('&');

    document.getElementById('fully-result').innerHTML = '<p class="empty-msg">Loading&hellip;</p>';
    document.getElementById('fully-count-badge').textContent = '&hellip;';
    document.getElementById('fully-search').value = '';
    fullySearch=''; fullySpg=0; fullyPage=0;
    fullySelectedSet = null; // CHANGE 2a

    fetch(AJAX_FILE + '?action=fully&' + qs)
        .then(function(r){ return r.json(); })
        .then(function(d){
            fullyData = Array.isArray(d) ? d : [];
            fullySelectedSet = new Set(fullyData.map(function(r){ return r.regno; })); // CHANGE 2b
            document.getElementById('fully-count-badge').textContent =
                fullyData.length + ' student' + (fullyData.length !== 1 ? 's' : '');
            renderFullyTable();
        })
        .catch(function(){ document.getElementById('fully-result').innerHTML='<p class="empty-msg">Error loading data.</p>'; });

    document.getElementById('partial-result').innerHTML = '<p class="empty-msg">Loading&hellip;</p>';
    document.getElementById('partial-count-badge').textContent = '&hellip;';
    document.getElementById('partial-search').value = '';
    partialSearch=''; partialSpg=0; partialPage=0;
    partialSelectedSet = null; // CHANGE 2c

    fetch(AJAX_FILE + '?action=partial&' + qs)
        .then(function(r){ return r.json(); })
        .then(function(d){
            partialData = Array.isArray(d) ? d : [];
            partialSelectedSet = new Set(partialData.map(function(r){ return r.regno; })); // CHANGE 2d
            document.getElementById('partial-count-badge').textContent =
                partialData.length + ' student' + (partialData.length !== 1 ? 's' : '');
            renderPartialTable();
        })
        .catch(function(){ document.getElementById('partial-result').innerHTML='<p class="empty-msg">Error loading data.</p>'; });
}

function getFilters(){
    return {
        year:       document.getElementById('gf-year').value,
        bm:         document.getElementById('gf-bm').value,
        program:    document.getElementById('gf-program').value,
        department: document.getElementById('gf-department').value,
        branch:     document.getElementById('gf-branch').value
    };
}

function applyFilter(){
    var f = getFilters();
    updateFilterLabel(f);
    loadStats(f);
    document.getElementById('details-section').style.display = 'block';
    loadDetails(f);
    setTimeout(function(){ document.getElementById('overview').scrollIntoView({behavior:'smooth',block:'start'}); }, 100);
}

function resetFilter(){
    document.getElementById('gf-year').value = '';
    document.getElementById('gf-bm').value = '';
    var ids = ['gf-program','gf-department','gf-branch'];
    for (var i = 0; i < ids.length; i++) {
        var s = document.getElementById(ids[i]);
        s.innerHTML = '<option value="">&mdash;</option>';
        s.disabled = true;
    }
    var lbl = document.getElementById('filter-label');
    lbl.textContent = ''; lbl.style.display = 'none';
    document.getElementById('details-section').style.display = 'none';
    loadStats({});
}

function updateFilterLabel(f){
    var parts = [];
    if(f.year) parts.push(f.year);
    if(f.bm) parts.push(f.bm);
    if(f.program) parts.push(f.program);
    if(f.department) parts.push(f.department);
    if(f.branch) parts.push(f.branch);
    var lbl = document.getElementById('filter-label');
    if(parts.length){ lbl.textContent = 'Showing: ' + parts.join(' \u203a '); lbl.style.display = 'inline-block'; }
    else{ lbl.textContent = ''; lbl.style.display = 'none'; }
}

function esc(s){
    if(s==null||s==='') return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function sectionBadges(r){
    var s = function(v){ return parseInt(v) === 1; };
    var badge = function(label, ok){ return '<span class="section-badge ' + (ok?'ok':'no') + '">' + (ok?'&#10003;':'&#10007;') + ' ' + label + '</span>'; };
    return '<div class="section-badges">' +
        badge('Details',  s(r.details_done)) +
        badge('Reflect',  s(r.reflect_done)) +
        badge('Feedback', s(r.feedback_done)) +
    '</div>';
}

// ── CHECKBOX HELPERS (Fully Filled) ──────────────────────────────────────────
function toggleAllFully(masterCb) {
    var cbs = document.querySelectorAll('.fully-row-check');
    for (var i = 0; i < cbs.length; i++) {
        cbs[i].checked = masterCb.checked;
        _styleRow(cbs[i]);
    }
    updateFullySelectedCount();
}

function onFullyRowCheck(cb) {
    // CHANGE 4: write back to Set using data-regno
    var regno = cb.getAttribute('data-regno');
    if (cb.checked) fullySelectedSet.add(regno); else fullySelectedSet.delete(regno);
    _styleRow(cb);
    var all  = document.querySelectorAll('.fully-row-check');
    var chkd = document.querySelectorAll('.fully-row-check:checked');
    var master = document.getElementById('fully-check-all');
    if (master) {
        master.indeterminate = chkd.length > 0 && chkd.length < all.length;
        master.checked = chkd.length === all.length && all.length > 0;
    }
    updateFullySelectedCount();
}

function _styleRow(cb) {
    var tr = cb.parentNode;
    while (tr && tr.tagName !== 'TR') tr = tr.parentNode;
    if (tr) {
        if (cb.checked) tr.classList.remove('row-unchecked');
        else tr.classList.add('row-unchecked');
    }
}

function updateFullySelectedCount() {
    // CHANGE 5: count from Set scoped to current search, not DOM checkboxes
    var src = fullySearch
        ? fullyData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(fullySearch)>-1||(r.name||'').toLowerCase().indexOf(fullySearch)>-1; })
        : fullyData;
    var checked = fullySelectedSet ? src.filter(function(r){ return fullySelectedSet.has(r.regno); }).length : 0;
    var total   = src.length;
    var el = document.getElementById('fully-selected-count');
    if (el) el.innerHTML = '<strong>' + checked + '</strong> of ' + total + ' selected';
    var btn = document.getElementById('fully-bulk-btn');
    if (btn) {
        btn.disabled = checked === 0;
        btn.innerHTML = checked > 0
            ? '<span class="zip-icon">&#128230;</span> Download ' + checked + ' PDF' + (checked>1?'s':'') + ' as ZIP'
            : '<span class="zip-icon">&#128230;</span> Bulk Download ZIP';
    }
}

// ── Fully filled table ───────────────────────────────────────────────────────
function renderFullyTable(){
    var box = document.getElementById('fully-result');
    var src = fullySearch
        ? fullyData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(fullySearch)>-1||(r.name||'').toLowerCase().indexOf(fullySearch)>-1; })
        : fullyData;
    var total = src.length;
    if(!total){ box.innerHTML='<p class="empty-msg">'+(fullySearch?'No matches found.':'No completely filled records for this filter.')+'</p>'; return; }
    var pg = fullySearch ? fullySpg : fullyPage;
    var pages = Math.ceil(total/PAGE_SIZE);
    var start = pg*PAGE_SIZE, end = Math.min(start+PAGE_SIZE,total);
    var slice = src.slice(start,end);

    var bulkBar = '<div class="bulk-bar">' +
      '<div class="bulk-bar-left">' +
        '<label class="bulk-sel-label">' +
          '<input type="checkbox" id="fully-check-all" checked onchange="toggleAllFully(this)">' +
          ' Select / Deselect All' +
        '</label>' +
        '<span class="bulk-count" id="fully-selected-count"><strong>' + slice.length + '</strong> of ' + slice.length + ' selected</span>' +
      '</div>' +
      '<div style="display:flex;gap:8px;flex-wrap:wrap;">' +
        '<button class="btn-bulk-excel" id="fully-excel-btn" onclick="exportToExcelFully()"><span class="xl-icon">&#128202;</span>Export Excel</button>' +
        '<button class="btn-bulk-zip" id="fully-bulk-btn" onclick="bulkDownloadZip()"><span class="zip-icon">&#128230;</span>Download ' + slice.length + ' PDF' + (slice.length>1?'s':'') + ' as ZIP</button>' +
      '</div>' +
    '</div>';

    var html = '<div class="result-info"><span class="badge-blue">' + total + ' student' + (total!==1?'s':'') + '</span></div>' +
    bulkBar +
    '<div class="table-scroll"><table class="data-table"><thead><tr>' +
      '<th style="width:36px;text-align:center;"><input type="checkbox" id="fully-check-all-hdr" checked style="accent-color:var(--gold);width:15px;height:15px;cursor:pointer;" onchange="document.getElementById(\'fully-check-all\').checked=this.checked;toggleAllFully(this);"></th>' +
      '<th>#</th><th>Reg No</th><th>Name</th><th>Level</th><th>Program</th><th>Department</th><th>Branch</th><th>Year</th><th>Sections</th><th>Actions</th>' +
    '</tr></thead><tbody>';

    for (var i = 0; i < slice.length; i++) {
        var r = slice[i];
        var rowJson = JSON.stringify(r).replace(/'/g,"&apos;");
        // CHANGE 3: read checked state from Set; add data-regno attribute
        var isChecked = fullySelectedSet && fullySelectedSet.has(r.regno);
        html += '<tr' + (isChecked ? '' : ' class="row-unchecked"') + '>' +
          '<td style="text-align:center;"><input type="checkbox" class="fully-row-check row-checkbox" ' + (isChecked ? 'checked' : '') + ' data-regno="' + esc(r.regno) + '" data-row=\'' + rowJson + '\' onchange="onFullyRowCheck(this)"></td>' +
          '<td class="num">' + (start+i+1) + '</td>' +
          '<td class="mono">' + esc(r.regno) + '</td>' +
          '<td>' + (esc(r.name)||'<span style="color:var(--muted)">&mdash;</span>') + '</td>' +
          '<td><span class="badge-gold">' + (esc(r.bm)||'&mdash;') + '</span></td>' +
          '<td><span class="badge-course">' + (esc(r.program)||'&mdash;') + '</span></td>' +
          '<td style="font-size:.78rem;max-width:180px;">' + (esc(r.department)||'&mdash;') + '</td>' +
          '<td>' + (r.branch ? '<span class="badge-branch">'+esc(r.branch)+'</span>' : '&mdash;') + '</td>' +
          '<td>' + (esc(r.year)||'&mdash;') + '</td>' +
          '<td>' + sectionBadges(r) + '</td>' +
          '<td><div class="action-btns">' +
            '<button class="btn-view" onclick=\'viewProfile(' + JSON.stringify(r) + ')\'>&#128065; View</button>' +
            '<button class="btn-dl"   onclick=\'downloadPDF(' + JSON.stringify(r) + ')\'>&#8595; PDF</button>' +
          '</div></td>' +
        '</tr>';
    }

    html += '</tbody></table></div>' +
    '<div class="pagination-bar">' +
      '<span class="page-info">Showing ' + (start+1) + '&ndash;' + end + ' of ' + total + ' &middot; Page ' + (pg+1) + '/' + pages + '</span>' +
      '<div class="page-btns">' +
        '<button class="btn-ghost" onclick="changeFullyPage(-1)" ' + (pg===0?'disabled':'') + '>&larr; Prev</button>' +
        '<button class="btn-ghost" onclick="changeFullyPage(1)"  ' + (pg>=pages-1?'disabled':'') + '>Next &rarr;</button>' +
      '</div>' +
    '</div>';

    box.innerHTML = html;

    var hdrCb = document.getElementById('fully-check-all-hdr');
    var barCb = document.getElementById('fully-check-all');
    if (hdrCb && barCb) {
        hdrCb.addEventListener('change', function(){ barCb.checked = hdrCb.checked; updateFullySelectedCount(); });
        barCb.addEventListener('change', function(){ hdrCb.checked = barCb.checked; });
    }
    updateFullySelectedCount();
}

function changeFullyPage(dir){
    if(fullySearch){
        var src = fullyData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(fullySearch)>-1||(r.name||'').toLowerCase().indexOf(fullySearch)>-1; });
        fullySpg = Math.max(0, Math.min(fullySpg+dir, Math.ceil(src.length/PAGE_SIZE)-1));
    } else {
        fullyPage = Math.max(0, Math.min(fullyPage+dir, Math.ceil(fullyData.length/PAGE_SIZE)-1));
    }
    renderFullyTable();
    document.getElementById('sec-fully').scrollIntoView({behavior:'smooth',block:'start'});
}

// ── CHECKBOX HELPERS (Partially Filled) ──────────────────────────────────────
function toggleAllPartial(masterCb) {
    var cbs = document.querySelectorAll('.partial-row-check');
    for (var i = 0; i < cbs.length; i++) {
        cbs[i].checked = masterCb.checked;
        _styleRow(cbs[i]);
    }
    updatePartialSelectedCount();
}

function onPartialRowCheck(cb) {
    // CHANGE 9: write back to Set using data-regno
    var regno = cb.getAttribute('data-regno');
    if (cb.checked) partialSelectedSet.add(regno); else partialSelectedSet.delete(regno);
    _styleRow(cb);
    var all  = document.querySelectorAll('.partial-row-check');
    var chkd = document.querySelectorAll('.partial-row-check:checked');
    var master = document.getElementById('partial-check-all');
    if (master) {
        master.indeterminate = chkd.length > 0 && chkd.length < all.length;
        master.checked = chkd.length === all.length && all.length > 0;
    }
    updatePartialSelectedCount();
}

// function updatePartialSelectedCount() {
//     var total   = document.querySelectorAll('.partial-row-check').length;
//     var checked = document.querySelectorAll('.partial-row-check:checked').length;
//     var el  = document.getElementById('partial-selected-count');
//     if (el) el.innerHTML = '<strong>' + checked + '</strong> of ' + total + ' selected';
//     var btn = document.getElementById('partial-excel-btn');
//     if (btn) btn.disabled = checked === 0;
// }
function updatePartialSelectedCount() {

    var src = partialSearch
        ? partialData.filter(function(r){
            return (r.regno||'').toLowerCase().indexOf(partialSearch)>-1 ||
                   (r.name||'').toLowerCase().indexOf(partialSearch)>-1;
        })
        : partialData;

    var checked = partialSelectedSet
        ? src.filter(function(r){
            return partialSelectedSet.has(r.regno);
        }).length
        : 0;

    var total = src.length;

    var el = document.getElementById('partial-selected-count');

    if (el) {
        el.innerHTML =
            '<strong>' + checked + '</strong> of ' + total + ' selected';
    }

    var btn = document.getElementById('partial-bulk-btn');

    if (btn) {
        btn.disabled = checked === 0;

        btn.innerHTML = checked > 0
            ? '<span class="zip-icon">&#128230;</span> Download ' +
              checked + ' PDF' + (checked > 1 ? 's' : '') + ' as ZIP'
            : '<span class="zip-icon">&#128230;</span> Bulk Download ZIP';
    }
}

// ── Partial table ────────────────────────────────────────────────────────────
function renderPartialTable(){
    var box = document.getElementById('partial-result');
    var src = partialSearch
        ? partialData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(partialSearch)>-1||(r.name||'').toLowerCase().indexOf(partialSearch)>-1; })
        : partialData;
    var total = src.length;
    if(!total){ box.innerHTML='<p class="empty-msg">'+(partialSearch?'No matches found.':'No partially filled records for this filter.')+'</p>'; return; }
    var pg = partialSearch ? partialSpg : partialPage;
    var pages = Math.ceil(total/PAGE_SIZE);
    var start = pg*PAGE_SIZE, end = Math.min(start+PAGE_SIZE,total);
    var slice = src.slice(start,end);

    var bulkBar = '<div class="bulk-bar">' +
      '<div class="bulk-bar-left">' +
        '<label class="bulk-sel-label">' +
          '<input type="checkbox" id="partial-check-all" checked onchange="toggleAllPartial(this)">' +
          ' Select / Deselect All' +
        '</label>' +
        '<span class="bulk-count" id="partial-selected-count"><strong>' + slice.length + '</strong> of ' + slice.length + ' selected</span>' +
      '</div>' +
      '<button class="btn-bulk-excel" id="partial-excel-btn" onclick="exportToExcelPartial()"><span class="xl-icon">&#128202;</span>Export Excel</button>' +
    '</div>';

    var html = '<div class="result-info"><span class="badge-red">' + total + ' student' + (total!==1?'s':'') + '</span></div>' +
    bulkBar +
    '<div class="table-scroll"><table class="data-table"><thead><tr>' +
      '<th style="width:36px;text-align:center;"><input type="checkbox" id="partial-check-all-hdr" checked style="accent-color:var(--gold);width:15px;height:15px;cursor:pointer;" onchange="document.getElementById(\'partial-check-all\').checked=this.checked;toggleAllPartial(this);"></th>' +
      '<th>#</th><th>Reg No</th><th>Name</th><th>Level</th><th>Branch</th><th>Year</th><th>Missing Sections</th>' +
    '</tr></thead><tbody>';

    for (var i = 0; i < slice.length; i++) {
        var r = slice[i];
        var rowJson = JSON.stringify(r).replace(/'/g,"&apos;");
        var missing = [];
        if(parseInt(r.details_done)!==1)  missing.push('Details');
        if(parseInt(r.reflect_done)!==1)  missing.push('Reflections');
        if(parseInt(r.feedback_done)!==1) missing.push('Exit Feedback');
        var missingHtml = '';
        for (var m = 0; m < missing.length; m++) {
            missingHtml += '<span class="badge-red" style="margin-right:4px;">' + missing[m] + '</span>';
        }
        // CHANGE 8: read checked state from Set; add data-regno attribute
        var isChecked = partialSelectedSet && partialSelectedSet.has(r.regno);
        html += '<tr' + (isChecked ? '' : ' class="row-unchecked"') + '>' +
          '<td style="text-align:center;"><input type="checkbox" class="partial-row-check row-checkbox" ' + (isChecked ? 'checked' : '') + ' data-regno="' + esc(r.regno) + '" data-row=\'' + rowJson + '\' onchange="onPartialRowCheck(this)"></td>' +
          '<td class="num">' + (start+i+1) + '</td>' +
          '<td class="mono">' + esc(r.regno) + '</td>' +
          '<td>' + (esc(r.name)||'<span style="color:var(--muted)">&mdash;</span>') + '</td>' +
          '<td><span class="badge-gold">' + (esc(r.bm)||'&mdash;') + '</span></td>' +
          '<td>' + (r.branch ? '<span class="badge-branch">'+esc(r.branch)+'</span>' : '<span style="color:var(--muted)">&mdash;</span>') + '</td>' +
          '<td>' + (esc(r.year)||'&mdash;') + '</td>' +
          '<td>' + missingHtml + '</td>' +
        '</tr>';
    }

    html += '</tbody></table></div>' +
    '<div class="pagination-bar">' +
      '<span class="page-info">Showing ' + (start+1) + '&ndash;' + end + ' of ' + total + ' &middot; Page ' + (pg+1) + '/' + pages + '</span>' +
      '<div class="page-btns">' +
        '<button class="btn-ghost" onclick="changePartialPage(-1)" ' + (pg===0?'disabled':'') + '>&larr; Prev</button>' +
        '<button class="btn-ghost" onclick="changePartialPage(1)"  ' + (pg>=pages-1?'disabled':'') + '>Next &rarr;</button>' +
      '</div>' +
    '</div>';

    box.innerHTML = html;

    var hdrCb = document.getElementById('partial-check-all-hdr');
    var barCb = document.getElementById('partial-check-all');
    if (hdrCb && barCb) {
        hdrCb.addEventListener('change', function(){ barCb.checked = hdrCb.checked; updatePartialSelectedCount(); });
        barCb.addEventListener('change', function(){ hdrCb.checked = barCb.checked; });
    }
    updatePartialSelectedCount();
}

function changePartialPage(dir){
    if(partialSearch){
        var src = partialData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(partialSearch)>-1||(r.name||'').toLowerCase().indexOf(partialSearch)>-1; });
        partialSpg = Math.max(0, Math.min(partialSpg+dir, Math.ceil(src.length/PAGE_SIZE)-1));
    } else {
        partialPage = Math.max(0, Math.min(partialPage+dir, Math.ceil(partialData.length/PAGE_SIZE)-1));
    }
    renderPartialTable();
    document.getElementById('sec-partial').scrollIntoView({behavior:'smooth',block:'start'});
}

// ── Profile card HTML builder (Editorial Broadsheet) ─────────────────────────
function buildProfileHTML(d) {
    var _e = function(s) {
        if (s == null) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    };
    var v = function(k) { return _e(d[k] || ''); };

    var year    = v('year');
    var dept    = v('department') || v('branch') || '';
    var stampYr = year ? (year.indexOf('-') > -1 ? year.split('-')[1] : year) : new Date().getFullYear();

    // ── Photo
    var photoInner = d.photo_path
        ? '<img src="' + _e(d.photo_path) + '" crossorigin="anonymous" alt="Photo" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentNode.innerHTML=\'&#128100;\'">'
        : '&#128100;';

    // ── Tags row
    var tags = '';
    if (d.bm)      tags += '<span class="bs-tag bs-tag-filled">' + v('bm') + '</span>';
    if (d.program) tags += '<span class="bs-tag">' + v('program') + '</span>';
    if (d.branch)  tags += '<span class="bs-tag">' + v('branch') + '</span>';
    if (d.year)    tags += '<span class="bs-tag">Batch ' + year + '</span>';

    // ── Info table rows
    var info = '';
    if (d.regno)      info += '<tr><td class="bs-it-lbl">Reg. No.</td><td class="bs-it-val">' + v('regno') + '</td></tr>';
    if (d.dob)        info += '<tr><td class="bs-it-lbl">Date of Birth</td><td class="bs-it-val">' + v('dob') + '</td></tr>';
    if (d.department) info += '<tr><td class="bs-it-lbl">Department</td><td class="bs-it-val">' + cleanDept(v('department')) + '</td></tr>';
    if (d.year)       info += '<tr><td class="bs-it-lbl">Batch Year</td><td class="bs-it-val">' + year + '</td></tr>';
    if (d.company)    info += '<tr><td class="bs-it-lbl" style="padding-right:20px;">Company</td><td class="bs-it-val">' + v('company') + '</td></tr>';
    if (d.location)   info += '<tr><td class="bs-it-lbl">Location</td><td class="bs-it-val">' + v('location') + '</td></tr>';
    if (d.exam) {
        var exams = d.exam.split(',');
        var eps = '';
        for (var ei = 0; ei < exams.length; ei++) {
            var et = exams[ei].trim();
            if (et) eps += '<span class="bs-ep">' + _e(et) + '</span>';
        }
        if (eps) info += '<tr><td class="bs-it-lbl">Exams</td><td class="bs-it-val">' + eps + '</td></tr>';
    }
    if (d.fav)   info += '<tr><td class="bs-it-lbl">Hobbies</td><td class="bs-it-val">' + v('fav') + '</td></tr>';
    if (d.extra) info += '<tr><td class="bs-it-lbl">Achievements</td><td class="bs-it-val">' + v('extra') + '</td></tr>';

    // ── Opinions
    var opCount = (d.opinions || []).length;
    var opinionsBlock = '';
    if (opCount > 0) {
        var opsHTML = '';
        for (var oi = 0; oi < d.opinions.length; oi++) {
            var op = d.opinions[oi];
            var parts = (op.author_name || op.author_id || '?').split(' ').slice(0, 2);
            var ini = '';
            for (var ni = 0; ni < parts.length; ni++) ini += (parts[ni][0] || '').toUpperCase();
            opsHTML += '<div class="bs-op-card">' +
                '<div class="bs-oc-author-row">' +
                    '<div class="bs-oc-ava">' + _e(ini) + '</div>' +
                    '<div>' +
                        '<span class="bs-oc-name">' + _e(op.author_name || op.author_id) + '</span>' +
                        '<span class="bs-oc-id">' + _e(op.author_id) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="bs-oc-text">' + _e(op.opinion) + '</div>' +
            '</div>';
        }
        opinionsBlock =
            '<div class="bs-op-head">' +
                '<span class="bs-oh-title">OPINIONS</span>' +
                '<span class="bs-oh-subtitle">from friends</span>' +
                '<span class="bs-oh-count">' + opCount + ' Note' + (opCount !== 1 ? 's' : '') + '</span>' +
            '</div>' +
            '<div class="bs-op-rule"></div>' +
            opsHTML;
    } else {
        opinionsBlock = '<div class="bs-op-rule"></div>';
    }

    // ── Right sidebar
    var sidebar = '<div class="bs-rs-photo" style="height:225px;">' + photoInner + '</div>';
    if (d.year)     sidebar += '<div class="bs-rs-batch"><div class="bs-rs-batch-lbl">Batch Year</div><div class="bs-rs-batch-val">' + year + '</div></div>';

    return (
        '<div class="bs-card">' +
          '<div class="bs-inner">' +
'<img src="logo1.svg" crossorigin="anonymous" alt="Vignan Logo" style="position:absolute;top:110px;left:50px;width:164px;height:164px;object-fit:contain;" onerror="this.style.display=\'none\'">' +
            // Stamp
            '<div class="bs-stamp">' +
              '<div>Slam</div><div>Book</div>' +
              '<div class="bs-stamp-year">' + _e(stampYr) + '</div>' +
            '</div>' +

            '<div class="bs-masthead">' +
              '<div class="bs-mast-pre">Vignan\'s University &middot; Vadlamudi</div>' +
              '<div class="bs-mast-title-row" style="display:flex;align-items:center;justify-content:center;gap:14px;">' +
                '<div class="bs-mast-title">SLAM BOOK</div>' +
              '</div>' +
              '<div class="bs-mast-sub" style="justify-content:center;margin-top:8px;">' +
                '<strong style="font-size:1rem;letter-spacing:.14em;color:#D4820A;">Class of ' + (year || '&mdash;') + '</strong>' +
              '</div>' +
            '</div>' +

            // Main grid
            '<div class="bs-grid">' +

              // Left column
              '<div class="bs-col-left">' +
                '<div class="bs-name-block">' +
                  '<div class="bs-nb-byline">Profile of</div>' +
                  '<div class="bs-nb-name">' + (v('name') || '&mdash;') + '</div>' +
                  (d.nickname ? '<div class="bs-nb-nick">&ldquo;&nbsp;' + v('nickname') + '&nbsp;&rdquo;</div>' : '') +
                  '<div class="bs-tag-row">' + tags + '</div>' +
                '</div>' +
                '<table class="bs-info-table"><tbody>' + info + '</tbody></table>' +
                opinionsBlock +
              '</div>' +

              // Right sidebar
              '<div class="bs-col-right">' + sidebar + '</div>' +

            '</div>' +

            // Footer
            '<div class="bs-footer">' +
              '<span class="bs-ft">Vignan\'s University &middot; Official Slam Book</span>' +
              '<span class="bs-ft"><span class="bs-ft-ochre">&#9733;</span> ' + (year || 'Class') + ' <span class="bs-ft-ochre">&#9733;</span></span>' +
              '<span class="bs-ft">' + cleanDept(_e(dept)) + '</span>' +
            '</div>' +

          '</div>' +
        '</div>'
    );
}

// ── Core PDF renderer ─────────────────────────────────────────────────────────
function renderStudentToPDF(studentData) {
    var holder = document.getElementById('pdf-render-holder');
    holder.innerHTML = buildProfileHTML(studentData);

    return new Promise(function(resolve, reject) {
        setTimeout(function() {
            var cardEl = holder.querySelector('.bs-card');
            if (!cardEl) { reject(new Error('Profile card element not found')); return; }
            html2canvas(cardEl, {
                scale: 2, useCORS: true, allowTaint: false,
                backgroundColor: '#ffffff', logging: false,
                width: cardEl.scrollWidth, height: cardEl.scrollHeight
            }).then(function(canvas) {
                holder.innerHTML = '';
                var jsPDF  = window.jspdf.jsPDF;
                var pdf    = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                var imgW   = 210;
                var imgH   = Math.min(canvas.height * imgW / canvas.width, 297);
                var imgData = canvas.toDataURL('image/jpeg', 0.95);
                pdf.addImage(imgData, 'JPEG', 0, 0, imgW, imgH);
                resolve(pdf);
            }).catch(function(e) { holder.innerHTML = ''; reject(e); });
        }, 600);
    });
}

// ── Single PDF download ───────────────────────────────────────────────────────
function downloadPDF(r) {
    var spinner    = document.getElementById('dl-spinner');
    var spinnerMsg = document.getElementById('dl-spinner-msg');
    spinnerMsg.textContent = 'Generating PDF\u2026';
    spinner.classList.add('show');

    fetch(AJAX_FILE + '?action=student_detail&regno=' + encodeURIComponent(r.regno))
        .then(function(det){ return det.json(); })
        .then(function(dj){
            var merged = {};
            for (var k in r) if (r.hasOwnProperty(k)) merged[k] = r[k];
            for (var k in dj) if (dj.hasOwnProperty(k)) merged[k] = dj[k];
            return renderStudentToPDF(merged);
        })
        .then(function(pdf){
            var fname = 'SlamBook_' + (r.regno || 'Student').replace(/[^a-zA-Z0-9]/g,'_') + '.pdf';
            pdf.save(fname);
            spinner.classList.remove('show');
        })
        .catch(function(err){
            console.error('PDF error:', err);
            alert('Could not generate PDF. Please try again.\n\nError: ' + err.message);
            spinner.classList.remove('show');
        });
}

function downloadCurrentModalPDF() {
    if (!_currentModalData) return;
    downloadPDF(_currentModalData);
}

// ── Bulk ZIP download ─────────────────────────────────────────────────────────
function cancelBulkDownload() {
    bulkCancelled = true;
    document.getElementById('bp-cancel-btn').textContent = 'Cancelling\u2026';
    document.getElementById('bp-cancel-btn').disabled = true;
}

function bulkDownloadZip() {
    // CHANGE 6: read from Set across all pages, not just DOM checkboxes
    var src = fullySearch
        ? fullyData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(fullySearch)>-1||(r.name||'').toLowerCase().indexOf(fullySearch)>-1; })
        : fullyData;
    var students = fullySelectedSet ? src.filter(function(r){ return fullySelectedSet.has(r.regno); }) : [];
    if (!students.length) { alert('No students selected. Please check at least one student.'); return; }

    bulkCancelled = false;
    var overlay   = document.getElementById('bulk-progress-overlay');
    var barFill   = document.getElementById('bp-bar-fill');
    var progText  = document.getElementById('bp-progress-text');
    var subText   = document.getElementById('bp-sub');
    var stuName   = document.getElementById('bp-student-name');
    var cancelBtn = document.getElementById('bp-cancel-btn');
    cancelBtn.textContent = '\u2715 Cancel';
    cancelBtn.disabled    = false;
    barFill.style.width   = '0%';
    progText.textContent  = '0 / ' + students.length;
    subText.textContent   = 'Generating ' + students.length + ' PDF' + (students.length>1?'s':'') + '\u2026';
    stuName.textContent   = '';
    overlay.classList.add('open');

    var zip = new JSZip();
    var done = 0, skipped = 0;
    var idx = 0;

    function processNext() {
        if (bulkCancelled || idx >= students.length) {
            if (bulkCancelled) {
                document.getElementById('pdf-render-holder').innerHTML = '';
                overlay.classList.remove('open');
                return;
            }
            stuName.textContent = '';
            subText.textContent = 'Packing ZIP archive\u2026';
            barFill.style.width = '100%';
            zip.generateAsync({ type:'blob', compression:'DEFLATE', compressionOptions:{level:6} })
                .then(function(blob) {
                    var url = URL.createObjectURL(blob);
                    var a   = document.createElement('a');
                    a.href  = url;
                    var ts  = new Date().toISOString().slice(0,10);
                    a.download = 'SlamBook_Bulk_' + ts + '.zip';
                    document.body.appendChild(a); a.click(); document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                    subText.textContent = skipped > 0
                        ? 'Done! ' + (done-skipped) + ' PDFs zipped \u00b7 ' + skipped + ' skipped.'
                        : 'Done! ' + done + ' PDFs zipped successfully.';
                    progText.textContent = '\u2705 Complete';
                    stuName.textContent  = '';
                    setTimeout(function(){ overlay.classList.remove('open'); }, 2200);
                })
                .catch(function(err) {
                    console.error('ZIP pack error:', err);
                    alert('Could not pack ZIP. Please try again.');
                    overlay.classList.remove('open');
                });
            return;
        }

        var r = students[idx];
        stuName.textContent = 'Processing: ' + (r.name || r.regno);

        fetch(AJAX_FILE + '?action=student_detail&regno=' + encodeURIComponent(r.regno))
            .then(function(det){ return det.json(); })
            .then(function(dj){
                var merged = {};
                for (var k in r) if (r.hasOwnProperty(k)) merged[k] = r[k];
                for (var k in dj) if (dj.hasOwnProperty(k)) merged[k] = dj[k];
                return renderStudentToPDF(merged);
            })
            .then(function(pdf){
                var fname = 'SlamBook_' + (r.regno || 'Student').replace(/[^a-zA-Z0-9]/g,'_') + '.pdf';
                zip.file(fname, pdf.output('arraybuffer'));
                done++; idx++;
                var pct = Math.round(done / students.length * 100);
                barFill.style.width  = pct + '%';
                progText.textContent = done + ' / ' + students.length;
                processNext();
            })
            .catch(function(err){
                console.warn('Skipped', r.regno, err);
                skipped++; done++; idx++;
                processNext();
            });
    }

    processNext();
}

// ── Excel export — completely filled ─────────────────────────────────────────
function exportToExcelFully() {
    // CHANGE 7: read from Set across all pages, not just DOM checkboxes
    var src = fullySearch
        ? fullyData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(fullySearch)>-1||(r.name||'').toLowerCase().indexOf(fullySearch)>-1; })
        : fullyData;
    var students = fullySelectedSet ? src.filter(function(r){ return fullySelectedSet.has(r.regno); }) : [];
    if (!students.length) { alert('No students selected.'); return; }

    var rows = [];
    for (var i = 0; i < students.length; i++) {
        var r = students[i];
        rows.push({
            '#': i+1, 'Reg No': r.regno||'', 'Name': r.name||'', 'Level (BM)': r.bm||'',
            'Program': r.program||'', 'Department': r.department||'', 'Branch / Spec': r.branch||'',
            'Batch Year': r.year||'', 'Mobile': r.mobile||'', 'Alt / Parent No': r.alt_mobile||'',
            'Details Done': parseInt(r.details_done)===1?'Yes':'No',
            'Reflections Done': parseInt(r.reflect_done)===1?'Yes':'No',
            'Feedback Done': parseInt(r.feedback_done)===1?'Yes':'No'
        });
    }
    var ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [{wch:5},{wch:15},{wch:28},{wch:10},{wch:16},{wch:32},{wch:22},{wch:12},{wch:14},{wch:16},{wch:14},{wch:16},{wch:14}];
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Completely Filled');
    var summary = [["Vignan's University \u2014 Slam Book Export"],[''],['Export Type','Completely Filled'],['Total Rows',students.length],['Generated On',new Date().toLocaleString('en-IN')]];
    var ws2 = XLSX.utils.aoa_to_sheet(summary); ws2['!cols']=[{wch:20},{wch:40}];
    XLSX.utils.book_append_sheet(wb, ws2, 'Info');
    XLSX.writeFile(wb, 'SlamBook_CompletelyFilled_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

// ── Excel export — partially filled ──────────────────────────────────────────
function exportToExcelPartial() {
    // CHANGE 10: read from Set across all pages, not just DOM checkboxes
    var src = partialSearch
        ? partialData.filter(function(r){ return (r.regno||'').toLowerCase().indexOf(partialSearch)>-1||(r.name||'').toLowerCase().indexOf(partialSearch)>-1; })
        : partialData;
    var students = partialSelectedSet ? src.filter(function(r){ return partialSelectedSet.has(r.regno); }) : [];
    if (!students.length) { alert('No students selected.'); return; }

    var rows = [];
    for (var i = 0; i < students.length; i++) {
        var r = students[i];
        var missing = [];
        if (parseInt(r.details_done)!==1) missing.push('Details');
        if (parseInt(r.reflect_done)!==1) missing.push('Reflections');
        if (parseInt(r.feedback_done)!==1) missing.push('Exit Feedback');
        rows.push({
            '#': i+1, 'Reg No': r.regno||'', 'Name': r.name||'', 'Level (BM)': r.bm||'',
            'Program': r.program||'', 'Department': r.department||'', 'Branch / Spec': r.branch||'',
            'Batch Year': r.year||'',
            'Details Done': parseInt(r.details_done)===1?'\u2713 Yes':'\u2717 No',
            'Reflections Done': parseInt(r.reflect_done)===1?'\u2713 Yes':'\u2717 No',
            'Feedback Done': parseInt(r.feedback_done)===1?'\u2713 Yes':'\u2717 No',
            'Missing Sections': missing.join(', ')||'None', 'Sections Pending': missing.length
        });
    }
    var ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [{wch:5},{wch:15},{wch:28},{wch:10},{wch:16},{wch:32},{wch:22},{wch:12},{wch:14},{wch:16},{wch:14},{wch:30},{wch:16}];
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Partially Filled');
    var summary = [["Vignan's University \u2014 Slam Book Export"],[''],['Export Type','Partially Filled'],['Total Rows',students.length],['Generated On',new Date().toLocaleString('en-IN')]];
    var ws2 = XLSX.utils.aoa_to_sheet(summary); ws2['!cols']=[{wch:20},{wch:40}];
    XLSX.utils.book_append_sheet(wb, ws2, 'Info');
    XLSX.writeFile(wb, 'SlamBook_PartiallyFilled_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

// ── View modal ────────────────────────────────────────────────────────────────
function viewProfile(r) {
    _currentModalData = r;
    fetch(AJAX_FILE + '?action=student_detail&regno=' + encodeURIComponent(r.regno))
        .then(function(res){ return res.json(); })
        .then(function(detail) {
            var merged = {};
            for (var k in r) if (r.hasOwnProperty(k)) merged[k] = r[k];
            for (var k in detail) if (detail.hasOwnProperty(k)) merged[k] = detail[k];
            _currentModalData = merged;
            document.getElementById('profile-print-area').innerHTML = buildProfileHTML(merged);
            document.getElementById('profile-modal').classList.add('open');
        })
        .catch(function() {
            document.getElementById('profile-print-area').innerHTML = buildProfileHTML(r);
            document.getElementById('profile-modal').classList.add('open');
        });
}

function closeModal(){
    document.getElementById('profile-modal').classList.remove('open');
    _currentModalData = null;
}

document.getElementById('profile-modal').addEventListener('click', function(e){
    if (e.target === this) closeModal();
});

// ── Cascade dropdowns ─────────────────────────────────────────────────────────
function setupCascade(){
    var gfBm   = document.getElementById('gf-bm');
    var gfProg = document.getElementById('gf-program');
    var gfDept = document.getElementById('gf-department');
    var gfBr   = document.getElementById('gf-branch');

    gfBm.addEventListener('change', function(){
        var bm = this.value;
        gfProg.innerHTML = '<option value="">' + (bm ? '&mdash; All Programs &mdash;' : '&mdash; Select Level First &mdash;') + '</option>';
        gfDept.innerHTML = '<option value="">&mdash; All Departments &mdash;</option>';
        gfBr.innerHTML   = '<option value="">&mdash; All Branches &mdash;</option>';
        gfDept.disabled = true; gfBr.disabled = true;
        if (bm && CASCADE_DATA[bm]) {
            var progs = Object.keys(CASCADE_DATA[bm]).sort();
            for (var i = 0; i < progs.length; i++) gfProg.appendChild(new Option(progs[i], progs[i]));
            gfProg.disabled = false;
        } else { gfProg.disabled = true; }
    });

    gfProg.addEventListener('change', function(){
        var bm = gfBm.value, prog = this.value;
        gfDept.innerHTML = '<option value="">' + (prog ? '&mdash; All Departments &mdash;' : '&mdash; Select Program First &mdash;') + '</option>';
        gfBr.innerHTML   = '<option value="">&mdash; All Branches &mdash;</option>'; gfBr.disabled = true;
        if (bm && prog && CASCADE_DATA[bm] && CASCADE_DATA[bm][prog]) {
            var depts = Object.keys(CASCADE_DATA[bm][prog]).sort();
            for (var i = 0; i < depts.length; i++) gfDept.appendChild(new Option(depts[i], depts[i]));
            gfDept.disabled = false;
        } else { gfDept.disabled = true; }
    });

    gfDept.addEventListener('change', function(){
        var bm = gfBm.value, prog = gfProg.value, dept = this.value;
        gfBr.innerHTML = '<option value="">' + (dept ? '&mdash; All Branches &mdash;' : '&mdash; Select Dept First &mdash;') + '</option>';
        if (bm && prog && dept && CASCADE_DATA[bm] && CASCADE_DATA[bm][prog] && CASCADE_DATA[bm][prog][dept] && CASCADE_DATA[bm][prog][dept].length) {
            var branches = CASCADE_DATA[bm][prog][dept].slice().sort();
            for (var i = 0; i < branches.length; i++) gfBr.appendChild(new Option(branches[i], branches[i]));
            gfBr.disabled = false;
        } else { gfBr.disabled = true; }
    });
}

// ── Utilities ─────────────────────────────────────────────────────────────────
function setLoading(on){ document.getElementById('loading-bar').style.display = on ? 'block' : 'none'; }
function setActive(el){
    var items = document.querySelectorAll('.nav-item');
    for (var i = 0; i < items.length; i++) items[i].classList.remove('active');
    el.classList.add('active');
}
function initSparkles(){
    var c = document.getElementById('sparkles');
    for (var i = 0; i < 20; i++) {
        var s = document.createElement('div'); s.className = 'sparkle';
        s.style.left = (Math.random()*100) + '%';
        s.style.top  = (Math.random()*100) + '%';
        s.style.setProperty('--d', (2+Math.random()*3) + 's');
        s.style.setProperty('--dl', (Math.random()*3) + 's');
        c.appendChild(s);
    }
}
function cleanDept(s) {
    if (!s) return '';
    s = s.trim();
    if (s.toLowerCase().indexOf('department of ') === 0) s = s.slice('department of '.length);
    return s.charAt(0).toUpperCase() + s.slice(1);
}
</script>
</body>
</html>