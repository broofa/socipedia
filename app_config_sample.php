<?
// Project name (displayed in page header)
define('PROJECT_NAME', 'Your Project Name');  // Displayed in banner

// A string that is unique to your site.  E.g. use one from https://www.grc.com/passwords.htm
define('BARD_SITE_KEY', 'pick a random string for your site');     

// Database configuration.  See ./system/application/config/database.php for explanation 
// and other options.  See also 
// http://codeigniter.com/user_guide/database/configuration.html
$GLOBALS['bard_db_config'] = array(
  'hostname' => "your_db_host.com",
  'username' => "your_db_username",
  'password' => "your_db_password",
  'database' => "your_db_name",
  'dbdriver' => "mysql" // Change this if you're not using mysql
);

// Directory for uploaded files (images).
// IMPORTANT: This *must* be relative to the webroot (i.e. the directory this
// config file is in).  Unless you have a specific reason to change this, don't 
// worry about it.
define('BARD_UPLOAD_DIR', 'static/uploads');   

// If you want to have a master password to allow editing of any entry, generate one at pages/generate_password, and paste the resulting hash in here:
// Go to /pages/generate_password to create a password hash
define('BARD_MASTER_HASH', 'insert_generated_hash_here');     

// Google Maps Key (obtain from http://www.google.com/apis/maps/)
define('BARD_GEOCODE_KEY', 'Your Google Maps Key');  

// Akismet configuration (see http://vanhegan.net/software/akismet/ for details)
$GLOBALS['akismet_key']   = 'Your akismet key';         // API Key (obtain from http://wordpress.com)
$GLOBALS['akismet_home']  = 'Your site URL';
$GLOBALS['akismet_ua']    = 'your_app_name/1.0';
?>
