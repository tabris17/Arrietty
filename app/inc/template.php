<?php

define('TPL_PATH', __DIR__.'/../tpl/');
define('THEME_FILE', __DIR__.'/../tpl/theme.php');

$template_blocks = array();

function template_render($file, $params)
{
	$template_file = TPL_PATH . $file . '.php';
	$render = function () use ($params, $template_file) {
		extract($params);
		require $template_file;
	};
	ob_start();
	$render();
	$content = ob_get_contents();
	ob_end_clean();
	$path = str_repeat('../', count(explode('/', $file)) - 1);
	global $template_query;
	$query = $template_query;
	$render = function ($content) use ($path, $query) {
		require THEME_FILE;
	};
	$render($content);
}

function template_block($name)
{
	global $template_blocks;
	if (isset($template_blocks[$name])) {
		return $template_blocks[$name];
	}
}

function template_prepend_title()
{
	global $template_prepend_title;
	return $template_prepend_title;
}

function template_append_title()
{
	global $template_append_title;
	return $template_append_title;
}

function template_prependTitle($title)
{
	global $template_prepend_title;
	$template_prepend_title = $title;
}

function template_appendTitle($title)
{
	global $template_append_title;
	$template_append_title = $title;
}

function template_block_begin()
{
	ob_start();
}

function template_block_end($name)
{
	global $template_blocks;
	$content = ob_get_contents();
	$template_blocks[$name] = $content;
	ob_end_clean();
}

function template_setQuery($query)
{
	global $template_query;
	$template_query = $query;
}