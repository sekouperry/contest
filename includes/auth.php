<?php
require_once 'config.php';

$facebook = new Facebook(array(
    'appId' =>  $global['app_id'] ,
    'secret' => $global['app_secret'],
));

if(isset($data['app_data'])){
	$redirectUri = $global['app_link'] . "&app_data=" . $data['app_data'];
}else{
	$redirectUri = $global['app_link'];
}

$login_url = array(
								'canvas'    => 1,
								'fbconnect' => 0,
								'redirect_uri' => $redirectUri,
								'scope' => $global['app_perms']  
					);

//echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
//echo "<pre>";
//print_r($_COOKIE);
//echo "</pre>";


$userId = $facebook->getUser();

if($userId){//echo "xxxxxxxxxx$userId" . "xxxxxxxxxxxx";
	try{
		$userData = $facebook->api('/me');
	}catch(FacebookApiException $e){//echo "yyyyyyyyyy$userId" . "yyyyyyyyyy";
	if(isset($_COOKIE["fbm_" . $global['app_id']])){
		$domain_cookie = str_replace("base_domain=", "", $_COOKIE["fbm_" . $global['app_id']]);
		setcookie("fbsr_" . $global['app_id'], '', time()-75000, "/", $domain_cookie);
		setcookie("fbm_" . $global['app_id'], '', time()-75000, "/", $domain_cookie);
	}elseif(isset($_COOKIE["fbsr_" . $global['app_id']])){
		$domain_cookie = str_replace("base_domain=", "", $_COOKIE["fbsr_" . $global['app_id']]);
		
		//echo "<pre>";
		//print_r($domain_cookie);
		//echo "</pre>";
		setcookie("fbsr_" . $global['app_id'], '', time()-75000);
		setcookie("fbm_" . $global['app_id'], '', time()-75000);
		//setcookie("fbsr_" . $global['app_id'], '', time()-75000, "/", $domain_cookie);
		//setcookie("fbm_" . $global['app_id'], '', time()-75000, "/", $domain_cookie);
		
	}
		
		$facebook->destroySession();
		$userId = NULL;
	}
}


if(!($userId))
{
	$loginUrl = $facebook->getLoginUrl($login_url);
	echo "<script>top.location.href = '".$loginUrl."';</script>";
	//echo "<a href='$loginUrl' >dstgdfgfdhg</a>";
	exit();
	//header ("Location: ". $loginUrl);
	//exit;
}


	$extendedAccessToken = $facebook->getExtendedAccessToken();
	$facebook->setAccessToken($extendedAccessToken);
	
	
	try{
		$userData = $facebook->api('/me');
	}catch(FacebookApiException $e){
	}

	if($userData)
	{
		$userDbData = userExist($userData['id']);
		if(! $userDbData){
			createUser($userData, $facebook->getAccessToken());
			if($global['auto_post']){
				try{
					$publishStream = $facebook->api("/" . $userData['id'] . "/feed", 'post', array(
						'message'		=> $global['message'],
						'link'			=> $global['home_link'],
						'picture'		=> $global['picture'],
						'name'			=> $global['name'],
						'caption'		=> $global['caption'],
						'description'	=> $global['description'],
						));
				}catch(FacebookApiException $e){
				}	
			}
					
		}else
		{
			$option['access_token'] = $facebook->getAccessToken();
			updateUser($option, $userDbData[0]['id'], $userDbData[0]['uid']);
		}
	}


?>