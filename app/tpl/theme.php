<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo template_prepend_title()?>Arrietty<?php echo template_append_title()?></title>

<link href="<?php echo $path?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $path?>css/style.css" rel="stylesheet">
<!--[if lt IE 9]>
<script src="<?php echo $path?>js/html5shiv.js"></script>
<script src="<?php echo $path?>js/respond.min.js"></script>
<![endif]-->
<?php echo template_block('head')?>
</head>
<body>

<div class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $path?>archive/new.php">新建文档</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="<?php echo navbar_get_activity('主页')?>"><a href="<?php echo $path?>list.php">主页</a></li>
				<li class="<?php echo navbar_get_activity('分类')?>"><a href="<?php echo $path?>categories.php">分类</a></li>
				<li class="<?php echo navbar_get_activity('文档')?>"><a href="<?php echo $path?>archives.php">文档</a></li>
				<li class="<?php echo navbar_get_activity('收藏')?>"><a href="<?php echo $path?>favorite.php">收藏</a></li>
				<li class="<?php echo navbar_get_activity('附件')?>"><a href="<?php echo $path?>attachments.php">附件</a></li>
				<li><a href="<?php echo $path?>signout.php">退出</a></li>
			</ul>
			<form class="navbar-form navbar-right" role="search" action="<?php echo $path?>search.php" method="get">
				<input type="hidden" name="scope" value="title" id="search-scope">
				<div class="input-group">
					<input type="text" class="form-control" name="q" placeholder="搜索" value="<?php echo htmlspecialchars($query)?>">
					<div class="input-group-btn">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
						<ul class="dropdown-menu pull-right">
							<li><a id="search-title"><strong>标题</strong></a></li>
							<li><a id="search-abstract">摘要</a></li>
							<li><a id="search-all">标题及摘要</a></li>
						</ul>
					</div>
				</div>
			</form>
		</div><!--/.nav-collapse -->
	</div>
</div>
<?php echo $content;?>
<script src="<?php echo $path?>js/jquery.min.js"></script>
<script src="<?php echo $path?>js/bootstrap.min.js"></script>
<?php echo template_block('body')?>
<script type="text/javascript">
$("#search-title").click(function(){$("#search-scope").val("title");$(".navbar-form").submit();});
$("#search-abstract").click(function(){$("#search-scope").val("abstract");$(".navbar-form").submit();});
$("#search-all").click(function(){$("#search-scope").val("all");$(".navbar-form").submit();});
</script>
</body>
</html>