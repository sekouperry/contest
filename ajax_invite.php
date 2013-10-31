<?php
require_once('includes/config.php');
require_once('includes/auth.php');
require_once('includes/functions.php');

if(isset($_GET['request']) && isset($_GET['ids']) && isset($_GET['photo_id']) && isset($_GET['photo_url'])){
	$request_id = $_GET['request'];
	$ids = explode("-",$_GET['ids']);
	$photo_id = $_GET['photo_id'];
	$photo_url = $_GET['photo_url'];
					
	$body = array();
	$body['message'] = $global['message'];
	$body['name'] = $global['name'];
	$body['link'] = $global['home_link'] . '?app_data=' . $photo_id;
	$body['picture'] = $global['home_link'] . 'uploads/small/' . $photo_url;
	$body['caption'] = $global['caption'];
	$body['description'] = $global['description'];
	
	$batchPost = array();
	
	foreach($ids as $id){
	$requestArray = array();
	$requestArray['request_id'] = $request_id;
	$requestArray['fb_id'] = $id;
	$requestArray['photo_id'] = $photo_id;	
	echo insertRequest($requestArray);
	
	$batchPost[] = array(
	    'method' => 'POST',
	    'relative_url' => "/$id/feed",
	    'body' => http_build_query($body) );
	}
	
	try{
		$facebook->api('?batch='.urlencode(json_encode($batchPost)), 'POST');
	}catch (FacebookApiException $e) {
	}
}else{
	return 0;
}


?>