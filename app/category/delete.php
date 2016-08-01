<script type="text/javascript">
<?php
require '../inc/common.php';
require '../inc/category.php';
$pdo = db_open();

$id = request_post('id' , 'int');
if ($id === false) {
	echo 'parent.failure("参数错误");';
	goto END;
}


$pdo->beginTransaction();
if (false === category_delete($id)) {
	$pdo->rollBack();
	echo 'parent.failure('.var_export(category_getLastError(), true).');';
	goto END;
}
if (false === $pdo->commit()) {
	list (, , $error) = $pdo->errorInfo();
	echo 'parent.failure(',addcslashes($error),');';
	goto END;
}
echo 'parent.success();';
END:
?>
</script>