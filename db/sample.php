<?php

/*  Micah's MySQL Database Class - Sample Usage 
 *  4.17.2005 - Micah Carrick, email@micahcarrick.com
 *
 *  This is a sample file on using my database class.  Hopefully it will provide
 *  you with enough information to use the class.  You should also look through
 *  the comments in the db.class.php file to get additional information about
 *  any specific function.
*/

require_once('db.class.php');
$db = new db_class;

// Open up the database connection.  You can either setup your database login
// information in the db.class.php file or you can overide the defaults here. 
// If you setup the default values, you just have to call $db->connect() without
// any parameters-- which is much easier.

if (!$db->connect('localhost', 'user', 'password', 'database_name', true)) 
   $db->print_last_error(false);

// Create the table (if it doesn't exist) by executing the external sql
// file with the create table SQL statement.  

echo "Executing SQL commands in external file test_data.sql...<br>";
if (!$db->execute_file('test_data.sql')) $db->print_last_error(false);
$db->print_last_query();


// This I find very handy.  You can build an array as you are working through,
// for example, POST variables and validating the data or formatting the data
// etc.  By defaul, the class will add slashes (addslashes()) to all string data
// being input using this function.  you can override that by doing:
// $db->auto_slashes = false;

// You cannot perform any MySQL functions when using insert_array() as the the
// function will be enclosed in quotes and not executed.  If you have some fancy
// MySQL functions you'll want to use the insert_sql() function in which you 
// provide all the sql.

// Also, it's worth pointing out that if the table in which data is being inserted
// has an auto_increment value (as this one does), then the function will return
// that value which is generated.

echo "<br>Adding data to the table from an array...<br>";
$data = array(
   'user_name' => 'Micah Carrick', 
   'email' => 'email@micahcarrick.com', 
   'date_added' => '04/13/2003 4:12 PM',
   'age' => 24,
   'random_text' => "This ain't no regular text.  It's got some \"quotes\" and what not!"
   );
$user_id = $db->insert_array('users', $data);
if (!$user_id) $db->print_last_error(false);
$db->print_last_query();
$db->dump_query("SELECT * FROM users WHERE user_id=$user_id");


// This is similar to the above, only it updates the data rather than insert. Also
// you'll notice that in the first one we used a string to represent the date 
// and this time we're using the time function to generate a timestamp.  This is
// done to illustrate the class' ability to convert the date and time formats
// to a MySQL compatible format.  I like that alot :)

echo "<br>Updating the data in the table by changing the date_added... ";
$data = array('date_added' => time());
$rows = $db->update_array('users', $data, "user_id=$user_id");
if (!$rows) $db->print_last_error(false);
if ($rows > 0) echo "$rows rows updated.";
$db->print_last_query();
$db->dump_query("SELECT * FROM users WHERE user_id=$user_id");


// And now I'll just show you really quickly how to use this class to itereate
// a results set.  It's not much differnt that without using the class.  In fact,
// if you don't need to use the stripslashes and addslashes, that is, if 
// $db->auto_slahses=false then using the get_row() function is totally pointless
// and can be replaced with mysql_fetch_array($r);

echo "<br>Example of how to iterate through a result set...<br> ";
$r = $db->select("SELECT user_name, email FROM users");
while ($row=$db->get_row($r, 'MYSQL_ASSOC')) {
   echo '<b>'.$row['user_name']."</b>'s email address is <b>".$row['email']."</b><br>";
} 

?>