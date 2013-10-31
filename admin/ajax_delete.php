<?php 
@session_start();
if(!isset($_SESSION['SESS_MEMBER_ID']) ||($_SESSION['SESS_MEMBER_ID']) != '$^$%^&&^*&^*&') {
echo 0;
}else{
require_once '../includes/config.php';
if($global['demo']){
echo -1;
}else{
if(isset($_GET['pic'])){
$id = $_GET['pic'];
$photoDetails = getPhotoById($id);
$PhotoUserDetails = getUserById($photoDetails[0]['user_id']);
if($db->misc("DELETE FROM photos WHERE id = $id")){
$to  = $PhotoUserDetails[0]['email'];
$subject = "Photo Rejected";
$message = $global['deleted_email'];
$headers  = 'MIME-Version: 1.0'."\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
$headers .= 'From: Photo Contest <no-replay@appstico.com>'."\r\n";
mail($to,$subject,$message,$headers);
echo 1;
}else{
echo 0;
}
}else{
echo 0;
}
}
}; ?>