<?php
const ARCHIVE_CONTENT_TYPE_HTML = 1;
const ARCHIVE_CONTENT_TYPE_TEXT = 2;
const ARCHIVE_CONTENT_TYPE_MARKDOWN = 3;

function archive_getAbstract($contentType, $content)
{
	switch ($contentType) {
		case ARCHIVE_CONTENT_TYPE_HTML:
			$content = strip_tags($content);
			break;
		case ARCHIVE_CONTENT_TYPE_TEXT:
			break;
		default:
			throw new Exception('未知的文档内容类型');
	}
	$content = strtr($content, array("\r\n"=>' ', "\n\r" =>' ', "\r" => ' ', "\n" =>' '));
	return trim(mb_substr($content, 0, 300, 'UTF-8'));
}

function archive_formatContent($contentType, $content)
{
	switch ($contentType) {
		case ARCHIVE_CONTENT_TYPE_TEXT:
			$content = htmlspecialchars($content);
			$content = preg_replace('/[\r\n]+/', '</p><p>', $content);
			$content = '<p>' . $content . '</p>';
			break;
		case ARCHIVE_CONTENT_TYPE_HTML:
			break;
		default:
			throw new Exception('未知的文档内容类型');
	}
	return $content;
}

function archive_getContentTypeName($contentType)
{
	switch ($contentType) {
		case ARCHIVE_CONTENT_TYPE_TEXT:
			return '文本';
		case ARCHIVE_CONTENT_TYPE_HTML:
			return 'HTML';
		case ARCHIVE_CONTENT_TYPE_MARKDOWN:
			return 'MARKDOWN';
		default:
			throw new Exception('未知的文档内容类型');
	}
}

function archive_getContentLength($contentLength)
{
	if ($contentLength > 1024 * 1024) {
		return number_format($contentLength / (1024 * 1024)) . 'M';
	} elseif ($contentLength > 1024) {
		return number_format($contentLength / (1024)) . 'KB';
	} else {
		return number_format($contentLength) . 'Bytes';
	}
}

function archive_fetchImagesFromHtml($html)
{
	require 'simple_html_dom.php';
	$images = array();
	foreach($html->find('img') as $element) {
		$src = $element->src;
		if (empty($images[$src])) {
			$images[$src] = file_get_contents($src);
		}
	}
	return $images;
}

