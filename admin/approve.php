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
foreach($pics_dec as $pic_dec){
$db->misc("UPDATE `photos` SET votes = votes - ".$pic_dec['count'] ." WHERE id = ".$pic_dec['pic_id']);
}
$db->delete("votes","user_id IN ($users_csv)");
$db->delete("photos","user_id IN ($users_csv)");
$db->delete("users","id IN ($users_csv)");
$success_message = "Deleted Successfully";
}else{
$error_message = "Action disabled in demo mode";
}
}
if(isset($_POST['approved_email']) ||isset($_POST['deleted_email'])){
$options = array();
$options['value'] = $_POST['approved_email'];
$db->update('config',$options,"field = 'approved_email'");
$global['approved_email'] = $_POST['approved_email'];
$options = array();
$options['value'] = $_POST['deleted_email'];
$db->update('config',$options,"field = 'deleted_email'");
$global['deleted_email'] = $_POST['deleted_email'];
$success_message = "Updated Successfully";
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
$to  = 'no-replay@appstico.com';
$subject = $_POST['subject'];
$message = $_POST['message'];
$headers  = 'MIME-Version: 1.0'."\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
$headers .= 'From: Photo Contest <no-replay@appstico.com>'."\r\n";
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
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	function approve_photo(id){
		$.ajax({
		  url: \'ajax_approve.php?pic=\'+id,
		  success: function(data) {
			if(data == 1){
				$("#cell_"+id).hide();
				$("#action_disabled").hide();
			}else if(data == -1){
				$("#action_disabled").show();
			}
		  }
		});
	}
	
	function delete_photo(id){
		if (confirm("Are you sure you want to delete?")) {
			$.ajax({
			  url: \'ajax_delete.php?pic=\'+id,
			  success: function(data) {
				if(data == 1){
					$("#cell_"+id).hide();
					$("#action_disabled").hide();
				}else if(data == -1){
					$("#action_disabled").show();
				}
			  }
			});
		}		
	}
	
	function formSubmit()
	{
	document.getElementById(\'email_content\').submit();
	}

</script>


</head>
<body>
<div id="container">
	<div id="content">
		<div id="left-sidebar" >
			<a class="button blue1" href="index.php">SETTINGS</a>
			<a class="button blue1" href="users.php">USERS</a>
			<a class="button blue1" href="export.php">EXPORT</a>
			';if( !$global['auto_approve'] ) echo '<a class="button blue1_active" href="approve.php">APPROVE</a>';;echo '		</div>
		<div id="main" >
			';
if($global['demo']) echo "<div class='info'>Demo Mode: Some functions are disabled</div>";
if(isset($success_message)) echo "<div class='success'>$success_message</div>";
if(isset($error_message)) echo "<div class='error'>$error_message</div>";
;echo '			<div style="display:none;" id="action_disabled" class=\'error\'>Action disabled in demo mode</div>
';
$photosCount = admin_countphotosToApprove();
$limit = 9;
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
$photos = admin_photosToApprove($start,$limit);
;echo '	
	<form action="" method="post" id="email_content" >	
	<h2>Email content</h2>
	<span>Compose emails that are auto send to users when their photo is approved or deleted</span></br>
	<span>You can use HTML</span></br><br>
	<span>Email for Approved photos</span></br>
	<textarea style="width:90%" name="approved_email" >';echo $global['approved_email'];;echo '</textarea></br><br>
	<span>Email for Deleted photos</span></br>
	<textarea style="width:90%" name="deleted_email" >';echo $global['deleted_email'];;echo '</textarea></br>	
	<a onclick="formSubmit()" class="button blue1" href="#">SAVE</a>
	</form></br>
	
	';
if($photos &&count($photos) >0){
;echo '	
	
	
		<table cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr valign="top">
	';
foreach($photos as $photo){
$PhotoUserDetails = getUserById($photo['user_id']);
;echo '
						
						<td id="cell_';echo $photo['id'];;echo '" valign="top" style="float: left;">
							<div class="outer-box">
								<div class="inner-box">
									<a href="image.php?id=';echo $photo['id'];;echo '"><img src="';echo $global['home_link'];;echo 'uploads/small/';echo $photo['filename'];;echo '"></a>
								</div>
								<div class="details-box" >
									<div style="width:150px; font-size:12px;padding: 5px 0;text-align: center;">From:&nbsp;<font color="#21681c">';echo shortenString($PhotoUserDetails[0]['name'],20);;echo '</font></div>
									<div style="text-align:center;width:150px;padding: 5px 0;" >
										<a id="approve_';echo $photo['id'];;echo '" href="#" onclick="approve_photo(';echo $photo['id'];;echo ')" class="vote">Approve</a>
										<a id="delete_';echo $photo['id'];;echo '" href="#" onclick="delete_photo(';echo $photo['id'];;echo ')" class="vote">Delete</a>
									</div>
								</div>
							</div>
						</td>
						
	';
}
;echo '					</tr>
				</tbody>
				</table><br><br>
	';
echo pagination($page,$photosCount,$limit,$adjacents);
}else{
echo "<h3>No Photos to approve.</h3>";
}
;echo '		
		</div>
		<div class="clearfix"><!-- --></div> 
	</div>
   <p style="text-align: center;">Developed by: <a href="http://appstico.com" target="_blank">Appstico.com</a></p><br><br>
</div><!-- container End -->
</body>
</html>'; ?>