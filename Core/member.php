<?php
if(!defined('IN_CRONLITE'))exit();
$clientip=$_SERVER['REMOTE_ADDR'];
if(isset($_COOKIE["admin_token"]))
{
	$token=authcode(daddslashes($_COOKIE['admin_token']), 'DECODE', SYS_KEY);
	list($user, $sid) = explode("\t", $token);
	$session=md5($config['user'].$user.$password_hash);
	if($session==$sid) {
		$islogin=true;
	}
}

if(isset($_COOKIE["user_token"]))
{
	$token=authcode(daddslashes($_COOKIE['user_token']), 'DECODE', SYS_KEY);
	list($id, $sid) = explode("\t", $token);
	$userinfo = $DB->get_row("SELECT * FROM user WHERE id='$id' limit 1");
	$session=md5($userinfo['id'].$userinfo['pass'].$password_hash);
	if($session==$sid) {
		$login=true;
	}
}
if (!$login&&empty($userinfo)&&isset($_GET['key'])&&$_GET['key']!='0'&&isset($config['server_api'])&&$config['server_api']=='0') {
	$key = GetGet('key');
	$userinfo = $DB->get_row("SELECT * FROM user WHERE `key`='$key' limit 1");
	if (!empty($userinfo)) {
		$login=true;
	}
}
?>