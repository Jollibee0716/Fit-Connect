<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><meta name="my-avatar" content="{{ auth()->user()->avatar_url }}"><title>FITCONNECT | Member Portal</title>
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


/* Header shortcuts — same placement and visual treatment as Gym Owner. */
.topright{display:flex;align-items:center;gap:16px}
.header-message{position:relative;border:0;background:none;color:var(--ink);font-size:19px;line-height:1;padding:5px;cursor:pointer;border-radius:8px}
.header-message:hover,.header-message:focus-visible{background:var(--line)}
.topright .avatar{flex:none}

/* MEMBER VIEW BINDINGS — visuals only; the existing application requests remain unchanged. */
:root{color-scheme:dark}
button,input,select,textarea{font:inherit}
button{cursor:pointer}
.app{min-height:640px}
.brand{display:flex;align-items:center;gap:10px;padding:20px 16px;margin:0}
.brandmark svg{width:22px;height:22px}.brandmark{width:36px;height:36px;border-radius:10px;background:var(--accent);color:var(--navtxt);display:grid;place-items:center;flex:none;font-size:20px}
.brandname{display:flex;flex-direction:column;line-height:1.15;min-width:0}
.brandname b{font-size:17px;font-weight:700;letter-spacing:0}
.brandname small{font-size:11px;color:var(--accent);font-weight:600;letter-spacing:.08em;text-transform:uppercase}
nav.menu{padding:0 8px;display:flex;flex-direction:column;gap:6px}
nav.menu .nav{width:100%;display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;color:var(--ink);background:none;border:0;font-size:13px;font-weight:500;text-align:left;margin:0;line-height:1.35}
nav.menu .nav span{width:17px;flex:none;text-align:center;font-size:15px}
nav.menu .nav:hover{background:var(--line)}
nav.menu .nav.active{background:var(--accent);color:var(--navtxt)}
.sidenavfoot{margin-top:auto;padding:0 8px 14px}
.logout{width:100%;background:none;border:0;color:#ff8a65;font-size:14px;text-align:left;border-radius:8px;padding:10px 12px}
.logout:hover{background:var(--line)}
.topbar{display:flex;justify-content:space-between;align-items:center;padding:14px 22px;border-bottom:1px solid var(--line);font-weight:600;min-height:61px}
.topbar strong{font-size:14px}
.topright{display:flex;align-items:center;gap:16px;font-size:13px;font-weight:500;color:var(--ink)}
.avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#ffb84d,#cc5500);color:var(--navtxt);display:grid;place-items:center;font-size:11px;font-weight:700}
.content{padding:20px 22px;flex:1}
.page.hidden{display:none!important}
.eyebrow{color:var(--accent);font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:4px}
.page>p{margin:4px 0 16px;color:var(--mute);font-size:14px}
.hero{display:flex;align-items:center;justify-content:space-between;gap:20px;margin:14px 0;padding:20px;border-radius:10px;border:1px solid var(--line);background:var(--panel)}
.hero h1{margin:3px 0 5px;font-size:22px}
.hero p{margin:4px 0 16px;color:var(--mute);font-size:14px}
.button{background:var(--accent);color:var(--navtxt);border:0;border-radius:8px;padding:9px 16px;font-size:14px;font-weight:600}
.button:hover{filter:brightness(1.08)}.button:disabled{opacity:.6;cursor:wait}
.button.outline,.button.secondary{background:none;color:var(--ink);border:1px solid var(--line)}
.button.outline:hover,.button.secondary:hover{background:var(--line)}
.button.danger{background:var(--red);color:#fff}
.cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin:18px 0}
.card,.stat{background:var(--panel);border:1px solid var(--line);border-radius:10px;padding:14px 16px;box-shadow:none}
.card h3{font-size:16px;margin:9px 0 6px}
.card p{font-size:14px;color:var(--mute);line-height:1.55}
.card .price{font-size:25px;color:var(--accent);font-weight:700;margin:12px 0}
.meta,.muted{color:var(--mute);font-size:12px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:18px 0}
.stat small{display:block;color:var(--mute);font-size:12px;font-weight:600}
.stat b{display:block;font-size:20px;margin:5px 0;overflow-wrap:anywhere}
.gridhead{display:flex;align-items:center;justify-content:space-between;gap:14px;margin:18px 0 14px}
.gridhead h2{margin:0;font-size:16px}
.tile{width:42px;height:42px;display:grid;place-items:center;border-radius:9px;background:rgba(255,153,0,.13);font-size:24px}
.input,.select{background:var(--panel);color:var(--ink);border:1px solid var(--line);border-radius:8px;padding:9px 12px;font-size:14px;width:100%;max-width:none;margin:4px 0 0}
.input::placeholder{color:var(--mute)}.input:focus-visible,.select:focus-visible{outline:2px solid var(--accent)}
.search{max-width:320px;margin:0}
.tag,.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:99px;font-size:12px;font-weight:500;background:rgba(74,222,128,.15);color:var(--green)}
.notice{padding:10px 14px;border-radius:10px;margin:0 0 14px;background:rgba(255,153,0,.15);color:var(--accent);font-size:13px}
.notice.error{background:rgba(239,68,68,.15);color:#ff8a65}.notice:empty{display:none}
.checkout{margin-top:12px;display:grid;gap:6px}
.checkout label{font-size:12px;color:var(--mute)}
.checkout .button{margin-top:10px}
.row{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.row>*{flex:initial}
.line{display:flex;justify-content:space-between;gap:12px;align-items:center;padding:10px 0;border-bottom:1px solid var(--line);font-size:14px}
.line:last-child{border-bottom:0}.line .meta{margin-top:4px}
.empty{padding:20px;color:var(--mute);font-size:14px;text-align:center;border:1px dashed var(--line);border-radius:10px}
@media(max-width:900px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.side{width:100%;min-height:0}.app{flex-direction:column}.brand{padding:12px 16px}nav.menu{flex-direction:row;flex-wrap:wrap;padding:0 8px 10px}.menu .nav{width:auto!important}.sidenavfoot{margin:0;padding:0 8px 10px}.logout{width:auto}.stats{grid-template-columns:1fr 1fr}.hero{flex-wrap:wrap}}
@media(max-width:480px){.cards,.stats{grid-template-columns:1fr}.gridhead{align-items:stretch;flex-direction:column}.search{max-width:none}.hero>div:last-child{display:none}.topbar{gap:10px}.topright{gap:7px}}

.topright .avatar{position:relative;overflow:hidden}.topright .avatar img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%}
.pf-av img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%}
@media(max-width:760px){.pf-grid{grid-template-columns:1fr}.stats{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.stats{grid-template-columns:1fr}}

/* Matching the supplied Gym Owner Dashboard's navigation/header geometry exactly. */
.side .logo{display:flex;align-items:center;gap:10px;padding:20px 16px;margin:0}
.side .logo-mark{width:36px;height:36px;border-radius:10px;background:var(--accent);color:var(--navtxt);display:grid;place-items:center;flex:none}
.side .logo-mark svg{width:22px;height:22px}
.side .logo-txt{display:flex;flex-direction:column;line-height:1.15;min-width:0}
.side .logo-txt b{font-size:17px;font-weight:700}
.side .logo-txt small{font-size:11px;color:var(--accent);font-weight:600;letter-spacing:.08em;text-transform:uppercase}
.side nav.menu{padding:0 8px;display:flex;flex-direction:column;gap:6px}
.side nav.menu .nav{display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;color:var(--ink);font:inherit;font-size:14px;font-weight:500;background:none;border:0;text-align:left;line-height:normal;width:100%}
.side nav.menu .nav:hover{background:var(--line)}
.side nav.menu .nav.active{background:var(--accent);color:var(--navtxt)}
.side .sidenavfoot{margin-top:auto;padding:0 8px 14px}
.side .logout{background:none;border:0;color:#ff8a65;font:inherit;font-size:14px;cursor:pointer;text-align:left;border-radius:8px;padding:10px 12px;width:100%}
.side .logout:hover{background:var(--line)}
.main .top.topbar{display:flex;justify-content:space-between;align-items:center;padding:14px 22px;border-bottom:1px solid var(--line);font-weight:600;min-height:unset;gap:10px}
.main .top .r{display:flex;align-items:center;gap:16px;font-size:13px;font-weight:500}
.main .top .header-message{border-radius:0;box-shadow:none;position:relative;display:inline-block;color:inherit;text-decoration:none;font-size:16px;border:0;background:none;padding:0;line-height:normal;cursor:pointer}
.main .top .header-message:hover{background:none;color:var(--accent)}
.main .top .pb{display:flex;align-items:center;gap:8px;color:inherit;text-decoration:none;padding:4px 6px;border-radius:8px;background:none;border:0;cursor:pointer}
.main .top .pb:hover{background:var(--line)}
.main .top .av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#ffb84d,#cc5500);flex:none;position:relative;display:grid;place-items:center;font-size:11px;font-weight:700;color:var(--navtxt);overflow:hidden}
.main .top .av.dot::after{content:"";position:absolute;right:-1px;bottom:-1px;width:9px;height:9px;border-radius:50%;background:var(--green);border:2px solid var(--panel);z-index:1}
.main .top .avatar img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:50%}
/* The original owner profile style: keep its three summary cards and two-column forms. */
#page-profile .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:14px}
#page-profile .stats .card strong{font-size:20px}
#page-profile .pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px}
#page-profile .row{display:flex;gap:12px;flex-wrap:nowrap;align-items:initial}
#page-profile .row>*{flex:1}
@media(max-width:760px){.side nav.menu{flex-direction:row;flex-wrap:wrap}.side nav.menu .nav{width:auto}.side .sidenavfoot{margin:0;padding:0 8px 10px}.side .logout{width:auto}#page-profile .pf-grid{grid-template-columns:1fr}#page-profile .row{flex-direction:column;gap:0}}
@media(max-width:480px){#page-profile .stats{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="app">
<aside class="side"><div class="logo"><span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 6.5v11M17.5 6.5v11M3.5 9v6M20.5 9v6M6.5 12h11"/></svg></span><span class="logo-txt"><b>FitConnect</b><small>Gym Member</small></span></div><nav class="menu" id="nav">
<button class="nav active" data-tab="dashboard"><span>▦</span> Dashboard</button>
<button class="nav" data-tab="gyms"><span>⌕</span> Explore Gyms</button>
<button class="nav" data-tab="mygym"><span>⌂</span> My Gym</button>
<button class="nav" data-tab="plans"><span>♢</span> Membership Plans</button>
<button class="nav" data-tab="products"><span>▣</span> Shop Products</button>
<button class="nav" data-tab="orders"><span>▤</span> My Orders</button>
<button class="nav" data-tab="payments"><span>◈</span> Payments</button>
<button class="nav" data-tab="classes"><span>▦</span> Classes & Attendance</button>
</nav><div class="sidenavfoot"><form action="{{ route('logout') }}" method="POST">@csrf<button class="logout" type="submit">↪ Log out</button></form></div></aside>
<main class="main"><header class="top topbar"><span id="topTitle">Dashboard</span><div class="r topright"><button type="button" class="badge header-message" aria-label="Messages" title="Messages" onclick="showTab('messages')">&#9993;<b id="mail-n" hidden></b></button><button type="button" class="pb profile-trigger" aria-label="My profile" title="My profile" onclick="showTab('profile')"><span class="av dot avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span></button></div></header>
<div class="content"><div id="feedback" class="notice" role="status" aria-live="polite"></div>
<section id="page-dashboard" class="page"><div class="hero"><div><div class="eyebrow" >YOUR FITNESS, YOUR WAY</div><h1>Welcome back, {{ auth()->user()->name }}!</h1><p>Discover gyms, join a membership, and shop fitness essentials.</p><button class="button" onclick="showTab('gyms')">Explore Gyms →</button></div><div style="font-size:90px;opacity:.8">🏋️</div></div><div class="stats"><div class="stat"><small>Selected Gym</small><b id="statGym">Not selected</b></div><div class="stat"><small>Membership</small><b id="statMember">No active plan</b></div><div class="stat"><small>Quick Shopping</small><b>Products & Plans</b></div></div><div class="gridhead"><h2>Quick actions</h2></div><div class="cards"><div class="card"><div class="tile">🏢</div><h3>Find your gym</h3><p>Browse available gyms and select one.</p><button class="button" onclick="showTab('gyms')">Explore Gyms</button></div><div class="card"><div class="tile">🎟️</div><h3>Join a membership</h3><p>Find plans available at your selected gym.</p><button class="button" onclick="showTab('plans')">View Plans</button></div><div class="card"><div class="tile">🛍️</div><h3>Shop products</h3><p>Buy items even if you don't have an active membership.</p><button class="button" onclick="showTab('products')">Shop Now</button></div></div></section>
<section id="page-gyms" class="page hidden"><div class="eyebrow">DISCOVER</div><h1>Explore Gyms</h1><p>Choose a gym to browse its products, services, and plans.</p><div class="gridhead"><h2>Available gyms</h2><input class="input search" id="gymSearch" placeholder="Search gym, location, or description..." aria-label="Search gyms"></div><div id="gymsGrid" class="cards"></div></section>
<section id="page-mygym" class="page hidden">
  <button type="button" class="button outline" onclick="showTab('gyms')" style="margin-bottom:16px">← Back to Explore Gyms</button>
  <div class="eyebrow">GYM DETAILS</div>
  <h1>Gym Profile</h1>
  <div id="myGymBox" class="card" style="margin-top:14px"></div>
  <div id="gymMembershipStatus" class="card" style="margin-top:14px" aria-live="polite"></div>
  <div class="gridhead"><h2>Membership Plans</h2><button type="button" class="button outline" onclick="showTab('plans')">See All Plans →</button></div>
  <div id="gymPlansPreview" class="cards"></div>
  <div class="gridhead"><h2>Products</h2><button type="button" class="button outline" onclick="showTab('products')">Shop All Products →</button></div>
  <div id="gymProductsPreview" class="cards"></div>
</section>
<section id="page-plans" class="page hidden"><div class="eyebrow">MEMBERSHIPS</div><h1>Choose Your Plan</h1><p>Choose a gym first, then purchase a membership. Your payment request requires gym owner confirmation.</p><div id="plansGrid" class="cards"></div></section>
<section id="page-products" class="page hidden"><div class="eyebrow">FITNESS SHOP</div><h1>Shop Products</h1><p>No active membership required. Select a gym to see its available products.</p><div id="productsGrid" class="cards"></div></section>
<section id="page-orders" class="page hidden"><div class="eyebrow">PURCHASES</div><h1>My Orders</h1><div id="ordersList" class="card"></div></section>
<section id="page-payments" class="page hidden"><div class="eyebrow">TRANSACTIONS</div><h1>Membership Payments</h1><div id="paymentsList" class="card"></div></section>
<section id="page-classes" class="page hidden"><div class="eyebrow">COMING NEXT</div><h1>Classes & Attendance</h1><div class="card"><h3>Member class schedule</h3><p>This section is reserved for class bookings and attendance history. Your existing check-in endpoint can be integrated after class browsing is added.</p></div></section>
<section id="page-messages" class="page hidden">
  <h1>Messages</h1>
  <p class="sub">Chat with the owner of your gym.</p>
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
<section id="page-profile" class="page hidden">
  <h1>My profile</h1>
  <div class="box" style="margin-top:16px">
    <div class="pf-hero">
      <div class="pf-av" id="pav" aria-label="Your profile picture"></div>
      <div>
        <h2 id="pname">{{ auth()->user()->name }}</h2>
        <p id="pmail">{{ auth()->user()->email }}</p>
        <div class="pf-meta"><span class="tag" id="paccount">Active</span><span class="tag off">Member</span><span class="tag pending" id="pmembership">No active membership</span></div>
        <div class="pf-actions">
          <label class="btn ghost sm" style="cursor:pointer">Change photo<input type="file" id="avf" accept="image/png,image/jpeg,image/webp" hidden></label>
          <button class="btn ghost sm" type="button" id="avrm">Remove photo</button>
        </div>
      </div>
    </div>
  </div>
  <div class="stats">
    <div class="card"><span>Last login</span><strong id="st-login">—</strong></div>
    <div class="card"><span>Member since</span><strong id="st-since">—</strong></div>
    <div class="card"><span>Selected gym</span><strong id="st-gym">None selected</strong></div>
  </div>
  <div class="pf-grid">
    <form class="box" id="pf" novalidate>
      <h3>Personal information</h3><p class="sub">Update how your name and contact details appear.</p>
      <label class="fld">Full name<input id="on" name="name" required autocomplete="name"></label>
      <label class="fld">Email<input id="oe" name="email" required type="email" autocomplete="email"></label>
      <div class="row"><label class="fld">Phone<input id="op" name="phone" autocomplete="tel" type="tel"></label>
      <label class="fld">Location<input id="ol" name="location"></label></div>
      <label class="fld">Role<input value="Member" disabled></label>
      <label class="fld">About you<textarea id="ob" name="bio" maxlength="1000"></textarea></label>
      <span class="ferr" id="pf-err" hidden></span>
      <button class="btn" type="submit">Save changes</button><span class="okmsg" id="pf-ok" hidden>Profile saved</span>
    </form>
    <div>
      <form class="box" id="pw" novalidate>
        <h3>Change password</h3><p class="sub">Use at least 8 characters.</p>
        <label class="fld">Current password<input id="pw0" type="password" autocomplete="current-password" required></label>
        <label class="fld">New password<input id="pw1" type="password" autocomplete="new-password" required minlength="8"></label>
        <label class="fld">Confirm new password<input id="pw2" type="password" autocomplete="new-password" required></label>
        <span class="ferr" id="pw-err" hidden></span>
        <button class="btn" type="submit">Update password</button><span class="okmsg" id="pw-ok" hidden>Password updated</span>
      </form>
      <div class="box" style="margin-top:14px">
        <h3>Session</h3><p class="sub">Sign out of your FitConnect member account on this device.</p>
        <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn danger" type="submit">Log out</button></form>
      </div>
    </div>
  </div>
</section>
</div></main></div>
<script>
const API='/api/member';let selectedGym=null,gyms=[],catalog={plans:[],products:[]};
let currentMembership={active:false,status:'none',plan:null,due_date:null,start_date:null};
const $=id=>document.getElementById(id);const safe=x=>String(x??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));const money=x=>'₱'+Number(x||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
function notify(message,error=false){$('feedback').textContent=message;$('feedback').className='notice'+(error?' error':'');window.scrollTo({top:0,behavior:'smooth'});} 
async function request(path,options={}){const response=await fetch(API+path,{credentials:'same-origin',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},...options});let data=await response.json().catch(()=>({}));if(!response.ok){const detail=data.errors?Object.values(data.errors).flat().join(' '):data.message;throw Error(detail||`Request failed (${response.status})`)}return data}
async function run(fn){try{await fn()}catch(e){notify(e.message,true)}}
async function showTab(tab){
  document.querySelectorAll('.page').forEach(e=>e.classList.toggle('hidden',e.id!=='page-'+tab));
  document.querySelectorAll('.nav').forEach(e=>e.classList.toggle('active',e.dataset.tab===tab));
  $('topTitle').textContent=({profile:'My Profile',messages:'Messages',mygym:'Gym Profile'})[tab] || document.querySelector('.nav.active')?.textContent.trim() || 'Member Dashboard';
  if(tab==='gyms')await loadGyms();
  if(['plans','products','mygym'].includes(tab))await loadCatalog();
  if(tab==='orders')await loadOrders();
  if(tab==='payments'){await loadPayments();await loadOverview();}
  if(tab==='mygym'){await loadOverview();renderGym();renderGymPreviews();}
  if(tab==='profile')await loadMemberProfile();
  if(tab==='messages')await loadChatContacts();
}

function cardEmpty(message){return '<div class="empty" style="grid-column:1/-1">'+safe(message)+'</div>'}
async function loadGyms(){
  const data=await request('/portal/gyms');
  gyms=Array.isArray(data.gyms)?data.gyms:[];
  renderGyms();
}
function renderGyms(){
  const q=$('gymSearch').value.trim().toLowerCase();
  const matches=gyms.filter(g=>[g.name,g.address,g.about,g.description,g.phone].join(' ').toLowerCase().includes(q));
  $('gymsGrid').innerHTML=matches.map(g=>{
    const chosen=Number(selectedGym?.id)===Number(g.id);
    return `<article class="card">
      <div class="tile">🏋️</div>
      <h3>${safe(g.name)}</h3>
      <p>📍 ${safe(g.address||'Location not provided')}</p>
      <p class="meta">☎ ${safe(g.phone||'Phone not provided')}</p>
      <h4 style="margin:12px 0 4px;color:var(--accent)">About this Gym</h4>
      <p style="white-space:pre-wrap">${safe(g.about||g.description||'No description provided yet.')}</p>
      ${g.hours?`<p class="meta">🕒 ${safe(g.hours)}</p>`:''}
      ${chosen?'<span class="tag">Selected gym</span>':''}
      <div style="margin-top:14px"><button type="button" class="button" onclick="openGym(${Number(g.id)})">View Gym →</button></div>
    </article>`;
  }).join('')||cardEmpty('No registered gyms match your search.');
}
async function openGym(id){
  await run(async()=>{
    const gym=gyms.find(g=>Number(g.id)===Number(id));
    if(!gym)throw Error('Gym not found. Refresh Explore Gyms.');
    await request('/portal/select-gym',{method:'POST',body:JSON.stringify({gym_id:id})});
    await loadOverview();
    // Keep the latest discoverable description if overview omits optional fields.
    if(selectedGym && Number(selectedGym.id)===Number(id)){
      selectedGym={...gym,...selectedGym,about:selectedGym.about||gym.about||gym.description||'',hours:selectedGym.hours||gym.hours||''};
    }
    renderGyms();
    await showTab('mygym');
  });
}
async function chooseGym(id){return openGym(id);}
function renderGym(){
  if(!selectedGym){
    $('myGymBox').innerHTML=`<p>Select a gym from Explore Gyms to get started.</p><button type="button" class="button" onclick="showTab('gyms')">Explore Gyms</button>`;
    return;
  }
  $('myGymBox').innerHTML=`<div class="tile">🏢</div>
    <h2>${safe(selectedGym.name)}</h2>
    <p>📍 ${safe(selectedGym.address||'Location not provided')}</p>
    <p>☎ ${safe(selectedGym.phone||'Phone not provided')}</p>
    <h3 style="color:var(--accent)">About this Gym</h3>
    <p style="white-space:pre-wrap">${safe(selectedGym.about||selectedGym.description||'No description provided yet.')}</p>
    ${selectedGym.hours?`<p>🕒 ${safe(selectedGym.hours)}</p>`:''}
    <div class="row" style="justify-content:flex-start;flex-wrap:wrap">
      <button type="button" class="button" onclick="showTab('plans')">Join Membership</button>
      <button type="button" class="button outline" onclick="showTab('products')">Shop Products</button>
      <button type="button" class="button secondary" onclick="showTab('gyms')">Explore Other Gyms</button>
    </div>`;
}
function renderGymPreviews(){
  const pb=$('gymPlansPreview'),sb=$('gymProductsPreview');
  if(!selectedGym){pb.innerHTML=cardEmpty('Choose a gym first.');sb.innerHTML=cardEmpty('Choose a gym first.');return;}
  pb.innerHTML=(catalog.plans||[]).map(p=>`<article class="card">
    <div class="tile">🎟️</div><h3>${safe(p.name)}</h3>
    <div class="price">${money(p.price)}</div>
    <p>${Number(p.days)} days of membership</p>
    <button type="button" class="button" onclick="showTab('plans')">Join Membership →</button>
  </article>`).join('')||cardEmpty('No membership plans available for this gym.');
  sb.innerHTML=(catalog.products||[]).map(p=>`<article class="card">
    <div class="tile">🛍️</div><h3>${safe(p.name)}</h3>
    <p>${safe(p.description||'Gym product')}</p><div class="price">${money(p.price)}</div>
    <p class="meta">${Number(p.stock)} in stock</p>
    <button type="button" class="button" onclick="showTab('products')">View Products →</button>
  </article>`).join('')||cardEmpty('No products available for this gym.');
}
function membershipLabel(m){
  if(m.active) return 'Active - '+(m.plan||'Membership')+(m.due_date?' (until '+m.due_date+')':'');
  if(m.status==='pending') return 'Pending approval';
  if(m.status==='expired') return 'Membership expired';
  return 'No active membership';
}
function renderMembershipStatus(){
  const text=membershipLabel(currentMembership);
  const stat=$('statMember');
  if(stat)stat.textContent=text;
  const profile=$('pmembership');
  if(profile){
    profile.textContent=text;
    profile.className='tag '+(currentMembership.active?'':currentMembership.status==='pending'?'pending':'off');
  }
  const panel=$('gymMembershipStatus');
  if(panel){
    panel.hidden=!selectedGym;
    if(selectedGym){
      panel.innerHTML=`<h3 style="margin:0 0 8px">Membership Status</h3>
        <span class="tag ${currentMembership.active?'':currentMembership.status==='pending'?'pending':'off'}">${safe(text)}</span>
        ${currentMembership.active?`<p style="margin-top:10px">Plan: <strong>${safe(currentMembership.plan||'-')}</strong></p>
          <p>Valid until: <strong>${safe(currentMembership.due_date||'-')}</strong></p>`:
          `<p style="margin-top:10px">${currentMembership.status==='pending'?'Your payment request is awaiting the gym owner’s approval.':
          currentMembership.status==='expired'?'Your previous membership has expired.':'Choose a plan to request membership.'}</p>`}`;
    }
  }
}
async function loadOverview(){
  const d=await request('/portal/overview');
  selectedGym=d.gym||null;
  currentMembership=d.membership||{active:false,status:'none'};
  $('statGym').textContent=selectedGym?.name||'Not selected';
  renderMembershipStatus();
  renderGym();
}
async function loadCatalog(){if(!selectedGym){catalog={plans:[],products:[]};$('plansGrid').innerHTML=cardEmpty('Select a gym first in Explore Gyms.');$('productsGrid').innerHTML=cardEmpty('Select a gym first in Explore Gyms.');return}catalog=await request('/portal/catalog');$('plansGrid').innerHTML=catalog.plans.map(p=>`<article class="card"><div class="tile">🎟️</div><h3>${safe(p.name)}</h3><div class="price">${money(p.price)}</div><p>${Number(p.days)} days of membership</p><span class="tag">${safe(selectedGym.name)}</span><div class="checkout"><label>Payment method</label><select class="select" id="planmethod-${p.id}"><option>GCash</option><option>Maya</option><option>Cash</option></select><label>Payment reference (optional)</label><input class="input" id="planref-${p.id}" maxlength="60" placeholder="Reference number"><button class="button" onclick="buyPlan(${Number(p.id)})">Request Membership</button></div></article>`).join('')||cardEmpty('No active membership plans for this gym.');$('productsGrid').innerHTML=catalog.products.map(p=>`<article class="card"><div class="tile">🛍️</div><h3>${safe(p.name)}</h3><p>${safe(p.description||'Gym product')}</p><div class="price">${money(p.price)}</div><span class="tag">${Number(p.stock)} in stock</span><div class="checkout"><label>Quantity</label><input class="input" type="number" min="1" max="${Math.min(10,Number(p.stock))}" value="1" id="quantity-${p.id}"><label>Payment method</label><select class="select" id="method-${p.id}"><option>GCash</option><option>Maya</option><option>Cash</option></select><label>Reference (optional)</label><input class="input" maxlength="60" id="reference-${p.id}" placeholder="Reference number"><button class="button" onclick="buyProduct(${Number(p.id)})">Place Order</button></div></article>`).join('')||cardEmpty('No products in stock for this gym.')}
async function buyPlan(id){await run(async()=>{const method=$('planmethod-'+id).value,reference=$('planref-'+id).value;await request('/membership-payments',{method:'POST',body:JSON.stringify({plan_id:id,method,reference})});await loadOverview();await showTab('payments');notify('Membership payment request submitted. Await gym owner approval.');})}
async function buyProduct(id){await run(async()=>{const quantity=Number($('quantity-'+id).value),method=$('method-'+id).value,reference=$('reference-'+id).value;await request('/portal/orders',{method:'POST',body:JSON.stringify({product_id:id,quantity,method,reference})});notify('Order submitted. The gym owner must confirm payment.');await showTab('orders')})}
async function loadOrders(){const d=await request('/portal/orders');$('ordersList').innerHTML=d.orders.map(o=>`<div class="line row"><div><strong>#${Number(o.id)} · ${safe(o.product_name)}</strong><div class="meta">Qty ${Number(o.quantity)} · ${money(o.total)} · ${safe(o.method||'')}</div></div><span class="badge">${safe(o.status)}</span></div>`).join('')||'<div class="empty">You have no product orders yet.</div>'}
async function loadPayments(){const d=await request('/portal/payments');$('paymentsList').innerHTML=d.payments.map(p=>`<div class="line row"><div><strong>${safe(p.plan)}</strong><div class="meta">${money(p.amount)} · ${safe(p.method||'')} · ${safe(p.reference||'No reference')}</div></div><span class="badge">${safe(p.status)}</span></div>`).join('')||'<div class="empty">You have no membership payment requests yet.</div>'}
// Member profile: same layout as the Gym Owner profile; authenticated member endpoints.
let memberProfile = null;
let memberAvatar = document.querySelector('meta[name="my-avatar"]')?.content || '';
function memberInitials(name){return (String(name||'M').trim().split(/\s+/).slice(0,2).map(w=>w.charAt(0).toUpperCase()).join(''))||'M';}
function photoPath(url){try{const u=new URL(url,location.origin);return u.origin===location.origin && u.pathname.includes('/storage/')?u.pathname+u.search:'';}catch(e){return '';}}
function memberPhoto(el,name,url){el.textContent=memberInitials(name);const path=photoPath(url);if(path){const img=document.createElement('img');img.src=path;img.alt='';img.onerror=()=>img.remove();el.appendChild(img);}}
function profileError(id,message){const el=$(id);el.textContent=message||'';el.hidden=!message;}
async function loadMemberProfile(){
  try {
    const data=await request('/portal/profile');memberProfile=data.user;memberAvatar=memberProfile.avatar_url||'';
    $('pname').textContent=memberProfile.name||'';$('pmail').textContent=memberProfile.email||'';
    memberPhoto($('pav'),memberProfile.name,memberAvatar);
    memberPhoto(document.querySelector('.topright .avatar'),memberProfile.name,memberAvatar);
    $('on').value=memberProfile.name||'';$('oe').value=memberProfile.email||'';
    $('op').value=memberProfile.phone||'';$('ol').value=memberProfile.location||'';$('ob').value=memberProfile.bio||'';
    $('st-login').textContent=memberProfile.last_login_at?new Date(memberProfile.last_login_at).toLocaleString('en-US',{month:'short',day:'numeric',year:'numeric'}):'Not recorded';
    $('st-since').textContent=memberProfile.created_at?new Date(memberProfile.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}):'—';
    $('st-gym').textContent=selectedGym?.name||'None selected';
    $('paccount').textContent=memberProfile.status==='active'?'Active':'Account: '+(memberProfile.status||'Unknown');
    renderMembershipStatus();
  } catch(e){profileError('pf-err',e.message);}
}
$('pf').addEventListener('submit',async e=>{
  e.preventDefault();profileError('pf-err','');$('pf-ok').hidden=true;
  const b=e.submitter;b.disabled=true;
  try{await request('/portal/profile',{method:'PUT',body:JSON.stringify({name:$('on').value.trim(),email:$('oe').value.trim(),phone:$('op').value.trim(),location:$('ol').value.trim(),bio:$('ob').value.trim()})});await loadMemberProfile();$('pf-ok').hidden=false;notify('Profile saved');}
  catch(err){profileError('pf-err',err.message);}finally{b.disabled=false;}
});
$('pw').addEventListener('submit',async e=>{
  e.preventDefault();profileError('pw-err','');$('pw-ok').hidden=true;
  if($('pw1').value!==$('pw2').value){profileError('pw-err','New passwords do not match.');return;}
  const b=e.submitter;b.disabled=true;
  try{await request('/portal/profile/password',{method:'PUT',body:JSON.stringify({current_password:$('pw0').value,password:$('pw1').value,password_confirmation:$('pw2').value})});$('pw').reset();$('pw-ok').hidden=false;notify('Password updated');}
  catch(err){profileError('pw-err',err.message);}finally{b.disabled=false;}
});
$('avf').addEventListener('change',async e=>{
  const f=e.target.files?.[0];if(!f)return;
  if(f.size>2*1024*1024){profileError('pf-err','Image must be 2 MB or smaller.');e.target.value='';return;}
  const form=new FormData();form.append('avatar',f);
  try{const r=await fetch('/api/profile/avatar',{method:'POST',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:form});const d=await r.json();if(!r.ok)throw Error(d.message||'Could not upload photo');await loadMemberProfile();notify('Photo updated');}
  catch(err){profileError('pf-err',err.message);}finally{e.target.value='';}
});
$('avrm').addEventListener('click',async()=>{
  try{const r=await fetch('/api/profile/avatar',{method:'DELETE',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}});const d=await r.json();if(!r.ok)throw Error(d.message||'Could not remove photo');await loadMemberProfile();notify('Photo removed');}
  catch(err){profileError('pf-err',err.message);}
});

