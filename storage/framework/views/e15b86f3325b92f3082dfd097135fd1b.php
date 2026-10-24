<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title>FitConnect Dashboard</title>
<style>
:root{--bg:#0e0c0a;--panel:#18130f;--ink:#ffffff;--mute:#a8a29e;--line:rgba(255,255,255,.12);--accent:#ff9900;--blue:#ff9900;--orange:#ffffff;--green:#4ade80;--red:#ef4444;--nav:#ff9900;--navtxt:#1a1208;
box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
*{box-sizing:border-box}
html,body{margin:0}
[hidden]{display:none!important}
body{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:var(--ink);background:radial-gradient(60% 50% at 15% 10%,rgba(255,153,0,.22),transparent 70%),radial-gradient(50% 45% at 90% 85%,rgba(204,85,0,.22),transparent 70%),#0a0908;background-attachment:fixed;min-height:100vh;padding:24px}
:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
.app{display:flex;max-width:1180px;margin:0 auto;background:var(--panel);border-radius:14px;overflow:hidden;box-shadow:0 20px 60px rgba(20,20,60,.18);min-height:640px}
.side{width:180px;flex:none;border-right:1px solid var(--line);display:flex;flex-direction:column}
.logo{display:flex;align-items:center;gap:10px;padding:20px 16px 18px}
.logo-mark{width:36px;height:36px;border-radius:10px;background:var(--accent);color:var(--navtxt);display:grid;place-items:center;flex:none}
.logo-mark svg{width:22px;height:22px}
.logo-txt{display:flex;flex-direction:column;line-height:1.15;min-width:0}
.logo-txt b{font-size:17px;font-weight:700}
.logo-txt small{font-size:11px;color:var(--accent);font-weight:600;letter-spacing:.08em;text-transform:uppercase}
nav{padding:0 8px;display:flex;flex-direction:column;gap:6px}
nav a,.out{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;color:var(--ink);text-decoration:none;font-size:14px;font-weight:500}
nav a:hover,.out:hover{background:var(--line)}
nav a.on{background:var(--nav);color:var(--navtxt)}
.nav-count{margin-left:auto;background:var(--red);color:#fff;font-size:10px;border-radius:99px;padding:1px 6px}
.sp{flex:1}
.out{margin:0 8px 12px;width:calc(100% - 16px);background:none;border:0;font-family:inherit;cursor:pointer;text-align:left;color:#ff8a65}
.me{border-top:1px solid var(--line);padding:14px 16px;display:flex;align-items:center;gap:10px;font-size:13px;color:inherit;text-decoration:none}
.me:hover,.me.on{background:var(--line)}
.me small{display:block;color:var(--mute)}
.view{display:none}.view.on{display:block}
.pm{position:relative}
.pb{display:flex;align-items:center;gap:8px;background:none;border:0;color:inherit;font:inherit;cursor:pointer;text-align:left;padding:4px 6px;border-radius:8px}
.pb:hover{background:var(--line)}.pb small{display:block;color:var(--mute)}
.dd{position:absolute;right:0;top:calc(100% + 6px);background:var(--panel);border:1px solid var(--line);border-radius:10px;min-width:170px;padding:6px;box-shadow:0 10px 30px rgba(0,0,0,.5);z-index:5}
.dd a,.dd button{display:block;width:100%;text-align:left;padding:8px 10px;border-radius:6px;color:var(--ink);text-decoration:none;font:inherit;font-size:13px;background:none;border:0;cursor:pointer}
.dd a:hover,.dd button:hover{background:var(--line)}
.dd .lo{color:#ff8a65}
table{width:100%;border-collapse:collapse;font-size:14px}
th,td{text-align:left;padding:10px 12px;border-bottom:1px solid var(--line)}
th{color:var(--mute);font-weight:600;font-size:12px}
td small{display:block;color:var(--mute);font-size:12px}
.tool{margin:14px 0}
input,textarea{background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:9px 12px;font:inherit;font-size:14px;width:100%}
.tool input{max-width:320px}
input:disabled{opacity:.6;cursor:not-allowed}
textarea{resize:vertical;min-height:84px}
.tag{padding:2px 8px;border-radius:99px;font-size:12px;background:rgba(74,222,128,.15);color:var(--green)}
.tag.off{background:var(--line);color:var(--mute)}
.tag.pending{background:rgba(255,153,0,.15);color:var(--accent)}
.tag.rej{background:rgba(239,68,68,.15);color:#ff8a65}
.btn{background:var(--accent);color:#1a1208;border:0;border-radius:8px;padding:9px 16px;font-weight:600;font-family:inherit;font-size:14px;cursor:pointer}
.btn:hover{filter:brightness(1.08)}
.btn:disabled{opacity:.6;cursor:wait}
.btn.ghost{background:none;color:var(--ink);border:1px solid var(--line)}
.btn.ghost:hover{background:var(--line)}
.btn.danger{background:var(--red);color:#fff}
.btn.sm{padding:5px 10px;font-size:12px}
.cell-acts{display:flex;gap:6px;flex-wrap:wrap}
.fld{display:block;margin:12px 0;font-size:13px;color:var(--mute)}
.fld input,.fld textarea{margin-top:4px}
.av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#ffb84d,#cc5500);flex:none;position:relative;display:grid;place-items:center;font-size:11px;font-weight:700;color:#1a1208}
.av::after{content:"";position:absolute;right:-1px;bottom:-1px;width:9px;height:9px;border-radius:50%;background:var(--green);border:2px solid var(--panel)}
.main{flex:1;min-width:0;display:flex;flex-direction:column}
.top{display:flex;justify-content:space-between;align-items:center;padding:14px 22px;border-bottom:1px solid var(--line);font-weight:600}
.top .r{display:flex;align-items:center;gap:16px;font-size:13px;font-weight:500}
.badge{position:relative;display:inline-block;color:inherit;text-decoration:none}
.badge b{position:absolute;top:-6px;right:-6px;background:var(--red);color:#fff;font-size:9px;border-radius:50%;width:15px;height:15px;display:grid;place-items:center}
.body{padding:20px 22px;background:color-mix(in srgb,var(--bg) 40%,var(--panel));flex:1}
.head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap}
h1{font-size:22px;margin:0}
.head p{margin:4px 0 0;color:var(--mute);font-size:14px}
.date{border:1px solid var(--line);background:var(--panel);border-radius:8px;padding:8px 12px;font-size:13px;font-weight:500}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin:18px 0}
.card{background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:14px 16px}
.card span{font-size:12px;color:var(--mute);font-weight:600}
.card strong{display:block;font-size:28px;margin:2px 0}
.card em{font-style:normal;font-size:12px;color:var(--green)}
.card a.pend{display:block;margin-top:2px;font-size:12px;color:var(--accent);text-decoration:none}
.card a.pend:hover{text-decoration:underline}
.chart{background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:16px}
.ch{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
.ch h2{font-size:17px;margin:0}
select{background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:6px;padding:6px 10px;font-size:13px}
.leg{display:flex;justify-content:center;gap:16px;font-size:12px;margin:8px 0 0}
.leg i{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:5px}
svg{width:100%;height:auto;display:block}
svg text{fill:var(--ink);font-size:12px;font-family:inherit}
svg .m{fill:var(--mute)}

/* Tabs */
.tabs{display:flex;gap:6px;flex-wrap:wrap;margin:14px 0 0}
.tab{display:inline-flex;gap:6px;align-items:center;padding:7px 12px;border-radius:8px;border:1px solid var(--line);background:none;color:var(--ink);font:inherit;font-size:13px;font-weight:500;cursor:pointer}
.tab:hover{background:var(--line)}
.tab.on{background:var(--nav);color:var(--navtxt);border-color:transparent}
.tab b{font-size:11px;opacity:.75}

/* Gym picker */
.gym-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;margin-top:14px}
.gym-card{display:flex;flex-direction:column;gap:6px;text-align:left;background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:10px;padding:16px;font:inherit;cursor:pointer}
.gym-card:hover{border-color:var(--accent)}
.gym-card small{color:var(--mute);font-size:12px}

/* Combined gym profile */
.gym-hero{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;padding:18px;border:1px solid var(--line);border-radius:12px;background:var(--panel)}
.gym-panel{margin-top:16px}.gym-panel h3{margin:4px 0 12px}.gym-panel .gym-grid{margin-top:12px}
.gym-tile{border:1px solid var(--line);background:var(--panel);border-radius:10px;padding:14px;min-width:0}
.gym-tile img{width:100%;height:150px;object-fit:cover;border-radius:8px;margin-bottom:10px}
/* Profile page */
.pf-hero{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
.pf-av{width:84px;height:84px;border-radius:50%;background:linear-gradient(135deg,#ffb84d,#cc5500);display:grid;place-items:center;font-size:30px;font-weight:700;color:#1a1208;flex:none;position:relative}
.pf-av::after{content:"";position:absolute;right:3px;bottom:3px;width:16px;height:16px;border-radius:50%;background:var(--green);border:3px solid var(--panel)}
.pf-hero h2{margin:0;font-size:22px}
.pf-hero p{margin:4px 0 8px;color:var(--mute);font-size:14px}
.pf-meta{display:flex;gap:8px;flex-wrap:wrap}
.pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px}
.pf-grid h3{margin:0 0 4px;font-size:16px}
.pf-grid .sub{margin:0 0 6px;color:var(--mute);font-size:13px}
.row{display:flex;gap:12px}.row>*{flex:1}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:14px}
.stats .card strong{font-size:20px}
.msg{font-size:13px;margin-left:10px}
.msg.ok{color:var(--green)}.msg.err{color:#ff8a65}
.field-err{color:#ff8a65;font-size:12px;margin-top:4px;display:block}

/* Modal */
.ov{position:fixed;inset:0;background:rgba(0,0,0,.65);display:grid;place-items:center;z-index:20;padding:20px}
.modal{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:22px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.6)}
.modal h2{margin:0 0 6px;font-size:18px}
.modal p{margin:0 0 18px;color:var(--mute);font-size:14px}
.modal .acts{display:flex;gap:10px;justify-content:flex-end}

/* Toast */
.toast{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);background:var(--panel);border:1px solid var(--line);color:var(--ink);padding:10px 16px;border-radius:10px;font-size:13px;box-shadow:0 10px 30px rgba(0,0,0,.5);z-index:30}

/* Messages */
.mail{display:grid;grid-template-columns:300px 1fr;gap:14px;margin-top:16px}
.mail .chart{padding:12px}
.cv{display:flex;gap:10px;align-items:center;width:100%;padding:10px;border-radius:8px;background:none;border:0;color:inherit;font:inherit;text-align:left;cursor:pointer}
.cv:hover,.cv.on{background:var(--line)}
.cv>div{min-width:0;flex:1}
.cv small{display:block;color:var(--mute);font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.mail .tabs{margin:10px 0}
.mail .tab{padding:5px 10px;font-size:12px}
.ms-head{font-weight:600;padding:4px 4px 10px;border-bottom:1px solid var(--line)}
.ms-body{height:400px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;padding:10px 4px}
.bub{max-width:75%;padding:8px 12px;border-radius:12px;font-size:14px;background:var(--line);white-space:pre-wrap;word-break:break-word}
.bub.mine{align-self:flex-end;background:var(--accent);color:#1a1208}
.bub small{display:block;font-size:10px;opacity:.7;margin-top:2px}
.ms-form{display:flex;gap:8px;margin-top:10px}
.ms-form textarea{min-height:44px;height:44px}
.ms-empty{color:var(--mute);font-size:13px;padding:8px}

/* Clickable names + details popup */
.lnk{background:none;border:0;padding:0;color:var(--accent);font:inherit;font-weight:600;cursor:pointer;text-align:left}
.lnk:hover{text-decoration:underline}
.dl{display:grid;grid-template-columns:150px 1fr;gap:8px 12px;font-size:14px;margin:12px 0 18px}
.dl dt{color:var(--mute)}
.dl dd{margin:0;word-break:break-word}
.dl h3{grid-column:1/-1;margin:10px 0 0;font-size:14px;border-top:1px solid var(--line);padding-top:12px}
.modal.wide{max-width:520px;max-height:85vh;overflow-y:auto}
.nowrap{white-space:nowrap}

/* Avatars (profile photos) */
.av{overflow:hidden}
.av img,.pf-av img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%}
.pf-av{overflow:hidden}
.who{display:inline-flex;align-items:center;gap:8px;min-width:0}
.who .av{width:28px;height:28px;font-size:10px}
.who .av::after{display:none}
.av.big{width:84px;height:84px;font-size:30px}
.av.big::after{display:none}
.pf-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:6px}

@media (max-width:760px){
  body{padding:0}
  .app{flex-direction:column;border-radius:0}
  .side{width:100%;border-right:0;border-bottom:1px solid var(--line)}
  nav{flex-direction:row;flex-wrap:wrap}
  .sp,.me{display:none}
  .out{margin:6px 8px;width:auto}
  .cards,.stats,.pf-grid{grid-template-columns:1fr}
  .row{flex-direction:column;gap:0}
  .mail{grid-template-columns:1fr}
}
@media (prefers-reduced-motion:reduce){*{transition:none!important}}
</style>
</head>
<body>

<div class="app" id="app">
  <aside class="side">
    <div class="logo">
      <span class="logo-mark">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 6.5v11M17.5 6.5v11M3.5 9v6M20.5 9v6M6.5 12h11"/></svg>
      </span>
      <span class="logo-txt"><b>FitConnect</b><small>Admin</small></span>
    </div>
    <nav>
      <a class="on" href="#dashboard">&#9638; Dashboard</a>
      <a href="#users">&#9786; Users</a>
      <a href="#coaches">&#9733; Gym Owners <span class="nav-count" id="nav-pend" hidden></span></a>
      <a href="#gyms">&#9962; Gyms</a>
      <a href="#analytics">&#9636; Analytics</a>
      <a href="#audit">&#9776; Activity log</a>
      <a href="#messages">&#9993; Messages <span class="nav-count" id="nav-chat" hidden></span></a>
    </nav>
    <div class="sp"></div>
    <button class="out" type="button" data-logout>&#10162; Log out</button>
    <a class="me" href="#profile" id="me"><span class="av" data-ini></span><div><b id="mn">Admin</b><small>Active</small></div></a>
  </aside>
  <section class="main">
    <header class="top">
      <span id="t">Dashboard</span>
      <div class="r">
        <a class="badge" id="mail" href="#messages" title="Messages">&#9993;<b id="mail-n" hidden></b></a>
        <a class="badge" id="bell" href="#coaches" title="Gym Owners awaiting approval">&#9830;<b id="bell-n" hidden></b></a>
        <div class="pm">
          <button class="pb" id="pb" type="button" aria-haspopup="true" aria-expanded="false"><span class="av" data-ini></span><span>Admin<small id="pn"></small></span><span>&#9662;</span></button>
          <div class="dd" id="dd" hidden><a href="#profile">My profile</a><button class="lo" type="button" data-logout>Log out</button></div>
        </div>
      </div>
    </header>
    <div class="body">

<!-- DASHBOARD -->
<section class="view on" id="v-dashboard">
  <div class="head">
    <div><h1>Dashboard Overview</h1><p>Welcome back, Admin <span data-first></span>!</p></div>
    <div class="date" id="today"></div>
  </div>
  <div class="cards">
    <div class="card"><span>TOTAL USERS</span><strong id="s-users">0</strong><em id="s-users-sub"></em></div>
    <div class="card"><span>APPROVED GYM OWNERS</span><strong id="s-coaches">0</strong><a class="pend" id="s-pend" href="#coaches" hidden></a></div>
  </div>
  <div class="chart">
    <div class="ch"><h2 id="chart-title">User &amp; Gym Owner Analytics</h2><select aria-label="Chart range"><option id="chart-opt">Number of People</option></select></div>
    <div class="leg"><span><i style="background:var(--blue)"></i>Users</span><span><i style="background:var(--orange)"></i>Approved gym owners</span></div>
    <svg id="c" viewBox="0 0 800 360" role="img" aria-label="Users and approved gym owners per month"></svg>
  </div>
</section>

<!-- USERS -->
<section class="view" id="v-users">
  <h1>Users</h1>
  <div class="tool"><input id="qu" placeholder="Search users" aria-label="Search users"></div>
  <div class="chart" style="overflow-x:auto"><table><thead><tr><th>Name</th><th>Email</th><th>Joined</th><th>Last login</th><th>Status</th><th>Action</th></tr></thead><tbody id="tu"></tbody></table></div>
</section>

<!-- COACHES -->
<section class="view" id="v-coaches">
  <h1>Gym Owners</h1>
  <p style="margin:4px 0 0;color:var(--mute);font-size:14px">Review and approve gym owner applications.</p>
  <div class="tabs" id="tabs"></div>
  <div class="tool"><input id="qc" placeholder="Search gym owners" aria-label="Search gym owners"></div>
  <div class="chart" style="overflow-x:auto"><table><thead><tr><th>Name</th><th>Gym</th><th>Clients</th><th>Approval</th><th>Last login</th><th>Status</th><th>Action</th></tr></thead><tbody id="tc"></tbody></table></div>
</section>

<!-- GYMS: consolidated gym profile, members, products, and coaches -->
<section class="view" id="v-gyms">
  <div class="head"><div><h1>Gyms</h1><p>Select a gym to see its profile, members, products, and coaches.</p></div></div>
  <div id="m-gyms">
    <div class="cards">
      <div class="card"><span>TOTAL GYMS</span><strong id="g-count">0</strong></div>
      <div class="card"><span>APPROVED GYMS</span><strong id="g-appr">0</strong></div>
      <div class="card"><span>TOTAL CLIENTS</span><strong id="g-clients">0</strong></div>
    </div>
    <div class="tool"><input id="qmg" placeholder="Search gyms or owners" aria-label="Search gyms"></div>
    <div class="gym-grid" id="mg-list"></div>
    <!-- Retained for compatibility with existing view logic -->
    <div hidden><input id="qg"><table><tbody id="tg"></tbody></table></div>
  </div>
  <div id="m-detail" hidden>
    <button class="btn ghost sm" type="button" id="m-back" style="margin:6px 0 14px">&larr; All gyms</button>
    <div class="gym-hero">
      <div><h2 id="m-gname" style="margin:0;font-size:24px"></h2><p id="m-gsub" style="margin:5px 0;color:var(--mute)"></p></div>
      <span id="gym-status" class="tag"></span>
    </div>
    <div class="cards">
      <div class="card"><span>REGISTERED MEMBERS</span><strong id="gym-member-count">0</strong></div>
    </div>
    <div class="tabs" id="gym-tabs" aria-label="Gym details"></div>
    <div id="gym-overview" class="chart gym-panel">
      <h3>Gym information</h3><dl id="gym-info" class="dl"></dl>
    </div>
    <div id="gym-members" class="gym-panel" hidden>
      <h3>Members</h3>
      <div class="tool"><input id="gym-member-search" placeholder="Search members"></div>
      <div class="chart" style="overflow-x:auto"><table><thead><tr><th>Member</th><th>Email</th><th>Registered</th></tr></thead><tbody id="gym-members-body"></tbody></table></div>
    </div>
    <div id="gym-products" class="gym-panel" hidden><h3>Gym products</h3><div id="gym-products-list" class="gym-grid"></div></div>
    <div id="gym-coaches" class="gym-panel" hidden><h3>Gym coaches / trainers</h3><div id="gym-coaches-list" class="gym-grid"></div></div>
  </div>
</section>

<!-- ANALYTICS -->
<section class="view" id="v-analytics"><h1>Analytics</h1><div class="chart" style="margin-top:16px"><div class="leg"><span><i style="background:var(--blue)"></i>Users</span><span><i style="background:var(--orange)"></i>Approved gym owners</span></div><svg id="c2" viewBox="0 0 800 360" role="img" aria-label="Users and approved gym owners per month"></svg></div></section>

<!-- ACTIVITY LOG -->
<section class="view" id="v-audit">
  <h1>Activity log</h1>
  <p style="margin:4px 0 0;color:var(--mute);font-size:14px">Every change to user accounts, recorded automatically by the database.</p>
  <div class="tool" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    <input id="qa" placeholder="Search by account id, name or email" aria-label="Search the log">
    <select id="fa" aria-label="Filter by action"><option value="">All actions</option><option>INSERT</option><option>UPDATE</option><option>DELETE</option></select>
  </div>
  <div class="chart" style="overflow-x:auto"><table><thead><tr><th>When</th><th>Action</th><th>Account</th><th>Changed by</th><th>Details</th></tr></thead><tbody id="ta"></tbody></table></div>
  <div style="display:flex;gap:10px;align-items:center;margin-top:12px">
    <button class="btn ghost sm" type="button" id="ap">&larr; Newer</button>
    <button class="btn ghost sm" type="button" id="an">Older &rarr;</button>
    <span style="color:var(--mute);font-size:13px" id="apg"></span>
  </div>
</section>

<!-- MESSAGES -->
<section class="view" id="v-messages">
  <h1>Messages</h1>
  <p style="margin:4px 0 0;color:var(--mute);font-size:14px">Send and receive messages with gym owners and members.</p>
  <div class="mail">
    <div class="chart">
      <input id="msq" placeholder="Search gym owners or members" aria-label="Search people">
      <div class="tabs" id="mstabs"></div>
      <div id="mslist"></div>
    </div>
    <div class="chart">
      <div class="ms-head" id="mshead">Select a conversation</div>
      <div class="ms-body" id="msbody"></div>
      <form class="ms-form" id="msf" hidden>
        <textarea id="msb" placeholder="Write a message (Enter to send)" maxlength="2000" aria-label="Message"></textarea>
        <button class="btn" type="submit">Send</button>
      </form>
    </div>
  </div>
</section>

<!-- ADMIN PROFILE -->
<section class="view" id="v-profile">
  <h1>Admin profile</h1>
  <div class="chart" style="margin-top:16px">
    <div class="pf-hero">
      <div class="pf-av" data-ini></div>
      <div>
        <h2 id="ph-name"></h2>
        <p id="ph-email"></p>
        <div class="pf-meta"><span class="tag">Active</span><span class="tag off">Administrator</span></div>
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
    <div class="card"><span>Last login</span><strong id="st-login">Just now</strong></div>
    <div class="card"><span>Member since</span><strong id="st-since">-</strong></div>
    <div class="card"><span>Permissions</span><strong>Full access</strong></div>
  </div>

  <div class="pf-grid">
    <form class="chart" id="pf" method="POST" action="<?php echo e(route('profile.update')); ?>" novalidate>
      <?php echo csrf_field(); ?>
      <?php echo method_field('PATCH'); ?>
      <h3>Personal information</h3>
      <p class="sub">Update how your name and contact details appear.</p>
      <label class="fld">Full name<input id="fn" autocomplete="name" name="name"></label>
      <label class="fld">Email<input id="fe" type="email" autocomplete="email" name="email"></label>
      <div class="row">
        <label class="fld">Phone<input id="fph" type="tel" autocomplete="tel" name="phone"></label>
        <label class="fld">Location<input id="fl" name="location"></label>
      </div>
      <label class="fld">Role<input value="Admin" disabled></label>
      <label class="fld">About<textarea id="fb" name="bio"></textarea></label>
      <span class="field-err" id="pf-err" hidden></span>
      <button class="btn" type="submit" id="sv">Save changes</button><span class="msg ok" id="ok" hidden>Profile saved</span>
    </form>

    <form class="chart" id="pw" method="POST" action="<?php echo e(route('profile.password')); ?>" novalidate>
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <h3>Change password</h3>
      <p class="sub">Use at least 8 characters.</p>
      <label class="fld">Current password<input id="pw0" type="password" autocomplete="current-password" name="current_password"></label>
      <label class="fld">New password<input id="pw1" type="password" autocomplete="new-password" name="password"></label>
      <label class="fld">Confirm new password<input id="pw2" type="password" autocomplete="new-password" name="password_confirmation"></label>
      <span class="field-err" id="pw-err" hidden></span>
      <button class="btn" type="submit">Update password</button><span class="msg ok" id="pw-ok" hidden>Password updated</span>
      <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
      <h3>Session</h3>
      <p class="sub">Sign out of the admin panel on this device.</p>
      <button class="btn danger" type="button" data-logout>Log out</button>
    </form>
  </div>
</section>

    </div>
  </section>
</div>

<!-- LOG OUT CONFIRMATION -->
<div class="ov" id="ov" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="mt">
    <h2 id="mt">Log out of FitConnect?</h2>
    <p>You will need to sign in again to reach the dashboard.</p>
    <div class="acts"><button class="btn ghost" type="button" id="mc">Stay signed in</button><button class="btn danger" type="button" id="mo">Log out</button></div>
  </div>
</div>

<!-- DELETE CONFIRMATION -->
<div class="ov" id="dov" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="dt">
    <h2 id="dt">Delete?</h2>
    <p id="dp"></p>
    <div class="acts"><button class="btn ghost" type="button" id="dc">Cancel</button><button class="btn danger" type="button" id="dx">Delete</button></div>
  </div>
</div>

<!-- DEACTIVATE CONFIRMATION -->
<div class="ov" id="aov" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="at">
    <h2 id="at">Deactivate?</h2>
    <p id="ap-text"></p>
    <div class="acts"><button class="btn ghost" type="button" id="ac">Cancel</button><button class="btn danger" type="button" id="ax">Deactivate</button></div>
  </div>
</div>

<!-- PERSON DETAILS -->
<div class="ov" id="vov" hidden>
  <div class="modal wide" role="dialog" aria-modal="true" aria-labelledby="vt">
    <h2 id="vt">Details</h2>
    <div id="vbody">Loading...</div>
    <div class="acts"><button class="btn ghost" type="button" id="vc">Close</button></div>
  </div>
</div>

<!-- GYM DETAILS -->
<div class="ov" id="gov" hidden>
  <div class="modal wide" role="dialog" aria-modal="true" aria-labelledby="gt">
    <h2 id="gt">Gym</h2>
    <div id="gbody"></div>
    <div class="acts"><button class="btn ghost" type="button" id="gc">Close</button></div>
  </div>
</div>

<div class="toast" id="toast" role="status" hidden></div>

<form id="lf" method="POST" action="<?php echo e(route('logout')); ?>" hidden><?php echo csrf_field(); ?></form>

<script>
/* ---------- helpers ---------- */
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const $=id=>document.getElementById(id);
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fmtDate=iso=>new Date(iso+'T00:00:00').toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
const fmtLogin=iso=>iso?new Date(iso).toLocaleString([], {month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'}):'Never';
let toastT;
function flash(msg){const t=$('toast');t.textContent=msg;t.hidden=false;clearTimeout(toastT);toastT=setTimeout(()=>t.hidden=true,3500)}

/* ---------- data (loaded from the database) ---------- */
let USERS=[],COACHES=[];

/* ---------- avatars (profile photos) ---------- */
let AVMAP={};
/* Accepts any /storage/ URL and turns it into a relative path, so localhost vs 127.0.0.1:8000 never blocks it */
const safeImg=u=>{try{if(!u)return '';const x=new URL(u,location.origin);return x.pathname.includes('/storage/')?x.pathname+x.search:''}catch(e){return ''}};
const avOf=id=>AVMAP[id]||null;
function avatar(name,url,extra=''){
  const s=safeImg(url);
  return `<span class="av ${extra}">${esc(initials(name||''))}${s?`<img src="${esc(s)}" alt="" loading="lazy" onerror="this.remove()">`:''}</span>`;
}
const who=(name,id,inner)=>`<span class="who">${avatar(name,avOf(id))}${inner}</span>`;
function setAv(el,name,url){
  el.textContent=initials(name||'');
  const s=safeImg(url);
  if(s){const i=new Image();i.alt='';i.onerror=()=>i.remove();i.src=s;el.appendChild(i)}
}
async function loadAvatars(){
  try{
    const j=await api('/api/admin/avatars');
    const m={};(j.avatars||[]).forEach(a=>m[a.id]=a.url);
    if(JSON.stringify(m)!==JSON.stringify(AVMAP)){AVMAP=m;renderAll();if(location.hash==='#messages')renderMail()}
  }catch(e){}
}

async function api(url,method='GET',body){
  const r=await fetch(url,{method,headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},body:body?JSON.stringify(body):undefined});
  const j=await r.json().catch(()=>({}));
  if(!r.ok)throw new Error((j.errors&&Object.values(j.errors)[0]?.[0])||j.message||'Request failed (HTTP '+r.status+').');
  return j;
}
let peopleErr=false;
async function loadPeople(){
  try{
    const j=await api('/api/admin/people');
    USERS=j.users;COACHES=j.coaches;peopleErr=false;
  }catch(e){if(!peopleErr)flash('Could not load users and gym owners.');peopleErr=true}
  renderAll();renderMailBadge();
  loadGyms();
  loadAvatars();
  if(location.hash==='#messages'){renderMail();renderThread()}
}

/* ---------- profile state (from the database) ---------- */
<?php
    $u = auth()->user();
    $profileData = [
        'name'       => $u->name,
        'email'      => $u->email,
        'phone'      => $u->phone ?? '',
        'location'   => $u->location ?? '',
        'bio'        => $u->bio ?? '',
        'avatar_url' => $u->avatar_url,
    ];
    $lastLoginIso   = $u->last_login_at ? $u->last_login_at->toIso8601String() : null;
    $memberSinceIso = $u->created_at ? $u->created_at->toIso8601String() : null;
?>
let P=<?php echo json_encode($profileData, 15, 512) ?>;
let lastLogin=<?php echo json_encode($lastLoginIso, 15, 512) ?>;
const memberSince=<?php echo json_encode($memberSinceIso, 15, 512) ?>;
async function send(url,method,body){
  const r=await fetch(url,{method,headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(body)});
  const j=await r.json().catch(()=>({}));
  if(!r.ok)throw new Error(j.errors?Object.values(j.errors)[0][0]:(j.message||('Request failed (HTTP '+r.status+').')));
  return j;
}

/* ---------- chart (cumulative people per month) ---------- */
const YEAR=new Date().getFullYear(),NOW=new Date().getMonth();
const M=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"].slice(0,Math.max(NOW+1,2));
const monthIdx=d=>{const y=+d.slice(0,4);return y>YEAR?M.length-1:y<YEAR?0:Math.min(+d.slice(5,7)-1,M.length-1)};
function series(dates){const a=M.map(()=>0);dates.forEach(d=>{for(let i=monthIdx(d);i<M.length;i++)a[i]++});return a}
function drawChart(el){
  const U=series(USERS.map(u=>u.joined));
  const C=series(COACHES.filter(c=>c.approval==='approved').map(c=>c.date));
  const L=56,R=20,TOP=20,W=800,H=360,pw=W-L-R,ph=H-TOP-40;
  const max=Math.max(5,...U,...C),raw=max/7,pow=Math.pow(10,Math.floor(Math.log10(raw))),f=raw/pow;
  const step=(f<=1?1:f<=2?2:f<=5?5:10)*pow,top=Math.ceil(max/step)*step;
  const x=i=>L+40+i*(pw-80)/(M.length-1),y=v=>TOP+ph-v/top*ph;
  let s='';
  for(let v=0;v<=top;v+=step){s+=`<line x1="${L}" x2="${W-R}" y1="${y(v)}" y2="${y(v)}" stroke="var(--line)"/><text class="m" x="${L-8}" y="${y(v)+4}" text-anchor="end">${v}</text>`}
  s+=`<text class="m" transform="translate(14 ${TOP+ph/2}) rotate(-90)" text-anchor="middle">Number of People</text>`;
  const line=d=>d.map((v,i)=>`${i?'L':'M'}${x(i)},${y(v)}`).join('');
  const area=(d,c)=>`<path d="${line(d)}L${x(d.length-1)},${y(0)}L${x(0)},${y(0)}Z" fill="${c}" opacity=".12"/>`;
  s+=area(U,'var(--blue)')+area(C,'var(--orange)');
  s+=`<path d="${line(U)}" fill="none" stroke="var(--blue)" stroke-width="2"/><path d="${line(C)}" fill="none" stroke="var(--orange)" stroke-width="2"/>`;
  [[U,'var(--blue)'],[C,'var(--orange)']].forEach(([d,c])=>d.forEach((v,i)=>s+=`<circle cx="${x(i)}" cy="${y(v)}" r="3.5" fill="var(--panel)" stroke="${c}" stroke-width="2"/><text x="${x(i)}" y="${y(v)-12}" text-anchor="middle">${v}</text>`));
  M.forEach((m,i)=>s+=`<text class="m" x="${x(i)}" y="${H-14}" text-anchor="middle">${m}</text>`);
  el.innerHTML=s;
}

/* ---------- gyms (name of each owner's gym + total clients) ---------- */
let GYMS=[],qGyms='';

/* Owner id of a gym, whichever key the API uses */
const gymOwnerId=g=>g.user_id??g.owner_id??g.owner?.id;

/* Gym that belongs to a given owner (user id) */
const gymOf=userId=>{const g=GYMS.find(x=>gymOwnerId(x)==userId);return g?g.gym_name:'-'};

/* Owner (from COACHES) of a gym: match by id, then email, then name */
const coachOfGym=g=>
  COACHES.find(c=>c.id==gymOwnerId(g))
  || COACHES.find(c=>g.owner_email&&c.email&&c.email===g.owner_email)
  || COACHES.find(c=>g.owner_name&&c.name===g.owner_name);

/* A gym is approved when its owner is approved (same source as the Gym Owners tab).
   Falls back to the gym's own status only if no owner can be matched. */
const gymApproved=g=>{
  const c=coachOfGym(g);
  return c ? c.approval==='approved' : g.status==='active';
};

async function loadGyms(){
  try{
    const j=await api('/api/admin/gyms');
    GYMS=(j.gyms||[]).map(g=>({
      ...g,
      gym_name:g.gym_name||g.name||g.business_name||g.gym?.gym_name||'Gym #'+g.id,
      gym_address:g.gym_address||g.address||'',
      owner_name:g.owner_name||g.owner?.name||'',
      total_clients:Number(g.total_clients??g.members_count??0),
      member_ids:Array.isArray(g.member_ids)?g.member_ids:[]
    }));
  }catch(e){console.error('Failed to load gyms:',e);flash('Gym list: '+e.message)}
  renderGyms();renderCoaches();renderMemberships();
}
function renderGyms(){
  $('g-count').textContent=GYMS.length;
  $('g-appr').textContent=GYMS.filter(g=>gymApproved(g)).length;
  $('g-clients').textContent=GYMS.reduce((s,g)=>s+Number(g.total_clients),0);
  const q=qGyms.toLowerCase();
  $('tg').innerHTML=GYMS
    .filter(g=>(g.gym_name+' '+g.owner_name+' '+g.gym_address).toLowerCase().includes(q))
    .map(g=>{
      const on=gymApproved(g);
      return `<tr><td><button type="button" class="lnk" data-gym="${g.id}">${esc(g.gym_name)}</button></td><td>${esc(g.owner_name)}</td><td>${esc(g.gym_address)}</td><td>${g.total_clients}</td><td><span class="tag ${on?'':'pending'}">${on?'Approved':'Pending'}</span></td></tr>`;
    }).join('')||'<tr><td colspan="5">No gyms found</td></tr>';
}
function gymDetailsHTML(g){
  return `<dl class="dl"><dt>Gym name</dt><dd><b>${esc(g.gym_name)}</b></dd><dt>Owner</dt><dd>${esc(g.owner_name)}</dd><dt>Owner email</dt><dd>${esc(g.owner_email)}</dd><dt>Phone</dt><dd>${esc(g.phone_number)}</dd><dt>Address</dt><dd>${esc(g.gym_address)}</dd><dt>Total clients</dt><dd>${g.total_clients}</dd><dt>Status</dt><dd>${gymApproved(g)?'Approved':'Pending approval'}</dd></dl>`;
}
function openGym(id){
  const g=GYMS.find(x=>x.id==id);if(!g)return;
  $('gt').textContent=g.gym_name;
  $('gbody').innerHTML=gymDetailsHTML(g);
  $('gov').hidden=false;$('gc').focus();
}
$('gc').onclick=()=>$('gov').hidden=true;
$('gov').addEventListener('click',e=>{if(e.target===$('gov'))$('gov').hidden=true});
$('qg').oninput=e=>{qGyms=e.target.value;renderGyms()};
document.addEventListener('click',e=>{
  const g=e.target.closest('[data-gym]');
  if(g)openGym(g.dataset.gym);
});
document.addEventListener('keydown',e=>{if(e.key==='Escape')$('gov').hidden=true});

/* ---------- users ---------- */
let qUsers='',qCoaches='',coachTab='all';
const toggleBtn=p=>`<button class="btn sm ghost" type="button" data-toggle="${p.id}" data-to="${p.active?0:1}">${p.active?'Deactivate':'Activate'}</button>`;
function renderUsers(){
  const q=qUsers.toLowerCase();
  const rows=USERS.filter(u=>[u.name,u.email,fmtDate(u.joined),fmtLogin(u.last_login),u.active?'active':'inactive'].join(' ').toLowerCase().includes(q));
  $('tu').innerHTML=rows.map(u=>`<tr><td>${who(u.name,u.id,`<button type="button" class="lnk" data-view="${u.id}">${esc(u.name)}</button>`)}</td><td>${esc(u.email)}</td><td>${fmtDate(u.joined)}</td><td class="nowrap">${esc(fmtLogin(u.last_login))}</td><td><span class="tag ${u.active?'':'off'}">${u.active?'Active':'Inactive'}</span></td><td><div class="cell-acts">${toggleBtn(u)}<button class="btn sm danger" type="button" data-del="user" data-id="${u.id}">Delete</button></div></td></tr>`).join('')||'<tr><td colspan="6">No results found</td></tr>';
}

/* ---------- coaches ---------- */
function renderCoaches(){
  const cnt={all:COACHES.length,pending:0,approved:0,rejected:0};
  COACHES.forEach(c=>cnt[c.approval]++);
  $('tabs').innerHTML=[['all','All'],['pending','Pending'],['approved','Approved'],['rejected','Rejected']]
    .map(([k,l])=>`<button type="button" class="tab ${coachTab===k?'on':''}" data-tab="${k}">${l} <b>${cnt[k]}</b></button>`).join('');
  const q=qCoaches.toLowerCase();
  const rows=COACHES
    .filter(c=>(coachTab==='all'||c.approval===coachTab)&&(c.name+' '+gymOf(c.id)).toLowerCase().includes(q))
    .sort((a,b)=>(a.approval==='pending'?0:1)-(b.approval==='pending'?0:1)||a.name.localeCompare(b.name));
  $('tc').innerHTML=rows.map(c=>{
    const cls=c.approval==='pending'?'pending':c.approval==='rejected'?'rej':'';
    const approve=c.approval!=='approved'?`<button class="btn sm" type="button" data-act="approve" data-id="${c.id}">Approve</button>`:'';
    const reject=c.approval!=='rejected'?`<button class="btn sm ghost" type="button" data-act="reject" data-id="${c.id}">${c.approval==='approved'?'Revoke':'Reject'}</button>`:'';
    return `<tr><td>${who(c.name,c.id,`<button type="button" class="lnk" data-view="${c.id}">${esc(c.name)}</button>`)}</td><td>${esc(gymOf(c.id))}</td><td>${c.clients}</td><td><span class="tag ${cls}">${c.approval[0].toUpperCase()+c.approval.slice(1)}</span></td><td class="nowrap">${esc(fmtLogin(c.last_login))}</td><td><span class="tag ${c.active?'':'off'}">${c.active?'Active':'Inactive'}</span></td><td><div class="cell-acts">${approve}${reject}${toggleBtn(c)}<button class="btn sm danger" type="button" data-del="gym_owner" data-id="${c.id}">Delete</button></div></td></tr>`;
  }).join('')||'<tr><td colspan="7">No gym owners found</td></tr>';
}

/* ---------- stats, badges ---------- */
function renderStats(){
  const approved=COACHES.filter(c=>c.approval==='approved').length;
  const pending=COACHES.filter(c=>c.approval==='pending').length;
  const active=USERS.filter(u=>u.active).length;
  $('s-users').textContent=USERS.length.toLocaleString();
  $('s-users-sub').textContent=active+' active';
  $('s-coaches').textContent=approved.toLocaleString();
  const p=$('s-pend');p.hidden=!pending;p.textContent=pending+' pending approval \u2192';
  const n=$('nav-pend');n.hidden=!pending;n.textContent=pending;
  const b=$('bell-n');b.hidden=!pending;b.textContent=pending;
}

function renderAll(){
  renderUsers();renderCoaches();renderStats();renderGyms();renderMemberships();
  drawChart($('c'));drawChart($('c2'));
}

/* ---------- profile rendering ---------- */
const initials=n=>n.split(/\s+/).filter(Boolean).slice(0,2).map(w=>w[0].toUpperCase()).join('')||'A';
function renderProfile(){
  $('mn').textContent='Admin '+P.name;
  $('pn').textContent=P.name;
  $('ph-name').textContent=P.name;
  $('ph-email').textContent=P.email;
  document.querySelectorAll('[data-ini]').forEach(e=>setAv(e,P.name,P.avatar_url));
  document.querySelectorAll('[data-first]').forEach(e=>e.textContent=P.name.split(/\s+/)[0]);
  $('st-login').textContent=lastLogin?new Date(lastLogin).toLocaleString([], {month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}):'Just now';
  $('st-since').textContent=memberSince?new Date(memberSince).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}):'-';
}
function fillForm(){
  $('fn').value=P.name;$('fe').value=P.email;$('fph').value=P.phone;$('fl').value=P.location;$('fb').value=P.bio;
}

/* ---------- navigation ---------- */
const TITLES={dashboard:'Dashboard',users:'Users',coaches:'Gym Owners',gyms:'Gyms',analytics:'Analytics',audit:'Activity log',messages:'Messages',profile:'Admin profile'};
const dd=$('dd'),pb=$('pb');
function closeMenu(){dd.hidden=true;pb.setAttribute('aria-expanded','false')}
function show(h){
  if(!TITLES[h])h='dashboard';
  document.querySelectorAll('.view').forEach(v=>v.classList.toggle('on',v.id=='v-'+h));
  document.querySelectorAll('nav a').forEach(a=>a.classList.toggle('on',a.getAttribute('href')=='#'+h));
  $('me').classList.toggle('on',h=='profile');
  $('t').textContent=TITLES[h];
  if(h=='profile'){fillForm();renderProfile();['pf-err','ok','pw-err','pw-ok'].forEach(i=>$(i).hidden=true)}
  if(h=='audit')loadAudit();
  if(h=='gyms')loadGyms();
  if(h=='messages'){renderMail();renderThread();refreshChat()}
  closeMenu();
  window.scrollTo(0,0);
}
window.addEventListener('hashchange',()=>show(location.hash.slice(1)));

/* ---------- log out ---------- */
function openModal(){closeMenu();$('ov').hidden=false;$('mc').focus()}
function closeModal(){$('ov').hidden=true}
function logout(){closeModal();$('lf').submit()}
$('mo').onclick=logout;
$('mc').onclick=closeModal;
$('ov').addEventListener('click',e=>{if(e.target===$('ov'))closeModal()});

/* ---------- delete ---------- */
let pendingDel=null;
function openDelete(type,id){
  const item=(type==='user'?USERS:COACHES).find(x=>x.id==id);
  if(!item)return;
  pendingDel={type,id:item.id,name:item.name};
  $('dt').textContent='Delete '+item.name+'?';
  $('dp').textContent=type==='user'
    ?'This permanently removes the member and their subscription. This cannot be undone.'
    :'This permanently removes the gym owner. This cannot be undone.';
  closeMenu();$('dov').hidden=false;$('dc').focus();
}
function closeDelete(){$('dov').hidden=true;pendingDel=null}
$('dc').onclick=closeDelete;
$('dov').addEventListener('click',e=>{if(e.target===$('dov'))closeDelete()});
$('dx').onclick=async()=>{
  if(!pendingDel)return;
  const {id,name}=pendingDel;
  try{
    await api('/api/admin/users/'+id,'DELETE');
    closeDelete();flash(name+' deleted');await loadPeople();
  }catch(ex){closeDelete();flash(ex.message)}
};

/* ---------- activate / deactivate ---------- */
let pendingAct=null;
async function setActive(id,active){
  const p=[...USERS,...COACHES].find(x=>x.id==id);if(!p)return;
  try{
    await api('/api/admin/users/'+id+'/status','PATCH',{active});
    flash(p.name+(active?' activated':' deactivated'));
    await loadPeople();
  }catch(ex){flash(ex.message)}
}
function askToggle(id,active){
  const p=[...USERS,...COACHES].find(x=>x.id==id);if(!p)return;
  if(active){setActive(id,true);return}          // activating needs no confirmation
  pendingAct={id:p.id,name:p.name};
  $('at').textContent='Deactivate '+p.name+'?';
  $('ap-text').textContent='They will be signed out and will not be able to log in until you activate the account again.';
  closeMenu();$('aov').hidden=false;$('ac').focus();
}
function closeAct(){$('aov').hidden=true;pendingAct=null}
$('ac').onclick=closeAct;
$('aov').addEventListener('click',e=>{if(e.target===$('aov'))closeAct()});
$('ax').onclick=async()=>{
  if(!pendingAct)return;
  const {id}=pendingAct;closeAct();
  await setActive(id,false);
};

document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    if(!$('aov').hidden)closeAct();
    else if(!$('dov').hidden)closeDelete();
    else if(!$('ov').hidden)closeModal();
    else closeMenu();
  }
});

/* ---------- search + tabs ---------- */
$('qu').oninput=e=>{qUsers=e.target.value;renderUsers()};
$('qc').oninput=e=>{qCoaches=e.target.value;renderCoaches()};

/* ---------- approve / reject ---------- */
async function setApproval(id,status){
  const c=COACHES.find(x=>x.id==id);if(!c)return;
  try{
    const j=await api('/api/admin/coaches/'+id+'/approval','PATCH',{status});
    let msg=c.name+' '+status;
    if(status==='approved')msg+=j.emailed?' (email sent)':' (email not sent)';
    // Safety net: if the server says it saved something other than "active", tell the admin.
    if(status==='approved'&&j.status&&!['active','inactive'].includes(String(j.status).toLowerCase())){
      msg='Saved status is "'+j.status+'", not active. Check the users.status column.';
    }
    flash(msg);
    // Follow the coach to the tab they now belong to so it doesn't look like nothing happened.
    if(coachTab!=='all'&&coachTab!==status)coachTab=(status==='approved'?'approved':'rejected');
    await loadPeople();
  }catch(ex){flash(ex.message)}
}

/* ---------- global clicks ---------- */
document.addEventListener('click',e=>{
  const del=e.target.closest('[data-del]');
  if(del){openDelete(del.dataset.del,del.dataset.id);return}
  const tg=e.target.closest('[data-toggle]');
  if(tg){askToggle(tg.dataset.toggle,tg.dataset.to==='1');return}
  const act=e.target.closest('[data-act]');
  if(act){setApproval(act.dataset.id,act.dataset.act==='approve'?'approved':'rejected');return}
  const tab=e.target.closest('[data-tab]');
  if(tab){coachTab=tab.dataset.tab;renderCoaches();return}
  if(e.target.closest('[data-logout]')){openModal();return}
  const a=e.target.closest('a[href^="#"]');
  if(a&&a.getAttribute('href').length>1){
    e.preventDefault();
    const h=a.getAttribute('href');
    if(a.id==='bell'||a.id==='s-pend'){coachTab='pending';renderCoaches()}
    if(location.hash!==h)location.hash=h;else show(h.slice(1));
    return;
  }
  if(e.target.closest('#pb')){dd.hidden=!dd.hidden;pb.setAttribute('aria-expanded',String(!dd.hidden))}
  else if(!e.target.closest('#dd'))closeMenu();
});

/* ---------- profile forms (saved to the database) ---------- */
$('pf').addEventListener('submit',async e=>{
  e.preventDefault();
  const n=$('fn').value.trim(),em=$('fe').value.trim(),err=$('pf-err');
  if(!n){err.textContent='Enter your full name.';err.hidden=false;return}
  if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(em)){err.textContent='Enter a valid email address, like name@example.com.';err.hidden=false;return}
  err.hidden=true;
  const data={name:n,email:em,phone:$('fph').value.trim(),location:$('fl').value.trim(),bio:$('fb').value.trim()};
  try{
    await send(<?php echo json_encode(route('profile.update'), 15, 512) ?>,'PATCH',data);
    P=Object.assign(P,data);
    renderProfile();
    const o=$('ok');o.hidden=false;setTimeout(()=>o.hidden=true,2500);
  }catch(ex){err.textContent=ex.message;err.hidden=false}
});
$('pw').addEventListener('submit',async e=>{
  e.preventDefault();
  const a=$('pw0').value,b=$('pw1').value,c=$('pw2').value,err=$('pw-err');
  if(b.length<8){err.textContent='New password must be at least 8 characters.';err.hidden=false;$('pw-ok').hidden=true;return}
  if(b!==c){err.textContent='New password and confirmation do not match.';err.hidden=false;$('pw-ok').hidden=true;return}
  try{
    await send(<?php echo json_encode(route('profile.password'), 15, 512) ?>,'PUT',{current_password:a,password:b,password_confirmation:c});
    err.hidden=true;$('pw').reset();
    const o=$('pw-ok');o.hidden=false;setTimeout(()=>o.hidden=true,2500);
  }catch(ex){err.textContent=ex.message;err.hidden=false;$('pw-ok').hidden=true}
});

/* ---------- profile photo (upload / remove) ---------- */
$('avf').addEventListener('change',async e=>{
  const f=e.target.files[0];e.target.value='';
  if(!f)return;
  if(f.size>2*1024*1024)return flash('Image must be under 2 MB.');
  const body=new FormData();body.append('avatar',f);
  try{
    const r=await fetch('/api/profile/avatar',{method:'POST',credentials:'same-origin',
      headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF},body});
    const j=await r.json().catch(()=>({}));
    if(!r.ok)throw new Error(j.errors?Object.values(j.errors)[0][0]:(j.message||'Upload failed'));
    P.avatar_url=j.avatar_url;renderProfile();flash('Photo updated');
  }catch(err){flash(err.message)}
});
$('avrm').onclick=async()=>{
  try{await api('/api/profile/avatar','DELETE');P.avatar_url=null;renderProfile();flash('Photo removed')}
  catch(err){flash(err.message)}
};

/* ---------- activity log (server-side paging, works with millions of rows) ---------- */
let aPage=1,aTimer;
const pj=s=>{try{return typeof s==='string'?JSON.parse(s):(s||{})}catch(e){return {}}};
function auditDetail(a){
  const o=pj(a.old_values),n=pj(a.new_values);
  if(a.action==='INSERT')return 'Created as '+(n.role||'user')+' ('+(n.status||'')+')';
  if(a.action==='DELETE')return 'Deleted '+(o.name||'')+' ('+(o.email||'')+')';
  const ch=['name','email','role','status'].filter(k=>o[k]!==n[k]).map(k=>k+': '+o[k]+' \u2192 '+n[k]);
  if(Number(n.password_changed))ch.push('password changed');
  return ch.join('; ')||'Updated';
}
async function loadAudit(){
  const qs=new URLSearchParams({page:aPage,q:$('qa').value.trim(),action:$('fa').value});
  $('ta').innerHTML='<tr><td colspan="5">Loading...</td></tr>';
  try{
    const r=await fetch('/api/audit?'+qs,{headers:{'Accept':'application/json'}});
    if(!r.ok)throw new Error();
    const j=await r.json();
    $('ta').innerHTML=j.data.map(a=>{
      const acct=pj(a.new_values).name||pj(a.old_values).name||('#'+a.record_id);
      const cls=a.action==='INSERT'?'':a.action==='UPDATE'?'pending':'rej';
      return `<tr><td>${esc(a.created_at)}</td><td><span class="tag ${cls}">${esc(a.action)}</span></td><td>${esc(acct)}</td><td>${esc(a.by_name||'Direct database edit')}</td><td>${esc(auditDetail(a))}</td></tr>`;
    }).join('')||'<tr><td colspan="5">No activity found</td></tr>';
    $('ap').disabled=!j.prev_page_url;
    $('an').disabled=!j.next_page_url;
    $('apg').textContent='Page '+j.current_page;
  }catch(e){$('ta').innerHTML='<tr><td colspan="5">Could not load the log.</td></tr>'}
}
$('qa').oninput=()=>{clearTimeout(aTimer);aTimer=setTimeout(()=>{aPage=1;loadAudit()},400)};
$('fa').onchange=()=>{aPage=1;loadAudit()};
$('ap').onclick=()=>{if(aPage>1){aPage--;loadAudit()}};
$('an').onclick=()=>{aPage++;loadAudit()};

/* ---------- REAL MESSAGING (shared Laravel API) ---------- */
let msKey=null,msFilter='all',chatContacts=[],chatMessages=[],chatMyId=null;
let threadRequest=0,chatRefreshBusy=false,chatSending=false;
const chatTime=ts=>ts?new Date(ts).toLocaleString([], {month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}):'';

function renderMailBadge(){
  const n=chatContacts.reduce((total,c)=>total+Number(c.unread||0),0);
  for(const id of ['mail-n','nav-chat']){
    const el=$(id);if(!el)continue;
    el.hidden=n===0;el.textContent=n>99?'99+':n;
  }
}
function renderMail(){
  $('mstabs').innerHTML=[['all','All'],['owner','Gym Owners']]
    .map(([key,title])=>`<button type="button" class="tab ${msFilter===key?'on':''}" data-mstab="${key}">${title}</button>`).join('');
  const q=$('msq').value.trim().toLowerCase();
  const contacts=chatContacts.filter(c=>(msFilter==='all'||c.role===msFilter)&&String(c.name||'').toLowerCase().includes(q));
  $('mslist').innerHTML=contacts.map(c=>`<button type="button" class="cv ${String(msKey)===String(c.id)?'on':''}" data-conv="${Number(c.id)}">
    ${avatar(c.name,avOf(c.id))}<div><b>${esc(c.name||'Unknown')}</b> <span class="tag off">Gym Owner</span>
    <small>${esc(c.last_message||'Start a conversation')}</small></div>
    ${Number(c.unread)>0?`<span class="nav-count">${Number(c.unread)}</span>`:''}</button>`).join('')
    ||'<p class="ms-empty">No contacts found.</p>';
}
function renderThread(){
  const c=chatContacts.find(p=>String(p.id)===String(msKey));
  const box=$('msbody');$('msf').hidden=!c;
  if(!c){$('mshead').textContent='Select a conversation';box.innerHTML='';return}
  $('mshead').innerHTML=esc(c.name)+' <span class="tag off">'+'Gym Owner'+'</span>';
  const nearBottom=box.scrollHeight-box.scrollTop-box.clientHeight<80;
  box.innerHTML=chatMessages.map(m=>{
    const mine=Number(m.sender_id)===Number(chatMyId);
    return `<div class="bub ${mine?'mine':''}">${esc(m.body)}<small>${esc(chatTime(m.created_at))}</small></div>`;
  }).join('')||'<p class="ms-empty">No messages yet. Say hello!</p>';
  if(nearBottom||box.dataset.newThread==='1'){box.scrollTop=box.scrollHeight;box.dataset.newThread='0'}
}
async function loadChatContacts(){
  const j=await api('/api/chat/contacts');
  chatContacts=(j.contacts||[]).filter(c=>c.role==='owner');
  if(msKey&&!chatContacts.some(c=>String(c.id)===String(msKey))){msKey=null;chatMessages=[];chatMyId=null;++threadRequest}
  renderMailBadge();renderMail();renderThread();
}
async function loadChatThread(){
  if(!msKey)return;
  const target=String(msKey),request=++threadRequest;
  const j=await api('/api/chat/'+encodeURIComponent(target));
  if(request!==threadRequest||String(msKey)!==target)return;
  chatMessages=j.messages||[];
  chatMyId=j.my_id;
  renderThread();
}
async function refreshChat(){
  if(chatRefreshBusy||document.hidden)return;
  chatRefreshBusy=true;
  try{
    await loadChatContacts();
    if(location.hash==='#messages'&&msKey){
      await loadChatThread();
      await loadChatContacts();
    }
  }catch(e){console.error('Chat refresh failed:',e);if(location.hash==='#messages')flash(e.message)}
  finally{chatRefreshBusy=false}
}
async function openConv(id){
  msKey=String(id);++threadRequest;
  chatMessages=[];chatMyId=null;$('msbody').dataset.newThread='1';
  renderMail();renderThread();
  try{await loadChatThread();await loadChatContacts();$('msb').focus()}
  catch(e){flash(e.message)}
}
document.addEventListener('click',e=>{
  const c=e.target.closest('[data-conv]');if(c){openConv(c.dataset.conv);return}
  const t=e.target.closest('[data-mstab]');if(t){msFilter=t.dataset.mstab;renderMail()}
});
$('msq').addEventListener('input',renderMail);
$('msf').addEventListener('submit',async e=>{
  e.preventDefault();
  const body=$('msb').value.trim(),target=msKey;
  if(!body||!target||chatSending)return;
  chatSending=true;
  const button=$('msf').querySelector('button[type="submit"]');button.disabled=true;
  try{
    await api('/api/chat/'+encodeURIComponent(target),'POST',{body});
    $('msb').value='';
    if(String(msKey)===String(target))await loadChatThread();
    await loadChatContacts();
    $('msbody').scrollTop=$('msbody').scrollHeight;
  }catch(e){flash(e.message)}
  finally{chatSending=false;button.disabled=false}
});
$('msb').addEventListener('keydown',e=>{
  if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();$('msf').requestSubmit()}
});
setInterval(refreshChat,5000);
refreshChat();

/* ---------- person details popup ---------- */
const val=v=>(v===null||v===undefined||v==='')?'-':esc(v);
const UL=[['first_name','First name'],['last_name','Last name'],['name','Username'],['email','Email'],['role','Role'],['status','Status'],['gender','Gender'],['contact_number','Contact number'],['date_of_birth','Date of birth'],['address','Address'],['created_at','Registered'],['last_login_at','Last login']];
const ML=[['emergency_contact_name','Emergency contact'],['emergency_contact_number','Emergency number'],['height_cm','Height (cm)'],['weight_kg','Weight (kg)'],['fitness_goal','Fitness goal'],['join_date','Join date']];
const TL=[['specialty','Specialty'],['certification','Certification'],['years_experience','Years of experience'],['bio','Bio']];
function rows(list,obj){return list.map(([k,l])=>`<dt>${l}</dt><dd>${val(obj[k])}</dd>`).join('')}
async function viewPerson(id){
  $('vov').hidden=false;$('vbody').textContent='Loading...';
  try{
    const j=await api('/api/admin/users/'+id+'/details');
    const u=j.user,p=j.profile;
    u.last_login_at=fmtLogin(u.last_login_at);
    const full=[u.first_name,u.last_name].filter(Boolean).join(' ')||u.name;
    $('vt').textContent=full;
    const isCoach=u.role==='gym_owner';
    const gym=isCoach?GYMS.find(x=>gymOwnerId(x)==u.id):null;
    $('vbody').innerHTML=`<div style="display:flex;justify-content:center;margin-bottom:14px">${avatar(full,avOf(u.id),'big')}</div><dl class="dl">`+rows(UL,u)+
      `<h3>${isCoach?'Gym Owner details':'Member details'}</h3>`+
      (gym?`<dt>Gym name</dt><dd><b>${esc(gym.gym_name)}</b></dd><dt>Gym phone</dt><dd>${esc(gym.phone_number)}</dd><dt>Gym address</dt><dd>${esc(gym.gym_address)}</dd><dt>Total clients</dt><dd>${gym.total_clients}</dd>`:
        (p?rows(isCoach?TL:ML,p):'<dt></dt><dd>No extra details saved (older account).</dd>'))+'</dl>';
  }catch(ex){$('vbody').textContent='Could not load details.'}
}
document.addEventListener('click',e=>{
  const v=e.target.closest('[data-view]');
  if(v){viewPerson(v.dataset.view);return}
  if(e.target===$('vov')||e.target.closest('#vc'))$('vov').hidden=true;
});
document.addEventListener('keydown',e=>{if(e.key==='Escape')$('vov').hidden=true});

/* ---------- gyms: picker + detail (no payments) ---------- */
const CUR='\u20B1';
const dayISO=d=>d?String(d).slice(0,10):'';
const fd=d=>d?fmtDate(dayISO(d)):'-';
const money=v=>CUR+Number(v||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
let mGym=null,qmg='';

const gymMembers=g=>USERS.filter(u=>(g.member_ids||[]).some(id=>id==u.id));

function renderGymPicker(){
  const q=qmg.toLowerCase();
  const list=GYMS.filter(g=>(g.gym_name+' '+g.owner_name+' '+(g.gym_address||'')).toLowerCase().includes(q));
  $('mg-list').innerHTML=list.map(g=>`<button type="button" class="gym-card" data-mgym="${g.id}"><b>${esc(g.gym_name)}</b><small>${esc(g.owner_name)} &middot; ${esc(g.gym_address)}</small><div class="cell-acts"><span class="tag ${gymApproved(g)?'':'pending'}">${gymApproved(g)?'Approved':'Pending'}</span><span class="tag off">${Number(g.total_clients||0)} members</span></div><small style="color:var(--accent)">View gym details \u2192</small></button>`).join('')||'<p class="ms-empty">No gyms found.</p>';
}

let gymTab='overview',gymCatalog={};
const gymTabNames=[['overview','Overview'],['members','Members'],['products','Products'],['coaches','Coaches']];
function renderGymPanels(){
  $('gym-tabs').innerHTML=gymTabNames.map(([key,title])=>`<button type="button" class="tab ${gymTab===key?'on':''}" data-gtab="${key}">${title}</button>`).join('');
  for(const [key] of gymTabNames) $('gym-'+key).hidden=gymTab!==key;
}
function renderGymDetails(g){
  renderGymPanels();
  const members=gymMembers(g);
  const infoPairs=[['Gym',g.gym_name],['Owner',g.owner_name],['Owner email',g.owner_email],['Phone',g.phone_number],['Address',g.gym_address],['Total registered members',members.length],['Approval status',gymApproved(g)?'Approved':'Pending']];
  $('gym-info').innerHTML=infoPairs.map(([name,value])=>`<dt>${esc(name)}</dt><dd>${esc(value??'-')}</dd>`).join('');
  const q=$('gym-member-search').value.trim().toLowerCase();
  $('gym-members-body').innerHTML=members.filter(u=>(u.name+' '+u.email).toLowerCase().includes(q)).map(u=>
    `<tr><td>${who(u.name,u.id,`<button class="lnk" type="button" data-view="${u.id}">${esc(u.name)}</button>`)}</td><td>${esc(u.email)}</td><td>${fd(u.joined)}</td></tr>`
  ).join('')||'<tr><td colspan="3">No members found for this gym.</td></tr>';
  renderGymCatalog(g.id);
}
// Products and trainers come from a read-only admin endpoint.
// Expected response: { products: [{id,name,price,stock,image_url}], coaches: [{id,name,specialty}] }
async function loadGymCatalog(id){
  gymCatalog[id]={loading:true,products:[],coaches:[],error:null};renderGymCatalog(id);
  try{
    const result=await api('/api/admin/gyms/'+encodeURIComponent(id)+'/catalog');
    gymCatalog[id]={products:Array.isArray(result.products)?result.products:[],coaches:Array.isArray(result.coaches)?result.coaches:[],error:null};
  }catch(e){
    console.error('Gym catalog request failed', {gymId:id,error:e});
    gymCatalog[id]={products:[],coaches:[],error:'Catalog request failed: '+e.message+' \u2014 check your browser Network tab and storage/logs/laravel.log'};
  }
  if(String(mGym)===String(id))renderGymCatalog(id);
}
function renderGymCatalog(id){
  const data=gymCatalog[id];
  if(!data){$('gym-products-list').textContent='Open Products to load the catalog.';$('gym-coaches-list').textContent='Open Coaches to load trainers.';return}
  for(const [kind,target] of [['products','gym-products-list'],['coaches','gym-coaches-list']]){
    const panel=$(target);
    if(data.loading){panel.textContent='Loading...';continue}
    if(data.error){panel.textContent=data.error;continue}
    const items=data[kind]||[];
    panel.innerHTML=items.map(item=>{
      const imgSrc=kind==='products'?safeImg(item.image_url):'';
      const image=imgSrc?`<img src="${esc(imgSrc)}" alt="${esc(item.name)}">`:'';
      const detail=kind==='products'?`${money(item.price)} \u00B7 ${Number(item.stock||0)} in stock`:esc(item.specialty||'Trainer');
      return `<div class="gym-tile">${image}<b>${esc(item.name)}</b><p class="sub">${detail}</p></div>`;
    }).join('')||`<p class="ms-empty">No ${kind} recorded for this gym.</p>`;
  }
}
function renderMemberships(){
  const g=GYMS.find(x=>x.id==mGym);
  if(!g)mGym=null;
  $('m-gyms').hidden=!!g;$('m-detail').hidden=!g;
  if(!g){renderGymPicker();return}
  $('m-gname').textContent=g.gym_name;
  $('m-gsub').textContent=g.owner_name+' \u00B7 '+g.gym_address;
  $('gym-status').textContent=gymApproved(g)?'Approved':'Pending';
  $('gym-status').className='tag '+(gymApproved(g)?'':'pending');
  $('gym-member-count').textContent=gymMembers(g).length;
  renderGymDetails(g);
}
$('qmg').oninput=e=>{qmg=e.target.value;renderGymPicker()};
$('m-back').onclick=()=>{mGym=null;gymTab='overview';renderMemberships()};
$('gym-member-search').oninput=()=>{const g=GYMS.find(x=>x.id==mGym);if(g)renderGymDetails(g)};
document.addEventListener('click',e=>{
  const c=e.target.closest('[data-mgym]');
  if(c){mGym=c.dataset.mgym;gymTab='overview';renderMemberships();loadGymCatalog(mGym);return}
  const tab=e.target.closest('[data-gtab]');
  if(tab){gymTab=tab.dataset.gtab;renderGymPanels()}
});

/* ---------- start ---------- */
$('today').textContent=new Date().toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
$('chart-title').textContent='User & Gym Owner Analytics (Jan - '+M[M.length-1]+' '+YEAR+')';
$('chart-opt').textContent='Number of People '+YEAR;
renderAll();renderProfile();show(location.hash.slice(1));
loadPeople();
setInterval(()=>{if(!document.hidden)loadPeople()},20000);
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\dashboard\firstwebsite\resources\views/admin_dash.blade.php ENDPATH**/ ?>