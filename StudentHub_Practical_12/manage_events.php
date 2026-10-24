<?php
require_once __DIR__ . "/includes/auth.php"; auth_start_session(); $admin=require_role("admin"); require_once __DIR__ . "/db.php";
function eh($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,"UTF-8");}
if(empty($_SESSION["event_csrf"]))$_SESSION["event_csrf"]=bin2hex(random_bytes(32));$token=$_SESSION["event_csrf"];
$msg="";$bad=false;$edit=null;
if($_SERVER["REQUEST_METHOD"]==="POST"){
 if(!hash_equals($token,(string)($_POST["csrf_token"]??""))){$msg="Invalid session token."; $bad=true;}
 else{
  $action=(string)($_POST["action"]??"");$id=filter_var($_POST["event_id"]??null,FILTER_VALIDATE_INT);
  try{
   if($action==="delete"&&$id){$s=$pdo->prepare("DELETE FROM events WHERE event_id=:id");$s->execute([":id"=>$id]);$msg="Event deleted.";}
   elseif(in_array($action,["create","update"],true)){
    $title=trim((string)($_POST["title"]??""));$category=trim((string)($_POST["category"]??""));$date=(string)($_POST["event_date"]??"");$location=trim((string)($_POST["location"]??""));$desc=trim((string)($_POST["description"]??""));$organizer=trim((string)($_POST["organizer"]??""));$poster=null;
    if($title===""||strlen($title)>150||$category===""||strlen($category)>60||$location===""||strlen($location)>150||$organizer===""||strlen($organizer)>120||$desc==="")throw new RuntimeException("Complete all event fields within the allowed lengths.");
    $dt=DateTime::createFromFormat("!Y-m-d",$date);if(!$dt||$dt->format("Y-m-d")!==$date)throw new RuntimeException("Choose a valid event date.");
    if(isset($_FILES["poster"])&&$_FILES["poster"]["error"]!==UPLOAD_ERR_NO_FILE){
     if($_FILES["poster"]["error"]!==UPLOAD_ERR_OK)throw new RuntimeException("Poster upload failed.");
     if($_FILES["poster"]["size"]>2*1024*1024)throw new RuntimeException("Poster must be 2 MB or smaller.");
     $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($_FILES["poster"]["tmp_name"]);$allowed=["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp"];
     if(!isset($allowed[$mime]))throw new RuntimeException("Poster must be JPG, PNG, or WebP.");
     $poster="event_".bin2hex(random_bytes(12)).".".$allowed[$mime];
     if(!move_uploaded_file($_FILES["poster"]["tmp_name"],__DIR__."/uploads/event_posters/".$poster))throw new RuntimeException("Could not save poster. Check upload directory permissions.");
    }
    if($action==="create"){
     $s=$pdo->prepare("INSERT INTO events(title,category,event_date,location,description,organizer,poster_filename) VALUES(:t,:c,:d,:l,:x,:o,:p)");
     $s->execute([":t"=>$title,":c"=>$category,":d"=>$date,":l"=>$location,":x"=>$desc,":o"=>$organizer,":p"=>$poster]);
     $eventId=(int)$pdo->lastInsertId();$msg="Event created.";
    }else{
     if(!$id)throw new RuntimeException("Invalid event ID.");
     if($poster!==null){
      $s=$pdo->prepare("UPDATE events SET title=:t,category=:c,event_date=:d,location=:l,description=:x,organizer=:o,poster_filename=:p WHERE event_id=:id");
      $s->execute([":t"=>$title,":c"=>$category,":d"=>$date,":l"=>$location,":x"=>$desc,":o"=>$organizer,":p"=>$poster,":id"=>$id]);
     }else{
      $s=$pdo->prepare("UPDATE events SET title=:t,category=:c,event_date=:d,location=:l,description=:x,organizer=:o WHERE event_id=:id");
      $s->execute([":t"=>$title,":c"=>$category,":d"=>$date,":l"=>$location,":x"=>$desc,":o"=>$organizer,":id"=>$id]);
     }$eventId=$id;$msg="Event updated.";
    }
    $a=$pdo->prepare("INSERT INTO audit_logs(user_id,action,details) VALUES(:u,:a,:d)");
    $a->execute([":u"=>$admin["id"],":a"=>"event_".$action,":d"=>"Event ID ".$eventId]);
   }else throw new RuntimeException("Invalid action.");
  }catch(RuntimeException $ex){$msg=$ex->getMessage();$bad=true;}
   catch(PDOException $ex){error_log($ex->getMessage());$msg="Database operation failed. Confirm the Practical 12 schema migration was applied.";$bad=true;}
 }
}
$editId=(int)($_GET["edit"]??0);if($editId){$s=$pdo->prepare("SELECT * FROM events WHERE event_id=:id");$s->execute([":id"=>$editId]);$edit=$s->fetch()?:null;}
$q=trim((string)($_GET["q"]??""));$s=$pdo->prepare("SELECT * FROM events WHERE title LIKE :q OR category LIKE :q OR location LIKE :q ORDER BY event_date DESC");$s->execute([":q"=>"%".$q."%"]);$events=$s->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Manage Events | StudentHub</title><link rel="stylesheet" href="css/style.css"></head><body>
<header class="site-header"><div class="header-inner"><a class="site-title" href="admin.php">StudentHub Admin</a><nav class="main-nav"><a href="admin.php">Dashboard</a><a href="manage_students.php">Students</a><a href="manage_events.php">Events</a><a href="logout.php">Logout</a></nav></div></header>
<main class="page-container"><section class="intro-panel"><h1>Event Management</h1><p>Create, edit, delete and search events. Posters: JPG, PNG or WebP, maximum 2 MB.</p></section>
<?php if($msg!==""):?><p class="server-message <?=$bad?"server-error":"server-success"?>" role="status"><?=eh($msg)?></p><?php endif;?>
<section class="section-block"><h2><?=$edit?"Edit Event":"Add Event"?></h2><form method="post" enctype="multipart/form-data" class="crud-edit-form"><input type="hidden" name="csrf_token" value="<?=eh($token)?>"><input type="hidden" name="action" value="<?=$edit?"update":"create"?>"><?php if($edit):?><input type="hidden" name="event_id" value="<?=eh($edit["event_id"])?>"><?php endif;?>
<label>Title<input name="title" maxlength="150" required value="<?=eh($edit["title"]??"")?>"></label><label>Category<input name="category" maxlength="60" required value="<?=eh($edit["category"]??"")?>"></label><label>Date<input type="date" name="event_date" required value="<?=eh($edit["event_date"]??"")?>"></label><label>Location<input name="location" maxlength="150" required value="<?=eh($edit["location"]??"")?>"></label><label>Organizer<input name="organizer" maxlength="120" required value="<?=eh($edit["organizer"]??"")?>"></label><label>Description<textarea name="description" required><?=eh($edit["description"]??"")?></textarea></label><label>Poster (JPG/PNG/WebP, max 2 MB)<input type="file" name="poster" accept="image/jpeg,image/png,image/webp"></label><button class="primary-button"><?=$edit?"Save Event":"Create Event"?></button><?php if($edit):?><a href="manage_events.php">Cancel</a><?php endif;?></form></section>
<section class="section-block"><h2>Events</h2><form method="get"><label>Search title/category/location <input type="search" name="q" value="<?=eh($q)?>"></label><button class="primary-button">Search</button></form><div class="table-wrap"><table><thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Location</th><th>Poster</th><th>Actions</th></tr></thead><tbody><?php foreach($events as $e):?><tr><td><?=eh($e["event_id"])?></td><td><?=eh($e["title"])?></td><td><?=eh($e["category"])?></td><td><?=eh($e["event_date"])?></td><td><?=eh($e["location"])?></td><td><?php if(!empty($e["poster_filename"])):?><img width="70" src="<?=eh("uploads/event_posters/".$e["poster_filename"])?>" alt="Event poster"><?php else:?>None<?php endif;?></td><td><a href="manage_events.php?edit=<?=eh($e["event_id"])?>">Edit</a><form method="post" onsubmit="return confirm('Delete this event?')"><input type="hidden" name="csrf_token" value="<?=eh($token)?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="event_id" value="<?=eh($e["event_id"])?>"><button class="danger-button">Delete</button></form></td></tr><?php endforeach;?><?php if(!$events):?><tr><td colspan="7">No events found.</td></tr><?php endif;?></tbody></table></div></section></main></body></html>