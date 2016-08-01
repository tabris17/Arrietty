<?php
session_start();
if (isset($_SESSION['signin'])) {
	if (false === db_set($_SESSION['signin'])) {
		unset($_SESSION['signin']);
		die('登录错误');
	}
} else {
	die('请先登录');
}
