<?php 
require_once('auth.php');
require_once('../includes/config.php');
if(isset($_POST['link']) ||isset($_POST['message']) ||isset($_POST['picture']) ||isset($_POST['name']) ||isset($_POST['caption']) ||isset($_POST['caption']) ||isset($_POST['activate']) ){
if(isset($_POST['link']) ||isset($_POST['message']) ||isset($_POST['picture'])){
$body = array();
$body['message'] = $_POST['message'];
$body['name'] = $_POST['name'];
$body['caption'] = $_POST['caption'];
$body['link'] = $_POST['link'];
$body['picture'] = $_POST['picture'];
$body['description'] = $_POST['description'];
$data = array();
$data['field'] = 'body';
$data['value'] = base64_encode(serialize($body));
if( !$global['demo'] ){
$db->update('config',$data,"`field`='body'");
foreach($body as $key=>$value){
$global[$key] = $body[$key];
}
}
$data = array();
$data['field'] = 'auto_post';
if(isset($_POST['activate'])){
$data['value'] = 1;
}
else{
$data['value'] = 0;
}
if( !$global['demo'] ){
$db->update('config',$data,"`field`='auto_post'");
$global['auto_post'] = $data['value'];
$success_message = "Update Successfull";
}else{
$error_message = "Action disabled in demo mode";
}
}else{
$error_message = "Action disabled in demo mode";
}
}
if(isset($_POST['end_date'])){
if(admin_checkDateTime($_POST['end_date'])){
$data = array();
$data['field'] = 'end_date';
$data['value'] = $_POST['end_date'];
if( !$global['demo'] ){
$db->update('config',$data,"`field`='end_date'");
$global['end_date'] = $data['value'];
$success_message = "Update Successfull";
}else{
$error_message = "Update Unsuccessfull";
}
}else{
$error_message = "Dtae / Time format incorrect";
}
}
if(isset($_POST['approve_hidden'])){
$data = array();
$data['field'] = 'auto_approve';
if(isset($_POST['approve'])){
$data['value'] = 1;
}
else{
$data['value'] = 0;
}
if( !$global['demo'] ){
$db->update('config',$data,"`field`='auto_approve'");
$data1 = array();
$data1['approved'] = 1;
$db->update('photos',$data1,"`approved`=0");
$global['auto_approve'] = $data['value'];
$success_message = "Update Successfull";
}else{
$error_message = "Update Unsuccessfull";
}
}
if(isset($_POST['new_end_date'])){
if(admin_checkDateTime($_POST['new_end_date'])){
$data = array();
$data['field'] = 'version';
$data['value'] = $global['version'] +1;
if( !$global['demo'] ){
$db->update('config',$data,"`field`='version'");
$global['version'] = $data['value'];
}
$data = array();
$data['field'] = 'end_date';
$data['value'] = $_POST['new_end_date'];
if( !$global['demo'] ){
$db->update('config',$data,"`field`='end_date'");
$global['end_date'] = $data['value'];
$success_message = "New contest created Successfully";
}else{
$error_message = "Action disabled in demo mode";
}
}else{
$error_message = "Date / Time format incorrect";
}
}
;echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">

<head>

<title>Admin Panel</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="styles/style.css" type="text/css" />

<link rel="stylesheet" href="styles/jquery-ui-1.8.21.custom.css" type="text/css" media="screen" />

<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>

<script src="js/jquery-ui-1.8.21.custom.min.js?v=2" type="text/javascript"></script>

<script src="js/jquery-ui-timepicker-addon.js?v=3" type="text/javascript"></script>

<style type="text/css">.ui-datepicker {font-size: 11px;margin-left:10px}</style>

