<?php
const PAGE_SIZE = 10;
const INDEX_NUM_TOP_CATEGORY = 50;

require 'inc/common.php';
require 'inc/archive.php';

//navbar_set_activity('主页');
//$page = request_get('p', 'int', 1);

$pdo = db_open();
$lastArchives = $pdo->query('SELECT id,name,categories,abstract,creation_time,views,content_length,content_type FROM archive ORDER BY id DESC LIMIT 0,'.PAGE_SIZE);
if ($lastArchives === false) {
	$lastUpdate = '';
} else {
	$lastArchives = $lastArchives->fetchAll();
	$lastUpdate = current($lastArchives)['creation_time'];
}

$topCategoies = $pdo->query('SELECT id,name,full_name,items FROM `category` ORDER BY items DESC,left_value ASC LIMIT 0,'.INDEX_NUM_TOP_CATEGORY)->fetchAll();

template_render('list', array(
	'last_archives' => $lastArchives,
	'last_update' => $lastUpdate,
	'top_categories' => $topCategoies,
));
