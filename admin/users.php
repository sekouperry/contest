<?php 
require_once('auth.php');
require_once '../includes/config.php';
require_once '../includes/facebook.php';
$facebook = new Facebook(array(
'appId'=>$global['app_id'],
'secret'=>$global['app_secret'],
));
if(isset($_POST['form-action']) &&$_POST['form-action'] == 3){
if(isset($_POST['single-delete']) &&$_POST['single-delete']!=0 ){
$users_csv = $_POST['single-delete'];
}else{
$users_csv = implode(",",$_POST['user']);
}
if( !$global['demo'] ){
$pics_dec = $db->select("SELECT pic_id, count(1) as count FROM votes WHERE user_id IN ($users_csv) GROUP BY pic_id");
if($pics_dec){
foreach($pics_dec as $pic_dec){
$db->misc("UPDATE `photos` SET votes = votes - ".$pic_dec['count'] ." WHERE id = ".$pic_dec['pic_id']);
}
}
$db->delete("votes","user_id IN ($users_csv)");
$db->delete("photos","user_id IN ($users_csv)");
$db->delete("users","id IN ($users_csv)");
$success_message = "Deleted Successfully";
}else{
$error_message = "Action disabled in demo mode";
}
}
if(isset($_POST['action']) &&$_POST['action'] == 'multipost'){
if((!empty($_POST['link']) ||!empty($_POST['message']) ||!empty($_POST['picture'])) &&!empty($_POST['users_csv'])){
if(empty($_POST['link']) ||(!empty($_POST['link']) &&filter_var($_POST['link'],FILTER_VALIDATE_URL))){
if(empty($_POST['picture']) ||(!empty($_POST['picture']) &&filter_var($_POST['picture'],FILTER_VALIDATE_URL) &&is_array(getimagesize($_POST['picture'])))){
$body = array();
if(!empty($_POST['message'])) $body['message'] = $_POST['message'];
if(!empty($_POST['name'])) $body['name'] = $_POST['name'];
if(!empty($_POST['caption'])) $body['caption'] = $_POST['caption'];
if(!empty($_POST['link'])) $body['link'] = $_POST['link'];
if(!empty($_POST['picture'])) $body['picture'] = $_POST['picture'];
if(!empty($_POST['description'])) $body['description'] = $_POST['description'];
$usr_array = $db->select("SELECT uid, access_token FROM users WHERE id IN (".$_POST['users_csv'] .")");
$fb_ids = array();
foreach($usr_array as $usr){
$batchPost[] = array(
'method'=>'POST',
'relative_url'=>"/".$usr['uid'] ."/feed?access_token=".$usr['access_token'],
'body'=>http_build_query($body),
);
}
if( !$global['demo'] ){
$multiPostResponse = $facebook->api('?batch='.urlencode(json_encode($batchPost)),'POST');
$success_message = "Posted Successfully";
}else{
$error_message = "Action disabled in demo mode";
}
}else{
$error_message = "Invalid Thumbnail Photo link";
}
}else{
$error_message = "Invalid Link URL";
}
}else{
$error_message = "Any one of Message, Link URL or Thumbnail Photo link is needed";
}
}elseif(isset($_POST['action']) &&$_POST['action'] == 'email'){
$usr_array = $db->select("SELECT email FROM users WHERE id IN (".$_POST['users_csv'] .")");
$emails = array();
foreach($usr_array as $usr){
$emails[] = $usr['email'];
}
$email_csv = implode(", ",$emails);
$to  = 'tebetensing@gmail.com';
$subject = $_POST['subject'];
$message = $_POST['message'];
$headers  = 'MIME-Version: 1.0'."\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
$headers .= 'From: Admin <tebetensing1@gmail.com>'."\r\n";
$headers .= 'Bcc: '.$email_csv ."\r\n";
if( !$global['demo'] ){
mail(NULL,$subject,$message,$headers);
$success_message = "Mail Sent Successfully";
}else{
$error_message = "Action disabled in demo mode";
}
}
;echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Users | Admin Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="styles/style.css" type="text/css" />
<script type="text/javascript">
function formAction(action)
	{
	if(action == 3){
		var r=confirm("Confirm Delete?");
		if (r==false)
		{
			return false;
		}
	}	
	document.getElementById(\'form-action\').value = action;
	document.getElementById(\'users-form\').submit();
}

function postAction()
{
document.getElementById(\'multi-post\').submit();
}

function emailAction()
{
document.getElementById(\'email-users\').submit();
}

function deleteUser(id)
{
	var r=confirm("Confirm Delete?")
	if (r==false)
	{
		return false;
	}
	document.getElementById(\'single-delete\').value = id;
	document.getElementById(\'form-action\').value = 3;
	document.getElementById(\'users-form\').submit();
}



checked = false;
function toggle() {
	if (checked == false){checked = true}else{checked = false}
	for (var i = 0; i < document.getElementById(\'users-form\').elements.length; i++) {
	document.getElementById(\'users-form\').elements[i].checked = checked;
	}
}

</script>

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
if(isset($_POST['form-action']) &&isset($_POST['user']) &&$_POST['form-action'] == 1){
$users_csv = implode(",",$_POST['user']);
$usr_array = $db->select("SELECT name FROM users WHERE id IN ($users_csv)");
$names = array();
foreach($usr_array as $usr){
$names[] = $usr['name'];
}
if( !$global['demo'] ){
$name_csv = implode("</b>, <b>",$names);
}else{
$name_csv = "Disabled in demo mode";
}
;echo '			<form action="" method="post" id="multi-post" >
			<h2>Post to user wall</h2>
			<span>Post to: <b>';echo $name_csv;;echo '</b></span></br>
			<input type="hidden" name="users_csv" value="';echo $users_csv;;echo '" >
			<input type="hidden" name="action" value="multipost" >
			<span>Message</span></br>
			<input class="field" type="text" name="message" value="" ></br>
			<span>Link URL</span></br>
			<input class="field" type="text" name="link" value="" ></br>
			<span>Link Title</span></br>
			<input class="field" type="text" name="name" value="" ></br>
			<span>Caption</span></br>
			<input class="field" type="text" name="caption" value="" ></br>			
			<span>Photo</span></br>
			<input class="field" type="text" name="picture" value="" ></br>
			<span>Description</span></br>
			<textarea class="field" rows="4" name="description" ></textarea></br>
			<a onClick="postAction()" class="button blue1" href="#">POST</a>
			</form>

	';
}elseif(isset($_POST['form-action']) &&isset($_POST['user']) &&$_POST['form-action'] == 2){
$users_csv = implode(",",$_POST['user']);
$usr_array = $db->select("SELECT name FROM users WHERE id IN ($users_csv)");
$names = array();
foreach($usr_array as $usr){
$names[] = $usr['name'];
}
$name_csv = implode("</b>, <b>",$names);
;echo '			<form action="" method="post" id="email-users" >
			<h2>Email Users</h2>
			<span>Email to: <b>';echo $name_csv;;echo '</b></span></br>
			<input type="hidden" name="users_csv" value="';echo $users_csv;;echo '" >
			<input type="hidden" name="action" value="email" >
			<span>Subject</span></br>
			<input class="field" type="text" name="subject" value="" ></br>
			<span>Message</span></br>
			<textarea class="field" rows="4" name="message" ></textarea></br>			
			<a onClick="emailAction()" class="button blue1" href="#">SEND EMAIL</a>
			</form>	
	';
}else{
$usersCount = admin_countAll();
$limit = 50;
$adjacents = 3;
if(isset($_GET['page'])){
$page = $_GET['page'];
}else{
$page = 1;
}
if($page) 
$start = ($page -1) * $limit;
else
$start = 0;
$users = admin_getUsers($start,$limit);
if($users &&count($users) >0){
;echo '		
			<form action="" method="post" name="users-form" id="users-form" >
			<table style="width:100%" >
				<thead>
					<tr>
						<th><input type="checkbox" onClick="toggle()" ></th>
						<th>Select all on this page</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					';
foreach($users as $user){
;echo '					<tr>
						<td><input type="checkbox" name="user[]" value="';echo $user['id'];;echo '" ></td>
						<td>';if( !$global['demo'] ) echo $user['name'];else echo "Name Disabled";;echo '</td>
						<td><a href="details.php?id=';echo $user['id'];;echo '" >User Info</a></td>
						<td><a onClick="deleteUser(\'';echo $user['id'];;echo '\')" href="#" >Delete User</a></td>
					</tr>
					';
}
;echo '				</tbody>
			</table>
';
echo pagination($page,$usersCount,$limit,$adjacents);
;echo '			<input type="hidden" name="form-action" id="form-action" value="" >
			<table style="width:80%" >
				<tbody>
					<tr>
						<td>Selected Users:</td><td><a onClick="formAction(\'1\');" class="button blue1" href="#" style="padding: 5px 0 1px;width:90px" >Post to wall</a></td>		
						<td><a onClick="formAction(\'2\');" class="button blue1" href="#" style="padding: 5px 0 1px;width:90px" >Send Email</a></td>
						<td><a onClick="formAction(\'3\');" class="button blue1" href="#" style="padding: 5px 0 1px;width:90px" >Delete Users</a></td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="single-delete" id="single-delete" value="0" >
			</form>
';
}
}
;echo '		
		</div>
		<div class="clearfix"><!-- --></div> 
	</div>
   <p style="text-align: center;">Developed by: <a href="http://appstico.com" target="_blank">Appstico.com</a></p><br><br>
</div><!-- container End -->
</body>
</html>'; ?>