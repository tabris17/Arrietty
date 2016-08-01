<?php template_block_begin()?>
<style type="text/css">
#frm-save{margin-bottom:20px;}
#sel_categories_chosen{width:100%!important;}
.tab-content{padding-top:6px;}
.search-field input{height:25px!important;line-height:20px;}
.err-input{border:1px solid red;color:red;}
</style>
<link href="../chosen/chosen.min.css" rel="stylesheet">
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<?php template_block_end('head')?>
<?php template_prependTitle('新建文档 - ')?>
<div class="container">

<div class="alert alert alert-danger" id="failure">
	<button type="button" class="close">&times;</button>
	<strong>保存失败：</strong><span id="error"></span>
</div>

<form role="form" action="create.php" method="post" target="submit" id="frm-save">

<div class="row">
	<div class="col-md-8">
		<div class="form-group">
			<label for="ipt-name">文档名</label>
			<input type="text" class="form-control" id="ipt-name" name="name" placeholder="输入文档名">
		</div>
		<div class="form-group">
			<label for="ipt-source">来源</label>
			<input type="text" class="form-control" id="ipt-source" name="source" placeholder="输入来源">
		</div>
		<div class="form-group">
			<label for="txt-content">内容</label>
			<ul class="nav nav-tabs" id="tab-content">
				<li class="active"><a href="#html" data-toggle="tab">HTML</a></li>
				<li><a href="#text" data-toggle="tab">文本</a></li>
			</ul>
			<input type="hidden" name="content_type" id="ipt-content-type" value="<?php echo ARCHIVE_CONTENT_TYPE_HTML?>">
			<div class="tab-content">
				<div class="tab-pane active" id="html">
					<textarea class="form-control" id="txt-content" name="content_html" placeholder="输入内容"></textarea><script type="text/javascript">CKEDITOR.replace('txt-content');</script>
				</div>
				<div class="tab-pane" id="text">
					<textarea class="form-control" name="content_text" placeholder="输入内容" rows="10"></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="ipt-abstract">摘要</label>
			<textarea class="form-control" id="ipt-abstract" name="abstract" placeholder="输入摘要"></textarea>
		</div>
		<div class="checkbox">
			<label><input type="checkbox" checked value="1" name="is_compressed"><span class="glyphicon glyphicon-compressed"></span> 压缩正文</label>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">分类</div>
			<div class="panel-body">
				<div class="form-group">
					<select data-placeholder="选择分类..." class="chosen-select form-control" id="sel-categories" multiple name="categories[]">
<?php 
foreach ($categories as $category) {
	echo '<option>',htmlspecialchars($category['full_name']),'</option>';
}
?>
					</select>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" checked value="1" name="auto_append_parent">自动添加上级分类</label>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">附件</div>
			<div class="panel-body">
				
			</div>
		</div>
	</div>
</div>
<div>
	<button type="submit" class="btn btn-primary">保存</button>
	<button type="reset" class="btn btn-default">重置</button>
</div>

</form>
</div>

<iframe name="submit" class="hidden"></iframe>
<div class="modal fade" id="processing" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p>正在处理&hellip;</p>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="success" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<p>保存成功！</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">继续</button>
				<button type="button" class="btn btn-default">查看</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<?php template_block_begin()?>
<script type="text/javascript" src="../chosen/chosen.jquery.min.js"></script>
<script type="text/javascript">
var config = {
	'.chosen-select' : {no_results_text: "按回车添加分类"},
};
for (var selector in config) {
	$(selector).chosen(config[selector]);
}

$("#sel_categories_chosen .search-field input").keyup(function (event) {
	if (event.which == 13) {
		$("#sel-categories").append("<option selected>"+$(this).val()+"</option>").trigger('chosen:updated');
		event.preventDefault();
	}
});

if (location.hash === '#text') {
	$("#ipt-content-type").val(<?php echo ARCHIVE_CONTENT_TYPE_TEXT?>);
	$('#tab-content a:last').tab('show');
}

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	switch ($(e.target).html()) {
		case 'HTML':
			$("#ipt-content-type").val(<?php echo ARCHIVE_CONTENT_TYPE_HTML?>);
			break;
		case '文本':
			$("#ipt-content-type").val(<?php echo ARCHIVE_CONTENT_TYPE_TEXT?>);
			break;
		case 'Markdown':
			$("#ipt-content-type").val(<?php echo ARCHIVE_CONTENT_TYPE_MARKDOWN?>);
			break;
	}
});
$("#frm-save").submit(function () {
	$('#processing').modal('show');
});
$('#processing').on('hide.bs.modal', function (e) {
	if (window.processed !== true) {
		e.preventDefault();
	}
});
window.success = function (id) {
	window.processed = true;
	$('#processing').modal('hide');
	$('#frm-save')[0].reset();
	$("#sel_categories_chosen li.search-choice").remove();
	$("#sel-categories").trigger("chosen:updated");
	CKEDITOR.instances['txt-content'].setData("");
	$('#success').modal('show');
	$('#success button.btn-default').click(function () {
		window.location = './?id='+id;
	});
	$('#failure').css('display', 'none');
};
window.failure = function (error) {
	window.processed = true;
	$('#processing').modal('hide');
	$('#error').html(error);
	$('#failure').css('display', 'block');
};
$('#failure>button.close').click(function () {
	$(this).parent().css('display', 'none');
});

$("#ipt-name").change(function () {
	var name = $(this).val();
	$.ajax({
		type: "POST",
		url: "unique.php",
		data: {name: name}
	}).done(function (msg) {
		if (msg == "true") {
			$("#ipt-name").addClass("err-input");
		} else {
			$("#ipt-name").removeClass("err-input");
		}
	});
});
</script>
<?php template_block_end('body')?>
