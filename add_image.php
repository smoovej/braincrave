<?php
print_r($_REQUEST);
require_once('ImageStuffer.php');
$stuffer = new ImageStuffer();

if (!empty($_REQUEST['youtube_url'])) {
  require_once('db/db.class.php');

  // Open the DB connection                                                                                                                                                                        
  $db = new db_class();
  if (!$db->connect($GLOBALS['db_hostname'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_name'], true)) {
    $db->print_last_error(false);
  }

  // Get the user info into the DB
  $user_data = $stuffer->getUserData( );
  $sql = "SELECT id from users where username = '" . mysql_real_escape_string($user_data['username']) . "';";
  $user_id = $db->select_one($sql);
  if (!$user_id) {
    $user_id = $db->insert_array('users', $user_data);
  }

  // cram the video into the db
  $youtube_url = mysql_real_escape_string($_REQUEST['youtube_url']);
  $youtube_id  = get_youtubeid( $youtube_url );
  $create_time = empty($create_time) ? time() : $create_time;
  $video_data = array('youtube_url' => $youtube_id
		      , 'type' => 'video'
			, 'user_id' => $user_id
			, 'create_time' => time());
  $video_id = $db->insert_array('images', $video_data);
  
  if (is_numeric($video_id)) {
    header('Location: http://www.braincrave.org/?id=' . $video_id . "&success=1");
  } else {
    $stuffer->error("Really?  You screwed this up?  All you had to do was paste in ONE THING.  Maybe you should try again.");
  }

} else {

  $image_id = $stuffer->stuff();
  if ($image_id) {
    // require the twitter library                                             
    require "twitter/twitter.lib.php";
    
    // your twitter username and password                                       
    $username = "braincrave";
    $password = "kivar0cks";
    
    // initialize the twitter class                                            
    $twitter = new Twitter($username, $password);
    
    $witty_shit = array("Oh no, not again",
			"There goes another 60 seconds of your life",
			"@ashtonkutcher I made this just for you",
			"@CNN Breaking news!!",
			"Shouldn't you be fixing the build instead of looking at this",
			"So this is what's been clogging up the tubes",
			"I can't believe I accidentally the whole thing",
			"I am the president of asia.  Your argument is invalid",
			"I can count to potato",
			"That's a cravin",
			"gigity",
			"I honor the place where my brain and your crave become one"
			);
    
    $random_key = array_rand($witty_shit);  
    $tinyURL = tinyUrl("http://www.braincrave.org/?id=" . $image_id);
    $tweet = $witty_shit[ $random_key ] . ": $tinyURL";
    $ret = $twitter->updateStatus($tweet);
    
    // Redirect to the bullshit that just got absorbed
    $stuffer->successRedirect($image_id);
  } else {
    $stuff->error("Strange things are afoot in Braincrave.  Image not uploaded.");
  }
  
}

function get_youtubeid($url) {
  if (preg_match('%youtube\\.com/(.+)%', $url, $match)) {
    $match = $match[1];
    $replace = array("watch?v=", "v/", "vi/");
    $match = str_replace($replace, "", $match);
  }
  return $match;
}
?>