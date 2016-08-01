<?php
require 'inc/common.php';
require 'inc/archive.php';
require 'inc/category.php';
require 'inc/paginator.php';

$orders = array(
	'id' => 'id', 
	'name' => 'name',
	'size' => 'content_length',
	'type' => 'content_type',
	'views' => 'views',
	'ctime' => 'creation_time',
	'mtime' => 'modification_time',
	'atime' => 'access_time',
);

const PAGE_SIZE = 50;
$page = request_get('p', 'int', 1);
$order = request_get('order', 'string', 'asc');
$sort = request_get('sort', 'string', 'id');
if (isset($orders[$sort])) {
	$queryOrder = " archive.{$orders[$sort]} ";
	if ($order === 'desc') {
		$queryOrder .= 'DESC ';
		$order = 'desc';
	} else {
		$order = 'asc';
	}
} else {
	$sort = 'id';
	$order = 'desc';
	$queryOrder = ' archive.id DESC ';
}
navbar_set_activity('收藏');

$pdo = db_open();
$categories = array();

$categoryIds = request_get('cid');
if (false !== $categoryIds) {
	$categoryIds = explode(',', $categoryIds);
	array_walk($categoryIds, function (&$id) {
		$id = (int)$id;
	});
	$rows = $pdo->query("SELECT * FROM `category` WHERE id in (".implode(',', $categoryIds).")")->fetchAll();
	foreach ($rows as $row) {
		$categories[$row['id']] = $row;
	}
}
$queryRows = "SELECT archive.id,archive.categories,archive.name,archive.creation_time,archive.modification_time,archive.access_time,archive.content_length,archive.views,archive.content_type FROM archive ";
$queryCount = "SELECT COUNT(*) FROM archive ";
$queryCrossedCategories = "SELECT `category`.* FROM `category` JOIN assn_category_archive AS ACA ON ACA.category_id=`category`.id";

$queryJoin = '';
$queryCategoriesJoin = '';
foreach ($categories as $cateId => $_) {
	$queryJoin .= " JOIN assn_category_archive AS ACA$cateId ON ACA$cateId.archive_id=archive.id AND ACA$cateId.category_id=$cateId";
	$queryCategoriesJoin .= " JOIN assn_category_archive AS ACA$cateId ON ACA$cateId.archive_id=ACA.archive_id AND ACA$cateId.category_id=$cateId";
}

$queryJoin .= " JOIN favorite ON favorite.archive_id=archive.id ";
$queryCategoriesJoin .= " JOIN favorite ON favorite.archive_id=ACA.archive_id ";

$queryLimit = " LIMIT ".($page - 1) * PAGE_SIZE.",".PAGE_SIZE;
$rowCount = $pdo->query($queryCount.$queryJoin)->fetch();
$rowCount = $rowCount[0];
$archives = $pdo->query($queryRows.$queryJoin." ORDER BY ".$queryOrder.$queryLimit)->fetchAll();

$crossedCategories = $pdo->query($queryCrossedCategories.$queryCategoriesJoin." GROUP BY `category`.id")->fetchAll();
foreach ($crossedCategories as $key => $crossedCategory) {
	if (isset($categories[$crossedCategory['id']])) {
		unset($crossedCategories[$key]);
	}
}

$renderTableHeader = function () use ($order, $sort) {
	$result = '';
	$headers = array(
		'id' => '<th><a href="%s">#</a>%s</th>',
		'name' => '<th><a href="%s">名称</a>%s</th>',
		'<th width="36"></th>',
		'size' => '<th><a href="%s">大小</a>%s</th>',
		'type' => '<th><a href="%s">类型</a>%s</th>',
		'views' => '<th><a href="%s">查看</a>%s</th>',
		'ctime' => '<th><a href="%s">创建时间</a>%s</th>',
		'mtime' => '<th><a href="%s">最后修改</a>%s</th>',
		'atime' => '<th><a href="%s">最后访问</a>%s</th>',
	);
	$query = $_GET;
	unset($query['page']);
	unset($query['sort']);
	unset($query['order']);
	foreach ($headers as $key => $header) {
		$query['sort'] = $key;
		if ($sort === $key) {
			if ($order === 'desc') {
				$caret = '<span class="caret"></span>';
			} else {
				$query['order'] = 'desc';
				$caret = '<span class="caret-up"></span>';
			}
			$result .= sprintf($header, '?'.http_build_query($query), $caret);
		} else {
			$result .= sprintf($header, '?'.http_build_query($query), '');
		}
	}
	return $result;
};

template_render('favorite', array(
	'archives' => $archives,
	'page' => $page,
	'pageSize' => PAGE_SIZE,
	'rowCount' => $rowCount,
	'pageCount' => (int)ceil($rowCount / PAGE_SIZE),
	'categories' => $categories,
	'crossedCategories' => isset($crossedCategories) ? $crossedCategories : null,
	'renderTableHeader' => $renderTableHeader,
));
