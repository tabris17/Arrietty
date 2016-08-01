<script type="text/javascript">
<?php
require '../inc/common.php';
require '../inc/category.php';
require '../inc/archive.php';
$pdo = db_open();

$id = request_post('id' , 'int');
if ($id === false) {
	echo 'parent.failure("参数错误");';
	goto END;
}

$stm1 = $pdo->prepare('DELETE FROM `assn_category_archive` WHERE `archive_id`=?');
$stm1->bindValue(1, $id);
$stm2 = $pdo->prepare('DELETE FROM `archive` WHERE `id`=?');
$stm2->bindValue(1, $id);


$pdo->beginTransaction();
if (false === $stm1->execute()) {
	list (, , $error) = $stm1->errorInfo();
	$pdo->rollBack();
	echo 'parent.failure(',addcslashes($error),');';
	goto END;
}
if (false === $stm2->execute()) {
	list (, , $error) = $stm1->errorInfo();
	$pdo->rollBack();
	echo 'parent.failure(',addcslashes($error),');';
	goto END;
}
if (false === $pdo->commit()) {
	list (, , $error) = $pdo->errorInfo();
	echo 'parent.failure(',addcslashes($error),');';
	goto END;
}
echo 'parent.success(',$id,');';
END:
?>
</script>