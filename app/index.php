<?php 
require 'inc/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (db_set($_POST['password'])) {
		$_SESSION['signin'] = db_get();
	} else {
		die('登录失败');
	}
	header('Location: list.php');
} elseif (isset($_SESSION['signin'])) {
	header('Location: list.php');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>登入</title>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css">
<style type="text/css">
body{padding:10px 50px;}
#signin{width:300px;height:260px;top:50%;position:absolute;left:50%;margin-left:-150px;margin-top:-130px;}
</style>
</head>

<body>
<div class="container" id="signin">
	<div class="panel panel-primary">
		<div class="panel-heading">用户登入</div>
		<div class="panel-body">
			<form method="post" role="form" action="./">
				<div class="form-group">
					<label for="password">密码</label>
					<input type="password" name="password" class="form-control" id="passowrd" placeholder="输入密码">
				</div>
				<button type="submit" class="btn btn-primary">登入</button>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>