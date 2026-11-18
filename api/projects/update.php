<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success'=>false,'message'=>'Method not allowed.'],405);
requireCsrf();
$id=(int)($_POST['id']??0);$name=postString('project_name',160);$description=postString('description',2000);
if($id<=0||$name==='')jsonResponse(['success'=>false,'message'=>'Project ID and name are required.'],422);
$pdo=getDatabaseConnection();$stmt=$pdo->prepare('UPDATE projects SET project_name=?,description=? WHERE id=? AND user_id=?');$stmt->execute([$name,$description,$id,$user['id']]);
if($stmt->rowCount()===0)jsonResponse(['success'=>false,'message'=>'Project not found or access denied.'],404);
logActivity($pdo,(int)$user['id'],'update_project','Updated project #'.$id);jsonResponse(['success'=>true,'message'=>'Project updated.']);
