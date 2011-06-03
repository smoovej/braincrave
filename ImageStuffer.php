<?php
require_once('config.php');
require_once('db/db.class.php');

/**
* Stuff images into the Braincrave Collective.
*/
class ImageStuffer {
	
	private $directory_self;
	private $uploadsDirectory;
	private $uploadForm;
	private $uploadSuccess;
	private $fieldname;
	private $errors;
	private $db;
	
	public function __construct() {
		// let's set some muthafuckin variables
		// make a note of the current working directory, relative to root.
		$this->directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);

		// make a note of the directory that will recieve the uploaded file
		$this->uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . $this->directory_self . 'bimages/';

		// make a note of the location of the upload form in case we need it
		$this->uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $this->directory_self;

		// make a note of the location of the success page
		$this->uploadSuccess = 'http://' . $_SERVER['HTTP_HOST'] . $this->directory_self . "?success";

		// this->fieldname used within the file <input> of the HTML form
		$this->fieldname = 'file';

		// Now let's deal with the upload

		// possible PHP upload this->errors
		$this->errors = array(1 => 'php.ini max file size exceeded',
		                2 => 'html form max file size exceeded',
		                3 => 'file upload was only partial',
		                4 => "You didn't attach a file.<br/>Go back to internet school.");
		
		// Open the DB connection
		$this->db = new db_class();
		if (!$this->db->connect($GLOBALS['db_hostname'], $GLOBALS['db_username'], $GLOBALS['db_password'], $GLOBALS['db_name'], true)) {
		   $this->db->print_last_error(false);
			die();
		}
	}
	
	public function stuff() {
		// check the upload form was actually submitted else print the form
		isset($_POST['submit_x'])
		    or $this->error('the upload form is needed', $this->uploadForm);

		// check for PHP's built-in uploading this->errors
		($_FILES[$this->fieldname]['error'] == 0)
		    or $this->error($this->errors[$_FILES[$this->fieldname]['error']], $this->uploadForm);
    
		// check that the file we are working on really was the subject of an HTTP upload
		@is_uploaded_file($_FILES[$this->fieldname]['tmp_name'])
		    or $this->error('not an HTTP upload', $this->uploadForm);
        
		// Get the image type
		$extension = $this->getImageExtension($_FILES[$this->fieldname]['tmp_name']);

		if ($extension === false) { $this->error('only image uploads are allowed', $this->uploadForm); }

		// make a unique filename for the uploaded file and check it is not already
		// taken... 
		$md5 = md5_file($_FILES[$this->fieldname]['tmp_name']);
		$fname = $md5 . '.' . $extension;
		if(file_exists($uploadFilename = $this->uploadsDirectory.$fname))
		{
		    $this->error('We already have that one!<br/>Be more original next time.', $this->uploadForm);
		}


		// now let's move the file to its final location and allocate the new filename to it
		@move_uploaded_file($_FILES[$this->fieldname]['tmp_name'], $uploadFilename)
		    or $this->error('receiving directory insufficient permission', $this->uploadForm);

		$user_data = $this->getUserData();

		$sql = "SELECT id from users where username = '" . addslashes($user_data['username']) . "';";

		$user_id = $this->db->select_one($sql);
		if (!$user_id) {
			$user_id = $this->db->insert_array('users', $user_data);
		}

		$image_id = $this->saveImageToDB($md5, $extension, $user_id, $create_time );
		
		return $image_id;
	}

	// If you got this far, everything has worked and the file has been successfully saved.
	// We are now going to redirect the client to a success page.
	public function successRedirect($id) {
		header('Location: ' . $this->uploadSuccess . '&id=' . $id);
	}
	
	public function saveImageToDB($md5, $extension, $user_id, $create_time) {
		$create_time = empty($create_time) ? time() : $create_time;
		$image_data = array('md5' => $md5
							, 'type' => $extension
							, 'user_id' => $user_id
							, 'create_time' => $create_time);
		$image_id = $this->db->insert_array('images', $image_data);
		return $image_id;
	}
	
	// The following function is an error handler which is used
	// to output an HTML error page if the file upload fails
	public function error($error, $location = null, $seconds = 5)
	{
		if (empty($location)) { $location = $this->uploadForm; }
 		
	    header("Refresh: $seconds; URL=\"$location\"");
	    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'."\n".
	    '"http://www.w3.org/TR/html4/strict.dtd">'."\n\n".
	    '<html lang="en">'."\n".
	    '    <head>'."\n".
	    '        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">'."\n\n".
	    '        <link rel="stylesheet" type="text/css" href="braincrave.css">'."\n\n".
	    '    <title>FAIL</title>'."\n\n".
	    '    </head>'."\n\n".
	    '    <body>'."\n\n".
	    '    <div id="Upload" align="center">'."\n\n".
	    '        <h1>FAIL</h1>'."\n\n".
	    '        <p>'."\n\n".
	    '        <span class="red">' . $error . '...</span><br/>'."\n\n".
	    '     </div>'."\n\n".
	    '</html>';
	    exit;
	} // end error handler


	/**
	* Determines the appropriate extension for an image
	* GIF/JPG/PNG/BMP
	* 
	* Defaults to JPG
	**/
	public function getImageExtension($filename) {
		// validation... since this is an image upload script we should run a check  
		// to make sure the uploaded file is in fact an image. Here is a simple check:
		// getimagesize() returns false if the file tested is not an image.
		$result = @getimagesize($filename);
		if ($result === false) {return false;}
	
		// Index 2 is one of the IMAGETYPE_XXX constants indicating the type of the image. See php.net/getimagesize
		$type = $result[2];
		switch ($type) {
			case IMAGETYPE_GIF:
				$extension = 'gif';
				break;
			case IMAGETYPE_JPEG:
				$extension = 'jpg';
				break;
			case IMAGETYPE_PNG:
				$extension = 'png';
				break;
			case IMAGETYPE_BMP:
				$extension = 'bmp';
				break;
			default:
				$extension = 'jpg';
				break;
		}

		return $extension;
	}

	public function getUserData() {
		$username = empty($_REQUEST['username']) ? "Braincrave" : $_REQUEST['username'];
		$create_time = time();
	
		return compact('username','create_time');
	}
}
?>