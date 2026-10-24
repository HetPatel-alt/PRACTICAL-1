<?php
require_once __DIR__ . "/../includes/auth.php"; auth_start_session(); require_once __DIR__ . "/../db.php";
header("Content-Type: application/json; charset=utf-8");header("Cache-Control: no-store");
function out($c,$d){http_response_code($c);echo json_encode($d,JSON_UNESCAPED_UNICODE);exit;}
$m=$_SERVER["REQUEST_METHOD"];
if($m==="GET"){
 $q=trim((string)($_GET["q"]??""));$s=$pdo->prepare("SELECT event_id,title,category,event_date,location,description,organizer,poster_filename FROM events WHERE title LIKE :q OR category LIKE :q OR location LIKE :q ORDER BY event_date LIMIT 100");$s->execute([":q"=>"%".$q."%"]);out(200,["success"=>true,"data"=>$s->fetchAll()]);
}
$user=require_role("admin");
if(!in_array($m,["POST","PUT","DELETE"],true))out(405,["success"=>false,"message"=>"Method not allowed"]);
$raw=file_get_contents("php://input");$data=$m==="POST"?$_POST:json_decode($raw,true);if(!is_array($data))$data=[];
if(!hash_equals((string)($_SESSION["api_csrf"]??""),(string)($data["csrf_token"]??"")))out(403,["success"=>false,"message"=>"Invalid CSRF token"]);
$id=filter_var($data["event_id"]??$_GET["event_id"]??null,FILTER_VALIDATE_INT);
try{
 if($m==="DELETE"){if(!$id)out(400,["success"=>false,"message"=>"event_id required"]);$s=$pdo->prepare("DELETE FROM events WHERE event_id=:id");$s->execute([":id"=>$id]);out($s->rowCount()?200:404,["success"=>(bool)$s->rowCount(),"message"=>$s->rowCount()?"Event deleted.":"Event not found."]);}
 if($m==="POST"||$m==="PUT"){
  $title=trim((string)($data["title"]??""));$category=trim((string)($data["category"]??""));$date=(string)($data["event_date"]??"");$location=trim((string)($data["location"]??""));$description=trim((string)($data["description"]??""));$organizer=trim((string)($data["organizer"]??""));
  if($title===""||$category===""||$location===""||$description===""||$organizer==="")out(422,["success"=>false,"message"=>"All event fields are required."]);
  $d=DateTime::createFromFormat("!Y-m-d",$date);if(!$d||$d->format("Y-m-d")!==$date)out(422,["success"=>false,"message"=>"Valid event_date required (YYYY-MM-DD)."]);
  if($m==="POST"){$s=$pdo->prepare("INSERT INTO events(title,category,event_date,location,description,organizer) VALUES(:t,:c,:d,:l,:x,:o)");$s->execute([":t"=>$title,":c"=>$category,":d"=>$date,":l"=>$location,":x"=>$description,":o"=>$organizer]);out(201,["success"=>true,"message"=>"Event created.","event_id"=>(int)$pdo->lastInsertId()]);}
  if(!$id)out(400,["success"=>false,"message"=>"event_id required"]);$s=$pdo->prepare("UPDATE events SET title=:t,category=:c,event_date=:d,location=:l,description=:x,organizer=:o WHERE event_id=:id");$s->execute([":t"=>$title,":c"=>$category,":d"=>$date,":l"=>$location,":x"=>$description,":o"=>$organizer,":id"=>$id]);out(200,["success"=>true,"message"=>"Event updated."]);
 }
}catch(PDOException $e){error_log($e->getMessage());out(500,["success"=>false,"message"=>"Database operation failed."]);}
