<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Slambook Profile Card Preview</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
/* Version 5.2 */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:#e8e0d0;display:flex;flex-direction:column;align-items:center;padding:30px 20px;font-family:'DM Sans',sans-serif;}
h2{font-family:'Playfair Display',serif;color:#4a3820;margin-bottom:20px;font-size:1.1rem;letter-spacing:.05em;}

/* ─── TEMPLATE WRAPPER ─── */
.pc-template-wrap{
    /* Replace url() below with the actual path to your slambook_template.jpg */
    background: url('slambook_template.jpg') no-repeat center top / 100% 100%;
    width: 794px;          /* A4 width at 96dpi */
    min-height: 1123px;    /* A4 height at 96dpi */
    position: relative;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
}

/* Content padded away from ALL four borders of the template */
.pc-inner{
    /* top ~148px clears the floral top border
       bottom ~115px clears the stamp/footer border
       sides ~44px clears the petal accents */
    padding: 148px 44px 120px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* ── Hero row ── */
.pc-hero{display:flex;align-items:flex-start;gap:20px;margin-bottom:4px;}
.pc-hero-photo{
    width:100px;height:110px;object-fit:cover;
    border-radius:10px;border:2px solid rgba(180,140,10,.45);
    flex-shrink:0;box-shadow:0 4px 18px rgba(0,0,0,.12);
}
.pc-hero-photo-ph{
    width:100px;height:110px;border-radius:10px;
    background:linear-gradient(135deg,#f5e8c0,#e8d090);
    border:2px solid rgba(180,140,10,.35);
    display:flex;align-items:center;justify-content:center;
    font-size:2.8rem;flex-shrink:0;
}
.pc-hero-text{flex:1;padding-top:4px;}
.pc-hero-label{font-size:.56rem;letter-spacing:.2em;text-transform:uppercase;color:#a07820;font-weight:700;margin-bottom:4px;}
.pc-hero-name{font-family:'Playfair Display',serif;font-size:1.85rem;color:#1a1220;line-height:1.1;margin-bottom:3px;}
.pc-hero-nick{font-size:.8rem;color:#7a6030;font-style:italic;margin-bottom:8px;}
.pc-hero-badges{display:flex;flex-wrap:wrap;gap:6px;}
.pc-hero-badge{
    background:rgba(180,140,10,.1);border:1px solid rgba(180,140,10,.3);
    color:#7a5c10;border-radius:5px;padding:2px 10px;
    font-size:.64rem;font-weight:600;
}

/* ── Divider ── */
.pc-divider{border:none;border-top:1px solid rgba(180,140,10,.22);margin:2px 0;}

/* ── Info grid ── */
.pc-info-grid{
    display:grid;grid-template-columns:1fr 1fr;
    border:1px solid rgba(180,140,10,.2);
    border-radius:8px;overflow:hidden;
    background:rgba(255,252,240,.72);
}
.pc-info-item{padding:9px 16px;border-bottom:1px solid rgba(180,140,10,.12);}
.pc-info-item:nth-child(odd){border-right:1px solid rgba(180,140,10,.12);}
.pc-info-item.pc-full{grid-column:span 2;border-right:none;}
.pc-info-lbl{font-size:.54rem;text-transform:uppercase;letter-spacing:.13em;color:#a07820;font-weight:700;margin-bottom:2px;}
.pc-info-val{font-size:.8rem;color:#1a1220;line-height:1.4;}
.pc-exam-pill{
    display:inline-block;background:rgba(180,140,10,.12);
    border:1px solid rgba(180,140,10,.28);color:#7a5c10;
    border-radius:4px;padding:1px 8px;font-size:.63rem;margin:1px 2px 1px 0;
}

/* ── Opinions ── */
.pc-op-section{margin-top:2px;}
.pc-op-heading{
    font-family:'Playfair Display',serif;font-size:.92rem;color:#1a1220;
    display:flex;align-items:center;gap:8px;margin-bottom:4px;
}
.pc-op-heading em{color:#a07820;font-style:italic;}
.pc-op-count-pill{
    background:rgba(180,140,10,.12);border:1px solid rgba(180,140,10,.25);
    color:#a07820;border-radius:20px;padding:1px 10px;
    font-size:.6rem;font-weight:700;font-family:'DM Sans',sans-serif;font-style:normal;
}
.pc-op-bar{width:36px;height:3px;background:#c8a020;border-radius:2px;margin-bottom:10px;}
.pc-op-card{
    background:rgba(255,252,240,.75);border:1px solid rgba(180,140,10,.17);
    border-radius:8px;padding:10px 14px;margin-bottom:7px;position:relative;
}
.pc-op-card:last-child{margin-bottom:0;}
.pc-op-card::before{
    content:'\201C';position:absolute;top:4px;right:12px;
    font-size:2.5rem;line-height:1;color:rgba(180,140,10,.1);font-family:Georgia,serif;
}
.pc-op-author-row{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
.pc-op-ava{
    width:28px;height:28px;border-radius:50%;
    background:linear-gradient(135deg,#D4A017,#F5C842);
    display:flex;align-items:center;justify-content:center;
    font-size:.7rem;font-weight:700;color:#0d1628;flex-shrink:0;
}
.pc-op-aname{font-size:.76rem;color:#1a1220;font-weight:600;display:block;}
.pc-op-aid  {font-size:.6rem;color:#888;font-family:monospace;display:block;}
.pc-op-text {font-size:.77rem;color:#3a3040;line-height:1.65;font-style:italic;}
.pc-op-empty{
    text-align:center;padding:18px 12px;color:#bbb;font-size:.78rem;
    background:rgba(255,252,240,.5);border:1px dashed rgba(180,140,10,.18);border-radius:8px;
}

/* Fallback when image missing — shows a dotted border placeholder */
.pc-template-wrap.no-bg{
    background: repeating-linear-gradient(
        45deg, #f5f0e8, #f5f0e8 10px, #ede8dc 10px, #ede8dc 20px
    );
    border: 2px dashed rgba(180,140,10,.4);
}
.no-bg-note{
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    background:rgba(180,140,10,.15);border:1px solid rgba(180,140,10,.3);
    border-radius:10px;padding:16px 24px;text-align:center;
    color:#7a5c10;font-size:.8rem;pointer-events:none;z-index:10;
    max-width:300px;
}
</style>
</head>
<body>

<h2>📄 Profile Card Preview — Place slambook_template.jpg in same folder to see background</h2>

<div class="pc-template-wrap" id="pc-template-wrap">

  <!-- Shown only when template image is missing (preview mode) -->
  <div class="no-bg-note" id="no-bg-note" style="display:none;">
    ℹ️ Place <strong>slambook_template.jpg</strong> in the same folder<br>as this HTML to see the floral template background.
  </div>

  <div class="pc-inner">

    <!-- Hero -->
    <div class="pc-hero">
      <div class="pc-hero-photo-ph">👤</div>
      <div class="pc-hero-text">
        <div class="pc-hero-label">Vignan's University · Slam Book</div>
        <div class="pc-hero-name">Ravi Kumar Sharma</div>
        <div class="pc-hero-nick">" Rocky "</div>
        <div class="pc-hero-badges">
          <span class="pc-hero-badge">UG</span>
          <span class="pc-hero-badge">B.Tech</span>
          <span class="pc-hero-badge">CSE — AI &amp; ML</span>
          <span class="pc-hero-badge">Batch 2021-2025</span>
        </div>
      </div>
    </div>

    <hr class="pc-divider">

    <!-- Info Grid -->
    <div class="pc-info-grid">
      <div class="pc-info-item">
        <div class="pc-info-lbl">Reg. Number</div>
        <div class="pc-info-val">21VV1A0512</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Date of Birth</div>
        <div class="pc-info-val">14 March 2003</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Mobile</div>
        <div class="pc-info-val">+91 98765 43210</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Alt. Mobile</div>
        <div class="pc-info-val">+91 87654 32109</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Department</div>
        <div class="pc-info-val">Computer Science &amp; Engineering</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Batch Year</div>
        <div class="pc-info-val">2024-2025</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Company / Plans</div>
        <div class="pc-info-val">TCS — Tata Consultancy Services</div>
      </div>
      <div class="pc-info-item">
        <div class="pc-info-lbl">Location</div>
        <div class="pc-info-val">Hyderabad, Telangana</div>
      </div>
      <div class="pc-info-item pc-full">
        <div class="pc-info-lbl">Competitive Exams</div>
        <div class="pc-info-val">
          <span class="pc-exam-pill">GATE 2025</span>
          <span class="pc-exam-pill">GRE</span>
          <span class="pc-exam-pill">CAT</span>
        </div>
      </div>
      <div class="pc-info-item pc-full">
        <div class="pc-info-lbl">Favourites / Hobbies</div>
        <div class="pc-info-val">Cricket, Chess, Cooking Telugu food, Binge-watching sci-fi series</div>
      </div>
      <div class="pc-info-item pc-full" style="border-bottom:none;">
        <div class="pc-info-lbl">Achievements</div>
        <div class="pc-info-val">1st Prize — Smart India Hackathon 2024; Dept Topper Sem 5 &amp; 6; NSS Volunteer of the Year</div>
      </div>
    </div>

    <hr class="pc-divider">

    <!-- Opinions -->
    <div class="pc-op-section">
      <div class="pc-op-heading">
        Friends' <em>Opinions</em>
        <span class="pc-op-count-pill">2 notes</span>
      </div>
      <div class="pc-op-bar"></div>

      <div class="pc-op-card">
        <div class="pc-op-author-row">
          <div class="pc-op-ava">PS</div>
          <div>
            <span class="pc-op-aname">Priya Srinivasan</span>
            <span class="pc-op-aid">21VV1A0534</span>
          </div>
        </div>
        <div class="pc-op-text">Rocky is the kind of friend who stays up all night to help you debug your code and still cracks jokes at 3 AM. Genuinely one of the most talented people I've met at Vignan. Wishing him all the success he truly deserves!</div>
      </div>

      <div class="pc-op-card">
        <div class="pc-op-author-row">
          <div class="pc-op-ava">AK</div>
          <div>
            <span class="pc-op-aname">Aditya Kalyan</span>
            <span class="pc-op-aid">21VV1A0501</span>
          </div>
        </div>
        <div class="pc-op-text">Four years flew by with this guy. From our first C-programming lab disasters to winning the hackathon together — every memory is gold. Go conquer the world, Rocky!</div>
      </div>
    </div>

  </div>
</div>

<script>
// Show placeholder note if template image fails to load
const wrap = document.getElementById('pc-template-wrap');
const note = document.getElementById('no-bg-note');
const img  = new Image();
img.src = 'slambook_template.jpg';
img.onerror = () => { wrap.classList.add('no-bg'); note.style.display='block'; };
</script>
</body>
</html>