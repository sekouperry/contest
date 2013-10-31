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
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	function approve_photo(id){
		$.ajax({
		  url: \'ajax_approve.php?pic=\'+id,
		  success: function(data) {
			if(data == 1){
				window.location.href = "approve.php";
				exit;
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
					window.location.href = "approve.php";
					exit;
				}else if(data == -1){
					$("#action_disabled").show();
				}
			  }
			});
		}		
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
		<div id="main" style="text-align:center;" >
			';
if($global['demo']) echo "<div class='info'>Demo Mode: Some functions are disabled</div>";
if(isset($success_message)) echo "<div class='success'>$success_message</div>";
if(isset($error_message)) echo "<div class='error'>$error_message</div>";
;echo '			<div style="display:none;" id="action_disabled" class=\'error\'>Action disabled in demo mode</div>
';
$photo = getPhotoById($id);
$owner = getUserById($photo[0]['user_id']);
;echo '	<img width=\'560px\' src=\'';echo $global['home_link'];;echo 'uploads/medium/';echo $photo[0]['filename'];;echo '\' /></br>
	<h3>Uploaded By: ';echo $owner[0]['name'];;echo '</h3>
	<a class="button blue1" onclick="approve_photo(';echo $photo[0]['id'];;echo ')" >Aprove</a>
	<a class="button blue1" onclick="delete_photo(';echo $photo[0]['id'];;echo ')" >Delete</a>
	
		

		
		</div>
		<div class="clearfix"><!-- --></div> 
	</div>
   
</div><!-- container End -->
</body>
</html>

'; ?>