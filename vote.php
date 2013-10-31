<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$user_id = getUserByUid($userData['id']);

if(isset($_GET['pic']) && isset($_GET['user'])){
	if($user_id[0]['id'] != $_GET['user']){
		echo -2;
	}else{
		$userObject = array();
		$userObject['user_id'] = $_GET['user'];
		$userObject['pic_id'] = $_GET['pic'];
		if(insertVote($userObject)){
			echo 1;
		}else{
			echo 0;
		}
	}	
}else{
	echo -1;
}


?>