/* Messages: same layout and interactions as the Gym Owner Dashboard. */
let msKey=null,msFilter='all',chatContacts=[],chatMessages=[],myChatId=null;
let chatThreadRequest=0,chatSending=false;
const msTime=ts=>ts?new Date(ts).toLocaleString([],{month:'short',day:'numeric',hour:'numeric',minute:'2-digit'}):'';
function chatAvatar(name){const initials=String(name||'?').trim().split(/\s+/).slice(0,2).map(x=>x[0]).join('').toUpperCase();return `<span class="av">${safe(initials)}</span>`;}
function renderMailBadge(){const n=chatContacts.reduce((sum,c)=>sum+Number(c.unread||0),0);const b=$('mail-n');if(b){b.hidden=!n;b.textContent=n>99?'99+':n;}}
async function loadChatContacts(){
  try{
    const j=await chatApi('/api/chat/contacts');
    chatContacts=(j.contacts||[]).filter(c=>c.role==='owner');
    if(msKey&&!chatContacts.some(c=>String(c.id)===String(msKey))){msKey=null;chatMessages=[];myChatId=null;chatThreadRequest++;}
    renderMailBadge();renderMail();renderThread();
  }catch(e){if(!$('page-messages').classList.contains('hidden'))notify(e.message,true);}
}
async function chatApi(path,method='GET',body=null){
  const r=await fetch(path,{method,credentials:'same-origin',headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:body?JSON.stringify(body):null});
  const d=await r.json().catch(()=>({}));
  if(!r.ok){throw new Error(d.errors?Object.values(d.errors).flat().join(' '):d.message||`Request failed (${r.status})`);}return d;
}
function renderMail(){
  $('mstabs').innerHTML=[['all','All'],['owner','Gym owners']].map(([k,l])=>`<button type="button" class="tab ${msFilter===k?'on':''}" data-mstab="${k}">${l}</button>`).join('');
  const q=$('msq').value.trim().toLowerCase();
  const list=chatContacts.filter(p=>String(p.name||'').toLowerCase().includes(q)).filter(p=>msFilter==='all'||p.role===msFilter);
  $('mslist').innerHTML=list.map(p=>`<button type="button" class="cv ${String(p.id)===String(msKey)?'on':''}" data-conv="${Number(p.id)}">
    ${chatAvatar(p.name)}<div><b>${safe(p.name)}</b><span class="tag off">Gym owner</span><small>${safe(p.last_message||'Start a conversation')}</small></div>
    ${Number(p.unread)>0?`<span class="cnt">${Math.min(99,Number(p.unread))}${Number(p.unread)>99?'+':''}</span>`:''}</button>`).join('')||'<p class="ms-empty">No contacts found. Join a gym to message its owner.</p>';
}
function renderThread(){
  const p=chatContacts.find(x=>String(x.id)===String(msKey)),box=$('msbody');
  $('msf').hidden=!p;
  if(!p){$('mshead').textContent='Select a conversation';box.innerHTML='';return;}
  $('mshead').textContent=p.name+' · Gym owner';
  const nearBottom=box.scrollHeight-box.scrollTop-box.clientHeight<85;
  box.innerHTML=chatMessages.map(m=>`<div class="bub ${Number(m.sender_id)===Number(myChatId)?'mine':''}">${safe(m.body)}<small>${safe(msTime(m.created_at))}</small></div>`).join('')||'<p class="ms-empty">No messages yet. Say hello!</p>';
  if(nearBottom||box.dataset.firstLoad==='1')box.scrollTop=box.scrollHeight;
  box.dataset.firstLoad='0';
}
async function loadChatThread(){
  if(!msKey)return;
  const key=String(msKey),seq=++chatThreadRequest;
  try{const j=await chatApi('/api/chat/'+encodeURIComponent(key));if(seq!==chatThreadRequest||key!==String(msKey))return;chatMessages=j.messages||[];myChatId=j.my_id;renderThread();}
  catch(e){if(seq===chatThreadRequest)notify(e.message,true);}
}
async function openConv(key){msKey=String(key);chatMessages=[];chatThreadRequest++;$('msbody').dataset.firstLoad='1';renderMail();renderThread();await loadChatThread();await loadChatContacts();$('msb').focus();}
document.addEventListener('click',e=>{const c=e.target.closest('[data-conv]');if(c){openConv(c.dataset.conv);return;}const t=e.target.closest('[data-mstab]');if(t){msFilter=t.dataset.mstab;renderMail();}});
$('msq').addEventListener('input',renderMail);
$('msf').addEventListener('submit',async e=>{
  e.preventDefault();const body=$('msb').value.trim(),key=msKey;if(!body||!key||chatSending)return;
  chatSending=true;const btn=$('msf').querySelector('button[type="submit"]');btn.disabled=true;
  try{await chatApi('/api/chat/'+encodeURIComponent(key),'POST',{body});$('msb').value='';if(String(msKey)===String(key)){await loadChatThread();$('msbody').scrollTop=$('msbody').scrollHeight;}await loadChatContacts();}
  catch(err){notify(err.message,true);}finally{chatSending=false;btn.disabled=false;}
});
$('msb').addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();$('msf').requestSubmit();}});

window.addEventListener('focus',()=>{if(!document.hidden)run(loadOverview)});
$('gymSearch').addEventListener('input',renderGyms);document.querySelectorAll('.nav').forEach(b=>b.addEventListener('click',()=>run(()=>showTab(b.dataset.tab))));run(async()=>{await loadOverview();await loadGyms()});
</script>
</body>
</html>
