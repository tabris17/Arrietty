<?php template_block_begin()?>
<style type="text/css">
.page-header{margin-top:0;}
.panel-heading{overflow:hidden;}
.panel-title a{font-weight:bold;}
.panel-title small.pull-right{margin-top:5px;}
.panel-body{font-size:14px;color:gray;line-height:22px;}
.panel-footer .list-inline{margin-bottom:0;}
.actions{float:right;}
</style>
<?php template_block_end('head')?>
<?php template_prependTitle('首页 - ')?>
<div class="container">

<div class="row">
	<div class="col-md-8">
		<div class="page-header"><h2>最新文档 <small><?php echo $last_update?></small></h2></div>
<?php foreach ($last_archives as $archive):?>
		<div class="panel panel-default" id="archive-<?php echo $archive['id']?>">
			<div class="panel-heading">
				<h3 class="panel-title">
					<a href="archive/?id=<?php echo $archive['id']?>" target="_blank"><?php echo htmlspecialchars($archive['name'])?></a>
					<small class="pull-right"><?php echo $archive['creation_time']?></small>
				</h3>
			</div>
			<div class="panel-body">
				<ul class="list-inline">
					<li><label>阅读次数:</label><?php echo $archive['views']?></li>
					<li><label>内容类型:</label><?php echo archive_getContentTypeName($archive['content_type'])?></li>
					<li><label>大小:</label><?php echo archive_getContentLength($archive['content_length'])?></li>
				</ul>
				<?php echo nl2br($archive['abstract'])?>&hellip;
				<a href="archive/?id=<?php echo $archive['id']?>" class="pull-right">查看详细&gt;&gt;</a>
			</div>
			<div class="panel-footer">
				<ul class="list-inline actions">
					<li><a href="#<?php echo $archive['id']?>" title="收藏"><span class="glyphicon glyphicon-heart-empty"></span></a></li>
					<li><a href="#<?php echo $archive['id']?>" class="btn-delete" title="删除"><span class="glyphicon glyphicon-trash"></span></a></li>
					<li><a href="archive/edit.php?id=<?php echo $archive['id']?>" title="编辑"><span class="glyphicon glyphicon-edit"></span></a></li>
				</ul>
				<ul class="list-inline">
<?php 
foreach (explode("\n", $archive['categories']) as $category) {
	echo "<li><a href=\"archives.php?cn=", urlencode($category),"\">$category</a></li>";
}
?>
				</ul>
			</div>
		</div>
<?php endforeach?>
	</div>
	<div class="col-md-4">
		<div class="page-header"><h2>常用分类</h2></div>
		<ul class="list-group">
<?php foreach ($top_categories as $category):?>
			<li class="list-group-item"><a href="archives.php?cid=<?php echo $category['id']?>"><?php echo htmlspecialchars($category['full_name'])?></a><span class="badge"><?php echo $category['items']?></span></li>
<?php endforeach?>
		</ul>
	</div>
</div>




</div>
<iframe name="submit" class="hidden"></iframe>
<form id="form-action" target="submit" action="archive/delete.php" method="post"><input type="hidden" name="id"></form>

<?php template_block_begin()?>
<script type="text/javascript">
$(".btn-delete").click(function (event) {
	var archiveId = $(this).attr("href").substring(1);
	if (confirm("是否删除文档 #" + archiveId)) {
		$("#form-action>input[name=id]").val(archiveId);
		$("#form-action").submit();
		event.preventDefault();
	}
});
window.success = function (id) {
	$("#archive-"+id).fadeOut(500, function(){$(this).remove();});
};
window.failure = function (error) {
	alert(error);
};
$('#failure>button.close').click(function () {
	$(this).parent().css('display', 'none');
});
</script>
<?php template_block_end('body')?>