<?php
declare(strict_types=1);
require_once dirname(__DIR__,2).'/config/app.php';$admin=requireAdmin();$pdo=getDatabaseConnection();
if($_SERVER['REQUEST_METHOD']==='GET'){$stmt=$pdo->query('SELECT * FROM test_cases ORDER BY id');jsonResponse(['success'=>true,'items'=>$stmt->fetchAll()]);}
if($_SERVER['REQUEST_METHOD']!=='POST')jsonResponse(['success'=>false,'message'=>'Method not allowed.'],405);requireCsrf();
$action=(string)($_POST['action']??'');$id=(int)($_POST['id']??0);
if($action==='delete'&&$id>0){$stmt=$pdo->prepare('DELETE FROM test_cases WHERE id=?');$stmt->execute([$id]);}
elseif(in_array($action,['create','update'],true)){
$name=postString('test_name',160);$description=postString('description',2000);$source=(string)($_POST['source_code']??'');$expected=postString('expected_behavior',2000);$category=postString('category',60)?:'General';
if($name===''||trim($source)===''||$expected==='')jsonResponse(['success'=>false,'message'=>'Name, source code and expected behavior are required.'],422);
if($action==='create'){ $stmt=$pdo->prepare('INSERT INTO test_cases(test_name,description,source_code,expected_behavior,category,created_by,is_active)VALUES(?,?,?,?,?,?,1)');$stmt->execute([$name,$description,$source,$expected,$category,$admin['id']]);}
else{if($id<=0)jsonResponse(['success'=>false,'message'=>'Test case ID is required.'],422);$stmt=$pdo->prepare('UPDATE test_cases SET test_name=?,description=?,source_code=?,expected_behavior=?,category=? WHERE id=?');$stmt->execute([$name,$description,$source,$expected,$category,$id]);}
}else jsonResponse(['success'=>false,'message'=>'Invalid action.'],422);
logActivity($pdo,(int)$admin['id'],'manage_test_cases','Updated test case data.');
jsonResponse(['success'=>true,'message'=>'Test case updated.']);
