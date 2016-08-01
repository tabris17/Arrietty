<?php template_block_begin()?>
<style type="text/css">
.dl-horizontal dt{width:60px;}
.dl-horizontal dd{word-wrap:break-word;word-break:break-all;}
@media (min-width: 768px) {.dl-horizontal dd{margin-left:70px;}}
.glyphicon-heart{color:red;}
</style>
<?php template_block_end('head')?>
<?php template_prependTitle(htmlspecialchars($archive['name']).' - ')?>
<div class="container">

<div class="alert alert alert-danger" id="failure">
	<button type="button" class="close">&times;</button>
	<strong>删除失败：</strong><span id="error"></span>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-header">
			<h1><?php echo htmlspecialchars($archive['name'])?></h1>
		</div>
		<div id="pager"></div>
		<?php echo archive_formatContent($archive['content_type'], $archive['is_compressed'] ? gzinflate($archive['content']) : $archive['content'])?>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">文档信息</div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt>创建时间</dt>
					<dd><?php echo empty($archive['creation_time']) ? '<abbr title="信息不详">N/A</abbr>' : $archive['creation_time']?></dd>
					<dt>修改时间</dt>
					<dd><?php echo empty($archive['modification_time']) ? '<abbr title="信息不详">N/A</abbr>' : $archive['modification_time']?></dd>
					<dt>上次访问</dt>
					<dd><?php echo empty($archive['access_time']) ? '<abbr title="信息不详">N/A</abbr>' : $archive['access_time']?></dd>
					<dt>是否压缩</dt>
					<dd><?php echo $archive['is_compressed'] ? '是' : '否'?></dd>
					<dt>内容类型</dt>
					<dd><?php echo archive_getContentTypeName($archive['content_type'])?></dd>
					<dt>阅读次数</dt>
					<dd><?php echo number_format($archive['views'])?> 次</dd>
					<dt>大小</dt>
					<dd><?php echo archive_getContentLength($archive['content_length'])?></dd>
					<dt>来源</dt>
					<dd><?php echo empty($archive['source']) ? '<abbr title="信息不详">N/A</abbr>' : "<a href=\"{$archive['source']}\" target=\"_blank\">{$archive['source']}</a>"?></dd>
					<dt>分类</dt>
					<dd>
						<ul class="list-inline">
<?php 
foreach (explode("\n", $archive['categories']) as $category) {
	echo "<li><a href=\"../archives.php?cn=", urldecode($category),"\">$category</a></li>";
}
?>
						</ul>
					</dd>
				</dl>
				<form role="form" class="text-center" id="form-action">
					<input type="hidden" name="id" value="<?php echo $archive['id']?>">
					<button type="button" class="btn btn-primary" id="btn-edit"><span class="glyphicon glyphicon-edit"></span> 修改</button>
					<?php if (empty($archive['fid'])):?>
					<button type="button" class="btn btn-default" id="btn-like"><span class="glyphicon glyphicon-heart-empty"></span> 收藏</button>
					<button type="button" class="btn btn-default hidden" id="btn-unlike"><span class="glyphicon glyphicon-heart"></span> 取消</button>
					<?php else:?>
					<button type="button" class="btn btn-default hidden" id="btn-like"><span class="glyphicon glyphicon-heart-empty"></span> 收藏</button>
					<button type="button" class="btn btn-default" id="btn-unlike"><span class="glyphicon glyphicon-heart"></span> 取消</button>
					<?php endif?>
					<button type="button" class="btn btn-default" id="btn-delete"><span class="glyphicon glyphicon-trash"></span> 删除</button>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<iframe name="submit" class="hidden"></iframe>

<?php template_block_begin()?>
<script type="text/javascript" src="../js/jquery.pin.min.js"></script>
<script type="text/javascript">
$("#btn-like").click(function() {
	var button = $(this);
	$.ajax({
		"url" : "like.php",
		"type" : "POST",
		"data" : {"id" : <?php echo $archive['id']?>}
	}).done(function(msg) {
		if (msg === "OK") {
			button.addClass("hidden");
			$("#btn-unlike").removeClass("hidden");
		} else {
			window.alert(msg);
		}
	});
	return false;
});
$("#btn-unlike").click(function() {
	var button = $(this);
	$.ajax({
		"url" : "unlike.php",
		"type" : "POST",
		"data" : {"id" : <?php echo $archive['id']?>}
	}).done(function(msg) {
		if (msg === "OK") {
			button.addClass("hidden");
			$("#btn-like").removeClass("hidden");
		} else {
			window.alert(msg);
		}
	});
	return false;
});

$("#pager").pin();
window.success = function () {
	location = <?php echo isset($_SERVER['HTTP_REFERER']) ? var_export($_SERVER['HTTP_REFERER'], true) : '"../"'?>;
};
window.failure = function (error) {
	alert(error);
};
$("#btn-edit").click(function () {
	$("#form-action").attr('target', '_self').attr('method', 'get').attr('action', 'edit.php').submit();
});
$("#btn-delete").click(function () {
	if (confirm('是否删除当前文档？')) {
		$("#form-action").attr('target', 'submit').attr('method', 'post').attr('action', 'delete.php').submit();
	}
});
</script>
<?php template_block_end('body')?>
