<?php
require '../inc/common.php';
require '../inc/archive.php';

navbar_set_activity('文档');

$pdo = db_open();
$categories = $pdo->query('SELECT id,name,full_name FROM category ORDER BY left_value')->fetchAll();

template_render('archive/new', array(
	'categories' => $categories,
));