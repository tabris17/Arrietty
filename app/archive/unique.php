<?php
require '../inc/common.php';
require '../inc/category.php';
require '../inc/archive.php';
$pdo = db_open();
$stm = $pdo->prepare("SELECT COUNT(*) FROM archive WHERE name=?");
$stm->bindValue(1, $_POST['name']);
if ($stm->execute()) {
	list($count) = $stm->fetch();
	if ($count) {
		die('true');
	}
}
echo 'false';