<?php
function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept:*/*";
	$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
	$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
	$httpheader[] = "Connection:close";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	if($post){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if($header){
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
	}
	if($cookie){
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if($referer){
		if($referer==1){
			curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
		}else{
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
	}
	if($ua){
		curl_setopt($ch, CURLOPT_USERAGENT,$ua);
	}else{
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; Android 4.4.2; NoxW Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36');
	}
	if($nobaody){
		curl_setopt($ch, CURLOPT_NOBODY,1);
	}
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
//获取post参数
function GetPost($str)
{
	if (isset($_POST[$str]) && $_POST[$str] != "") {
		return daddslashes($_POST[$str]);
	} else {
		json(array('code' => 0, 'msg' => $str . '参数为空！'));
	}
}
//获取get参数
function GetGet($str)
{
	if (isset($_GET[$str]) && $_GET[$str] != "") {
		return daddslashes($_GET[$str]);
	} else {
		json(array('code' => 0, 'msg' => '参数为空！'));
	}
}
//生成json
function json(array $res)
{
	exit(json_encode($res));
}
//密码加密
function getpwd($str)
{
	return md5($str . md5($str) . $str);
}
//CURL函数
function curl_get($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1');
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$content = curl_exec($ch);
	curl_close($ch);
	return ($content);
}
function real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}

function daddslashes($string, $force = 0, $strip = FALSE)
{
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if (!MAGIC_QUOTES_GPC || $force) {
		if (is_array($string)) {
			foreach ($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	$ckey_length = 4;
	$key = md5($key ? $key : ENCRYPT_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}

function random($length, $numeric = 0)
{
	$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for ($i = 0; $i < $length; $i++) {
		$hash .= $seed{
			mt_rand(0, $max)};
	}
	return $hash;
}

function decodeUnicode($str)
{

    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

function Node_Active($uid){
    global $DB;
    $node_id = $DB->get_row("SELECT * FROM li_user WHERE uid='$uid' limit 1")['node_id'];//查询节点ID
    $node = $DB->get_row("SELECT * FROM li_node WHERE id='$node_id' and status=1 limit 1");
    if ($node['team_id'] == 0)//判断是否为私人节点
        return true;//如果是返回真
    else{
        $team = $DB->get_row("SELECT * FROM li_team_user WHERE uid='$uid' and status=1 limit 1");
        $team_id = $DB->get_row("SELECT * FROM li_team_info WHERE team_name='{$team['team_name']}' limit 1")['id'];
        if ($node['team_id'] == $team_id)
            return true;
        else
            return false;
    }
}
function check_balance($userinfo,$kouchu){
    $balance = $userinfo['balance'];
    $balance_tmp = $userinfo['balance_tmp'];
    $rank = $userinfo['rank'];
    $uid = $userinfo['uid'];
    $balance_tmp = $balance_tmp - $kouchu;
    if($balance_tmp < 0){
        $balance = $balance + $balance_tmp;
        $balance_tmp = 0;
    }
    $result = array();
    $result['balance'] = $balance;
    $result['balance_tmp'] = $balance_tmp;
    if($balance<0 && $rank<2){
        json(['code' => 0, 'msg' => '余额不足']);
    }else{
        return $result;
    }
}

function showmsg($content = '未知的异常', $type = 4, $back = false, $title)//debug
{
	switch ($type) {
		case 1:
			$panel = "success";
			break;
		case 2:
			$panel = "info";
			break;
		case 3:
			$panel = "warning";
			break;
		case 4:
			$panel = "danger";
			break;
	}
	echo '<div class="panel panel-' . $panel . '">
      <div class="panel-heading">
        <h3 class="panel-title">提示信息</h3>
        </div>
        <div class="panel-body">';
	echo $content;

	if ($back) {
		echo '<hr/><a href="' . $back . '"><< 返回' . $title . '</a>';
		echo '<br/><a href="javascript:history.back(-1)"><< 返回上一页</a>';
	} else
		echo '<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a>';

	echo '</div>
    </div>';
}
