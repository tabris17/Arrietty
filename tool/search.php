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

console_write("数据库文件：");
$db = trim(fgets(STDIN));
$needles = array();
for ($i = 1; ; $i ++) {
	console_write("关键词{$i}：");
	list($needle) = fscanf(STDIN, "%s");
	$needle = trim($needle);
	if (empty($needle)) break;
	$needles[] = mb_convert_encoding($needle, 'UTF-8', 'GBK');
}
$pdo = db_open();
foreach ($pdo->query("SELECT id,name,is_compressed,content,content_type,abstract FROM archive") as $row) {
	$content = archive_formatContent($row['content_type'], $row['is_compressed'] ? gzinflate($row['content']) : $row['content']);
	foreach ($needles as $needle) {
		if (false === mb_strpos($content, $needle, 0, 'UTF-8')) {
			goto NEXT;
		}
	}
	console_write($row['name']);
	echo ':', $row['id'], PHP_EOL;
	file_put_contents(__DIR__.'\results.txt', '《'.$row['name'].'》 http://localhost:8000/archive/?id='.$row['id'].PHP_EOL.$row['abstract'].PHP_EOL, FILE_APPEND);
NEXT:
	continue;
}
