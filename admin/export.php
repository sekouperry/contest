<?php 
require_once('auth.php');
require_once('../includes/config.php');
$data = array();
foreach($_POST as $key=>$value){
$data[] = $key;
}
if(count($data)>0){
$query = "SELECT ".implode(",",$data) ." FROM users WHERE 1";
if( !$global['demo'] ){
admin_csvexport($query);
}else{
$error_message = "Action disabled in demo mode";
}
}
;echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Page Title Here</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="styles/style.css" type="text/css" />
<script type="text/javascript">
function exportSubmit()
{
document.getElementById(\'export\').submit();
}
</script>
</head>
<body>
<div id="container">
	<div id="content">
		<div id="left-sidebar" >
			<a class="button blue1" href="index.php">SETTINGS</a>
			<a class="button blue1" href="users.php">USERS</a>
			<a class="button blue1_active" href="export.php">EXPORT</a>
			';if( !$global['auto_approve'] ) echo '<a class="button blue1" href="approve.php">APPROVE</a>';;echo '		</div>
		<div id="main" >
			';
if($global['demo']) echo "<div class='info'>Demo Mode: Some functions are disabled</div>";
if(isset($success_message)) echo "<div class='success'>$success_message</div>";
if(isset($error_message)) echo "<div class='error'>$error_message</div>";
;echo '			<h2>Please select the data you want to export:</h2>
			<form action="" method="post" id="export" >
			<input type="checkbox" name="name" >
			<span>Name</span></br>
			<input type="checkbox" name="email" >
			<span>Email</span></br>
			<input type="checkbox" name="uid" >
			<span>Facebook ID</span></br>			
			<input type="checkbox" name="access_token" >
			<span>Access Token</span></br>
			<a onclick="exportSubmit()" class="button blue1" href="#">EXPORT</a>
			</form>
		
		</div>
		<div class="clearfix"><!-- --></div> 
	</div>
   <p style="text-align: center;">Developed by: <a href="http://appstico.com" target="_blank">Appstico.com</a></p><br><br>
</div><!-- container End -->
</body>
</html>
'; ?>