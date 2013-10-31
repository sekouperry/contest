<?php

require_once 'config.php';



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">

<head>

<title>Photo Contest</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="<?php echo($global['home_link']); ?>styles/style.css" type="text/css" />

<script type="text/javascript" src="<?php echo $global['home_link']?>js/jquery-1.7.2.min.js"></script>

<script type="text/javascript">

function vote(picId,userId)

{

	var vote = parseInt($("#vote_count_"+picId).text());

	$.ajax({

	  url: 'vote.php?pic='+picId+'&user='+userId,

	  success: function(data) {

		if(data == 1){

			$("#vote_count_"+picId).text(vote+1);

			$("#vote_"+picId).text("Voted");

			$("#likebox_"+picId).hide();

		}

	  }

	});

}

</script>

</head>

<body>

<div id="fb-root"></div>

<script>

  window.fbAsyncInit = function() {

    FB.init({

      appId      : '<?php echo $global['app_id']; ?>', // App ID

      channelUrl : '//<?php echo $global['home_link']; ?>channel.php', // Channel File

      status     : true, // check login status

      cookie     : true, // enable cookies to allow the server to access the session

      xfbml      : true  // parse XFBML

    });



    FB.Canvas.setSize({ height: 930 });
    FB.Canvas.setAutoGrow();

  };



  // Load the SDK Asynchronously

  (function(d){

     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];

     if (d.getElementById(id)) {return;}

     js = d.createElement('script'); js.id = id; js.async = true;

     js.src = "//connect.facebook.net/en_US/all.js";

     ref.parentNode.insertBefore(js, ref);

   }(document));

   

   

   function share(photo_id, photo_url){

	   	FB.ui(

		  {

		    method: 'feed',

		    name: '<?php echo $global['name']; ?>',

		    link: '<?php echo $global['app_link']; ?>&app_data=' + photo_id,

		    picture: '<?php echo $global['home_link']; ?>uploads/small/' + photo_url,

		    caption: '<?php echo $global['caption']; ?>',

		    description: '<?php echo $global['description']; ?>'

		  },

		  function(response) {

		    if (response && response.post_id) {

		      $("#share_"+photo_id).text("Shared");

		    }

		  }

		);

   }

   

   function invite(photo_id, photo_url){ 	



   	

   	

     FB.ui({ method: 'apprequests',

          message: 'Vote my Photo at <?php echo $global['app_link']; ?>&app_data=' + photo_id },

		  function(response) {

		  	if (response && response.to && response.request) {

		  		var ids = response.to.join('-');		  		



		  		

		  		$.ajax({

				  url: 'ajax_invite.php?photo_id='+photo_id+'&photo_url='+photo_url+'&request='+response.request+'&ids='+ids,

				  success: function(data) {

					$("#invite_"+photo_id).text("Invited");

				  }

				});

		  		

		      

		    }

		  }

      );

    }

   

</script>