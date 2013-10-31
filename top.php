<?php
require_once('includes/header.php');
require_once('includes/auth.php');
$user_id = getUserByUid($userData['id']);
?>      
		<div id="header"> 
			<div id="headbutton">
				<a class="button blue1_active" href="top.php">TOP</a>
				<a class="button blue1" href="gallery.php?page=1">GALLERY</a>
				<?php
				$photos = getPhotosofUser($user_id[0]['id']);
				if($photos && count($photos) > 0){
					echo '<a class="button blue1" href="details.php?item=' . $photos[0]['id'] . '">MY PHOTO</a>';
				}elseif(strtotime($global['end_date']) > time()){
					echo '<a class="button blue1" href="submit_photo.php">SUBMIT PHOTO</a>';
				}
				?>
												
			</div>
		</div>
		<div id="content">
			<div id="contentBody">
				
<?php
	$user_id = getUserByUid($userData['id']);
	$photos = topPhotos();

	
	if($photos && count($photos) > 0){
	?>
		<table cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr valign="top">
	<?php
	foreach($photos as $photo){
		$PhotoUserDetails = getUserById($photo['user_id']);
	?>		
						<td valign="top" style="float: left;">
							<div class="outer-box">
								<div class="inner-box">
									<a href="details.php?item=<?php echo $photo['id']; ?>"><img src="uploads/small/<?php echo $photo['filename']; ?>"></a>
								</div>
								<div class="details-box" >
									<div style="width:150px; font-size:12px;padding: 5px 0;text-align: center;">By:&nbsp;<font color="green"><?php echo shortenString($PhotoUserDetails[0]['name'], 20); ?></font></div>
									<div style="text-align:center;width:150px;padding: 5px 0;" >
										<a id="vote_<?php echo $photo['id']; ?>" href="#" onclick="vote(<?php echo $photo['id']; ?>, <?php echo $user_id[0]['id']; ?>)" class="vote"><?php if(hasVoted($photo['id'], $user_id[0]['id'])) echo "Voted"; else echo "Vote"; ?></a>
										<a id="share_<?php echo $photo['id']; ?>" href="#" onclick="share(<?php echo $photo['id']; ?>, '<?php echo $photo['filename']; ?>')" class="vote">Share</a>
										<a id="invite_<?php echo $photo['id']; ?>" href="#" onclick="invite(<?php echo $photo['id']; ?>, '<?php echo $photo['filename']; ?>')" class="vote">Invite</a>
									</div>
									<div style="width:150px; font-size:12px; padding: 5px 0;text-align: center;">
										<span>Vote(s):&nbsp;</span>
										<span id="vote_count_<?php echo $photo['id']; ?>" style="padding: 2px 5px 2px 5px;color:green;" ><?php echo $photo['votes']; ?></span>
									</div>
								</div>
							</div>
						</td>			

	<?php
	}
	?>
					</tr>
				</tbody>
				</table>
	<?php
	}else{
		echo "<h2>No Photos Yet !!</h2>";
	}	
	?>			
	
			</div>
			<div id="footer">				
				<a href="rules.html" class="footer-links" target="_blank" >Contest Rules</a>
				<a href="privacy.html" class="footer-links" target="_blank" >Privacy Policy</a>
			</div>
		</div>
       <?php

require_once('includes/footer.php');
?>