<?php

require_once('includes/header.php');

require_once('includes/auth.php');

$user_id = getUserByUid($userData['id']);



$pic_id = $_GET['item'];







?>

		<div id="header">

			<div id="headbutton">

				<a class="button blue1" href="top.php?type=General">TOP</a>

				<a class="button blue1" href="gallery.php?page=1">GALLERY</a>

				<?php

				$photos = getPhotosofUser($user_id[0]['id']);

				if($photos && count($photos) > 0){

					echo '<a class="button blue1';

					if($pic_id == $photos[0]['id']) echo '_active';

					echo '" href="details.php?item=' . $photos[0]['id'] . '">MY PHOTO</a>';

				}elseif(strtotime($global['end_date']) > time()){

					echo '<a class="button blue1" href="submit_photo.php">SUBMIT PHOTO</a>';

				}

				?>												

			</div>

		</div>

		<div id="content">

			<div id="contentBody">

<?php

if( !isApproved($pic_id) ) echo "<div class='info'>Image is awaiting approval. You will be notified via email when the image is approved.</div>";

$user_id = getUserByUid($userData['id']);

$photoDetails = getPhotoById($pic_id);

$PhotoUserDetails = getUserById($photoDetails[0]['user_id']);

$next = nextPhoto($pic_id);

$prev = prevPhoto($pic_id);



?>

				<div class="outer-box-details">

					<div class="inner-box-details">

						<img alt="<?php echo $photoDetails[0]['caption']; ?>" src="uploads/medium/<?php echo $photoDetails[0]['filename']; ?>" />					

					</div>

					<div style="font-size: 20px;    margin-top: 5px;    text-align: center;">

						By:&nbsp;<font color="green" ><?php echo $PhotoUserDetails[0]['name']; ?></font>

                    </div>

<?php if( isApproved($pic_id) ) { ?>

                    <div style="text-align:center;padding: 5px 0;margin: 5px 0;" >

										<a id="vote_<?php echo $photoDetails[0]['id']; ?>" href="#" onclick="vote(<?php echo $photoDetails[0]['id']; ?>, <?php echo $user_id[0]['id']; ?>)" class="vote1"><?php if(hasVoted($photoDetails[0]['id'], $user_id[0]['id'])) echo "Voted"; else echo "Vote"; ?></a>

										<a id="share_<?php echo $photoDetails[0]['id']; ?>" href="#" onclick="share(<?php echo $photoDetails[0]['id']; ?>, '<?php echo $photoDetails[0]['filename']; ?>')" class="vote1">Share</a>

										<a id="invite_<?php echo $photoDetails[0]['id']; ?>" href="#" onclick="invite(<?php echo $photoDetails[0]['id']; ?>, '<?php echo $photoDetails[0]['filename']; ?>')" class="vote1">Invite</a>

					</div>

					<div style="font-size: 20px;    margin: 5px 0;    text-align: center;">

						<span>Vote(s): </span>

						<span style="color:green;" id="vote_count_<?php echo $photoDetails[0]['id']; ?>"><?php echo $photoDetails[0]['votes']; ?></span>

					</div>

					<div style="font-size: 12px;    padding-left: 10px;" >

						Photo Promotion Link: <font color="green" ><?php echo $global['app_link']; ?>&amp;app_data=<?php echo $photoDetails[0]['id']; ?></font></br>

						You can also use this link to promote the photo and get more votes.

					</div>

<?php } ?>
				<div class="fb-comments" data-href="<?php echo $global['app_link']; ?>&amp;app_data=<?php echo $photoDetails[0]['id']; ?>" data-num-posts="5" data-width="700"></div>
				</div>

			</div>		

			<div id="footer">				

				<a href="rules.html" class="footer-links" target="_blank" >Contest Rules</a>

				<a href="privacy.html" class="footer-links" target="_blank" >Privacy Policy</a>

			</div>

		</div>

       <?php



require_once('includes/footer.php');

?>