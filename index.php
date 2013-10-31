<?php 
require_once('includes/config.php');
if(empty($_REQUEST["signed_request"])) {
if (isset($_GET['request_ids'])) {
header ('Location: '.$global['app_link'] .'&app_data=req'.$_GET['request_ids']);
exit;
}else{
if(isset($_GET['app_data'])){
header ('Location: '.$global['app_link'] .'&app_data='.$_GET['app_data']);
}else{
header ('Location: '.$global['app_link']);
}
exit;
}
echo "this page was not accessed through a Facebook page tab";
}else {
$data = parse_signed_request($_REQUEST["signed_request"],$global['app_secret']);
if( !isset($data["page"]) ){
if (isset($_GET['request_ids'])){
echo "<script>top.location.href = '".$global['app_link'] ."&app_data=req".$_GET['request_ids'] ."';</script>";
}else{
echo "<script>top.location.href = '".$global['app_link'] ."';</script>";
}
exit();
}
if (empty($data["page"]["liked"])) {
;echo '<div style="position:absolute;top:0;"><img src="images/landing.jpg" /></div>';
}else {
require_once('includes/auth.php');
if(isset($data['app_data'])){
if(substr($data['app_data'],0,3) == 'req'){
$request = substr($data['app_data'],3);
$request_ids = explode(',',$request);
foreach ($request_ids as $request_id)
{
$full_request_id = $request_id .'_'.$userData['id'];
try {
$delete_success = $facebook->api("/$full_request_id",'DELETE');
}catch (FacebookApiException $e) {
}
$result = $db->select("SELECT photo_id FROM requests WHERE request_id='$request_id' AND fb_id='".$userData['id'] ."'");
if($result){
$photo = $result[0]['photo_id'];
$modifyArray = array();
$modifyArray['deleted'] = 1;
$db->update('requests',$modifyArray,"request_id='$request_id' AND fb_id='".$userData['id'] ."'");
}else{
$photo = 0;
}
}
}else{
$photo = $data['app_data'];
}
if(isset($photo) &&$photo != 0){
echo "<script>self.location.href = 'details.php?item=".$photo ."';</script>";
}elseif(strtotime($global['end_date']) <= time()){
echo "<script>self.location.href = 'winner.php';</script>";
}else{
echo "<script>self.location.href = 'gallery.php';</script>";
}
}elseif(strtotime($global['end_date']) <= time()){
echo "<script>self.location.href = 'winner.php';</script>";
}else{
echo "<script>self.location.href = 'gallery.php';</script>";
}
exit();
}
}; ?>