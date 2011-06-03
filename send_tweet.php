<?php

// require the twitter library
require "twitter/twitter.lib.php";

// your twitter username and password
$username = "braincrave";
$password = $_REQUEST["password"];

// initialize the twitter class
$twitter = new Twitter($username, $password);

$tweet = $_REQUEST['tweet'];
$ret = $twitter->updateStatus($tweet);

header('Location: http://www.braincrave.org?tweetsuccess=true');
?>