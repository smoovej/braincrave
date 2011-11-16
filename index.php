<?php
	require_once('config.php');
	require_once('./rotate.php');
	require_once('db/db.class.php');
	
	// Open the DB connection
	$db = new db_class();
	if (!$db->connect($GLOBALS['db_hostname'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_name'], true)) {
	   $db->print_last_error(false);
	}
?>

<html>
	<head>
	<title>A SERIES OF TUBES</title>
	<link rel="stylesheet" type="text/css" href="braincrave.css" />
	<script src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="braincrave.js"></script>
	<script src="http://scripts.embed.ly/jquery.embedly.min.js"></script>
	</head>

	<body>
		<div align="center">
			<?php 
				if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
				  $image_id = $_REQUEST['id'];			  
				  $row = $db->select("SELECT * FROM images WHERE id = $image_id LIMIT 1");
				} else {
				  $row = $db->select("SELECT * FROM images ORDER BY RAND() LIMIT 1");
				}

				$media = $db->get_row($row, 'MYSQL_ASSOC');
				// we've got an image
				$md5 = $media['md5'];
			  
				$sql = "SELECT username FROM users u WHERE id = {$media['user_id']};";
				$poster = $db->select_one($sql);
				
				$image_id = $media['id'];
				$image_type = $media['type'];
				$image_url = "bimages/$md5.$image_type"; 
				$r = $db->select("SELECT id, md5, youtube_url, type FROM images WHERE id < $image_id ORDER BY id DESC LIMIT 1 ");
				$row = $db->get_row($r, 'MYSQL_ASSOC');
				$prev_img = $row['md5'] . '.' . $row['type'];
	
				$prev_img_id = $db->select_one("SELECT id FROM images WHERE id < $image_id ORDER BY id DESC LIMIT 1");
				$next_img_id = $db->select_one("SELECT id FROM images WHERE id > $image_id ORDER BY id ASC LIMIT 1");
				$most_recent_upload = $db->select_one("SELECT id FROM images ORDER BY id DESC LIMIT 1");
				
				$r = $db->select("SELECT id, md5, youtube_url, type FROM images WHERE id > $image_id ORDER BY id ASC LIMIT 1 ");
				$row = $db->get_row($r, 'MYSQL_ASSOC');
				$next_img = 'bimages/' . $row['md5'] . '.' . $row['type'];
			  
			?>
			<div class="braincrave_title">
				<a href="http://www.braincrave.org?id=<?php echo $prev_img_id; ?>">&larr;</a>&nbsp;&nbsp;&nbsp;
				BRAINCRAVE<a href="http://www.braincrave.org?id=<?php echo $most_recent_upload; ?>">.</a>
				&nbsp;&nbsp;&nbsp;<a href="http://www.braincrave.org?id=<?php echo $next_img_id; ?>">&rarr;</a>
			</div>
			<br/>
			<?php 
				if (isset($_REQUEST['success'])) {
					echo '<br/><span id="success" style="color:red; padding:20px; font-size: 24px;">Absorbed</span><br/>';
				} elseif 	(isset($_REQUEST['tweetsuccess'])) {
						echo '<br/><span id="tweetsuccess" style="color:red; padding:20px; font-size: 24px;">It Hath Been Twat</span><br/>';
				}
			?>
			<br/>
			<div id="the_shit">
				<?php
				$youtube_url = $media['youtube_url'];
				if (empty($youtube_url)) {
				  // display image
				  echo "<a href=\"http://www.braincrave.org\"><img border=\"0\" src=\"$image_url\"></a>";
				} else {
				  // we've got a video
				  ?>
				  <div class="video">
					<a href="<?php echo $youtube_url;?>">Fucking Braincraves, how do they work?</a>
				  </div>
				<?php } ?>
			</div>
			<br/>
			<span style="font-size: .7em;">Blame <?php echo empty($poster) ? 'Braincrave' : $poster; ?> for <a href="http://www.braincrave.org?id=<?php echo $image_id; ?>"> this one</a>.</span>
			<div style="padding:30px;">
				<a href="javascript://" id="toggle_upload_form">The collective values your contribution</a>
				<div id="upload_form">
					<form id="Upload" action="add_image.php" enctype="multipart/form-data" method="post">
				        <input type="hidden" name="MAX_FILE_SIZE" value="300000000">
				       
                        <p>Your Name: <input id="username" type="text" name="username"></p>
						<hr/>
						<p>File: <input id="file" type="file" name="file"></p>
						<p>- OR -</p>
						<p>Tube Url: <input id="youtube_url" type="text" name="youtube_url"/></p>
						<p><a style="font-size:.7em" href="http://embed.ly/providers">Which ultratubes can I use?</a></p>
				  <p>Which is hotter, fire or ice? <input type="text" name="question"/>
				        <p><input id="submit" type="image" name="submit" src="img/do_it.png" value="DO IT"></p>
					</form>
				</div>	
			</div>
			</div>	
			<br/><hr/><br/><br/>
<table width="100%">	
		<tr valign="top">
			<td width="33%">
                        <div align="center" >
                        <?php
                        $styles = array('Arghavan','mixtape02','milkmandan','WarwickPunksoc','yehya','NGTank','Headache','robbie','Handsandstainv2','KCwhiteiPod','LOST','SlashTop5');
$random_key = array_rand($styles);
                        ?>
<span style="font-size: 1.2em; padding-bottom: 15px;">Recently reverberating in the Braincave:</span>
<br/><br/>
<a href="http://www.last.fm/user/braincrave/?chartstyle=<?php echo $styles[ $random_key ]; ?>"><img src="http://imagegen.last.fm/<?php echo $styles[ $random_key ]; ?>/recenttracks/braincrave.gif" border="0" a\
lt="maesto's Profile Page" /></a>
</div>

			</td>
			
			<td width="33%">
				<div align="center">
			                <span style="font-size: 1.2em; padding-bottom: 15px;">Recently Twad</span><br/><br/>
					<div class="twitter_container">
						<script src="http://widgets.twimg.com/j/2/widget.js"></script>
						<script>
						new TWTR.Widget({
						  version: 2,
						  type: 'list',
						  rpp: 30,
						  interval: 30000,
						  title: 'Recently twad by',
						  subject: 'the collective',
						  width: 'auto',
						  height: 300,
						  theme: {
							shell: {
							  background: '#000000',
							  color: '#ffffff'
							},
							tweets: {
							  background: '#ffffff',
							  color: '#444444',
							  links: '#4065c2'
							}
						  },
						  features: {
							scrollbar: true,
							loop: false,
							live: true,
							hashtags: true,
							timestamp: true,
							avatars: true,
							behavior: 'all'
						  }
						}).render().setList('braincrave', 'cravers').start();
						</script>
					</div>
					<br/>
					Want to get in on this shnazzy shnit?<br/>Tweet your desires to <a href="http://twitter.com/braincrave">@braincrave</a>.
				</div>
			</td>
</table>
<hr/>
<div align="center">
	<a href="principia.html"><img width="400" border="0" src="http://www.braincrave.org/img/principia.png"/></a>
</div>

<div align="center" style="padding-top: 20px;">
	<a href="http://www.braincrave.org/mixtape"><img src="img/dancing_kid.gif" border="0"/></a>
	<div style="padding-top:20px;">
		Oh look, we made you an <blink><a href="http://www.braincrave.org/mixtape">AWESOME MIXTAPE</a></blink>
</div>


	<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-6161851-1");
pageTracker._trackPageview();
</script>
	</body>
</html>