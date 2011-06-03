<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Twitter Status</title>
</head>

<body>
<style type="text/css">
.woork{
	color:#444;
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	width:600px;
	margin: 0 auto;
}
.twitter_container{
	color:#444;
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	width:600px;
	margin: 0 auto;
}
.twitter_container a{
	color:#0066CC;
	font-weight:bold;
}
.twitter_status{
	height:60px;
	padding:6PX;
	border-bottom:solid 1px #DEDEDE;
}
.twitter_image{
	float:left; 
	margin-right:14px;
	border:solid 2px #DEDEDE;
	width:50px;
	height:50px;
}
.twitter_posted_at{
 font-size:11px;
 padding-top:4px;
 color:#999;
}
</style>
<div class="woork">
From Woork: <a href="http://woork.blogspot.com">http://woork.blogspot.com</a></div>
<div class="twitter_container">
<?php // require the twitter library
require "twitter.lib.php";

// your twitter username and password
$username = "YOUR USER NAME";
$password = "YOUR PASSWORD";

// initialize the twitter class
$twitter = new Twitter($username, $password);

// fetch your profile in xml format

$xml = $twitter->getPublicTimeline();

/* display the raw xml
echo '<pre>';
echo $xml;
echo '</pre>';*/

$twitter_status = new SimpleXMLElement($xml);
foreach($twitter_status->status as $status){
	echo '<div class="twitter_status">';
	foreach($status->user as $user){
		echo '<img src="'.$user->profile_image_url.'" class="twitter_image">';
		echo '<a href="http://www.twitter.com/'.$user->name.'">'.$user->name.'</a>: ';
	}
	echo $status->text;
	echo '<br/>';
	echo '<div class="twitter_posted_at"><strong>Posted at:</strong> '.$status->created_at.'</div>';
	echo '</div>';
}
?>
</div>
</body>
</html>
