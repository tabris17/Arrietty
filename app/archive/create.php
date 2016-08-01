<script type="text/javascript">
<?php
require '../inc/common.php';
require '../inc/category.php';
require '../inc/archive.php';

$pdo = db_open();

$stm = $pdo->prepare('SELECT id FROM `archive` WHERE `name`=?');
$stm1 = $pdo->prepare('INSERT INTO `archive` (`name`,`categories`,`abstract`,`source`,`content`,`is_compressed`,`content_type`,`views`,`content_length`) VALUES (?,?,?,?,?,?,?,0,?)');
$stm2 = $pdo->prepare('INSERT INTO `assn_category_archive` (`archive_id`,`category_id`) VALUES (?,?)');

$name = request_post('name');
$stm->bindValue(1, $name);
if (false === $stm->execute()) {
	list (, , $error) = $stm->errorInfo();
	echo 'parent.failure(',var_export($error, true),');';
	goto END;
}
if ($result = $stm->fetch()) {
	echo 'parent.failure("存在同名文档。<a href=\"./?id='.$result['id'].'\" target=\"_blank\">查看</a>");';
	goto END;
}


$categories = request_post('categories', 'array', array());
if (request_post('auto_append_parent', 'int', 0)) {
	$parentCategories = array();
	foreach ($categories as $category) {
		$parentCategory = '';
		foreach (explode(CATEGORY_SEPARATOR, $category) as $node) {
			$parentCategory .= CATEGORY_SEPARATOR . $node;
			$parentCategories[] = ltrim($parentCategory, CATEGORY_SEPARATOR);
		}
	}
	$categories = array_merge($categories, $parentCategories);
}
$categories = array_unique($categories);
sort($categories);
$source = request_post('source');
$contentType = request_post('content_type');
$isCompressed = request_post('is_compressed', 'int', 0);
switch ($contentType) {
	case ARCHIVE_CONTENT_TYPE_HTML:
		$content = request_post('content_html');
		break;
	case ARCHIVE_CONTENT_TYPE_TEXT:
		$content = request_post('content_text');
		break;
	case ARCHIVE_CONTENT_TYPE_MARKDOWN:
		$content = request_post('content_markdown');
		break;
}
$abstract = request_post('abstract');
$contentLength = strlen($content);

if (empty($name)) {
	echo 'parent.failure("文档名称不能为空");';
	goto END;
}
if (empty($content)) {
	echo 'parent.failure("文档内容不能为空");';
	goto END;
}
if (empty($abstract)) {
	$abstract = archive_getAbstract($contentType, $content);
}
if ($isCompressed) {
	$content = gzdeflate($content, 9);
}

$stm1->bindValue(1, $name);
$stm1->bindValue(2, implode("\n", $categories));
$stm1->bindValue(3, $abstract);
$stm1->bindValue(4, $source);
$stm1->bindValue(5, $content);
$stm1->bindValue(6, $isCompressed);
$stm1->bindValue(7, $contentType);
$stm1->bindValue(8, $contentLength);

$pdo->beginTransaction();

$categories = array_flip($categories);
foreach ($categories as $category => &$id) {
	$id = category_get($category);
	if ($id === false) {
		$error = category_getLastError();
		$pdo->rollBack();
		echo 'parent.failure(',var_export($error, true),');';
		goto END;
	}
}

if (false === $stm1->execute()) {
	list (, , $error) = $stm1->errorInfo();
	$pdo->rollBack();
	echo 'parent.failure(',var_export($error, true),');';
	goto END;
}
$archiveId = $pdo->lastInsertId();
$stm2->bindValue(1, $archiveId);
if (empty($categories)) {
	$stm2->bindValue(2, 0);
	if (false === $stm2->execute()) {
		list (, , $error) = $stm2->errorInfo();
		$pdo->rollBack();
		echo 'parent.failure(',var_export($error, true),');';
		goto END;
	}
} else {
	foreach ($categories as $categoryId) {
		$stm2->bindValue(2, $categoryId);
		if (false === $stm2->execute()) {
			list (, , $error) = $stm2->errorInfo();
			$pdo->rollBack();
			echo 'parent.failure(',var_export($error, true),');';
			goto END;
		}
	}
}
if (false === $pdo->commit()) {
	list (, , $error) = $pdo->errorInfo();
	echo 'parent.failure(',var_export($error, true),');';
	goto END;
}
echo 'parent.success(',$archiveId,')';
END:
?>
</script>