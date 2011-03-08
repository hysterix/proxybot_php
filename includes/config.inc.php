<?php

	// config.inc.php - database config file, curlheader

//database server
$config['server'] = "localhost";

//database login name
$config['user'] = "dbuser";
//database login password
$config['pass'] = "dbpass";

//database name
$config['database'] = "db";

// table prefix
$config['tablePrefix'] = "";

/* sql table names */
$config['tbl_new'] 		= 'new';	  // we dont need to be scouring through code
$config['tbl_good']     = 'good';	  // change these four variables to change the 
$config['tbl_inactive'] = 'inactive'; // default table names if you need to
$config['tbl_banned']   = 'banned';


// no need to touch below this (aka: please dont touch me down there anymore)
define('TBL_NEW',$config['tbl_new']);
define('TBL_GOOD',$config['tbl_good']);
define('TBL_INACTIVE',$config['tbl_inactive']);
define('TBL_BANNED',$config['tbl_banned']);

$config['needle'] = 'google';	// what to search for when connecting through the proxy

/* header info for curl (browser info needed its own function) */
$curlheader = array( 
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.8",
		"Accept-Language: en-us,en;q=0.5",
		"Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age=0",
		"Expires: 0",
		"Accept-Encoding: gzip,deflate",
		"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
		"Keep-Alive: 300",
		"Connection: keep-alive",
		"Pragma: " // browsers keep this blank
);

?>
