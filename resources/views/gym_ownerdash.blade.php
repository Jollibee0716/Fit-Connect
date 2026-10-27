<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="my-avatar" content="{{ optional(auth()->user())->avatar_url }}">
<title>FitConnect Gym Owner</title>
<style>
:root{--panel:#18130f;--ink:#fff;--mute:#a8a29e;--line:rgba(255,255,255,.12);--accent:#ff9900;--green:#4ade80;--red:#ef4444;--navtxt:#1a1208;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
*{box-sizing:border-box}html,body{margin:0}[hidden]{display:none!important}
body{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:var(--ink);background:radial-gradient(60% 50% at 15% 10%,rgba(255,153,0,.22),transparent 70%),#0a0908;background-attachment:fixed;min-height:100vh;padding:24px}
:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
.app{display:flex;max-width:1180px;margin:0 auto;background:var(--panel);border-radius:14px;overflow:hidden;min-height:640px}
.side{width:180px;flex:none;border-right:1px solid var(--line);display:flex;flex-direction:column}
.logo{display:flex;align-items:center;gap:10px;padding:20px 16px}
.logo-mark{width:36px;height:36px;border-radius:10px;background:var(--accent);color:var(--navtxt);display:grid;place-items:center;flex:none}
.logo-mark svg{width:22px;height:22px}
.logo-txt{display:flex;flex-direction:column;line-height:1.15;min-width:0}
.logo-txt b{font-size:17px;font-weight:700}
.logo-txt small{font-size:11px;color:var(--accent);font-weight:600;letter-spacing:.08em;text-transform:uppercase}
nav{padding:0 8px;display:flex;flex-direction:column;gap:6px}
nav a{display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;color:var(--ink);text-decoration:none;font-size:14px;font-weight:500}
nav a:hover{background:var(--line)}nav a.on{background:var(--accent);color:var(--navtxt)}
.cnt{margin-left:auto;background:var(--red);color:#fff;font-size:10px;border-radius:99px;padding:1px 6px}
.sp{flex:1}
.out{margin:0 8px 14px;padding:10px 12px;background:none;border:0;color:#ff8a65;font:inherit;font-size:14px;cursor:pointer;text-align:left;border-radius:8px}
.out:hover{background:var(--line)}
.main{flex:1;min-width:0;display:flex;flex-direction:column}
.top{display:flex;justify-content:space-between;align-items:center;padding:14px 22px;border-bottom:1px solid var(--line);font-weight:600}
.top .r{display:flex;align-items:center;gap:16px;font-size:13px;font-weight:500}
.badge{position:relative;display:inline-block;color:inherit;text-decoration:none;font-size:16px}
.badge b{position:absolute;top:-6px;right:-8px;background:var(--red);color:#fff;font-size:9px;font-weight:600;border-radius:99px;min-width:15px;height:15px;padding:0 3px;display:grid;place-items:center}
.pb{display:flex;align-items:center;gap:8px;color:inherit;text-decoration:none;padding:4px 6px;border-radius:8px}
.pb:hover{background:var(--line)}
.av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#ffb84d,#cc5500);flex:none;position:relative;display:grid;place-items:center;font-size:11px;font-weight:700;color:var(--navtxt)}
.av.dot::after{content:"";position:absolute;right:-1px;bottom:-1px;width:9px;height:9px;border-radius:50%;background:var(--green);border:2px solid var(--panel)}
.body{padding:20px 22px;flex:1}
.view{display:none}.view.on{display:block}
h1{font-size:22px;margin:0}.sub{margin:4px 0 0;color:var(--mute);font-size:14px}
.banner{margin:14px 0 0;padding:10px 14px;border-radius:10px;background:rgba(255,153,0,.15);color:var(--accent);font-size:14px}
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin:18px 0}
.cards.c3{grid-template-columns:repeat(3,1fr)}
.card,.box{background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:14px 16px}
.card span{font-size:12px;color:var(--mute);font-weight:600}
.card strong{display:block;font-size:28px;margin:2px 0}
.card em{font-style:normal;font-size:12px;color:var(--mute)}
a.card{color:inherit;text-decoration:none}a.card:hover{border-color:var(--accent)}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.box h2{font-size:16px;margin:0 0 10px}
.att{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--line);font-size:14px}
.att:last-child{border:0}.att small{color:var(--mute)}.att .btn{margin-left:auto}
table{width:100%;border-collapse:collapse;font-size:14px}
th,td{text-align:left;padding:10px 12px;border-bottom:1px solid var(--line)}
th{color:var(--mute);font-weight:600;font-size:12px}td small{display:block;color:var(--mute);font-size:12px}
.nowrap{white-space:nowrap}.scroll{overflow-x:auto}
.tag{padding:2px 8px;border-radius:99px;font-size:12px;background:rgba(74,222,128,.15);color:var(--green)}
.tag.off{background:var(--line);color:var(--mute)}.tag.pending{background:rgba(255,153,0,.15);color:var(--accent)}.tag.rej{background:rgba(239,68,68,.15);color:#ff8a65}
.btn{background:var(--accent);color:var(--navtxt);border:0;border-radius:8px;padding:9px 16px;font:inherit;font-size:14px;font-weight:600;cursor:pointer}
.btn:hover{filter:brightness(1.08)}.btn:disabled{opacity:.6;cursor:wait}
.btn.ghost{background:none;color:var(--ink);border:1px solid var(--line)}.btn.ghost:hover{background:var(--line)}
.btn.danger{background:var(--red);color:#fff}.btn.sm{padding:5px 10px;font-size:12px}
.acts{display:flex;gap:6px;flex-wrap:wrap}
.tabs{display:flex;gap:6px;flex-wrap:wrap;margin:14px 0 0}
.tab{padding:7px 12px;border-radius:8px;border:1px solid var(--line);background:none;color:var(--ink);font:inherit;font-size:13px;font-weight:500;cursor:pointer}
.tab:hover{background:var(--line)}.tab.on{background:var(--accent);color:var(--navtxt);border-color:transparent}.tab b{font-size:11px;opacity:.75}
a.tab{text-decoration:none;display:inline-block}
.fld{display:block;margin:10px 0;font-size:13px;color:var(--mute)}.fld input,.fld select{margin:4px 0 0;max-width:none}select{background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:9px 12px;font:inherit;font-size:14px;width:100%}
input{background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:9px 12px;font:inherit;font-size:14px;width:100%;max-width:320px;margin:14px 0}
input:disabled,input[readonly]{opacity:.6;cursor:not-allowed}
.lnk{background:none;border:0;padding:0;color:var(--accent);font:inherit;font-weight:600;cursor:pointer}
.empty{color:var(--mute);font-size:14px;padding:8px 0}
svg{width:100%;height:auto;display:block}svg text{fill:var(--ink);font-size:12px;font-family:inherit}svg .m{fill:var(--mute)}
.ov{position:fixed;inset:0;background:rgba(0,0,0,.65);display:grid;place-items:center;z-index:20;padding:20px}
.modal{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:22px;max-width:380px;width:100%}
.modal h2{margin:0 0 6px;font-size:18px}.modal p{margin:0 0 18px;color:var(--mute);font-size:14px}
.modal .acts{justify-content:flex-end;gap:10px}
.toast{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);background:var(--panel);border:1px solid var(--line);padding:10px 16px;border-radius:10px;font-size:13px;z-index:30}
textarea{background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:9px 12px;font:inherit;font-size:14px;width:100%;min-height:84px;resize:vertical;margin:4px 0 0}

/* Profile */
.pf-hero{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
.pf-av{width:84px;height:84px;border-radius:50%;background:linear-gradient(135deg,#ffb84d,#cc5500);display:grid;place-items:center;font-size:30px;font-weight:700;color:var(--navtxt);flex:none;position:relative}
.pf-av::after{content:"";position:absolute;right:3px;bottom:3px;width:16px;height:16px;border-radius:50%;background:var(--green);border:3px solid var(--panel)}
.pf-hero h2{margin:0;font-size:22px}
.pf-hero p{margin:4px 0 8px;color:var(--mute);font-size:14px}
.pf-meta{display:flex;gap:8px;flex-wrap:wrap}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:14px}
.stats .card strong{font-size:20px}
.pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px}
.pf-grid h3{margin:0 0 4px;font-size:16px}
.pf-grid .sub{margin:0 0 6px;font-size:13px}
.row{display:flex;gap:12px}.row>*{flex:1}
.okmsg{font-size:13px;margin-left:10px;color:var(--green)}
.ferr{color:#ff8a65;font-size:12px;display:block;margin:8px 0 0}.box form .btn,form.box .btn{margin-top:10px}

/* Attendance */
.at-form .row{align-items:flex-end}
.at-form .btn{flex:none;margin:0 0 10px}
.at-filters{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin:16px 0 0}
.at-filters .fld{margin:0}.at-filters .fld input{width:200px}
.codebig{font-size:30px;font-weight:700;letter-spacing:6px;color:var(--accent)}

/* Messages */
.mail{display:grid;grid-template-columns:300px 1fr;gap:14px;margin-top:16px}
.mail .box{padding:12px}
.mail input{max-width:none;margin:0}
.cv{display:flex;gap:10px;align-items:center;width:100%;padding:10px;border-radius:8px;background:none;border:0;color:inherit;font:inherit;text-align:left;cursor:pointer}
.cv:hover,.cv.on{background:var(--line)}
.cv>div{min-width:0;flex:1}
.cv small{display:block;color:var(--mute);font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.mail .tabs{margin:10px 0}
.mail .tab{padding:5px 10px;font-size:12px}
.ms-head{font-weight:600;padding:4px 4px 10px;border-bottom:1px solid var(--line)}
.ms-body{height:400px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;padding:10px 4px}
.bub{max-width:75%;padding:8px 12px;border-radius:12px;font-size:14px;background:var(--line);white-space:pre-wrap;word-break:break-word}
.bub.mine{align-self:flex-end;background:var(--accent);color:var(--navtxt)}
.bub small{display:block;font-size:10px;opacity:.7;margin-top:2px}
.ms-form{display:flex;gap:8px;margin-top:10px}
.ms-form textarea{min-height:44px;height:44px;margin:0}
.ms-empty{color:var(--mute);font-size:13px;padding:8px}

/* Avatars (profile photos) */
.av{overflow:hidden}
.av img,.pf-av img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%}
.pf-av{overflow:hidden}
.who{display:inline-flex;align-items:center;gap:8px;min-width:0}
.who .av{width:28px;height:28px;font-size:10px}
.who .av::after{display:none}
.pf-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:6px}

@media (max-width:760px){.pf-grid,.stats,.mail{grid-template-columns:1fr}.row{flex-direction:column;gap:0}}
@media (max-width:760px){body{padding:0}.app{flex-direction:column;border-radius:0}.side{width:100%;border-right:0;border-bottom:1px solid var(--line)}nav{flex-direction:row;flex-wrap:wrap}.sp{display:none}.cards,.grid2{grid-template-columns:1fr 1fr}}
@media (max-width:760px){.cards.c3{grid-template-columns:1fr}.at-form .btn{margin:0 0 10px}}
@media (max-width:480px){.cards,.grid2{grid-template-columns:1fr}}
/* Private payment proof preview */
.proof-dialog{max-width:min(92vw,720px);max-height:90vh;overflow:auto}
.proof-dialog img{display:block;max-width:100%;max-height:65vh;width:auto;height:auto;margin:12px auto;border-radius:8px;object-fit:contain}
.proof-dialog .proof-error{color:#ff8a65;white-space:pre-wrap}

/* Gym owner member details (read-only). */
.modal.member-details-modal{max-width:680px;max-height:88vh;overflow-y:auto}
.member-details-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:16px 0}
.member-details-field{padding:12px;background:rgba(255,255,255,.045);border:1px solid var(--line);border-radius:8px;overflow-wrap:anywhere}
.member-details-field small{display:block;font-size:11px;color:var(--mute);margin-bottom:4px}
.member-details-field strong{font-size:14px;font-weight:600;white-space:pre-wrap}
.member-details-wide{grid-column:1/-1}
@media(max-width:520px){.member-details-grid{grid-template-columns:1fr}.member-details-wide{grid-column:auto}}

/* FitConnect 2026 — consistent premium UI enhancement. API bindings unchanged. */
:root{--bg:#090b10;--panel:#141821;--ink:#f7f8fa;--mute:#a5aebb;--line:rgba(196,205,221,.14);--accent:#ff9f1a;--nav:#ff9f1a;--navtxt:#181009;--green:#47d6a1;--red:#fb6477;color-scheme:dark}
html{scroll-behavior:smooth}body{background:radial-gradient(ellipse 62% 45% at 5% 0%,rgba(255,159,26,.12),transparent 73%),radial-gradient(ellipse 43% 50% at 95% 95%,rgba(54,69,102,.16),transparent 75%),#090b10;color:var(--ink);font-feature-settings:'tnum' 1;line-height:1.5}
.app{max-width:1500px;min-height:calc(100vh - 48px);background:#10141c;border:1px solid var(--line);border-radius:22px;box-shadow:0 28px 90px rgba(0,0,0,.37);overflow:hidden}
.side{width:235px;background:linear-gradient(180deg,#161b25 0%,#12151d 100%);border-right:1px solid var(--line)}
.logo,.brand{padding:25px 20px 24px!important}.logo-mark,.brandmark{border-radius:12px!important;box-shadow:0 5px 22px rgba(255,159,26,.20)}.logo-txt b,.brandname b{font-size:19px!important;letter-spacing:-.55px}
nav,nav.menu,.side nav.menu{padding:0 12px!important;gap:5px!important}nav a,nav.menu .nav,.side nav.menu .nav{min-height:42px;border-radius:11px!important;transition:background .18s ease,color .18s ease,transform .18s ease;font-weight:540!important}nav a:hover,nav.menu .nav:hover,.side nav.menu .nav:hover{background:rgba(255,255,255,.075)!important;transform:translateX(2px)}nav a.on,nav.menu .nav.active,.side nav.menu .nav.active{background:linear-gradient(100deg,#ffab38,#f88b13)!important;color:#211302!important;box-shadow:0 7px 16px rgba(255,159,26,.14);font-weight:700!important}
.top,.topbar,.main .top.topbar{min-height:72px!important;padding:17px 29px!important;background:rgba(18,23,32,.92);border-bottom:1px solid var(--line);backdrop-filter:blur(12px)}
.body,.content{padding:30px!important;background:transparent!important}.view h1,.page h1,.head h1{font-size:clamp(23px,2.4vw,30px);letter-spacing:-.8px;font-weight:800;line-height:1.2}.sub,.head p,.page>p{line-height:1.7;color:var(--mute)}
.card,.stat,.box,.chart,.gym-card,.gym-hero,.gym-tile{background:linear-gradient(148deg,#1c2230,#171c26)!important;border:1px solid var(--line)!important;border-radius:16px!important;box-shadow:0 8px 24px rgba(0,0,0,.12)}
.card,.stat,.box,.chart{padding:20px!important}.cards,.stats,.grid2,.pf-grid,.gym-grid{gap:17px!important}.card strong,.stat b{letter-spacing:-.8px}.card span,.stat small{letter-spacing:.025em}.card:hover,.gym-card:hover,.gym-tile:hover{border-color:rgba(255,159,26,.36)!important;transition:border-color .2s,transform .2s}.gym-card:hover{transform:translateY(-3px)}
.btn,.button{border-radius:10px!important;font-weight:700!important;transition:transform .16s ease,filter .16s ease,box-shadow .16s ease}.btn:hover:not(:disabled),.button:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 6px 16px rgba(255,159,26,.15)}.btn.ghost,.button.outline,.button.secondary{border-color:rgba(219,228,240,.20)!important;background:rgba(255,255,255,.025)!important}.btn.danger,.button.danger{background:#d94356!important}
input,textarea,select,.input,.select{background:#111620!important;border:1px solid rgba(211,221,233,.20)!important;border-radius:10px!important;color:var(--ink)!important}input:focus,textarea:focus,select:focus{border-color:var(--accent)!important;outline:2px solid rgba(255,159,26,.18);outline-offset:1px}
.tabs{gap:8px!important}.tab{border-radius:10px!important;padding:8px 13px!important}.tab.on{background:var(--accent)!important;color:var(--navtxt)!important}
.scroll,.chart[style*="overflow-x"],.box.scroll{overflow-x:auto}table{border-collapse:separate;border-spacing:0;width:100%}th{font-size:11px!important;letter-spacing:.075em;text-transform:uppercase;background:rgba(255,255,255,.025);white-space:nowrap}td,th{padding:13px 14px!important}tbody tr:hover{background:rgba(255,255,255,.035)}tbody tr:last-child td{border-bottom:0}
.tag,.pill{font-weight:700;border-radius:99px}.tag.off{background:rgba(148,163,184,.14)}.tag.rej{background:rgba(251,100,119,.12);color:#ff8a99}.tag.deactivated{background:rgba(251,100,119,.12);color:#ff8a99}
.modal{background:#1a202c!important;border-radius:17px!important;box-shadow:0 25px 100px rgba(0,0,0,.65)}.ov{backdrop-filter:blur(5px)}.toast{background:#252d3a!important;border-color:rgba(255,159,26,.45)!important;box-shadow:0 12px 45px rgba(0,0,0,.4)}
.hero{border-radius:18px!important;background:radial-gradient(circle at 100% 10%,rgba(255,159,26,.13),transparent 55%),linear-gradient(120deg,#24212a,#1b2230)!important;padding:28px!important}.hero h1{font-size:clamp(24px,2.7vw,33px)!important}.tile{border-radius:13px!important;background:rgba(255,159,26,.12)!important}
.ms-body{scrollbar-width:thin;scrollbar-color:#414653 transparent}.bub{border-radius:14px!important}.bub.mine{background:var(--accent)}.date{background:#1c2230!important}
button:focus-visible,a:focus-visible{outline:2px solid var(--accent);outline-offset:3px}
@media(max-width:900px){.side{width:205px}.body,.content{padding:22px!important}.top,.topbar,.main .top.topbar{padding:14px 20px!important}.cards{grid-template-columns:repeat(2,minmax(0,1fr))!important}}
@media(max-width:760px){body{padding:0}.app{min-height:100vh;border:0;border-radius:0}.side{width:100%;border-right:0;border-bottom:1px solid var(--line)}.side nav,.side nav.menu{flex-direction:row!important;flex-wrap:nowrap!important;overflow-x:auto;padding:0 10px 12px!important;gap:6px!important;scrollbar-width:thin}.side nav a,.side nav.menu .nav{white-space:nowrap;width:auto!important;flex-shrink:0;min-height:38px;padding:8px 11px!important}.logo,.brand{padding:15px 16px!important}.body,.content{padding:18px!important}.top,.topbar,.main .top.topbar{min-height:60px!important;padding:12px 17px!important}.pf-grid,.grid2,.mail{grid-template-columns:1fr!important}.cards,.stats{grid-template-columns:repeat(2,minmax(0,1fr))!important}.row{flex-wrap:wrap}}
@media(max-width:480px){.cards,.stats,.grid2{grid-template-columns:1fr!important}.card,.stat,.box,.chart{padding:16px!important}.hero{padding:20px!important}th,td{padding:10px!important}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{animation:none!important;transition:none!important;scroll-behavior:auto!important}}

</style>
</head>
<body>
<div class="app">
  <aside class="side">
    <div class="logo">
      <span class="logo-mark">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 6.5v11M17.5 6.5v11M3.5 9v6M20.5 9v6M6.5 12h11"/></svg>
      </span>
      <span class="logo-txt"><b>FitConnect</b><small>Gym Owner</small></span>
    </div>
    <nav>
      <a class="on" href="#dashboard">&#9638; Dashboard</a>
      <a href="#payments">&#9830; Payments <span class="cnt" id="nav-p" hidden></span></a>
      <a href="#orders">&#9776; Orders <span class="cnt" id="nav-o" hidden></span></a>
      <a href="#members">&#9786; Members</a>
      <a href="#trainers">&#9733; Trainers</a>
      <a href="#classes">&#9201; Classes</a>
      <a href="#plans">&#9672; Plans</a>
      <a href="#products">&#9830; Products</a>
      <a href="#activity">&#9998; Activity</a>
       <a href="#messages">&#9993; Messages <span class="cnt" id="nav-chat" hidden></span></a>
    </nav>
    <div class="sp"></div>
    <button class="out" type="button" id="lo">&#10162; Log out</button>
  </aside>
  <section class="main">
    <header class="top">
      <span id="t">Dashboard</span>
      <div class="r">
        <a class="badge" href="#messages" title="Messages" aria-label="Messages">&#9993;<b id="mail-n" hidden></b></a>
        <a class="pb" href="#profile" title="My profile" aria-label="My profile"><span class="av dot" id="tav"></span></a>
      </div>
    </header>
    <div class="body">

<section class="view on" id="v-dashboard">
  <h1 id="gname">Dashboard</h1>
  <p class="sub" id="today"></p>
  <div class="banner" id="pendbanner" hidden>Your gym is waiting for admin approval. Members cannot join until it is approved.</div>
  <div class="cards">
    <div class="card"><span>Active members</span><strong id="c-act">0</strong><em id="c-tot"></em></div>
    <div class="card"><span>Revenue this month</span><strong id="c-rev">0</strong><em id="c-chg"></em></div>
    <a class="card" href="#payments"><span>Pending payments</span><strong id="c-pend">0</strong><em>Review now</em></a>
    <div class="card"><span>Overdue members</span><strong id="c-over">0</strong><em>Past due date</em></div>
  </div>
  <div class="grid2">
    <div class="box"><h2>Needs attention</h2><div id="att"></div></div>
    <div class="box"><h2>Recent payments</h2><div class="scroll"><table><tbody id="recent"></tbody></table></div></div>
  </div>
  <div class="box"><h2>Revenue by month</h2><svg id="rev" viewBox="0 0 800 300" role="img" aria-label="Revenue by month"></svg></div>
</section>

<section class="view" id="v-payments">
  <h1>Payments</h1><p class="sub">Review membership payment screenshots and reference numbers before accepting payments. Product orders remain separate.</p>
  <div class="tabs" id="ktabs"></div>
  <div class="tabs" id="ptabs"></div>
  <input id="qp" placeholder="Search name, plan, product, reference" aria-label="Search payments">
  <div class="box scroll" id="mbox"><table><thead><tr><th>Member</th><th>Plan</th><th>Amount</th><th>Method / Ref / Proof</th><th>Submitted</th><th>Due</th><th>Status</th><th>Action</th></tr></thead><tbody id="pbody"></tbody></table></div>
  <div class="box scroll" id="obox" hidden><table><thead><tr><th>Receipt</th><th>Member</th><th>Product</th><th>Total</th><th>Method / Ref</th><th>Status</th><th>Action</th></tr></thead><tbody id="obody"></tbody></table></div>
</section>

<section class="view" id="v-members">
  <h1>Members</h1><p class="sub">Manage memberships without deleting member accounts, records, or payment history.</p>
  <div class="tabs" id="mtabs"></div>
  <input id="qm" placeholder="Search name or email" aria-label="Search members">
  <div class="box scroll"><table><thead><tr><th>Member</th><th>Email</th><th>Plan</th><th>Due date</th><th>Membership</th><th>Action</th></tr></thead><tbody id="mbody"></tbody></table></div>
</section>

<section class="view" id="v-attendance">
  <h1>Attendance</h1><p class="sub">Generate a code for each class, check members in, and see who came and when.</p>
  <div class="tabs cltabs" hidden></div>
  <div class="cards c3">
    <div class="card"><span>Check-ins</span><strong id="a-n">0</strong><em id="a-d"></em></div>
    <div class="card"><span>Different members</span><strong id="a-u">0</strong><em>Counted once each</em></div>
    <div class="card"><span>Overrides</span><strong id="a-o">0</strong><em>Checked in without active membership</em></div>
  </div>
  <div class="box" style="margin-bottom:14px">
    <h2>Class codes</h2>
    <p class="sub" style="margin-bottom:8px">Generate a code for each class and give it to that class's coach. Members enter it to check in.</p>
    <div id="runlist"></div>
  </div>
  <form class="box at-form" id="af" novalidate>
    <h2>Check in a member</h2>
    <div class="row">
      <label class="fld">Member<select id="am"></select></label>
      <label class="fld">Class<select id="ac"></select></label>
      <button class="btn" type="submit">Check in</button>
    </div>
    <small style="color:var(--mute);font-size:12px">The check-in time is recorded automatically.</small>
  </form>
  <div class="at-filters">
    <label class="fld">Date<input type="date" id="ad"></label>
    <label class="fld">Search<input id="qat" placeholder="Member or class" aria-label="Search attendance"></label>
  </div>
  <div class="box scroll" style="margin-top:14px"><table><thead><tr><th>Time</th><th>Member</th><th>Class</th><th>Membership</th><th>Action</th></tr></thead><tbody id="atbody"></tbody></table></div>
</section>

<section class="view" id="v-messages">
  <h1>Messages</h1>
  <p class="sub">Chat with the FitConnect admin and members of your gym.</p>
  <div class="mail">
    <div class="box">
      <input id="msq" placeholder="Search conversations" aria-label="Search people">
      <div class="tabs" id="mstabs"></div>
      <div id="mslist"></div>
    </div>
    <div class="box">
      <div class="ms-head" id="mshead">Select a conversation</div>
      <div class="ms-body" id="msbody"></div>
      <form class="ms-form" id="msf" hidden>
        <textarea id="msb" placeholder="Write a message (Enter to send)" maxlength="2000" aria-label="Message"></textarea>
        <button class="btn" type="submit">Send</button>
      </form>
    </div>
  </div>
</section>

<section class="view" id="v-profile">
  <h1>My profile</h1>
  <div class="box" style="margin-top:16px">
    <div class="pf-hero">
      <div class="pf-av" id="pav"></div>
      <div>
        <h2 id="pname"></h2>
        <p id="pmail"></p>
        <div class="pf-meta"><span class="tag">Active</span><span class="tag off">Gym owner</span></div>
        <div class="pf-actions">
          <label class="btn ghost sm" style="cursor:pointer">Change photo
            <input type="file" id="avf" accept="image/png,image/jpeg,image/webp" hidden style="width:auto">
          </label>
          <button class="btn ghost sm" type="button" id="avrm">Remove photo</button>
        </div>
      </div>
    </div>
  </div>

  <div class="stats">
    <div class="card"><span>Last login</span><strong id="st-login">-</strong></div>
    <div class="card"><span>Member since</span><strong id="st-since">-</strong></div>
    <div class="card"><span>Permissions</span><strong>Own gym only</strong></div>
  </div>

  <div class="pf-grid">
    <form class="box" id="pf" novalidate>
      <h3>Personal information</h3>
      <p class="sub">Update how your name and contact details appear.</p>
      <label class="fld">Full name<input id="on" autocomplete="name"></label>
      <label class="fld">Email<input id="oe" type="email" autocomplete="email"></label>
      <div class="row">
        <label class="fld">Phone<input id="op" type="tel" autocomplete="tel"></label>
        <label class="fld">Location<input id="ol"></label>
      </div>
      <label class="fld">Role<input value="Gym owner" disabled></label>
      <label class="fld">About you<textarea id="ob"></textarea></label>
      <span class="ferr" id="pf-err" hidden></span>
      <button class="btn" type="submit">Save changes</button><span class="okmsg" id="pf-ok" hidden>Profile saved</span>
    </form>

    <form class="box" id="gf" novalidate>
      <h3>Gym details</h3>
      <p class="sub">Shown to members and the admin.</p>
      <label class="fld">Gym name<input id="gn"></label>
      <label class="fld">Address<input id="ga"></label>
      <label class="fld">Gym phone<input id="gp" type="tel"></label>
      <label class="fld">Gym description<textarea id="gabout" maxlength="2000" placeholder="Describe your gym, facilities, programs, and services."></textarea></label>
      <label class="fld">Opening hours<input id="ghours" maxlength="120" placeholder="Mon–Sat: 6:00 AM–10:00 PM; Sun: 8:00 AM–6:00 PM"></label>
      <span class="ferr" id="gf-err" hidden></span>
      <button class="btn" type="submit">Save gym details</button><span class="okmsg" id="gf-ok" hidden>Gym details saved</span>
    </form>

    <form class="box" id="pw" novalidate>
      <h3>Change password</h3>
      <p class="sub">Use at least 8 characters.</p>
      <label class="fld">Current password<input id="pw0" type="password" autocomplete="current-password"></label>
      <label class="fld">New password<input id="pw1" type="password" autocomplete="new-password"></label>
      <label class="fld">Confirm new password<input id="pw2" type="password" autocomplete="new-password"></label>
      <span class="ferr" id="pw-err" hidden></span>
      <button class="btn" type="submit">Update password</button><span class="okmsg" id="pw-ok" hidden>Password updated</span>
      <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
      <h3>Session</h3>
      <p class="sub">Sign out of the gym owner panel on this device.</p>
      <button class="btn danger" type="button" id="lo2">Log out</button>
    </form>
  </div>
</section>

<section class="view" id="v-activity">
  <h1>Activity log</h1><p class="sub">Everything you and your gym account changed, newest first.</p>
  <input id="qa" placeholder="Search activity" aria-label="Search activity">
  <div class="box scroll"><table><thead><tr><th>When</th><th>Who</th><th>Action</th><th>Details</th></tr></thead><tbody id="abody"></tbody></table></div>
</section>

<section class="view" id="v-classbookings">
  <button class="btn ghost sm" type="button" onclick="location.hash='#classes'">← Back to Classes</button>
  <h1 id="booking-title" style="margin-top:16px">Class Bookings</h1>
  <p class="sub">View session reservations and participants. Class slots are counted for each date.</p>
  <div class="at-filters">
    <label class="fld">Session date<input id="booking-date" type="date"></label>
    <button class="btn ghost" type="button" id="booking-refresh">Refresh</button>
  </div>
  <div class="cards c3">
    <div class="card"><span>Class capacity</span><strong id="booking-capacity">—</strong></div>
    <div class="card"><span>Booked</span><strong id="booking-count">—</strong></div>
    <div class="card"><span>Available slots</span><strong id="booking-free">—</strong></div>
  </div>
  <div class="box scroll">
    <h2>Participants</h2>
    <table><thead><tr><th>Member</th><th>Booked at</th><th>Status</th><th>Attendance</th><th>Action</th></tr></thead>
      <tbody id="booking-rows"><tr><td colspan="5" class="empty">Choose a class to view participants.</td></tr></tbody>
    </table>
  </div>
</section>

<section class="view" id="v-list">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap"><div><h1 id="lh"></h1><p class="sub" id="ls"></p></div><button class="btn" type="button" id="ladd"></button></div>
  <div class="tabs cltabs" hidden></div>
  <div class="box scroll" style="margin-top:14px"><table><thead id="lhead"></thead><tbody id="lbody"></tbody></table></div>
</section>

    </div>
  </section>
</div>

<div class="ov" id="rov" hidden><div class="modal" role="dialog" aria-modal="true" aria-labelledby="rt">
  <h2 id="rt">Revoke payment?</h2><p id="rp"></p>
  <textarea id="rr" hidden placeholder="Reason (required)" aria-label="Reason" style="margin:0 0 14px"></textarea>
  <div class="acts"><button class="btn ghost" type="button" id="rc">Cancel</button><button class="btn danger" type="button" id="rx">Revoke payment</button></div>
</div></div>
<div class="ov" id="lov" hidden><div class="modal" role="dialog" aria-modal="true" aria-labelledby="lt">
  <h2 id="lt">Log out of FitConnect?</h2><p>You will need to sign in again.</p>
  <div class="acts"><button class="btn ghost" type="button" id="lc">Stay signed in</button><button class="btn danger" type="button" id="lx">Log out</button></div>
</div></div>
<div class="ov" id="fov" hidden><form class="modal" id="ff" novalidate aria-labelledby="ft"><h2 id="ft"></h2><div id="fb"></div><span class="msg" id="fe" style="color:#ff8a65;font-size:12px" hidden></span><div class="acts" style="margin-top:16px"><button class="btn ghost" type="button" id="fc">Cancel</button><button class="btn" type="submit">Save</button></div></form></div>

<div class="ov" id="memberDetailsOverlay" hidden>
  <div class="modal member-details-modal" role="dialog" aria-modal="true" aria-labelledby="memberDetailsTitle">
    <h2 id="memberDetailsTitle">Member information</h2>
    <p id="memberDetailsMessage" aria-live="polite">Loading member details...</p>
    <div id="memberDetailsBody" hidden></div>
    <div class="acts"><button type="button" class="btn ghost" id="memberDetailsClose">Close</button></div>
  </div>
</div>
<div class="toast" id="toast" role="status" hidden></div>
<div class="ov" id="proofOverlay" hidden>
  <div class="modal proof-dialog" role="dialog" aria-modal="true" aria-labelledby="proofTitle">
    <h2 id="proofTitle">Payment screenshot</h2>
    <p id="proofMessage" aria-live="polite">Loading screenshot...</p>
    <img id="proofImage" alt="Uploaded payment receipt" hidden>
    <p id="proofError" class="proof-error" hidden></p>
    <div class="acts"><button type="button" class="btn ghost" id="proofClose">Close</button></div>
  </div>
</div>

@verbatim
<script>
const $=id=>document.getElementById(id);
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const day=d=>d?String(d).slice(0,10):'';
const fd=d=>d?new Date(day(d)+'T00:00:00').toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}):'-';
const todayISO=()=>{const d=new Date();return new Date(d-d.getTimezoneOffset()*60000).toISOString().slice(0,10)};
const daysTo=iso=>Math.round((new Date(iso+'T00:00:00')-new Date(todayISO()+'T00:00:00'))/864e5);
const money=v=>'₱'+Number(v||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
let tt;const flash=m=>{const t=$('toast');t.textContent=m;t.hidden=false;clearTimeout(tt);tt=setTimeout(()=>t.hidden=true,2600)};

/* ---------- avatars (profile photos) ---------- */
let AVMAP={};
/* Accepts any /storage/ photo URL and turns it into a relative path, so localhost vs 127.0.0.1:8000 never blocks it */
const safeImg=u=>{try{if(!u)return '';const x=new URL(u,location.origin);return x.pathname.includes('/storage/')?x.pathname+x.search:''}catch(e){return ''}};
/* My own photo, handed over by Blade so it survives refresh and new sessions */
let myAv=document.querySelector('meta[name="my-avatar"]')?.content||null;
const avOf=id=>AVMAP[id]||null;
function avatar(name,url,extra=''){
  const s=safeImg(url);
  return `<span class="av ${extra}">${esc(inits(name||''))}${s?`<img src="${esc(s)}" alt="" loading="lazy" onerror="this.remove()">`:''}</span>`;
}
const who=(name,id,inner)=>`<span class="who">${avatar(name,avOf(id))}${inner}</span>`;
function setAv(el,name,url){
  el.textContent=inits(name||'');
  const s=safeImg(url);
  if(s){const i=new Image();i.alt='';i.onerror=()=>i.remove();i.src=s;el.appendChild(i)}
}
async function loadAvatars(){
  try{
    const j=await api('/api/owner/avatars');
    const m={};(j.avatars||[]).forEach(a=>m[a.id]=a.url);
    if(JSON.stringify(m)!==JSON.stringify(AVMAP)){
      AVMAP=m;render();renderAttendance();
      if(location.hash==='#messages')renderMail();
    }
  }catch(e){}
}

/* ================= REAL API (Laravel, session + CSRF) ================= */
async function api(url,method='GET',body=null){
  const res=await fetch(url,{
    method,
    credentials:'same-origin',
    headers:{
      'Content-Type':'application/json',
      'Accept':'application/json',
      'X-Requested-With':'XMLHttpRequest',
      'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
    },
    body:body?JSON.stringify(body):null
  });
  const data=await res.json().catch(()=>({}));
  if(res.status===401||res.status===419){window.location.href='/log_in';throw new Error('Session expired')}
  if(!res.ok){
    const first=data.errors?Object.values(data.errors)[0][0]:null;
    throw new Error(first||data.message||'Request failed');
  }
  return data;
}

/* ================= STATE (all data comes from the real API, no demo data) ================= */
const DB={gym:{id:1,gym_name:'My Gym',status:'active'},trainers:[],classes:[],plans:[],products:[]};

/* ---- dashboard data ---- */
let GYM=null,MEMBERS=[],PAY=[],pTab='pending',mTab='all',qp='',qm='',busy=false,pendingPay=null,pendingFn=null,loadErr=false;
let ORDERS=[],pKind='members';
async function load(){
  try{PAY=(await api('/api/owner/payments')).payments||[];loadErr=false}
  catch(e){if(!loadErr)flash('Could not load your payments.');loadErr=true}
  if(!GYM)GYM=DB.gym;
  render();
}

/* ---- members (real API) ---- */
async function loadMembers(){
  try{MEMBERS=(await api('/api/owner/members')).members||[]}
  catch(e){flash(e.message)}
  render();
}

function info(u){
  /* real members: the server decides the membership state, unless it says 'none' (then we check payments) */
  if(u.membership_state && u.membership_state!=='none'){
    return {state:u.membership_state,pend:u.membership_state==='pending'?1:0,due:u.due_date?day(u.due_date):'',plan:u.plan||'-'};
  }
  const mine=PAY.filter(p=>p.user_id==u.user_id);
  const acc=mine.filter(p=>p.status==='accepted').sort((a,b)=>day(b.due_date).localeCompare(day(a.due_date)));
  const pend=mine.filter(p=>p.status==='pending').length,cur=acc[0];
  let state='none';
  if(cur)state=day(cur.due_date)>=todayISO()?'active':'overdue';else if(pend)state='pending';
  return {state,pend,due:cur?day(cur.due_date):'',plan:cur?cur.plan:(mine[0]?mine[0].plan:'-')};
}
const MS={deactivated:['Deactivated','deactivated'],inactive:['Deactivated','deactivated'],active:['Active',''],overdue:['Overdue','rej'],pending:['Pending','pending'],none:['No membership','off']};
const PS={pending:['Pending','pending'],accepted:['Accepted',''],revoked:['Revoked','rej']};
const OS={pending:['Pending','pending'],paid:['Paid',''],completed:['Handed over',''],cancelled:['Cancelled','rej']};
const dueText=iso=>{const d=daysTo(iso);return fd(iso)+`<small>${d>0?d+' day'+(d===1?'':'s')+' left':d===0?'due today':Math.abs(d)+' day'+(d===-1?'':'s')+' overdue'}</small>`};

/* ---- render ---- */
function render(){
  if(!GYM){return}
  $('gname').textContent=GYM.gym_name;
  $('pendbanner').hidden=GYM.status==='active';
  const infos=MEMBERS.map(u=>({u,i:info(u)}));
  const n=s=>infos.filter(x=>x.i.state===s).length;
  const pend=PAY.filter(p=>p.status==='pending');
  const ordPend=ORDERS.filter(o=>o.status==='pending');
  const now=new Date(),ym=d=>day(d).slice(0,7);
  const key=(y,m)=>y+'-'+String(m+1).padStart(2,'0');
  const cur=key(now.getFullYear(),now.getMonth()),pd=new Date(now.getFullYear(),now.getMonth()-1,1),prev=key(pd.getFullYear(),pd.getMonth());
  /* revenue = accepted membership payments + paid / handed-over product orders */
  const accO=ORDERS.filter(o=>o.status==='paid'||o.status==='completed').map(o=>({amount:o.total,paid_at:o.paid_at||o.created_at}));
  const acc=[...PAY.filter(p=>p.status==='accepted'),...accO];
  const sum=k=>acc.filter(p=>ym(p.paid_at)===k).reduce((s,p)=>s+Number(p.amount||0),0);
  const rc=sum(cur),rp=sum(prev);
  const allPend=pend.length+ordPend.length;
  $('c-act').textContent=n('active');$('c-tot').textContent=MEMBERS.length+' total members';
  $('c-rev').textContent=money(rc);
  $('c-chg').textContent=rp?((rc>=rp?'+':'')+Math.round((rc-rp)/rp*100)+'% vs last month'):'No data last month';
  $('c-pend').textContent=allPend;$('c-over').textContent=n('overdue');
  const nb=$('nav-p');nb.hidden=!pend.length;nb.textContent=pend.length;
  const no=$('nav-o');no.hidden=!ordPend.length;no.textContent=ordPend.length;

  const soon=infos.filter(x=>x.i.state==='active'&&x.i.due&&daysTo(x.i.due)<=7).sort((a,b)=>a.i.due.localeCompare(b.i.due));
  const over=infos.filter(x=>x.i.state==='overdue'&&x.i.due);
  let a='';
  pend.slice(0,4).forEach(p=>a+=`<div class="att"><div>${esc(p.name)}<br><small>${money(p.amount)} · ${esc(p.plan)} waiting for verification</small></div><a class="btn sm ghost" href="#payments">Review</a></div>`);
  ordPend.slice(0,3).forEach(o=>a+=`<div class="att"><div>${esc(o.name)}<br><small>${money(o.total)} · ${esc(o.product)} order waiting</small></div><a class="btn sm" href="#orders" data-goorders>Review</a></div>`);
  soon.slice(0,3).forEach(({u,i})=>a+=`<div class="att"><div>${esc(u.name)}<br><small>Expires ${daysTo(i.due)===0?'today':'in '+daysTo(i.due)+' days'}</small></div><span class="tag pending">Expiring</span></div>`);
  over.slice(0,3).forEach(({u,i})=>a+=`<div class="att"><div>${esc(u.name)}<br><small>${Math.abs(daysTo(i.due))} days overdue</small></div><span class="tag rej">Overdue</span></div>`);
  $('att').innerHTML=a||'<p class="empty">All caught up. Nothing needs your attention.</p>';

  $('recent').innerHTML=[...PAY].sort((x,y)=>day(y.paid_at).localeCompare(day(x.paid_at))).slice(0,5).map(p=>{const s=PS[p.status]||PS.pending;
    return `<tr><td>${esc(p.name)}<small>${fd(p.paid_at)}</small></td><td class="nowrap">${money(p.amount)}</td><td><span class="tag ${s[1]}">${s[0]}</span></td></tr>`}).join('')||'<tr><td class="empty">No payments yet</td></tr>';

  drawRevenue(acc);renderPayments(infos);renderMembers(infos);fillAttForm();
  renderMailBadge();if(location.hash==='#messages'){renderMail();renderThread()}
}

function drawRevenue(acc){
  const now=new Date(),mo=[];
  for(let i=5;i>=0;i--){const d=new Date(now.getFullYear(),now.getMonth()-i,1);mo.push({k:d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0'),l:d.toLocaleDateString('en-US',{month:'short'})})}
  const vals=mo.map(m=>acc.filter(p=>day(p.paid_at).slice(0,7)===m.k).reduce((s,p)=>s+Number(p.amount||0),0));
  const max=Math.max(1,...vals),W=800,H=300,L=20,B=40,T=24,bw=(W-L*2)/mo.length;
  let s='';
  vals.forEach((v,i)=>{const h=v/max*(H-B-T),x=L+i*bw+bw*.2,y=H-B-h;
    s+=`<rect x="${x}" y="${y}" width="${bw*.6}" height="${h}" rx="4" fill="var(--accent)"/><text x="${x+bw*.3}" y="${y-6}" text-anchor="middle">${v?'₱'+Math.round(v).toLocaleString():''}</text><text class="m" x="${x+bw*.3}" y="${H-16}" text-anchor="middle">${mo[i].l}</text>`});
  $('rev').innerHTML=s;
}

/* ---- trainers + classes (real API) ---- */
async function loadTrainers(){
  try{DB.trainers=(await api('/api/owner/trainers')).trainers||[]}catch(e){flash(e.message)}
  if(cur==='trainers')renderList();
}
async function loadClasses(){
  try{DB.classes=(await api('/api/owner/classes')).classes||[]}catch(e){flash(e.message)}
  if(cur==='classes')renderList();
  fillAttForm();
}

/* ---- plans (real API) ---- */
async function loadPlans(){
  try{DB.plans=(await api('/api/owner/plans')).plans||[]}catch(e){flash(e.message)}
  if(cur==='plans')renderList();
}

/* ---- products + product orders (real API) ---- */
async function loadProducts(){
  try{DB.products=(await api('/api/owner/products')).products||[]}catch(e){flash(e.message)}
  if(cur==='products')renderList();
}
async function loadOrders(){
  try{ORDERS=(await api('/api/owner/orders')).orders||[]}catch(e){flash(e.message)}
  render();
}
async function setOrder(id,status){
  if(busy)return;busy=true;
  try{
    await api('/api/owner/orders/'+id,'PATCH',{status});
    flash('Order '+(status==='completed'?'handed over':status));
    await loadOrders();await loadProducts();
  }catch(e){flash(e.message)}finally{busy=false}
}

function renderPayments(){
  const isO=pKind==='orders';
  const pm=PAY.filter(p=>p.status==='pending').length,po=ORDERS.filter(o=>o.status==='pending').length;
  $('ktabs').innerHTML=[['members','Memberships',pm],['orders','Product orders',po]].map(([k,l,n])=>`<button type="button" class="tab ${pKind===k?'on':''}" data-kind="${k}">${l} <b>${n}</b></button>`).join('');
  const list=isO?ORDERS:PAY,cnt={pending:list.filter(x=>x.status==='pending').length,all:list.length};
  $('ptabs').innerHTML=[['pending','Pending'],['all','All']].map(([k,l])=>`<button type="button" class="tab ${pTab===k?'on':''}" data-ptab="${k}">${l} <b>${cnt[k]}</b></button>`).join('');
  $('mbox').hidden=isO;$('obox').hidden=!isO;
  const q=qp.toLowerCase();
  if(!isO){
    $('pbody').innerHTML=PAY.filter(p=>(pTab==='all'||p.status==='pending')&&[p.name,p.plan,p.method,p.reference].join(' ').toLowerCase().includes(q)).map(p=>{
      const s=PS[p.status]||PS.pending;
      const acc=p.status==='pending'?`<button class="btn sm" type="button" data-pay="accept" data-id="${p.id}">Accept</button>`:'';
      const proof=p.proof_path ? `<button class="lnk" type="button" data-proof-type="membership" data-proof-id="${Number(p.id)}">View payment screenshot</button>` : `<span class="tag rej">No screenshot</span>`;
      const rev=p.status!=='revoked'?`<button class="btn sm ghost" type="button" data-pay="revoke" data-id="${p.id}">Revoke</button>`:'';
      return `<tr><td>${who(p.name,p.user_id,esc(p.name))}</td><td>${esc(p.plan)}</td><td class="nowrap">${money(p.amount)}</td><td>${esc(p.method||'-')}<small>Ref: ${esc(p.reference||'-')}</small><small>${proof}</small></td><td class="nowrap">${fd(p.paid_at)}</td><td class="nowrap">${p.status==='accepted'?fd(p.due_date):'-'}</td><td><span class="tag ${s[1]}">${s[0]}</span></td><td><div class="acts">${acc}${rev}</div></td></tr>`;
    }).join('')||`<tr><td colspan="8" class="empty">${pTab==='pending'?'No payments waiting for approval':'No payments found'}</td></tr>`;
    return;
  }
  $('obody').innerHTML=ORDERS.filter(o=>(pTab==='all'||o.status==='pending')&&[o.name,o.product,o.method,o.reference,o.receipt].join(' ').toLowerCase().includes(q)).map(o=>{
    const s=OS[o.status]||OS.pending;let a='';
    if(o.status==='pending' && o.proof_path && o.reference)a+=`<button class="btn sm" type="button" data-ord="paid" data-id="${o.id}">Mark paid</button>`;
    if(o.status==='paid' && (o.fulfillment_method||'pickup')==='pickup'){
      if(!o.fulfillment_status || o.fulfillment_status==='awaiting_preparation')a+=`<button class="btn sm" type="button" data-pickup="preparing" data-id="${o.id}">Prepare Order</button>`;
      if(o.fulfillment_status==='preparing')a+=`<button class="btn sm" type="button" data-pickup="ready_for_pickup" data-id="${o.id}">Ready for Pickup</button>`;
      if(o.fulfillment_status==='ready_for_pickup'){
        a+=`<button class="btn sm" type="button" data-pickup="picked_up" data-id="${o.id}">Picked Up</button>`;
        a+=`<button class="btn sm ghost" type="button" data-ord="completed" data-id="${o.id}">Complete</button>`;
      }
    }
    if(o.status==='paid' && o.fulfillment_method==='delivery'){
      const st=o.fulfillment_status||'awaiting_preparation';
      if(st==='awaiting_preparation')a+=`<button class="btn sm" type="button" data-delivery="preparing" data-id="${o.id}">Prepare Order</button>`;
      if(st==='preparing')a+=`<button class="btn sm" type="button" data-delivery="out_for_delivery" data-id="${o.id}">Out for Delivery</button>`;
      if(st==='out_for_delivery')a+=`<button class="btn sm" type="button" data-delivery="delivered" data-id="${o.id}">Delivered</button>`;
    }
    if((o.status==='pending'||o.status==='paid') && !(o.fulfillment_method==='delivery' && o.fulfillment_status==='out_for_delivery'))a+=`<button class="btn sm ghost" type="button" data-ord="cancelled" data-id="${o.id}">Cancel Order</button>`;
    return `<tr><td class="nowrap">${esc(o.receipt)}<small>${fd(o.created_at)}</small></td><td>${who(o.name,o.user_id,esc(o.name))}</td><td>${esc(o.product)} × ${o.quantity}<small>${esc((o.fulfillment_method||'pickup')==='pickup'?'Gym pickup':'Delivery')} · ${esc(o.fulfillment_status||'Awaiting preparation')}${o.fulfillment_method==='delivery'?`<br>📍 ${esc(o.delivery_address||'Address missing')}<br>☎ ${esc(o.delivery_phone||'Phone missing')}${o.delivery_notes?`<br>Note: ${esc(o.delivery_notes)}`:''}`:''}</small></td><td class="nowrap">${money(o.total)}</td><td>${esc(o.method||'-')}<small>Ref: ${esc(o.reference||'-')}</small><small>${o.proof_path?`<button class="lnk" type="button" data-proof-type="product" data-proof-id="${Number(o.id)}">View payment screenshot</button>`:'No screenshot uploaded'}</small></td><td><span class="tag ${s[1]}">${s[0]}</span><small>${esc((o.fulfillment_status||"awaiting_preparation").replace(/_/g," "))}</small></td><td><div class="acts">${a}</div></td></tr>`;
  }).join('')||`<tr><td colspan="7" class="empty">${pTab==='pending'?'No orders waiting for confirmation':'No product orders yet'}</td></tr>`;
}

function renderMembers(infos){
  const n=s=>infos.filter(x=>x.i.state===s).length;
  $('mtabs').innerHTML=[['all','All'],['active','Active'],['overdue','Overdue'],['pending','Pending'],['deactivated','Deactivated'],['none','No membership']].map(([k,l])=>`<button type="button" class="tab ${mTab===k?'on':''}" data-mtab="${k}">${l} <b>${k==='all'?infos.length:n(k)}</b></button>`).join('');
  const q=qm.toLowerCase();
  $('mbody').innerHTML=infos.filter(x=>(mTab==='all'||x.i.state===mTab)&&(x.u.name+' '+x.u.email).toLowerCase().includes(q)).map(({u,i})=>{const s=MS[i.state]||MS.none;
    return `<tr><td>${who(u.name,u.user_id,esc(u.name))}</td><td>${esc(u.email)}</td><td>${esc(i.plan)}</td><td class="nowrap">${i.due?dueText(i.due):'-'}</td><td><span class="tag ${s[1]}">${s[0]}</span></td><td><div class="acts"><button class="btn sm" type="button" data-view-member="${Number(u.id)}">View Details</button>${!['deactivated','inactive','none'].includes(i.state)?`<button class="btn sm ghost" type="button" data-deactivate-membership="${Number(u.id)}">Deactivate Membership</button>`:''}</div></td></tr>`}).join('')||'<tr><td colspan="6" class="empty">No members in this group</td></tr>';
}


/* ---- read-only member profile modal (scoped by the server to this gym) ---- */
const memberDetailField=(label,value,wide=false)=>`<div class="member-details-field ${wide?'member-details-wide':''}"><small>${esc(label)}</small><strong>${esc(value===null||value===undefined||value===''?'-':value)}</strong></div>`;
let memberDetailsSequence=0;
function closeMemberDetails(){
  memberDetailsSequence++;
  $('memberDetailsOverlay').hidden=true;
  $('memberDetailsBody').innerHTML='';
  $('memberDetailsBody').hidden=true;
}
async function viewMemberDetails(id){
  if(!Number.isSafeInteger(id)||id<1)return;
  const sequence=++memberDetailsSequence;
  $('memberDetailsTitle').textContent='Member information';
  $('memberDetailsMessage').textContent='Loading member details...';
  $('memberDetailsBody').hidden=true;
  $('memberDetailsOverlay').hidden=false;
  $('memberDetailsClose').focus();
  try{
    const result=await api('/api/owner/members/'+id);
    if(sequence!==memberDetailsSequence)return;
    const m=result.member;
    if(!m)throw new Error('The member profile was not returned.');
    const state=MS[m.membership_state]||MS.none;
    const last=m.last_check_in?new Date(String(m.last_check_in).replace(' ','T')).toLocaleString():'-';
    $('memberDetailsTitle').textContent=m.name||'Member information';
    $('memberDetailsMessage').innerHTML=`<span class="tag ${state[1]}">${esc(state[0])}</span> &nbsp; Read-only profile`;
    $('memberDetailsBody').innerHTML=`<div class="member-details-grid">
      ${memberDetailField('Full name',m.name)}
      ${memberDetailField('Email address',m.email)}
      ${memberDetailField('Contact number',m.phone)}
      ${memberDetailField('Gender',m.gender)}
      ${memberDetailField('Location / address',m.location,true)}
      ${memberDetailField('Gym enrollment date',m.join_date?fd(m.join_date):'-')}
      ${memberDetailField('Membership plan',m.plan)}
      ${memberDetailField('Membership expiry',m.due_date?fd(m.due_date):'-')}
      ${memberDetailField('Membership approved',m.paid_at?fd(m.paid_at):'-')}
      ${memberDetailField('Total gym check-ins',m.attendance_count)}
      ${memberDetailField('Last check-in',last)}
      ${memberDetailField('Fitness goal',m.fitness_goal)}
      ${memberDetailField('Emergency contact',m.emergency_contact_name)}
      ${memberDetailField('Emergency phone',m.emergency_contact_number)}
      ${memberDetailField('About member',m.bio,true)}
    </div>`;
    $('memberDetailsBody').hidden=false;
  }catch(err){
    if(sequence!==memberDetailsSequence)return;
    $('memberDetailsMessage').textContent='Could not load member details: '+err.message;
  }
}
$('memberDetailsClose').onclick=closeMemberDetails;
$('memberDetailsOverlay').addEventListener('click',e=>{if(e.target===$('memberDetailsOverlay'))closeMemberDetails()});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!$('memberDetailsOverlay').hidden)closeMemberDetails()});
document.addEventListener('click',e=>{
  const btn=e.target.closest('[data-view-member]');
  if(btn)viewMemberDetails(Number(btn.dataset.viewMember));
});

/* ---- membership payment actions (real API) ---- */
async function setPay(id,status){
  if(busy)return;const p=PAY.find(x=>x.id==id);if(!p)return;busy=true;
  try{await api('/api/owner/payments/'+id,'PATCH',{status});flash(p.name+'\u2019s payment '+status);await load();await loadMembers()}
  catch(e){flash(e.message)}finally{busy=false}
}
const closeRev=()=>{$('rov').hidden=true;$('rr').hidden=true;pendingPay=null;pendingFn=null};
function askConfirm(t,p,label,fn,withReason){
  $('rt').textContent=t;$('rp').textContent=p;$('rx').textContent=label;pendingFn=fn;
  $('rr').value='';$('rr').hidden=!withReason;$('rov').hidden=false;
  (withReason?$('rr'):$('rc')).focus();
}
function askRevoke(id){const p=PAY.find(x=>x.id==id);if(!p)return;pendingPay=p.id;
  $('rt').textContent='Revoke payment from '+p.name+'?';
  $('rp').textContent=p.status==='accepted'?'The membership period from this payment will be removed and the due date recalculated.':'This payment will be marked as revoked and will not count toward the membership.';
  pendingFn=()=>setPay(p.id,'revoked');$('rx').textContent='Revoke payment';$('rr').hidden=true;$('rov').hidden=false;$('rc').focus()}
$('rc').onclick=closeRev;
$('rx').onclick=async()=>{
  if(!$('rr').hidden&&!$('rr').value.trim()){$('rr').focus();return}
  const f=pendingFn,r=$('rr').value.trim();closeRev();if(f)await f(r);
};
$('rov').addEventListener('click',e=>{if(e.target===$('rov'))closeRev()});

/* ---- log out (real) ---- */
$('lo').onclick=()=>{$('lov').hidden=false;$('lc').focus()};
$('lo2').onclick=$('lo').onclick;
$('lc').onclick=()=>$('lov').hidden=true;
$('lx').onclick=async()=>{
  $('lx').disabled=true;
  try{await api('/logout','POST')}catch(e){}
  window.location.href='/log_in';
};
$('lov').addEventListener('click',e=>{if(e.target===$('lov'))$('lov').hidden=true});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeRev();closeForm();$('lov').hidden=true}});

/* ---- trainers, classes, plans, products (all real API) ---- */
let cur='trainers';
const API={products:'/api/owner/products',plans:'/api/owner/plans',trainers:'/api/owner/trainers',classes:'/api/owner/classes'};
const LOAD={products:()=>loadProducts(),plans:()=>loadPlans(),trainers:async()=>{await loadTrainers();await loadClasses()},classes:()=>loadClasses()};
const CFG={
 trainers:{t:'Trainers',s:'Coaches who work at your gym.',add:'Add trainer',head:['Name','Specialty','Phone'],f:[['name','Name'],['specialty','Specialty'],['phone','Phone']],row:x=>[esc(x.name),esc(x.specialty),esc(x.phone)]},
 classes:{t:'Classes',s:'Manage schedules, bookings, available slots, and attendance.',add:'Add class',head:['Class','Coach','Schedule','Capacity'],
  f:[['name','Class name'],['trainer_id','Coach','select'],['days','Days','days'],['start_time','Start time','time'],['duration_min','Duration (minutes)','number'],['capacity','Capacity','number']],
  row:x=>[esc(x.name),esc(x.trainer_name||'-'),esc(x.schedule_label),x.capacity],extra:x=>`<button class="btn sm" type="button" data-classbookings="${Number(x.id)}">Bookings</button>`},
 plans:{t:'Membership plans',s:'The plans members choose when they pay.',add:'Add plan',head:['Plan','Price','Duration'],f:[['name','Plan name'],['price','Price (₱)','number'],['days','Duration (days)','number']],row:x=>[esc(x.name),money(x.price),x.days+' day'+(x.days==1?'':'s')]},
 products:{t:'Products',s:'Items you sell. Members see these in their shop.',add:'Add product',head:['Product','Price','In stock','Sold'],f:[['name','Product name'],['price','Price (₱)','number'],['stock','Stock','number']],
  row:x=>[esc(x.name),money(x.price),x.stock?x.stock:'<span class="tag rej">Out of stock</span>',x.sold],extra:x=>`<button class="btn sm" type="button" data-sell="${x.id}" ${x.stock?'':'disabled'}>Sell 1</button>`}
};
function renderList(){
  const c=CFG[cur],items=DB[cur];
  $('lh').textContent=c.t;$('ls').textContent=c.s;$('ladd').textContent=c.add;
  $('lhead').innerHTML='<tr>'+c.head.map(h=>`<th>${h}</th>`).join('')+'<th>Action</th></tr>';
  $('lbody').innerHTML=items.map(x=>'<tr>'+c.row(x).map(v=>`<td>${v}</td>`).join('')+`<td><div class="acts">${c.extra?c.extra(x):''}<button class="btn sm ghost" type="button" data-edit="${x.id}">Edit</button><button class="btn sm ghost" type="button" data-del="${x.id}">Delete</button></div></td></tr>`).join('')||`<tr><td colspan="${c.head.length+1}" class="empty">Nothing here yet. Use "${c.add}" to create one.</td></tr>`;
}
let editId=null;
const DAYS=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
function fieldHtml([k,l,t],it){
  if(t==='select')return `<label class="fld">${l}<select name="${k}">${DB.trainers.map(x=>`<option value="${x.id}" ${it&&it.trainer_id==x.id?'selected':''}>${esc(x.name)}</option>`).join('')}</select></label>`;
  if(t==='days'){const on=it?it.days:[];return `<div class="fld">${l}<div class="acts" style="margin-top:6px">${DAYS.map((d,i)=>`<label style="display:flex;gap:4px;align-items:center;color:var(--ink)"><input type="checkbox" name="${k}" value="${i+1}" ${on.includes(i+1)?'checked':''} style="width:auto;margin:0">${d}</label>`).join('')}</div></div>`}
  if(t==='time')return `<label class="fld">${l}<input type="time" name="${k}" value="${it?esc(String(it[k]).slice(0,5)):''}"></label>`;
  return `<label class="fld">${l}<input name="${k}" value="${it?esc(it[k]):''}" ${t==='number'?'type="number" min="0" step="any"':''}></label>`;
}
function openForm(it){
  const c=CFG[cur];
  if(cur==='classes'&&!DB.trainers.length){flash('Add a trainer first');return}
  editId=it?it.id:null;$('ft').textContent=it?'Edit '+it.name:c.add;$('fe').hidden=true;
  $('fb').innerHTML=c.f.map(f=>fieldHtml(f,it)).join('');
  $('fov').hidden=false;$('fb').querySelector('input,select').focus();
}
const closeForm=()=>$('fov').hidden=true;
$('ladd').onclick=()=>openForm();$('fc').onclick=closeForm;
$('fov').addEventListener('click',e=>{if(e.target===$('fov'))closeForm()});
$('ff').addEventListener('submit',async e=>{
  e.preventDefault();const c=CFG[cur],o={};
  for(const [k,l,t] of c.f){
    if(t==='days'){
      const v=[...$('ff').querySelectorAll(`input[name="${k}"]:checked`)].map(x=>Number(x.value));
      if(!v.length){$('fe').textContent='Pick at least one day';$('fe').hidden=false;return}
      o[k]=v;continue}
    const v=$('ff').elements[k].value.trim();
    if(!v){$('fe').textContent='Fill in: '+l;$('fe').hidden=false;return}
    o[k]=t==='number'?Number(v):v}
  if(API[cur]){
    try{
      if(editId)await api(API[cur]+'/'+editId,'PUT',o);
      else await api(API[cur],'POST',o);
      closeForm();flash(editId?'Updated':'Added');await LOAD[cur]();
    }catch(err){$('fe').textContent=err.message;$('fe').hidden=false}
    return;
  }
  o.id=Date.now();
  if(editId){const t=DB[cur].find(y=>y.id==editId);delete o.id;Object.assign(t,o);closeForm();flash(t.name+' updated');renderList();return}
  DB[cur].push(o);closeForm();flash(c.add.replace('Add ','')+' added');renderList();
});
document.addEventListener('click',e=>{
  const d=e.target.closest('[data-del]');
  if(d){const x=DB[cur].find(y=>y.id==d.dataset.del);if(!x)return;
    askConfirm('Delete '+x.name+'?','This removes it from your gym. This cannot be undone.','Delete',async()=>{
      if(API[cur]){
        try{await api(API[cur]+'/'+x.id,'DELETE');flash(x.name+' deleted');await LOAD[cur]()}catch(err){flash(err.message)}
        return;
      }
      DB[cur]=DB[cur].filter(y=>y.id!=x.id);flash(x.name+' deleted');renderList();
    });return}
  const ed=e.target.closest('[data-edit]');
  if(ed){const x=DB[cur].find(y=>y.id==ed.dataset.edit);if(x)openForm(x);return}
  const sl=e.target.closest('[data-sell]');
  if(sl){
    api('/api/owner/products/'+sl.dataset.sell+'/sell','POST')
      .then(()=>{flash('Sold 1 (walk-in)');loadProducts();loadOrders()})
      .catch(err=>flash(err.message));
    return;
  }
  const deactivate=e.target.closest('[data-deactivate-membership]');
  if(deactivate){
    const u=MEMBERS.find(y=>Number(y.id)===Number(deactivate.dataset.deactivateMembership));
    if(!u)return;
    askConfirm('Deactivate '+u.name+'\'s membership?',
      'The member will retain their account, gym association and payment history. Their membership access will be disabled until reactivated by an authorized process.',
      'Deactivate Membership',async()=>{
        try{
          await api('/api/owner/members/'+u.id+'/membership/deactivate','PATCH');
          flash(u.name+'\'s membership deactivated');
          await loadMembers(); await load();
        }catch(err){flash('Could not deactivate membership: '+err.message)}
      });
    return;
  }
});

/* ================= PROFILE (real API) ================= */
let OWNER={},GYMP={};
const inits=n=>String(n||'').split(/\s+/).filter(Boolean).slice(0,2).map(w=>w[0].toUpperCase()).join('')||'O';
function setErr(id,m){const e=$(id);e.textContent=m||'';e.hidden=!m}
function okFlash(id){const o=$(id);o.hidden=false;setTimeout(()=>o.hidden=true,2500)}
function renderOwner(){setAv($('tav'),OWNER.name,myAv)}
function fillProfile(){
  if(!OWNER.name&&!OWNER.email)return;
  setAv($('pav'),OWNER.name,myAv);$('pname').textContent=OWNER.name||'';$('pmail').textContent=OWNER.email||'';
  $('st-login').textContent=OWNER.last_login_at?new Date(OWNER.last_login_at).toLocaleString([], {month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'}):'-';
  $('st-since').textContent=OWNER.created_at?fd(OWNER.created_at):'-';
  $('on').value=OWNER.name||'';$('oe').value=OWNER.email||'';$('op').value=OWNER.phone||'';$('ol').value=OWNER.location||'';$('ob').value=OWNER.bio||'';
  $('gn').value=GYMP.gym_name||'';$('ga').value=GYMP.gym_address||'';$('gp').value=GYMP.phone_number||'';$('gabout').value=GYMP.about||'';$('ghours').value=GYMP.hours||'';
  ['pf-err','gf-err','pw-err','pf-ok','gf-ok','pw-ok'].forEach(i=>{$(i).hidden=true});
  renderOwner();
}
async function loadProfile(){
  try{
    const j=await api('/api/owner/profile');
    OWNER=j.user||{};GYMP=j.gym||{};
    if(OWNER.avatar_url)myAv=OWNER.avatar_url;
    if(GYMP.gym_name){DB.gym.gym_name=GYMP.gym_name;if(GYM)GYM.gym_name=GYMP.gym_name;if($('gname'))$('gname').textContent=GYMP.gym_name}
    fillProfile();
  }catch(e){flash(e.message)}
}

$('pf').addEventListener('submit',async e=>{e.preventDefault();
  const n=$('on').value.trim();
  $('pf-ok').hidden=true;
  if(!n)return setErr('pf-err','Enter your full name.');
  setErr('pf-err','');
  const btn=e.submitter;if(btn)btn.disabled=true;
  try{
    await api('/api/owner/profile','PUT',{name:n,email:$('oe').value.trim(),phone:$('op').value.trim(),location:$('ol').value.trim(),bio:$('ob').value.trim()});
    await loadProfile();okFlash('pf-ok');flash('Profile saved');
  }catch(err){setErr('pf-err',err.message)}
  finally{if(btn)btn.disabled=false}
});

$('gf').addEventListener('submit',async e=>{e.preventDefault();
  const n=$('gn').value.trim();
  $('gf-ok').hidden=true;
  if(!n)return setErr('gf-err','Gym name is required.');
  setErr('gf-err','');
  const btn=e.submitter;if(btn)btn.disabled=true;
  try{
    await api('/api/owner/gym','PUT',{gym_name:n,gym_address:$('ga').value.trim(),phone_number:$('gp').value.trim(),about:$('gabout').value.trim(),hours:$('ghours').value.trim()});
    await loadProfile();okFlash('gf-ok');flash('Gym details saved');
  }catch(err){setErr('gf-err',err.message)}
  finally{if(btn)btn.disabled=false}
});

$('pw').addEventListener('submit',async e=>{e.preventDefault();
  const a=$('pw0').value,b=$('pw1').value,c=$('pw2').value;
  $('pw-ok').hidden=true;
  if(!a)return setErr('pw-err','Enter your current password.');
  if(b.length<8)return setErr('pw-err','New password must be at least 8 characters.');
  if(b!==c)return setErr('pw-err','New password and confirmation do not match.');
  setErr('pw-err','');
  const btn=e.submitter;if(btn)btn.disabled=true;
  try{
    await api('/api/owner/password','PUT',{current_password:a,password:b,password_confirmation:c});
    $('pw').reset();okFlash('pw-ok');flash('Password updated');
  }catch(err){setErr('pw-err',err.message)}
  finally{if(btn)btn.disabled=false}
});

/* ---- profile photo (upload / remove) ---- */
$('avf').addEventListener('change',async e=>{
  const f=e.target.files[0];e.target.value='';
  if(!f)return;
  if(f.size>2*1024*1024)return flash('Image must be under 2 MB.');
  const body=new FormData();body.append('avatar',f);
  try{
    const r=await fetch('/api/profile/avatar',{method:'POST',credentials:'same-origin',
      headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest',
      'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body});
    const j=await r.json().catch(()=>({}));
    if(!r.ok)throw new Error(j.errors?Object.values(j.errors)[0][0]:(j.message||'Upload failed'));
    OWNER.avatar_url=j.avatar_url;myAv=j.avatar_url;fillProfile();flash('Photo updated');
  }catch(err){flash(err.message)}
});
$('avrm').onclick=async()=>{
  try{await api('/api/profile/avatar','DELETE');OWNER.avatar_url=null;myAv=null;fillProfile();flash('Photo removed')}
  catch(err){flash(err.message)}
};

/* ---- messages (real Laravel API; shared with Admin Dashboard) ---- */
let msKey=null,msFilter='all',chatContacts=[],chatMessages=[],myChatId=null;
let chatThreadRequest=0,chatSending=false;
const msTime=ts=>ts?new Date(ts).toLocaleString([],{month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}):'';

async function loadChatContacts(){
  try{
    const j=await api('/api/chat/contacts');
    chatContacts=(j.contacts||[]).filter(c=>['admin','member'].includes(c.role));
    if(msKey&&!chatContacts.some(c=>String(c.id)===String(msKey))){msKey=null;chatMessages=[];myChatId=null;chatThreadRequest++;renderThread();}
    renderMailBadge();
    if(location.hash==='#messages')renderMail();
  }catch(e){console.error('Cannot load chat contacts:',e);if(location.hash==='#messages')flash(e.message)}
}
function renderMailBadge(){
  const n=chatContacts.reduce((sum,c)=>sum+Number(c.unread||0),0);
  for(const id of ['mail-n','nav-chat']){const b=$(id);if(!b)continue;b.hidden=!n;b.textContent=n>99?'99+':n;}
}
function renderMail(){
  $('mstabs').innerHTML=[['all','All'],['admin','Admin'],['member','Members']]
    .map(([k,l])=>`<button type="button" class="tab ${msFilter===k?'on':''}" data-mstab="${k}">${l}</button>`).join('');
  const q=$('msq').value.trim().toLowerCase();
  const list=chatContacts.filter(p=>String(p.name||'').toLowerCase().includes(q))
    .filter(p=>msFilter==='all'||p.role===msFilter);
  $('mslist').innerHTML=list.map(p=>`<button type="button" class="cv ${String(p.id)===String(msKey)?'on':''}" data-conv="${p.id}">
    ${avatar(p.name,avOf(p.id))}<div><b>${esc(p.name)}</b>
    <span class="tag off">${p.role==='admin'?'Admin':p.role==='member'?'Member':'Gym owner'}</span>
    <small>${esc(p.last_message||'Start a conversation')}</small></div>
    ${Number(p.unread)>0?`<span class="cnt">${Math.min(99,Number(p.unread))}${Number(p.unread)>99?'+':''}</span>`:''}</button>`).join('')||'<p class="ms-empty">No contacts found.</p>';
}
function renderThread(){
  const p=chatContacts.find(x=>String(x.id)===String(msKey)),box=$('msbody');
  $('msf').hidden=!p;
  if(!p){$('mshead').textContent='Select a conversation';box.innerHTML='';return}
  $('mshead').textContent=p.name+' · '+(p.role==='admin'?'Admin':p.role==='member'?'Member':'Gym owner');
  const nearBottom=box.scrollHeight-box.scrollTop-box.clientHeight<85;
  box.innerHTML=chatMessages.map(m=>`<div class="bub ${Number(m.sender_id)===Number(myChatId)?'mine':''}">${esc(m.body)}<small>${esc(msTime(m.created_at))}</small></div>`).join('')||'<p class="ms-empty">No messages yet. Say hello!</p>';
  if(nearBottom||box.dataset.firstLoad==='1')box.scrollTop=box.scrollHeight;
  box.dataset.firstLoad='0';
}
async function loadChatThread(){
  if(!msKey)return;
  const requestedKey=String(msKey),request=++chatThreadRequest;
  try{
    const j=await api('/api/chat/'+encodeURIComponent(requestedKey));
    if(request!==chatThreadRequest||requestedKey!==String(msKey))return;
    chatMessages=j.messages||[];myChatId=j.my_id;
    renderThread();
  }catch(e){if(request===chatThreadRequest){console.error('Cannot load chat thread:',e);flash(e.message)}}
}
async function openConv(key){
  msKey=String(key);chatMessages=[];chatThreadRequest++;
  $('msbody').dataset.firstLoad='1';
  renderMail();renderThread();
  await loadChatThread();await loadChatContacts();
  $('msb').focus();
}
document.addEventListener('click',e=>{
  const c=e.target.closest('[data-conv]');
  if(c){openConv(c.dataset.conv);return}
  const t=e.target.closest('[data-mstab]');
  if(t){msFilter=t.dataset.mstab;renderMail()}
});
$('msq').oninput=renderMail;
$('msf').addEventListener('submit',async e=>{
  e.preventDefault();const body=$('msb').value.trim(),key=msKey;
  if(!body||!key||chatSending)return;
  chatSending=true;
  const sendBtn=$('msf').querySelector('button[type="submit"]');sendBtn.disabled=true;
  try{
    await api('/api/chat/'+encodeURIComponent(key),'POST',{body});
    $('msb').value='';
    if(String(msKey)===String(key)){
      await loadChatThread();
      $('msbody').scrollTop=$('msbody').scrollHeight;
    }
    await loadChatContacts();
  }catch(err){flash(err.message)}
  finally{chatSending=false;sendBtn.disabled=false}
});
$('msb').addEventListener('keydown',e=>{
  if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();$('msf').requestSubmit()}
});
async function refreshChat(){
  if(document.hidden)return;
  await loadChatContacts();
  if(location.hash==='#messages'&&msKey){await loadChatThread();await loadChatContacts()}
}
setInterval(refreshChat,5000);
loadChatContacts();

/* ---- attendance (real API; members now come from the real members API) ---- */
let ATT=[],qat='',attDate=todayISO();
const clockTime=s=>s?new Date(String(s).replace(' ','T')).toLocaleTimeString([], {hour:'numeric',minute:'2-digit'}):'-';
function fillAttForm(){
  const am=$('am'),ac=$('ac');if(!am||!ac)return;
  const mv=am.value,cv=ac.value,wd=new Date().getDay()||7;
  const infos=MEMBERS.map(u=>({u,i:info(u)})).sort((a,b)=>a.u.name.localeCompare(b.u.name));
  am.innerHTML=infos.map(({u,i})=>`<option value="${u.id}">${esc(u.name)} · ${MS[i.state][0]}</option>`).join('');
  ac.innerHTML='<option value="">Gym floor (no class)</option>'+DB.classes.filter(c=>c.days.includes(wd)).map(c=>`<option value="${c.id}">${esc(c.name)} · ${esc(c.time_label)}</option>`).join('');
  if(mv)am.value=mv;if(cv)ac.value=cv;
}
async function loadAttendance(){
  try{ATT=(await api('/api/owner/attendance?date='+attDate)).attendance||[]}catch(e){flash(e.message)}
  renderAttendance();
}
function renderAttendance(){
  const q=qat.toLowerCase();
  $('a-n').textContent=ATT.length;
  $('a-u').textContent=new Set(ATT.map(a=>a.member_id)).size;
  $('a-o').textContent=ATT.filter(a=>a.override).length;
  $('a-d').textContent=attDate===todayISO()?'Today':fd(attDate);
  $('atbody').innerHTML=ATT.filter(a=>[a.member_name,a.class_name||'gym floor'].join(' ').toLowerCase().includes(q)).map(a=>{
    const s=MS[a.membership_state]||MS.none;
    return `<tr><td class="nowrap">${esc(clockTime(a.checked_in_at))}</td><td>${who(a.member_name,a.member_id,esc(a.member_name))}</td><td>${a.class_name?esc(a.class_name):'<span class="tag off">Gym floor</span>'}</td><td><span class="tag ${s[1]}">${s[0]}</span>${a.override?' <span class="tag pending">Override</span>':''}</td><td><button class="btn sm ghost" type="button" data-attdel="${a.id}">Undo</button></td></tr>`;
  }).join('')||'<tr><td colspan="5" class="empty">No check-ins for this day</td></tr>';
}
async function doCheckIn(u,reason){
  if(busy)return;busy=true;
  try{
    await api('/api/owner/attendance','POST',{member_id:u.id,class_id:$('ac').value||null,reason:reason||null});
    flash(u.name+' checked in');
    attDate=todayISO();$('ad').value=attDate;
    await loadAttendance();
  }catch(e){flash(e.message)}finally{busy=false}
}
$('af').addEventListener('submit',e=>{
  e.preventDefault();
  const u=MEMBERS.find(x=>x.id==$('am').value);
  if(!u){flash('Pick a member first');return}
  const i=info(u);
  if(i.state==='active'){doCheckIn(u);return}
  const why=i.state==='overdue'?u.name+'\u2019s membership expired '+Math.abs(daysTo(i.due))+' days ago.':i.state==='pending'?u.name+' has a payment waiting for approval.':u.name+' has no membership.';
  askConfirm('Membership is not active',why+' Give a reason to check them in anyway.','Check in anyway',r=>doCheckIn(u,r),true);
});
document.addEventListener('click',e=>{
  const x=e.target.closest('[data-attdel]');if(!x)return;
  const a=ATT.find(y=>y.id==x.dataset.attdel);if(!a)return;
  askConfirm('Undo check-in for '+a.member_name+'?','This removes the check-in record.','Undo check-in',async()=>{
    try{await api('/api/owner/attendance/'+a.id,'DELETE');flash('Check-in removed');await loadAttendance()}catch(err){flash(err.message)}
  });
});
$('ad').value=attDate;
$('ad').onchange=e=>{attDate=e.target.value||todayISO();loadAttendance()};
$('qat').oninput=e=>{qat=e.target.value;renderAttendance()};

/* ---- class codes (owner generates, coach announces) ---- */
let TODAY=[];
async function loadToday(){
  try{TODAY=(await api('/api/owner/classes/today')).classes||[]}catch(e){flash(e.message)}
  renderToday();
}
function renderToday(){
  $('runlist').innerHTML=TODAY.map(c=>{const k=c.code;
    const right=k
      ?`<div style="margin-left:auto;text-align:right"><div class="codebig">${esc(k.code)}</div><small data-exp="${esc(k.expires_at)}"></small></div><div class="acts"><button class="btn sm ghost" type="button" data-copy="${esc(k.code)}">Copy</button><button class="btn sm ghost" type="button" data-revcode="${k.id}">Revoke</button></div>`
      :`<button class="btn sm" type="button" data-gencode="${c.id}" ${c.status==='open'?'':'disabled'}>${c.status==='upcoming'?'Not open yet':c.status==='ended'?'Class ended':'Generate code'}</button>`;
    return `<div class="att"><div><b>${esc(c.name)}</b><br><small>Coach: ${esc(c.trainer_name||'none')} · ${esc(c.time_label)}</small></div>${right}</div>`}).join('')||'<p class="empty">No classes scheduled today.</p>';
}
document.addEventListener('click',async e=>{
  const g=e.target.closest('[data-gencode]');
  if(g){g.disabled=true;try{await api('/api/owner/classes/'+g.dataset.gencode+'/code','POST',{minutes:15});flash('Code generated');}catch(err){flash(err.message)}loadToday();return}
  const r=e.target.closest('[data-revcode]');
  if(r){try{await api('/api/owner/codes/'+r.dataset.revcode,'DELETE');flash('Code revoked')}catch(err){flash(err.message)}loadToday();return}
  const cp=e.target.closest('[data-copy]');
  if(cp){try{await navigator.clipboard.writeText(cp.dataset.copy);flash('Code copied')}catch(err){flash('Copy failed')}}
});
setInterval(()=>{
  if(location.hash!=='#attendance')return;
  let expired=false;
  document.querySelectorAll('[data-exp]').forEach(el=>{
    const s=Math.round((new Date(el.dataset.exp)-Date.now())/1000);
    if(s<=0)expired=true;else el.textContent='Expires in '+Math.floor(s/60)+':'+String(s%60).padStart(2,'0');
  });
  if(expired||new Date().getSeconds()===0)loadToday();
},1000);

/* ---- activity log (real API) ---- */
let AUDIT=[],qa='';
async function loadAudit(){
  try{AUDIT=(await api('/api/owner/audit')).logs||[]}catch(e){flash(e.message)}
  renderAudit();
}
function renderAudit(){
  const q=qa.toLowerCase();
  $('abody').innerHTML=AUDIT.filter(l=>[l.who,l.action,l.what,l.summary].join(' ').toLowerCase().includes(q)).map(l=>
    `<tr><td class="nowrap">${esc(new Date(String(l.created_at).replace(' ','T')).toLocaleString([], {month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}))}</td><td>${esc(l.who)}</td><td><span class="tag ${l.action==='deleted'?'rej':l.action==='created'?'':'pending'}">${esc(l.action)}</span></td><td>${esc(l.summary)}</td></tr>`
  ).join('')||'<tr><td colspan="4" class="empty">No activity yet</td></tr>';
}
$('qa').oninput=e=>{qa=e.target.value;renderAudit()};

/* ---- class booking management (owner) ---- */
let currentBookingClassId=null;
let currentBookingRows=[];
function nextClassDate(c){
  const d=new Date();
  for(let i=0;i<14;i++){
    const x=new Date(d.getFullYear(),d.getMonth(),d.getDate()+i);
    const weekday=x.getDay()||7;
    if((c.days||[]).map(Number).includes(weekday)){
      return `${x.getFullYear()}-${String(x.getMonth()+1).padStart(2,'0')}-${String(x.getDate()).padStart(2,'0')}`;
    }
  }
  return todayISO();
}
function openOwnerBookings(id){
  const cls=DB.classes.find(c=>Number(c.id)===Number(id));
  if(!cls){flash('Class not found. Refresh Classes.');return;}
  currentBookingClassId=Number(id);
  $('booking-date').value=nextClassDate(cls);
  location.hash='#classbookings';
  if(location.hash==='#classbookings')loadOwnerBookings();
}
async function loadOwnerBookings(){
  if(!currentBookingClassId){$('booking-rows').innerHTML='<tr><td colspan="5">Open a class from the Classes tab.</td></tr>';return;}
  const cls=DB.classes.find(c=>Number(c.id)===currentBookingClassId);
  $('booking-title').textContent=(cls?.name||'Class')+' — Bookings';
  const date=$('booking-date').value;
  $('booking-rows').innerHTML='<tr><td colspan="5">Loading bookings...</td></tr>';
  try{
    const j=await api('/api/owner/classes/'+currentBookingClassId+'/bookings?date='+encodeURIComponent(date));
    $('booking-capacity').textContent=j.capacity;
    $('booking-count').textContent=j.booked;
    $('booking-free').textContent=j.available;
    currentBookingRows=j.bookings||[];
    $('booking-rows').innerHTML=currentBookingRows.map(b=>`<tr>
      <td>${esc(b.name)}<small>${esc(b.email||'')}</small></td>
      <td>${esc(b.booked_at||'-')}</td>
      <td><span class="tag ${b.status==='cancelled'?'rej':b.status==='attended'?'':'pending'}">${esc(b.status)}</span></td>
      <td>${b.attended?'Checked in':'Not checked in'}</td>
      <td>${b.status==='booked'?`<button class="btn sm ghost" type="button" data-owner-cancel-booking="${Number(b.id)}">Cancel</button>`:'—'}</td>
    </tr>`).join('')||'<tr><td colspan="5" class="empty">No bookings for this session yet.</td></tr>';
  }catch(e){$('booking-rows').innerHTML=`<tr><td colspan="5">${esc(e.message)}</td></tr>`;}
}
$('booking-date').addEventListener('change',loadOwnerBookings);
$('booking-refresh').addEventListener('click',loadOwnerBookings);
document.addEventListener('click',e=>{
  const open=e.target.closest('[data-classbookings]');
  if(open){openOwnerBookings(open.dataset.classbookings);return;}
  const cancel=e.target.closest('[data-owner-cancel-booking]');
  if(cancel){
    const booking=currentBookingRows.find(b=>Number(b.id)===Number(cancel.dataset.ownerCancelBooking));
    if(!booking)return;
    askConfirm('Cancel '+booking.name+' booking?',
      'This releases the reserved slot for this class session.', 'Cancel booking', async()=>{
        try{
          await api('/api/owner/class-bookings/'+booking.id,'PATCH',{status:'cancelled'});
          flash('Booking cancelled');await loadOwnerBookings();
        }catch(err){flash(err.message)}
      });
  }
});

/* ---- navigation + clicks ---- */
const TITLES={classbookings:'Class Bookings',dashboard:'Dashboard',payments:'Payments',orders:'Orders',members:'Members',messages:'Messages',profile:'My profile',activity:'Activity',attendance:'Attendance'};
function clTabs(h){
  const on=h==='classes'||h==='attendance';
  document.querySelectorAll('.cltabs').forEach(el=>{
    el.hidden=!on;
    el.innerHTML=on?[['classes','Classes'],['attendance','Attendance']]
      .map(([k,l])=>`<a class="tab ${h===k?'on':''}" href="#${k}">${l}</a>`).join(''):'';
  });
}
function show(h){if(!TITLES[h]&&!CFG[h])h='dashboard';
  if(h==='orders'){pKind='orders';pTab='pending'}
  if(h==='payments'){pKind='members';pTab='pending'}
  const v=h==='orders'?'payments':(CFG[h]?'list':h);if(CFG[h]){cur=h;renderList()}
  if(h==='orders'||h==='payments')renderPayments();
  document.querySelectorAll('.view').forEach(x=>x.classList.toggle('on',x.id==='v-'+v));
  const navH=(h==='attendance'||h==='classbookings')?'classes':h;
  document.querySelectorAll('nav a').forEach(a=>a.classList.toggle('on',a.getAttribute('href')==='#'+navH));
  clTabs(h);
  $('t').textContent=h==='attendance'?'Classes':(CFG[h]?CFG[h].t:TITLES[h]);
  if(h==='profile')loadProfile();
  if(h==='products')loadProducts();
  if(h==='plans')loadPlans();
  if(h==='trainers')loadTrainers();
  if(h==='classes')loadClasses();
  if(h==='classbookings')loadOwnerBookings();
  if(h==='activity')loadAudit();
  if(h==='members')loadMembers();
  if(h==='attendance'){fillAttForm();loadClasses();loadAttendance();loadToday()}
  if(h==='payments'||h==='orders')loadOrders();
  if(h==='messages'){renderMail();renderThread();refreshChat()}
  window.scrollTo(0,0)}
window.addEventListener('hashchange',()=>show(location.hash.slice(1)));
let proofObjectUrl=null;
let proofAbort=null;
function closeProof(){
  if(proofAbort){proofAbort.abort();proofAbort=null;}
  if(proofObjectUrl){URL.revokeObjectURL(proofObjectUrl);proofObjectUrl=null;}
  $('proofImage').removeAttribute('src');
  $('proofImage').hidden=true;
  $('proofOverlay').hidden=true;
}
async function viewPaymentProof(type,id){
  if(!Number.isSafeInteger(id)||id<1)return;
  closeProof();
  $('proofTitle').textContent=(type==='membership'?'Membership payment #':'Product order #')+id;
  $('proofMessage').textContent='Loading screenshot...';
  $('proofError').hidden=true;
  $('proofOverlay').hidden=false;
  proofAbort=new AbortController();
  const controller=proofAbort;
  const url=type==='membership'
    ? `/api/owner/payments/${id}/proof`
    : `/api/owner/orders/${id}/proof`;
  try{
    const response=await fetch(url,{
      credentials:'same-origin',
      headers:{'Accept':'image/jpeg,image/png,image/webp'},
      cache:'no-store',
      signal:controller.signal
    });
    if(!response.ok){
      throw new Error(`Screenshot request returned HTTP ${response.status}. `+
        (response.status===403?'Your account is not authorized for this gym.':
         response.status===404?'The payment proof or route was not found.':
         response.status===401||response.status===302?'Please log in through the same Laravel address.':''));
    }
    const blob=await response.blob();
    if(!['image/jpeg','image/png','image/webp'].includes(blob.type)){
      throw new Error(`The server returned ${blob.type||'unknown content'} instead of a supported image. Check the Laravel login session and response headers.`);
    }
    if(controller.signal.aborted)return;
    proofObjectUrl=URL.createObjectURL(blob);
    const img=$('proofImage');
    img.onload=()=>{$('proofMessage').textContent='Uploaded payment screenshot';img.hidden=false;};
    img.onerror=()=>{$('proofMessage').textContent='Unable to display this image.';$('proofError').textContent='The returned image could not be decoded.';$('proofError').hidden=false;};
    img.src=proofObjectUrl;
  }catch(err){
    if(err.name==='AbortError')return;
    $('proofMessage').textContent='Screenshot unavailable';
    $('proofError').textContent=err.message;
    $('proofError').hidden=false;
  }finally{if(proofAbort===controller)proofAbort=null;}
}
$('proofClose').onclick=closeProof;
$('proofOverlay').addEventListener('click',e=>{if(e.target===$('proofOverlay'))closeProof();});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!$('proofOverlay').hidden)closeProof();});
document.addEventListener('click',e=>{
  const proofButton=e.target.closest('[data-proof-type]');
  if(proofButton){
    const type=proofButton.dataset.proofType;
    if(type==='membership'||type==='product')viewPaymentProof(type,Number(proofButton.dataset.proofId));
    return;
  }
  const kd=e.target.closest('[data-kind]');
  if(kd){pKind=kd.dataset.kind;pTab='pending';renderPayments();return}
  const go=e.target.closest('[data-goorders]');
  if(go){pKind='orders';pTab='pending'}
  const pk=e.target.closest('[data-pickup]');
  if(pk){
    const o=ORDERS.find(x=>x.id==pk.dataset.id);if(!o)return;
    const action=pk.dataset.pickup;
    const send=async()=>{if(busy)return;busy=true;try{
      await api('/api/owner/orders/'+o.id,'PATCH',{pickup_action:action});
      flash(action==='picked_up'?'Order picked up':action==='preparing'?'Order is preparing':'Order ready for pickup');
      await loadOrders();
    }catch(err){flash(err.message)}finally{busy=false}};
    if(action==='picked_up')askConfirm('Confirm pickup?', 'Only confirm after the customer actually receives the product.', 'Picked Up', send);
    else send();
    return;
  }
  const dv=e.target.closest('[data-delivery]');
  if(dv){
    const o=ORDERS.find(x=>x.id==dv.dataset.id);if(!o)return;
    const action=dv.dataset.delivery;
    const send=async()=>{if(busy)return;busy=true;try{
      await api('/api/owner/orders/'+o.id,'PATCH',{delivery_action:action});
      flash(action==='delivered'?'Order delivered':action==='out_for_delivery'?'Out for delivery':'Preparing delivery');
      await loadOrders();
    }catch(err){flash(err.message)}finally{busy=false}};
    if(action==='delivered')askConfirm('Confirm delivery?', 'Only mark Delivered after the customer receives the package.', 'Delivered', send);
    else if(action==='out_for_delivery')askConfirm('Send out for delivery?', 'Confirm the address and phone shown in this order first.', 'Out for Delivery', send);
    else send();
    return;
  }
  const od=e.target.closest('[data-ord]');
  if(od){
    const o=ORDERS.find(x=>x.id==od.dataset.id);if(!o)return;
    if(od.dataset.ord==='cancelled')askConfirm('Cancel order '+o.receipt+'?','Reserved stock will be restored. If payment was already received, handle the refund separately.','Cancel Order',()=>setOrder(o.id,'cancelled'));
    else if(od.dataset.ord==='completed')askConfirm('Complete order '+o.receipt+'?','Confirm the product has actually been collected by the customer. This action cannot be undone.','Complete Order',()=>setOrder(o.id,'completed'));
    else setOrder(o.id,od.dataset.ord);
    return;
  }
  const pa=e.target.closest('[data-pay]');
  if(pa){
    if(pa.dataset.pay==='accept'){
      const p=PAY.find(x=>x.id==pa.dataset.id);
      if(!p)return;
      if(!p.proof_path){flash('Payment has no uploaded screenshot. Cannot approve without proof.');return;}
      askConfirm('Accept membership payment?', 'Check the uploaded screenshot, transaction reference, and confirm the funds actually reached your account before accepting. Member: '+p.name+'; Reference: '+(p.reference||'-')+'; Amount: '+money(p.amount), 'Accept Payment', ()=>setPay(p.id,'accepted'));
    }else askRevoke(pa.dataset.id);
    return;
  }
  const pt=e.target.closest('[data-ptab]');if(pt){pTab=pt.dataset.ptab;renderPayments();return}
  const mt=e.target.closest('[data-mtab]');if(mt){mTab=mt.dataset.mtab;render()}
});
$('qp').oninput=e=>{qp=e.target.value;renderPayments()};
$('qm').oninput=e=>{qm=e.target.value;render()};

$('today').textContent=new Date().toLocaleDateString('en-US',{weekday:'long',month:'short',day:'numeric',year:'numeric'});
show(location.hash.slice(1));
load().then(loadProfile);loadMembers();loadOrders();loadProducts();loadTrainers().then(loadClasses);
renderOwner();loadAvatars();setInterval(()=>{if(!document.hidden)loadAvatars()},20000);
</script>
@endverbatim
</body>
</html>