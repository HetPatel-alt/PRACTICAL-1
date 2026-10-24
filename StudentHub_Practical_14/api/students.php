<?php
require_once __DIR__ . "/../includes/auth.php"; auth_start_session(); require_once __DIR__ . "/../db.php";
header("Content-Type: application/json; charset=utf-8"); header("Cache-Control: no-store");
function response($code,$data){http_response_code($code);echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;}
$method=$_SERVER["REQUEST_METHOD"];
if($method==="GET"){
 $q=trim((string)($_GET["q"]??""));$s=$pdo->prepare("SELECT student_id,full_name,email,course,study_year,gender FROM students WHERE role='student' AND (full_name LIKE :q OR email LIKE :q OR course LIKE :q) ORDER BY full_name LIMIT 100");
 $s->execute([":q"=>"%".$q."%"]);response(200,["success"=>true,"data"=>$s->fetchAll()]);
}
$user=require_role("admin");
if(!in_array($method,["POST","PUT","DELETE"],true))response(405,["success"=>false,"message"=>"Method not allowed"]);
$raw=file_get_contents("php://input");$input=$method==="POST"?$_POST:json_decode($raw,true);
if(!is_array($input))$input=[];
if(!hash_equals((string)($_SESSION["api_csrf"]??""),(string)($input["csrf_token"]??"")))response(403,["success"=>false,"message"=>"Invalid CSRF token"]);
$id=filter_var($input["student_id"]??$_GET["student_id"]??null,FILTER_VALIDATE_INT);
try{
 if($method==="POST"){
  $name=trim((string)($input["full_name"]??""));$email=trim((string)($input["email"]??""));$course=trim((string)($input["course"]??""));$year=filter_var($input["study_year"]??null,FILTER_VALIDATE_INT);
  $mobile=trim((string)($input["mobile"]??""));$gender=(string)($input["gender"]??"");$password=(string)($input["password"]??"");
  if($name===""||!filter_var($email,FILTER_VALIDATE_EMAIL)||$course===""||!in_array($year,[1,2,3,4],true)||!preg_match("/^(?:\\+91[- ]?)?[6-9][0-9]{9}$/",$mobile)||!in_array($gender,["Female","Male","Other","Prefer not to say"],true)||strlen($password)<8)response(422,["success"=>false,"message"=>"Valid name, email, mobile, course, year, gender and password (minimum 8 characters) are required."]);
  $s=$pdo->prepare("INSERT INTO students(full_name,email,mobile,course,study_year,gender,password_hash,role) VALUES(:n,:e,:m,:c,:y,:g,:p,'student')");
  $s->execute([":n"=>$name,":e"=>$email,":m"=>$mobile,":c"=>$course,":y"=>$year,":g"=>$gender,":p"=>password_hash($password,PASSWORD_DEFAULT)]);
  response(201,["success"=>true,"message"=>"Student created.","student_id"=>(int)$pdo->lastInsertId()]);
 }
 if(!$id)response(400,["success"=>false,"message"=>"A valid student_id is required."]);
 if($method==="DELETE"){
  $pdo->beginTransaction();$pdo->prepare("DELETE FROM registrations WHERE student_id=:id")->execute([":id"=>$id]);
  $s=$pdo->prepare("DELETE FROM students WHERE student_id=:id AND role='student'");$s->execute([":id"=>$id]);
  if(!$s->rowCount()){$pdo->rollBack();response(404,["success"=>false,"message"=>"Student not found or protected."]);}
  $pdo->commit();response(200,["success"=>true,"message"=>"Student deleted."]);
 }
 if($method==="PUT"){
  $name=trim((string)($input["full_name"]??""));$course=trim((string)($input["course"]??""));$year=filter_var($input["study_year"]??null,FILTER_VALIDATE_INT);
  if($name===""||$course===""||!in_array($year,[1,2,3,4],true))response(422,["success"=>false,"message"=>"Valid name, course and year are required."]);
  $s=$pdo->prepare("UPDATE students SET full_name=:n,course=:c,study_year=:y WHERE student_id=:id AND role='student'");$s->execute([":n"=>$name,":c"=>$course,":y"=>$year,":id"=>$id]);
  response(200,["success"=>true,"message"=>"Student updated."]);
 }
}catch(PDOException $e){error_log($e->getMessage());response(500,["success"=>false,"message"=>"Database operation failed."]);}