<?php
require_once __DIR__ . "/includes/auth.php";auth_start_session();
if(empty($_SESSION["api_csrf"]))$_SESSION["api_csrf"]=bin2hex(random_bytes(32));
$csrf=$_SESSION["api_csrf"];
function ah($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,"UTF-8");}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>StudentHub | API Explorer</title><link rel="stylesheet" href="css/style.css"></head><body>
<header class="site-header"><div class="header-inner"><a class="site-title" href="index.html">StudentHub</a><nav class="main-nav"><a href="index.html">Home</a><a href="login.php">Login</a><a href="admin.php">Admin</a></nav></div></header>
<main class="page-container"><section class="intro-panel"><h1>StudentHub JSON API</h1><p>Search students and events using Fetch API without a full page reload.</p></section>
<section class="section-block"><h2>Students</h2><label for="api-student-search">Search students</label><input id="api-student-search" type="search" placeholder="Name, email or course"><p id="api-student-status" role="status">Loading…</p><div id="api-students" class="data-grid"></div></section>
<section class="section-block"><h2>Events</h2><label for="api-event-search">Search events</label><input id="api-event-search" type="search" placeholder="Title, category or location"><p id="api-event-status" role="status">Loading…</p><div id="api-events" class="data-grid"></div></section>
</main>
<script>
const csrfToken = <?=json_encode($csrf)?>;
function esc(v){return String(v??"").replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));}
async function loadApi(path, query, listId, statusId, kind){
 const status=document.getElementById(statusId), list=document.getElementById(listId);
 status.textContent="Loading…";
 try{
  const response=await fetch(path+"?q="+encodeURIComponent(query),{headers:{Accept:"application/json"},cache:"no-store"});
  const payload=await response.json();
  if(!response.ok||!payload.success)throw new Error(payload.message||"Request failed.");
  const items=payload.data||[];
  list.innerHTML=items.length?items.map(item=>kind==="student"
   ?`<article class="data-card"><h3>${esc(item.full_name)}</h3><p>${esc(item.email)}</p><p>${esc(item.course)} · Year ${esc(item.study_year)}</p></article>`
   :`<article class="data-card"><h3>${esc(item.title)}</h3><p>${esc(item.category)} · ${esc(item.event_date)}</p><p>${esc(item.location)}</p><p>${esc(item.description)}</p></article>`).join(""):"<p>No records found.</p>";
  status.textContent=`Loaded ${items.length} record(s).`;
 }catch(error){list.replaceChildren();status.textContent="Could not load data: "+error.message;}
}
let studentTimer,eventTimer;
document.getElementById("api-student-search").addEventListener("input",e=>{clearTimeout(studentTimer);studentTimer=setTimeout(()=>loadApi("api/students.php",e.target.value,"api-students","api-student-status","student"),250);});
document.getElementById("api-event-search").addEventListener("input",e=>{clearTimeout(eventTimer);eventTimer=setTimeout(()=>loadApi("api/events.php",e.target.value,"api-events","api-event-status","event"),250);});
loadApi("api/students.php","","api-students","api-student-status","student");
loadApi("api/events.php","","api-events","api-event-status","event");
</script></body></html>