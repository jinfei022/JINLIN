
//购买操作
function buy(id) {
	layer.load(2);
	$.ajax({
		type: 'post',
		url: 'api.php?act=buy',
		dataType: 'json',
		timeout: 60000,
		data: {
			id: id
		},
		success: function (data) {
			layer.closeAll('loading');
			layer.msg(data.msg);
			if (data.code == 1) {
				url('deploy.php');
			}
		},
		error: function (error) {
			layer.closeAll('loading');
			layer.msg('服务器错误！');
		}
	});
}
//发送攻击
function send() {
	layer.load(1);
	$.ajax({
		url: "api.php?act=send",
		type: 'post',
		dataType: 'json',
		data: $('form').serialize(),
		success: function (data) {
			layer.closeAll('loading');
			layer.msg(data.msg);
		},
		error: function (error) {
			layer.closeAll('loading');
			layer.msg('服务器错误！');
		}
	});
}
//表单提交
function submit_form(url, choose) {
	layer.load(1);
	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: $(choose).serialize(),
		success: function (data) {
			layer.closeAll('loading');
			layer.msg(data.msg);
			if (choose == "#addkm" && data.code == 1) {
				var txt = data.msg + "</br>";
				$.each(data.resault, function (x, d) {
					txt += "卡密：" + d.key + "----" + d.money + "￥</br>";
				});
				$("#kms").css('display', 'block');
				$("#kms").html(txt);
			}
		},
		error: function (error) {
			layer.closeAll('loading');
			layer.msg('服务器错误！');
		}
	});
}
//ajax提交
function ajax(url, pararm) {
	layer.load(1);
	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: pararm,
		success: function (data) {
			layer.closeAll('loading');
			layer.msg(data.msg);
			if (data.code == 1) {
				$.pjax({
					url: window.location.href,
					container: '#pjax',
					fragment: '#pjax',
					timeout: 8000
				});
			}
		},
		error: function (error) {
			layer.closeAll('loading');
			layer.msg('服务器错误！');
		}
	});
}
function add_types(){
	var key = Number($('#types').attr('key'))+1;
	console.log(key)
	$('#types').attr('key',key);
	var txt = '<div class="input-group types_'+key+'"><span class="input-group-addon">参数:</span><input type="text" name="'+key+'" value="" class="form-control" autocomplete="off" required=""><span class="btn btn-danger input-group-addon" onclick="$(\'#types\').attr(\'key\',$(\'#types\').attr(\'key\')-1);$(\'.types_'+key+'\').remove()">删除</span></div><br class="types_'+key+'">';
	$("#list").before(txt);
}