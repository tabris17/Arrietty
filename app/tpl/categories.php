<?php template_block_begin()?>
<style type="text/css">
.tiles{position:relative;margin:0;padding:0;list-style:none;}
.tile{diplay:none;margin:0;padding:0;}
.list-group{width:270px;}
h2 a.sm{font-size:16px;}
</style>
<?php template_block_end('head')?>
<?php template_prependTitle('分类 - ')?>
<div class="container">
<h2>全部分类  <a href="archives.php?cid=uncategoried" class="sm">未归类文档</a></h2>
<iframe name="submit" style="display:none;"></iframe>
<form action="category/delete.php" method="post" id="main-form" target="submit">
<input type="hidden" name="id" id="ipt-id">
<div id="waterfall">
	<ul class="tiles">
		<li class="tile">
			<ul class="list-group">
			<?php foreach ($categories as $i => $category):?>
			<?php if ($i > 0 && $category['depth'] == 0):?>
			</ul>
		</li>
		<li class="tile">
			<ul class="list-group">
			<?php endif?>
				<li class="list-group-item" depth="<?php echo $category['depth']?>">
					<button type="button" name="remove" value="<?php echo $category['id']?>" class="pull-right btn btn-default btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
					<a href="archives.php?cid=<?php echo $category['id']?>"><?php echo htmlspecialchars($category['name'])?> <span class="badge"><?php echo $category['items']?></span></a>
				</li>
			<?php endforeach?>
			</ul>
		</li>
	</ul>
</div>
</form>
</div>

<?php template_block_begin()?>
<script type="text/javascript" src="js/jquery.wookmark.min.js"></script>
<script type="text/javascript">
$('.tiles>li').wookmark({
    autoResize: true,
    container: $('#waterfall'),
    offset: 10,
    outerOffset: 10,
    itemWidth: 270
});

$("li[depth]").each(function () {
	$(this).css({"padding-left":($(this).attr("depth") * 14 + 15) + "px"});
});

$("button[name=remove]").click(function () {
	var id = $(this).val();
	if (confirm("是否删除分类#"+id+"？")) {
		$("#ipt-id").val(id);
		$("#main-form").submit();
	}
});
window.success = function () {
	location.reload();
};
window.failure = function (error) {
	alert(error);
}
</script>
<?php template_block_end('body')?>