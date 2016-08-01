<?php
require '../inc/common.php';
require '../inc/archive.php';

navbar_set_activity('文档');

$archive_id = request_get('id', 'int');

if ($archive_id === false) {
	die('id 参数错误');
}

$pdo = db_open();
$archive = $pdo->query('SELECT archive.*,favorite.archive_id AS fid FROM archive LEFT JOIN favorite ON archive.id=favorite.archive_id WHERE archive.id='.$archive_id)->fetch();
if (!$archive) {
	die("id 为 $archive_id 记录不存在");
}

$pdo->exec('UPDATE archive SET access_time=datetime(\'now\') WHERE id='.$archive_id);

template_render('archive/index', array(
	'archive' => $archive,
));

$pdo->exec('UPDATE archive SET views=views+1 WHERE id='.$archive_id);