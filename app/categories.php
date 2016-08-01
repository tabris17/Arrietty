<?php
require 'inc/common.php';
require 'inc/category.php';

navbar_set_activity('分类');

$pdo = db_open();
$categories = $pdo->query("SELECT * FROM `category` ORDER BY `left_value`")->fetchAll();

template_render('categories', array(
	'categories' => $categories,
));
