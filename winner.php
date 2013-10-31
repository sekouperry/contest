<?php

require_once('includes/header.php');

require_once('includes/auth.php');

$user_id = getUserByUid($userData['id']);



if(strtotime($global['end_date']) <= time()){

	$contest_ended = true;

	$top_photo = topPhotos();

	$pic_id = $top_photo[0]['id'];

}





?>

		<div id="header">

		<?php if($contest_ended) { ?>

			<div id="conetst_ended" style="text-align:center;">

				<h2 style="margin-bottom: 0px; margin-top: 0px;">CONTEST ENDED</h2>

				<h2 style="margin-bottom: 0px; margin-top: -5px;">Winner photo</h2>

			</div>

				<?php } ?>

		</div>

		<div id="content">

			<div id="contentBody">

<?php

$user_id = getUserByUid($userData['id']);

$photoDetails = getPhotoById($pic_id);

$PhotoUserDetails = getUserById($photoDetails[0]['user_id']);



?>

				<div class="outer-box-details">

					<div class="inner-box-details">

						<img alt="<?php echo $photoDetails[0]['caption']; ?>" src="uploads/medium/<?php echo $photoDetails[0]['filename']; ?>" />					

					</div>

					<div style="font-size: 20px;    margin-top: 5px;    text-align: center;">

						By:&nbsp;<font color="green" ><?php echo $PhotoUserDetails[0]['name']; ?></font>

                    </div>

                    <div style="text-align:center;padding: 5px 0;margin: 5px 0;" >

						<!--<a id="vote_<?php echo $photoDetails[0]['id']; ?>" href="#" onclick="vote(<?php echo $photoDetails[0]['id']; ?>, <?php echo $user_id[0]['id']; ?>)" class="vote1"><?php if(hasVoted($photoDetails[0]['id'], $user_id[0]['id'])) echo "Voted"; else echo "Vote"; ?></a>-->

										<a id="share_<?php echo $photoDetails[0]['id']; ?>" href="#" onclick="share(<?php echo $photoDetails[0]['id']; ?>, '<?php echo $photoDetails[0]['filename']; ?>')" class="vote1">Share</a>

										<!--<a id="invite_<?php echo $photoDetails[0]['id']; ?>" href="#" onclick="invite(<?php echo $photoDetails[0]['id']; ?>, '<?php echo $photoDetails[0]['filename']; ?>')" class="vote1">Invite</a>-->

					</div>

					<div style="font-size: 20px;    margin: 5px 0;    text-align: center;">

						<span>Vote(s): </span>

						<span style="color:green;" id="vote_count_<?php echo $photoDetails[0]['id']; ?>"><?php echo $photoDetails[0]['votes']; ?></span>

					</div>

					<!--<div style="font-size: 12px;    padding-left: 10px;" >

						Photo Promotion Link: <font color="green" ><?php echo $global['app_link']; ?>&amp;app_data=<?php echo $photoDetails[0]['id']; ?></font></br>

						You can also use this link to promote the photo and get more votes.

					</div>-->
				<div class="fb-comments" data-href="<?php echo $global['app_link']; ?>&amp;app_data=<?php echo $photoDetails[0]['id']; ?>" data-num-posts="5" data-width="700"></div>
				</div>

			</div>		

			<div id="footer">				

				<a href="#" class="footer-links" target="_blank" >Contest Rules</a>

				<a href="#" class="footer-links" target="_blank" >Privacy Policy</a>

			</div>

		</div>

       <?php



require_once('includes/footer.php');

?>