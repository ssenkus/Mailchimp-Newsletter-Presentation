<?php

require_once 'mailchimp/inc/MCAPI.class.php';
require_once 'mailchimp/inc/config.inc.php'; //contains apikey

$api = new MCAPI($apikey);

?>
<!DOCTYPE html>
<html>
	
	<head>

		<title>Mailchimp Newsletters</title>

		<link href='http://static.mailchimp.com/favicon.ico' rel='icon' type='image/vnd.microsoft.icon' />
		<link href='http://static.mailchimp.com/favicon.ico' rel='shortcut icon' />
		
		<link rel="stylesheet" href="styles/mc-campaign-list.css">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css">
		
		<script src="http://code.jquery.com/jquery-1.9.0.js"></script>
		<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>

		
		<script type="text/javascript">
		
			function changeNewsletterURL(newURL) {
				var url = 'mailchimp/newsletters/' + newURL;
				document.getElementById("newsletterIframe").src = url;
			}
			
		</script>
				
	</head>
	
	<body>
	
		<div id="menu">
			<h1>Mailchimp Newsletters</h1>
			<h2>The list on the right is a collection of custom-designed newsletter campaigns sent with Mailchimp.</h2><p>Mailchimp is an excellent Email Marketing and Email List Management Service!<br /> <br /><a id="mc-button" href="http://mailchimp.com">Check out Mailchimp!</a></p>
		</div>
		
		<div id="c_list">
			<iframe id="newsletterIframe" style="float: left; width:675px; height:650px; overflow-x: hidden!important; overflow-y: scroll;" src="mailchimp/newsletters/3284217599.html"></iframe>
			<div id="accordion" style="width: 250px; float: left; margin-left:10px;">
		
<?php

if ($api->errorCode){

	echo "Unable to Pull list of Campaign!";
	echo "\n\tCode=".$api->errorCode;
	echo "\n\tMsg=".$api->errorMessage."\n";
	
} else {

	$campaigns = $api->campaigns('',0,100);
	$folders_arr = array_reverse($api->folders());

	$data_out = array();
	$count = 0;
	foreach($folders_arr as $a) {
		$count++;
		$newsletter_count = 0;
				
		echo '<h3 style="font-size: 16px; color:#000; font-weight: bold;">' . $a['name'] . "</h3><div><ul>";
		$data_out[$count] = array(
				'name' 		=> $a['name'],
				'folder_id' => $a['folder_id']
		);
		foreach($campaigns['data'] as $q) {

			if ($q['folder_id'] == $a['folder_id'])  {
			
				$data_out[$count][$newsletter_count]['archive_url'] = $q['archive_url'];
				$data_out[$count][$newsletter_count]['title'] 		= $q['title'];
				$data_out[$count][$newsletter_count]['subject'] 	= $q['subject'];
				$data_out[$count][$newsletter_count]['emails_sent'] = $q['emails_sent'];
				
				if (!file_exists('mailchimp/newsletters/' . $q['id'] .'.html')) { 
					$campaign_html = $api->campaignContent($q['id'], true);
					file_put_contents('mailchimp/newsletters/' . $q['id'] .'.html',$campaign_html); 
				}

				echo '<li class=""><a href="#" onclick="changeNewsletterURL(\'' . $q['id'] . '.html' .  '\');">' . 'ID: ' . $q['id'] . '</a></li>' . "\n";		
			
				$newsletter_count++;
			}
	
		}
		echo '</ul></div>';
	}
}

?>
			</div>
		</div>
		
		<script type="text/javascript">
		
			$( "#accordion" ).accordion({
				heightStyle: "content"
			
			});
		</script>
		
	</body>
</html>