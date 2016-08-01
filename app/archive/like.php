<?php
require '../inc/common.php';
require '../inc/category.php';
require '../inc/archive.php';
$pdo = db_open();
$stm = $pdo->prepare("INSERT INTO favorite (archive_id) SELECT archive.id FROM archive WHERE archive.id=?");
$stm->bindValue(1, $_POST['id']);
if ($stm->execute()) {
	die('OK');
}
list (, , $error) = $stm->errorInfo();
echo $error;