<script type="text/javascript">

	$(function() {

		$(\'#end_date\').datetimepicker({minDate:0, dateFormat: \'yy-mm-dd\'});

		$(\'#new_end_date\').datetimepicker({minDate:0, dateFormat: \'yy-mm-dd\'});

	});

function form1Submit()

{

document.getElementById(\'end_time\').submit();

}

function form2Submit()

{

document.getElementById(\'new_contest\').submit();

}

function form3Submit()

{

document.getElementById(\'settings\').submit();

}

function form4Submit()

{

document.getElementById(\'auto_approval_form\').submit();

}

</script>

</head>

<body>

<div id="container">

	<div id="content">

		<div id="left-sidebar" >

			<a class="button blue1_active" href="index.php">SETTINGS</a>

			<a class="button blue1" href="users.php">USERS</a>

			<a class="button blue1" href="export.php">EXPORT</a>

			';if( !$global['auto_approve'] ) echo '<a class="button blue1" href="approve.php">APPROVE</a>';;echo '
		</div>

		<div id="main" >

			';
if($global['demo']) echo "<div class='info'>Demo Mode: Some functions are disabled</div>";
if(isset($success_message)) echo "<div class='success'>$success_message</div>";
if(isset($error_message)) echo "<div class='error'>$error_message</div>";
;echo '
			<form action="" method="post" id="end_time" >

			<h2>Contest End Time</h2>

			<span>Set the time when contest should end</span></br>

			<span><b>End Date</b></span></br>

			<input style="width:200px;" type="text" name="end_date" id="end_date" value="';echo $global['end_date'];;echo '" ></br>

			<a onclick="form1Submit()" class="button blue1" href="#">SAVE</a>

			</form>

		

			<form action="" method="post" id="new_contest" >	

			<h2>Start new contest</h2>

			<span>If a contest is live, then will end the contest and start a new one.</span></br>

			<span><b>End Date</b></span></br>

			<input style="width:200px;" type="text" name="new_end_date" id="new_end_date" value="" ></br>			

			<a onclick="form2Submit()" class="button blue1" href="#">START NEW</a>

			</form>

			

			<form action="" method="post" id="auto_approval_form" >	

			<h2>Auto Approve Photos</h2>

			<input type="checkbox" name="approve" ';if($global['auto_approve'] == 1) echo "CHECKED";;echo '>

			<input type="hidden" value="1" name="approve_hidden" >

			<span>Automaticaly approve user photos. All pending approvals will be approved.</span></br>

			<a onclick="form4Submit()" class="button blue1" href="#">SAVE</a>

			</form>

		

			<form action="" method="post" id="settings" >	

			<h2>Auto post to user wall</h2>

			<input type="checkbox" name="activate" ';if($global['auto_post'] == 1) echo "CHECKED";;echo '>

			<span>Activate auto post to user wall when they first use the app</span></br>

			<span><b>Message</b></span></br>

			<input class="field" type="text" name="message" value="';echo $global['message'];;echo '" ></br>

			<span><b>Contest URL</b></span></br>

			<input class="field" type="text" name="link" value="';echo $global['link'];;echo '" ></br>

			<span><b>Contest Title</b></span></br>

			<input class="field" type="text" name="name" value="';echo $global['name'];;echo '" ></br>

			<span><b>Caption</b></span></br>

			<input class="field" type="text" name="caption" value="';echo $global['caption'];;echo '" ></br>			

			<span><b>Thumbnail URL</b></span></br>

			<input class="field" type="text" name="picture" value="';echo $global['picture'];;echo '" ></br>

			<span><b>Contest Description</b></span></br>

			<textarea class="field" rows="4" name="description" >';echo $global['description'];;echo '</textarea></br>

			<a onclick="form3Submit()" class="button blue1" href="#">SAVE</a>

			</form></br>

			<span><b>Explanation of form fields:</b></span></br>

			<img src="images/structure.png" >

		

		</div>

		<div class="clearfix"><!-- --></div> 

	</div>
   <p style="text-align: center;">Developed by: <a href="http://appstico.com" target="_blank">Appstico.com</a></p><br><br>
</div><!-- container End -->

</body>

</html>


'; ?>