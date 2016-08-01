<?php
require '../inc/common.php';
require '../inc/category.php';
require '../inc/archive.php';
$pdo = db_open();
$stm = $pdo->prepare("DELETE FROM favorite WHERE archive_id=?");
$stm->bindValue(1, $_POST['id']);
if ($stm->execute()) {
	die('OK');
}
list (, , $error) = $stm->errorInfo();
echo $error;