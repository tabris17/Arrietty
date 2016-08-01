<?php template_block_begin()?>
<style type="text/css">
.tiles{position:relative;margin:0;padding:0;list-style:none;}
.tile{diplay:none;margin:0;padding:0;}
.list-group{width:270px;}
.cate-remove{margin:0 10px;}
.cate-remove button{margin-top:8px;}
.cates{font-size:12px;margin-top:5px;}
thead tr th a{color:black;}
.like{color:#666;}
.unlike{color:red;}
</style>
<?php template_block_end('head')?>
<?php template_prependTitle('文档 - ')?>
<div class="container">
<h2>文档列表</h2>
<?php foreach ($categories as $category):?>
<?php if (count($categories) > 1):?>
<div class="cate-remove"><button type="button" value="<?php echo implode(',', array_filter(array_keys($categories), function ($val) use ($category) { return $val != $category['id']; }))?>" class="close" aria-hidden="true">&times;</button></div>
<?php endif?>
<ol class="breadcrumb">
	<?php 
	$path = explode(CATEGORY_SEPARATOR, $category['full_name']);
	$prefix = '';
	?>
	<?php foreach ($path as $key => $val): $prefix .= CATEGORY_SEPARATOR.$val?>
	<?php if ($key == count($path) - 1):?>
	<li class="active"><?php echo htmlspecialchars($val)?></li>
	<?php else:?>
	<li><a href="?cn=<?php echo urlencode(ltrim($prefix, CATEGORY_SEPARATOR))?>"><?php echo htmlspecialchars($val)?></a></li>
	<?php endif?>
	<?php endforeach?>
</ol>
<?php endforeach?>

<?php if (isset($crossedCategories) && count($crossedCategories)): $cid = implode(',', array_keys($categories))?>
<div class="panel panel-default">
	<div class="panel-heading">分类筛选</div>
	<div class="panel-body">
		<ul class="list-inline">
<?php foreach ($crossedCategories as $val):?>
			<li><a href="?cid=<?php echo $cid,',',$val['id']?>"><?php echo htmlspecialchars($val['full_name'])?></a></li>
<?php endforeach?>
		</ul>
	</div>
</div>
<?php endif?>

<div class="table-responsive">
<table class="table table-striped">
	<thead>
		<tr><?php echo $renderTableHeader()?></tr>
	</thead>
	<tbody>
<?php foreach ($archives as $archive):?>
		<tr>
			<td><?php echo $archive['id']?></td>
			<td>
				<a href="archive/?id=<?php echo $archive['id']?>" target="_blank"><?php echo htmlspecialchars($archive['name'])?></a>
				<div class="cates"><?php echo $archive['categories']?></div>
			</td>
			<td>
				<?php if (empty($archive['fid'])):?>
				<a href="#<?php echo $archive['id']?>" class="like" title="收藏" id="like-<?php echo $archive['id']?>"><span class="glyphicon glyphicon-heart-empty"></span></a>
				<a href="#<?php echo $archive['id']?>" class="unlike hidden" title="取消收藏" id="unlike-<?php echo $archive['id']?>"><span class="glyphicon glyphicon-heart"></span></a>
				<?php else:?>
				<a href="#<?php echo $archive['id']?>" class="like hidden" title="收藏" id="like-<?php echo $archive['id']?>"><span class="glyphicon glyphicon-heart-empty"></span></a>
				<a href="#<?php echo $archive['id']?>" class="unlike" title="取消收藏" id="unlike-<?php echo $archive['id']?>"><span class="glyphicon glyphicon-heart"></span></a>
				<?php endif?>
			</td>
			<td><?php echo archive_getContentLength($archive['content_length'])?></td>
			<td><?php echo archive_getContentTypeName($archive['content_type'])?></td>
			<td><?php echo $archive['views']?></td>
			<td><?php echo substr($archive['creation_time'], 0, 10)?></td>
			<td><?php echo substr($archive['modification_time'], 0, 10)?></td>
			<td><?php echo substr($archive['access_time'], 0, 10)?></td>
		</tr>
<?php endforeach?>
	</tbody>
</table>
</div>

<ul class="pagination pull-right">
	<?php echo paginator_render($page, $pageCount, 10, 'p')?>
</ul>

</div>

<?php template_block_begin()?>
<script type="text/javascript">
$("a.like").click(function() {
	var id = $(this).attr("href").substr(1);
	var button = $(this);
	$.ajax({
		"url" : "archive/like.php",
		"type" : "POST",
		"data" : {"id" : id}
	}).done(function(msg) {
		if (msg === "OK") {
			button.addClass("hidden");
			$("#unlike-"+id).removeClass("hidden");
		} else {
			window.alert(msg);
		}
	});
	return false;
});
$("a.unlike").click(function() {
	var id = $(this).attr("href").substr(1);
	var button = $(this);
	$.ajax({
		"url" : "archive/unlike.php",
		"type" : "POST",
		"data" : {"id" : id}
	}).done(function(msg) {
		if (msg === "OK") {
			button.addClass("hidden");
			$("#like-"+id).removeClass("hidden");
		} else {
			window.alert(msg);
		}
	});
	return false;
});
$(".cate-remove>button").click(function () {
	location = '?cid='+$(this).val();
});
</script>
<?php template_block_end('body')?>