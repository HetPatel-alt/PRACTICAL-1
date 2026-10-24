<?php
require_once __DIR__ . "/includes/auth.php";
auth_start_session();
$admin = require_role("admin");
require_once __DIR__ . "/db.php";
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); }
if (empty($_SESSION["crud_csrf"])) $_SESSION["crud_csrf"] = bin2hex(random_bytes(32));
$token = $_SESSION["crud_csrf"]; $message = ""; $error = false;
$courses = ["Information Technology","Computer Science","Computer Engineering","Other"];
$genders = ["Female","Male","Other","Prefer not to say"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 if (!hash_equals($token, (string)($_POST["csrf_token"] ?? ""))) { $message="Session token invalid. Refresh and retry."; $error=true; }
 else {
  $action=(string)($_POST["action"]??""); $id=filter_var($_POST["student_id"]??null,FILTER_VALIDATE_INT);
  try {
   if ($action==="update" && $id) {
    $name=trim((string)($_POST["full_name"]??"")); $email=trim((string)($_POST["email"]??""));
    $mobile=trim((string)($_POST["mobile"]??"")); $course=(string)($_POST["course"]??"");
    $year=(string)($_POST["study_year"]??""); $gender=(string)($_POST["gender"]??"");
    if(!preg_match("/^[A-Za-z][A-Za-z .'-]{1,99}$/",$name)) throw new RuntimeException("Enter a valid name.");
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new RuntimeException("Enter a valid email.");
    if(!preg_match("/^(?:\\+91[- ]?)?[6-9][0-9]{9}$/",$mobile)) throw new RuntimeException("Enter a valid Indian mobile number.");
    if(!in_array($course,$courses,true)||!in_array($year,["1","2","3","4"],true)||!in_array($gender,$genders,true)) throw new RuntimeException("Select valid course, year and gender.");
    $s=$pdo->prepare("UPDATE students SET full_name=:n,email=:e,mobile=:m,course=:c,study_year=:y,gender=:g WHERE student_id=:id AND role='student'");
    $s->execute([":n"=>$name,":e"=>$email,":m"=>$mobile,":c"=>$course,":y"=>(int)$year,":g"=>$gender,":id"=>$id]);
    $message=$s->rowCount()?"Student updated.":"No changes made or student not found.";
    $a=$pdo->prepare("INSERT INTO audit_logs (user_id, action, details) VALUES (:u,'student_update',:d)");
    $a->execute([":u"=>$admin["id"],":d"=>"Updated student ID ".$id]);
   } elseif($action==="delete" && $id) {
    $pdo->beginTransaction();
    $check=$pdo->prepare("SELECT student_id FROM students WHERE student_id=:id AND role='student'");
    $check->execute([":id"=>$id]);
    if(!$check->fetch()){ $pdo->rollBack(); throw new RuntimeException("Student not found or account is protected."); }
    $pdo->prepare("DELETE FROM registrations WHERE student_id=:id")->execute([":id"=>$id]);
    $pdo->prepare("DELETE FROM students WHERE student_id=:id AND role='student'")->execute([":id"=>$id]);
    $pdo->prepare("INSERT INTO audit_logs (user_id,action,details) VALUES (:u,'student_delete',:d)")->execute([":u"=>$admin["id"],":d"=>"Deleted student ID ".$id]);
    $pdo->commit(); $message="Student and linked registration deleted.";
   } else { throw new RuntimeException("Invalid action."); }
  } catch (RuntimeException $ex) { if($pdo->inTransaction())$pdo->rollBack(); $message=$ex->getMessage(); $error=true; }
    catch (PDOException $ex) { if($pdo->inTransaction())$pdo->rollBack(); error_log($ex->getMessage()); $message=$ex->getCode()==="23000"?"Email is already in use.":"Database operation failed."; $error=true; }
 }
}
$q=trim((string)($_GET["q"]??"")); $courseFilter=(string)($_GET["course"]??"");
if(!in_array($courseFilter,$courses,true))$courseFilter="";
$where=[];$params=[];
if($q!==""){$where[]="(full_name LIKE :a OR email LIKE :b OR course LIKE :c)";$like="%".$q."%";$params=[":a"=>$like,":b"=>$like,":c"=>$like];}
if($courseFilter!==""){$where[]="course=:course";$params[":course"]=$courseFilter;}
$ws=$where?" WHERE ".implode(" AND ",$where):"";
if(($_GET["export"]??"")==="csv"){
 $s=$pdo->prepare("SELECT student_id,full_name,email,mobile,course,study_year,gender,role,created_at FROM students".$ws." ORDER BY full_name");
 $s->execute($params); header("Content-Type: text/csv; charset=utf-8");header('Content-Disposition: attachment; filename="studenthub_students.csv"');
 $out=fopen("php://output","w");fputcsv($out,["ID","Name","Email","Mobile","Course","Year","Gender","Role","Created"]);while($r=$s->fetch(PDO::FETCH_NUM))fputcsv($out,$r);fclose($out);exit;
}
$c=$pdo->prepare("SELECT COUNT(*) FROM students".$ws);$c->execute($params);$total=(int)$c->fetchColumn();
$page=max(1,(int)($_GET["page"]??1));$pages=max(1,(int)ceil($total/10));$page=min($page,$pages);
$s=$pdo->prepare("SELECT student_id,full_name,email,mobile,course,study_year,gender,role FROM students".$ws." ORDER BY full_name LIMIT :lim OFFSET :off");
foreach($params as $k=>$v)$s->bindValue($k,$v,PDO::PARAM_STR);$s->bindValue(":lim",10,PDO::PARAM_INT);$s->bindValue(":off",($page-1)*10,PDO::PARAM_INT);$s->execute();$rows=$s->fetchAll();
function page_url($p){$x=$_GET;$x["page"]=$p;unset($x["export"]);return "manage_students.php?".http_build_query($x);}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Manage Students | StudentHub</title><link rel="stylesheet" href="css/style.css"></head><body>
<header class="site-header"><div class="header-inner"><a class="site-title" href="admin.php">StudentHub Admin</a><nav class="main-nav"><a href="admin.php">Dashboard</a><a href="manage_students.php">Students</a><a href="manage_events.php">Events</a><a href="logout.php">Logout</a></nav></div></header>
<main class="page-container"><section class="intro-panel"><h1>Student Management</h1><p>Search, filter, edit, delete and export student records.</p></section>
<?php if($message!==""):?><p class="server-message <?= $error?"server-error":"server-success" ?>" role="status"><?=h($message)?></p><?php endif;?>
<section class="section-block"><form method="get" class="crud-filter"><label>Search name/email/course<input name="q" value="<?=h($q)?>" type="search"></label><label>Course<select name="course"><option value="">All courses</option><?php foreach($courses as $co):?><option <?= $courseFilter===$co?"selected":"" ?> value="<?=h($co)?>"><?=h($co)?></option><?php endforeach;?></select></label><button class="primary-button">Search</button><a class="secondary-button" href="manage_students.php">Clear</a><a class="secondary-button" href="<?=h("manage_students.php?".http_build_query(array_merge($_GET,["export"=>"csv"])))?>">Export CSV</a></form>
<p><?=count($rows)?> of <?=$total?> records · Page <?=$page?> of <?=$pages?></p><div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Course</th><th>Year</th><th>Gender</th><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=h($r["student_id"])?></td><td><?=h($r["full_name"])?></td><td><?=h($r["email"])?></td><td><?=h($r["mobile"])?></td><td><?=h($r["course"])?></td><td><?=h($r["study_year"])?></td><td><?=h($r["gender"])?></td><td>
<?php if($r["role"]==="student"):?><details><summary>Edit</summary><form method="post" class="crud-edit-form"><input type="hidden" name="csrf_token" value="<?=h($token)?>"><input type="hidden" name="action" value="update"><input type="hidden" name="student_id" value="<?=h($r["student_id"])?>">
<label>Name<input name="full_name" value="<?=h($r["full_name"])?>" required></label><label>Email<input type="email" name="email" value="<?=h($r["email"])?>" required></label><label>Mobile<input name="mobile" value="<?=h($r["mobile"])?>" required></label>
<label>Course<select name="course"><?php foreach($courses as $co):?><option value="<?=h($co)?>" <?=$r["course"]===$co?"selected":""?>><?=h($co)?></option><?php endforeach;?></select></label>
<label>Year<select name="study_year"><?php for($y=1;$y<=4;$y++):?><option value="<?=$y?>" <?=((int)$r["study_year"]===$y?"selected":"")?>><?=$y?></option><?php endfor;?></select></label>
<label>Gender<select name="gender"><?php foreach($genders as $g):?><option value="<?=h($g)?>" <?=$r["gender"]===$g?"selected":""?>><?=h($g)?></option><?php endforeach;?></select></label><button class="primary-button">Save</button></form></details>
<form method="post" onsubmit="return confirm('Delete this student and linked registrations?')"><input type="hidden" name="csrf_token" value="<?=h($token)?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="student_id" value="<?=h($r["student_id"])?>"><button class="danger-button">Delete</button></form><?php else:?>Admin protected<?php endif;?></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="8">No matching records.</td></tr><?php endif;?></tbody></table></div><nav class="pagination"><?php if($page>1):?><a href="<?=h(page_url($page-1))?>">Previous</a><?php endif;?> <span>Page <?=$page?> of <?=$pages?></span> <?php if($page<$pages):?><a href="<?=h(page_url($page+1))?>">Next</a><?php endif;?></nav></section></main></body></html>