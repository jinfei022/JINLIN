<?php
/*
*Title:核心索引库
*Author:LI
*/
@header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL); ini_set("display_errors", 1);
error_reporting(0);
session_start();
define('IN_CRONLITE', true);
define('SYS_KEY','DSB');
$date = date("Y-m-d H:i:s");
session_start();
require_once('360safe/360webscan.php');
require 'config.php';
//连接数据库
include_once("db.class.php");
$DB = new DB($host, $user, $pwd, $dbname, $port);
if($lists=$DB->query("SELECT * FROM li_config")){
	while ($list = $DB->fetch($lists)) {
		$config[$list['key']] = $list['value'];
    }
}
include_once("email.class.php");
include "function.php";
$password_hash='!@#%!s!';
$x_hash = '19635c7c-cca1-45ba-8cfc-182eccc76015';
include "member.php";
?>