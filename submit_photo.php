<?php
require_once('includes/header.php');

require_once('includes/auth.php');
$user_id = getUserByUid($userData['id']);

define ("MAX_SIZE",10240);

$errors=0;

$newfilename = genRandomString();
while(filenameExist($newfilename)){
	$newfilename = genRandomString();
}
 

 
if(isset($_FILES["photo"]))
{
	$image = $_FILES["photo"]["name"];
	$uploadedfile = $_FILES['photo']['tmp_name'];

	if ($image) 
	{
		$filename = stripslashes($_FILES['photo']['name']);
		$extension = getExtension($filename);
		$extension = strtolower($extension);
		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
		{
			$errorMsg = '<font color="red">Unknown Image extension !!</font>';
			$errors=1;
		}
		else
		{
			$size=filesize($_FILES['photo']['tmp_name']); 
			if ($size > MAX_SIZE*1024)
			{
				$errorMsg = '<font color="red">You have exceeded the size limit !!</font>';
				$errors=1;
			}else{
				if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $_FILES['photo']['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);
				}
				else if($extension=="png")
				{
					$uploadedfile = $_FILES['photo']['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);
				}
				else 
				{
					$src = imagecreatefromgif($uploadedfile);
				}
				
				move_uploaded_file($_FILES['photo']['tmp_name'],"uploads/$newfilename.$extension");
	 
				$uploadedfile = "uploads/$newfilename.$extension";
				
				list($width,$height)=getimagesize($uploadedfile);
				
				if((($width/$height) >= (700/550)) && $width < 700){
					@unlink($uploadedfile);
					$errorMsg = '<font color="red">The image must be atleast 700px wide or 550px high !!</font>';
					$errors=1;
				}elseif((($width/$height) <= (700/550)) && $height < 550){
					@unlink($uploadedfile);
					$errorMsg = '<font color="red">The image must be atleast 700px wide or 550px high !!</font>';
					$errors=1;
				}else{
					if(($width/$height) > (700/550)){
						$newwidth=700;
						$newheight=($height/$width)*$newwidth;
					}else{
						$newheight=550;
						$newwidth=($width/$height)*$newheight;
					}
					
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					
								
					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);
					
		
					$filename1 = "uploads/medium/$newfilename.jpg";
					$filename2 = "uploads/small/$newfilename.jpg";
					
					imagejpeg($tmp,$filename1,90);
					
					square_crop($filename1, $filename2);
					
		
					
					imagedestroy($src);
					imagedestroy($tmp);
				}
			}
		}
	}
}
//If no errors registred, print the success message

	if(isset($_POST['submit']) && !isset($errorMsg))
	{
		$imgurl_check1 = "uploads/$newfilename.$extension";
		$imgurl_check2 = "uploads/medium/$newfilename.jpg";
		$imgurl_check3 = "uploads/small/$newfilename.jpg";
		if (!is_array(@getimagesize($imgurl_check1)) || !is_array(@getimagesize($imgurl_check2)) || !is_array(@getimagesize($imgurl_check3)))
		{
			@unlink($imgurl_check1);
			@unlink($imgurl_check2);
			@unlink($imgurl_check3);
			$uploadFailed = true;
			$errorMsg = '<font color="red">Image Upload failed !!</font>';
		}else{
			$user_details = getUserByUid($userData['id']);
			$photoArray = array();
			$photoArray['user_id'] = $user_details[0]['id'];
			$photoArray['filename'] = "$newfilename.jpg";
			$photoArray['caption'] = '';
			$photoArray['description'] = '';
			$photoArray['orig_ext'] = $extension;
			$newId = insertPhoto($photoArray);
			$uploadSuccess = true;
			$errorMsg = '<font color="green">Image Upload Successfull !!</font>';
			echo "<script>self.location.href = 'details.php?item=$newId';</script>";
			exit();
		}			
	}


?>
<script type="text/javascript">
var SITE = SITE || {};

SITE.fileInputs = function() {
  var $this = $(this),
      $val = $this.val(),
      valArray = $val.split('\\'),
      newVal = valArray[valArray.length-1],
      $button = $this.siblings('.button'),
      $fakeFile = $this.siblings('.file-holder');
  if(newVal !== '') {
    $button.text('Photo Chosen');
    if($fakeFile.length === 0) {
      $button.after('<span class="file-holder">' + newVal + '</span>');
    } else {
      $fakeFile.text(newVal);
    }
  }
};

$(document).ready(function() {
  $('.file-wrapper input[type=file]').bind('change focus click', SITE.fileInputs);
  
  $('#photo_form').submit(function(){			
		return submitForm();
    });
  
});

function submitForm()
{
	var pass = true;
   /* if($("#caption").val().length == 0)
	{
		pass = false;
		$("#caption").css("borderColor","red");
		$("#error-box").text('Fill Missing Fields !!');
	}else{
		$("#caption").css("borderColor","#171717 #333333");
	}
	
	if($("#description").val().length == 0)
	{
		pass = false;
		$("#description").css("borderColor","red");
		$("#error-box").text('Fill Missing Fields !!');
	}else{
		$("#description").css("borderColor","#171717 #333333");
	}*/
	
	if($("#photo").val().length == 0)
	{
		pass = false;
		$("#photo-box").css("borderColor","red");
		$("#error-box").text('Please select a photo !!');
	}else{
		$("#photo-box").css({'border-bottom': '0px solid #161712', 'border-top': '0px solid #262626'});
		
	}

		
    return pass;
}


</script>
		<div id="header"> 
			<div id="headbutton">
				<a class="button blue1" href="top.php">TOP</a>
				<a class="button blue1" href="gallery.php?page=1">GALLERY</a>
				<?php
				$photos = getPhotosofUser($user_id[0]['id']);
				if($photos && count($photos) > 0){
					echo '<a class="button blue1" href="details.php?item=' . $photos[0]['id'] . '">MY PHOTO</a>';
				}elseif(strtotime($global['end_date']) > time()){
					echo '<a class="button blue1_active" href="submit_photo.php">SUBMIT PHOTO</a>';
				}
				?>
												
			</div>
		</div>
		<div id="content">
			<div id="contentBody">
				<form id="photo_form" action="" method="post" enctype="multipart/form-data" >
<span class="file-wrapper" style="margin-left: 165px;    top: 137px;    z-index: 50;">
  <input type="file" name="photo" id="photo" />
  <span class="button">Choose a Photo</span>
</span>
				<div class="box">
					<h1>Upload Photo</h1>
<label id="error-box" style="font-size: 13px;    height: 15px;    padding-bottom: 2px;    padding-top: 2px;    text-align: center; color:Red;">
<?php
if(isset($errorMsg)) echo $errorMsg;
?>
</label>
<label id="photo-box" style="height:25px" >

</label>		            
		             <!--<label>
		               <span>Caption</span>
		               <input type="text" class="input_text" name="caption" id="caption"/>
		            </label>
		            <label>
		                <span>Description</span>
		                <textarea class="message" name="description" id="description"></textarea>
		                <input type="submit" class="button" value="Submit" name="submit" id="submit" />
		            </label>-->
		            <label>
		            	<input type="submit" class="button" value="Submit" name="submit" id="submit" />
		            </label>
		         </div>
		    </form>
			</div>
			<div id="footer">				

			</div>
		</div>
       <?php

require_once('includes/footer.php');
?>