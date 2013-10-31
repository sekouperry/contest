<?php 
require_once('../includes/config.php');
@session_start();
$errflag = false;
if(isset($_POST['submit'])){
if($_POST['password'] == '') {
$errmsg_arr = 'Password missing';
$errflag = true;
}else{
if($_POST['password'] == $global['admin-password']){
$_SESSION['SESS_MEMBER_ID'] = "$^$%^&&^*&^*&";
session_write_close();
header("location: index.php");
exit();
}else{
$errmsg_arr = 'Incorrect Password';
$errflag = true;
}
}
}
;echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">

<head>

<title>Admin Panel</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="styles/style.css" type="text/css" />

</head>

<body>

<div id="container">

	<div id="content">

		<p>&nbsp;</p>

		<form id="loginForm" name="loginForm" method="post" action="" style="margin-top:100px;" >

		  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">

		  	<tr>

		      <td width="112"></td>

		      <td width="188"><span>';if($errflag) echo $errmsg_arr;;echo '</span></td>

		    </tr>

		    <tr>

		      <td><b>Password</b></td>

		      <td><input name="password" type="password" class="textfield" id="password" /></td>

		    </tr>

		    <tr>

		      <td>&nbsp;</td>

		      <td><input class="button blue1" type="submit" name="submit" value="Login" style="margin-left:0px;width:80px;height:25px;padding: 3px 0 5px;" /></td>

		    </tr>

		  </table>

		</form>

	</div>

   <br><br><br><p style="text-align: center;">Developed by: <a href="http://appstico.com" target="_blank">Appstico.com</a></p><br><br>
<p style="text-align: center;"><a href="http://appstico.com/" target="_blank"><strong>Facebook Photo Contest</strong></a></p>
</div><!-- container End -->

</body>

</html>'; ?>