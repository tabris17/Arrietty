<?php
require '../inc/common.php';
require '../inc/archive.php';

navbar_set_activity('文档');

$archive_id = request_get('id', 'int');
if ($archive_id === false) {
	die('id 参数错误');
}

$pdo = db_open();
$archive = $pdo->query('SELECT * FROM archive WHERE id='.$archive_id);
if (!$archive) {
	die("id 为 $archive_id 记录不存在");
}
$archive = $archive->fetch();
//$pdo->exec('UPDATE archive SET access_time=datetime(\'now\') WHERE id='.$archive_id);

$categories = $pdo->query('SELECT id,name,full_name FROM category ORDER BY left_value')->fetchAll();

template_render('archive/edit', array(
	'archive' => $archive,
	'categories' => $categories,
));
