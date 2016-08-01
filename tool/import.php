<?php
require 'console.php';
require '../app/inc/archive.php';
require '../app/inc/category.php';

function db_open()
{
	global $dbInstance;
	global $db;
	if (empty($dbInstance)) {
		$dbInstance = new PDO("sqlite:{$db}");
	}
	return $dbInstance;
}

function getCategories($categories)
{
	$categoriesWithId = array();
	foreach ($categories as $category) {
		$category = mb_convert_encoding($category, 'UTF-8', 'GBK');
		$id = category_get($category);
		if ($id === false) trigger_error("获取分类“{$category}”编号出错");
		$categoriesWithId[$id] = $category;
	}
	return $categoriesWithId;
}

function createArchive($name, $categories, $content, $source, $creationTime) {
	$name = mb_convert_encoding($name, 'UTF-8', 'GBK');
	$content = mb_convert_encoding($content, 'UTF-8', 'GBK');
	$source = mb_convert_encoding($source, 'UTF-8', 'GBK');
	$pdo = db_open();
	$pdo->beginTransaction();
	$stm1 = $pdo->prepare('INSERT INTO `archive` (`name`,`categories`,`abstract`,`content`,`source`,`is_compressed`,`content_type`,`views`,`content_length`,`creation_time`) VALUES (?,?,?,?,?,1,'.ARCHIVE_CONTENT_TYPE_TEXT.',0,?,?)');
	$stm2 = $pdo->prepare('INSERT INTO `assn_category_archive` (`archive_id`,`category_id`) VALUES (?,?)');
	$stm1->bindValue(1, $name);
	$stm1->bindValue(2, implode("\n", $categories));
	$stm1->bindValue(3, archive_getAbstract(ARCHIVE_CONTENT_TYPE_TEXT, $content));
	$stm1->bindValue(4, gzdeflate($content, 9));
	$stm1->bindValue(5, $source);
	$stm1->bindValue(6, strlen($content));
	$stm1->bindValue(7, date('Y-m-d H:i:s', $creationTime));
	if (false === $stm1->execute()) {
		list (, , $error) = $pdo->errorInfo();
		console_write("导入文章《{$name}》错误：");
		echo $error, PHP_EOL;
		$pdo->rollBack();
		return false;
	}
	$stm2->bindValue(1, $pdo->lastInsertId());
	if (empty($categories)) {
		$stm2->bindValue(2, 0);
		if (false === $stm2->execute()) {
			list (, , $error) = $stm2->errorInfo();
			$pdo->rollBack();
			console_write("导入文章《{$name}》错误：");
			echo $error, PHP_EOL;
			return false;
		}
	} else {
		foreach ($categories as $categoryId => $val) {
			$stm2->bindValue(2, $categoryId);
			if (false === $stm2->execute()) {
				list (, , $error) = $stm2->errorInfo();
				$pdo->rollBack();
				console_write("导入文章《{$name}》错误：");
				echo $error, PHP_EOL;
				return false;
			}
		}
	}
	if (false === $pdo->commit()) {
		list (, , $error) = $pdo->errorInfo();
		console_write("导入文章《{$name}》错误：");
		echo $error, PHP_EOL;
		return false;
	}
	return true;
}

console_write("导入文件夹：");
$path = trim(fgets(STDIN));
console_write("数据库文件：");
$db = trim(fgets(STDIN));
$categories = array();
for ($i = 1; ; $i ++) {
	console_write("分类{$i}：");
	list($categoryName) = fscanf(STDIN, "%s");
	if (empty($categoryName)) break;
	$categories[] = $categoryName;
}

$parentCategories = array();
foreach ($categories as $category) {
	$parentCategory = '';
	foreach (explode(CATEGORY_SEPARATOR, $category) as $node) {
		$parentCategory .= CATEGORY_SEPARATOR . $node;
		$parentCategories[] = ltrim($parentCategory, CATEGORY_SEPARATOR);
	}
}
$categories = array_merge($categories, $parentCategories);

$categories = getCategories($categories);
$dir = dir($path);
while (false !== ($entry = $dir->read())) {
	if (pathinfo($entry, PATHINFO_EXTENSION) !== 'txt') continue;
	$name = pathinfo($entry, PATHINFO_FILENAME);
	$filename = $dir->path .DIRECTORY_SEPARATOR. $entry;
	$content = file_get_contents($filename);
	if (false === $content) {
		continue;
	}
	if (createArchive($name, $categories, $content, $filename, filemtime($filename))) {
		rename($filename, $filename.'.ok');
	}
}
$dir->close();