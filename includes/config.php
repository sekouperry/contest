<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

//header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header ('Content-type: text/html; charset=utf-8');
header('P3P: CP="NON DSP TAIa PSAa PSDa OUR IND UNI", policyref="/w3c/p3p.xml"');
header('P3P: CP="CAO PSA OUR"');

@session_start();
global $global, $db, $userId;

$secure = 'http://';
if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){$secure = 'https://';}

$global['app_id']    		  = '188899421260900'; // EDIT THIS - Your App ID
$global['app_secret']    	= '905eba7e2ee730819cff0971d1411e30'; // EDIT THIS - Your App Secret

$global['domain']         = 'ipagedeletedallmydat.ipage.com'; // EDIT THIS - Your Domain Name without http://www
$global['home_link']      = $secure.'www.'.$global['domain'].'/';
//$global['app_link']       = $secure.'www.'.$global['domain'].'//';
$global['app_link']       = $secure.'www.facebook.com/theinsightezine?sk=app_'. $global['app_id'];
$global['img_src']			  = $global['home_link'].'images/';
$global['rootPath']    		= dirname(__FILE__)."/../";
$global['app_name']    		= 'App';
$global['app_perms']    	= 'email publish_stream';
$global['fb_connect_js']  = $secure.'connect.facebook.net/en_US/all.js';
$global['uploadPath']     = $global['rootPath'].'uploads/';
$global['upload_link']    = $global['home_link'].'uploads/';

$global['dbhost']         = 'ipagedeletedallmydat.ipagemysql.com';
$global['dbusername']     = 'theinsight'; //EDIT THIS - Database User
$global['dbpassword']     = '19931110'; // EDIT THIS - Database Password
$global['dbdatabase']     = 'contest'; // EDIT THIS - Database Name

$global['admin-password'] = "123456"; // EDIT THIS - Admin password

$global['demo'] = false;

//$global['auto_approve'] = false;

//$global['version'] = 1;
//$global['end_date'] = "";

/*
$global['message'] = '';
$global['name'] = '';
$global['picture'] = '';
$global['caption'] = '';
$global['description'] = '';
*/

require_once('facebook.php');
require_once('db.php');
$db = new db();

define('LOOKUP_SIZE', 100);

require_once('functions.php');

$settings = importSettings();

foreach($settings as $setting){
	if($setting['field'] == 'body'){
		$body = unserialize(base64_decode($setting['value']));		
		$global['message'] = $body['message'];
		$global['name'] = $body['name'];
		$global['link'] = $body['link'];
		$global['picture'] = $body['picture'];
		$global['caption'] = $body['caption'];
		$global['description'] = $body['description'];
	}else{
		$global[$setting['field']] = $setting['value'];
	}	
}


?>