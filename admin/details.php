<?php 
require_once('auth.php');
require_once '../includes/config.php';
if(isset($_GET['id'])){
$id = $_GET['id'];
}else{
;echo '	<script type="text/javascript">
	<!--
	window.location = "index.php"
	//-->
	</script>
';
}
;echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>User Details | Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="styles/style.css" type="text/css" />

</head>
<body>
<div id="container">
	<div id="content">
		<div id="left-sidebar" >
			<a class="button blue1" href="index.php">SETTINGS</a>
			<a class="button blue1_active" href="users.php">USERS</a>
			<a class="button blue1" href="export.php">EXPORT</a>
			';if( !$global['auto_approve'] ) echo '<a class="button blue1" href="approve.php">APPROVE</a>';;echo '		</div>
		<div id="main" >
			';
if($global['demo']) echo "<div class='info'>Demo Mode: Some functions are disabled</div>";
if(isset($success_message)) echo "<div class='success'>$success_message</div>";
if(isset($error_message)) echo "<div class='error'>$error_message</div>";
;echo '';
$user = getUserById($id);
;echo '			<h2>';echo $user[0]['name'];;echo '</h2>
			<table style="width:100%" >
				<tbody>
					<tr>
						<td>Name</td>
						<td><input class="field" type="text" value="';if( !$global['demo'] ) echo $user[0]['name'];else echo "Disabled in demo mode";;echo '" readonly="readonly" ></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><input class="field" type="text" value="';if( !$global['demo'] ) echo $user[0]['email'];else echo "Disabled in demo mode";;echo '" readonly="readonly" ></td>
					</tr>
					<tr>
						<td style="width:20%" >Facebook ID</td>
						<td><input class="field" type="text" value="';if( !$global['demo'] ) echo $user[0]['uid'];else echo "Disabled in demo mode";;echo '" readonly="readonly" ></td>
					</tr>					
					<tr>
						<td>Access Token</td>
						<td><input class="field" type="text" value="';if( !$global['demo'] ) echo $user[0]['access_token'];else echo "Disabled in demo mode";;echo '" readonly="readonly" ></td>
					</tr>
				</tbody>
			</table>			

		
		</div>
		<div class="clearfix"><!-- --></div> 
	</div>
   <p style="text-align: center;">Developed by: <a href="http://appstico.com" target="_blank">Appstico.com</a></p><br><br>
</div><!-- container End -->
</body>
</html>
'; ?>