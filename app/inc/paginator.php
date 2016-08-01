<?php
/**
 * 
 * @param int $current 当前页码
 * @param int $count 页码总数
 * @param int $size 分页器显示页码数量
 * @param string $pageParam 分页参数名称
 */
function paginator_render($current, $count, $size, $pageParam)
{
	$params = $_GET;
	$buildUrl = function ($page) use ($params, $pageParam) {
		$params[$pageParam] = $page;
		return http_build_query($params);
	};
	$result = '';
	if ($current == 1) {
		$result .= '<li class="disabled"><a href="#">&laquo;</a></li>';
	} else {
		$result .= '<li><a href="?'.$buildUrl(1).'">&laquo;</a></li>';
	}
	$begin = 1;
	$end = $count;
	if ($size > 0 && $size < $count) {
		$begin = $current - (int)($size / 2);
		if ($begin <= 0) $begin = 1;
		$end = $begin + $size - 1;
		if ($end > $count) {
			$end = $count;
			$begin = $end - $size + 1;
		}
	}
	for ($i = $begin; $i <= $end; ++ $i) {
		if ($i == $current) {
			$result .= '<li class="active"><a href="#">'.$i.'</a></li>';
		} else {
			$result .= '<li><a href="?'.$buildUrl($i).'">'.$i.'</a></li>';
		}
	}
	if ($current == $count) {
		$result .= '<li class="disabled"><a href="#">&raquo;</a></li>';
	} else {
		$result .= '<li><a href="?'.$buildUrl($count).'">&raquo;</a></li>';
	}
	return $result;
}
