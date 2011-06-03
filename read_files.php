<?php
require_once('config.php');
require_once('db/db.class.php');
require_once('ImageStuffer.php');

$user_id = 1;

$db = new db_class();
if (!$db->connect($GLOBALS['db_hostname'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_name'], true)) {
   $db->print_last_error(false);
	die();
}

$stuffer = new ImageStuffer();

// open the current directory by opendir
$handle=opendir("bimages");
echo 'svn delete ';

while (($file = readdir($handle))!==false) {
	$filename  = 'bimages/'.$file;
	$extension = substr(strrchr($filename, '.'), 1);
	
	$md5 = md5_file($filename);
	$sql = "SELECT create_time FROM images WHERE md5 = '$md5'"; 
	$create_time = $db->select_one($sql);
	
	if (!$create_time) {
		$create_time = filemtime($filename);
		$stuffer->saveImageToDB($md5, $extension, $user_id, $create_time);
		rename($filename, 'bimages/'.$md5.'.'.$extension);
		echo " $filename";
	}
}

closedir($handle);

?>