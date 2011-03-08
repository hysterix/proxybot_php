<?php
# Name: Database.class.php
# File Description: MySQL Class to allow easy and clean access to common mysql commands
# Author: ricocheting
# Web: http://www.ricocheting.com/scripts/
# Update: 2/2/2009
# Version: 2.1
# Copyright 2003 ricocheting.com
# modified by hysterix 2010
# just added some functions and a few more vars


###################################################################################################
###################################################################################################
###################################################################################################
class Database {


var $server     = ""; //database server
var $user       = ""; //database login name
var $pass       = ""; //database login password
var $database   = ""; //database name
var $pre        = ""; //table prefix
var $tbl_good   = ""; //table good
var $tbl_new    = ""; //table new
var $tbl_banned = ""; //table banned


#######################
//internal info
var $record = array();

var $error = "";
var $errno = 0;

//table name affected by SQL query
var $field_table= "";

//number of rows affected by SQL query
var $affected_rows = 0;

var $link_id = 0;
var $query_id = 0;


#-#############################################
# desc: constructor
function Database($server, $user, $pass, $database, $pre='', $tbl_good, $tbl_new){
	$this->server=$server;
	$this->user=$user;
	$this->pass=$pass;
	$this->database=$database;
	$this->pre=$pre;
	$this->tbl_good=$tbl_good;
	$this->tbl_new=$tbl_new;
	$this->tbl_banned=$tbl_banned;
}#-#constructor()


#-#############################################
# desc: connect and select database using vars above
# Param: $new_link can force connect() to open a new link, even if mysql_connect() was called before with the same parameters
function connect($new_link=false) {
	$this->link_id=@mysql_connect($this->server,$this->user,$this->pass,$new_link);

	if (!$this->link_id) {//open failed
		$this->oops("Could not connect to server: <b>$this->server</b>.");
		}

	if(!@mysql_select_db($this->database, $this->link_id)) {//no database
		$this->oops("Could not open database: <b>$this->database</b>.");
		}

	// unset the data so it can't be dumped
	$this->server='';
	$this->user='';
	$this->pass='';
	$this->database='';
}#-#connect()


#-#############################################
# desc: close the connection
function close() {
	if(!mysql_close($this->link_id)){
		$this->oops("Connection close failed.");
	}
}#-#close()


#-#############################################
# Desc: escapes characters to be mysql ready
# Param: string
# returns: string
function escape($string) {
	if(get_magic_quotes_gpc()) $string = stripslashes($string);
	return mysql_real_escape_string($string);
}#-#escape()


#-#############################################
# Desc: executes SQL query to an open connection
# Param: (MySQL query) to execute
# returns: (query_id) for fetching results etc
function query($sql) {
	// do query
	$this->query_id = @mysql_query($sql, $this->link_id);

	if (!$this->query_id) {
		$this->oops("<b>MySQL Query fail:</b> $sql");
	}
	
	$this->affected_rows = @mysql_affected_rows();

	return $this->query_id;
}#-#query()


#-#############################################
# desc: fetches and returns results one line at a time
# param: query_id for mysql run. if none specified, last used
# return: (array) fetched record(s)
function fetch_array($query_id=-1) {
	// retrieve row
	if ($query_id!=-1) {
		$this->query_id=$query_id;
	}

	if (isset($this->query_id)) {
		$this->record = @mysql_fetch_assoc($this->query_id);
	}else{
		$this->oops("Invalid query_id: <b>$this->query_id</b>. Records could not be fetched.");
	}

	// unescape records
	if($this->record){
		$this->record=array_map("stripslashes", $this->record);
		//foreach($this->record as $key=>$val) {
		//	$this->record[$key]=stripslashes($val);
		//}
	}
	return $this->record;
}#-#fetch_array()


#-#############################################
# desc: returns all the results (not one row)
# param: (MySQL query) the query to run on server
# returns: assoc array of ALL fetched results
function fetch_all_array($sql) {
	$query_id = $this->query($sql);
	$out = array();

	while ($row = $this->fetch_array($query_id, $sql)){
		$out[] = $row;
	}

	$this->free_result($query_id);
	return $out;
}#-#fetch_all_array()


#-#############################################
# desc: frees the resultset
# param: query_id for mysql run. if none specified, last used
function free_result($query_id=-1) {
	if ($query_id!=-1) {
		$this->query_id=$query_id;
	}
	if(!@mysql_free_result($this->query_id)) {
		$this->oops("Result ID: <b>$this->query_id</b> could not be freed.");
	}
}#-#free_result()


#-#############################################
# desc: does a query, fetches the first row only, frees resultset
# param: (MySQL query) the query to run on server
# returns: array of fetched results
function query_first($query_string) {
	$query_id = $this->query($query_string);
	$out = $this->fetch_array($query_id);
	$this->free_result($query_id);
	return $out;
}#-#query_first()


#-#############################################
# desc: does an update query with an array
# param: table (no prefix), assoc array with data (doesn't need escaped), where condition
# returns: (query_id) for fetching results etc
function query_update($table, $data, $where='1') {
	$q="UPDATE ".$this->pre.$table." SET ";

	foreach($data as $key=>$val) {
		if(strtolower($val)=='null') $q.= "'$key' = NULL, ";
		elseif(strtolower($val)=='now()') $q.= "'$key' = NOW(), ";
		else $q.= "$key='".$this->escape($val)."', ";
	}

	$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	return $this->query($q);
}#-#query_update()


#-#############################################
# desc: does an insert query with an array
# param: table (no prefix), assoc array with data
# returns: id of inserted record, false if error
function query_insert($table, $data) {
	$q="INSERT IGNORE INTO ".$this->pre.$table." ";
	$v=''; $n='';

	foreach($data as $key=>$val) {
		$n.="$key, ";
		if(strtolower($val)=='null') $v.="NULL, ";
		elseif(strtolower($val)=='now()') $v.="NOW(), ";
		else $v.= "'".$this->escape($val)."', ";
	}

	$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
	if($this->query($q)){
		//$this->free_result();
		return mysql_insert_id();
	}
	else return false;

}#-#query_insert()


#-#############################################
# desc: eliminates duplicates 
# param: table (no prefix)
# returns: true on success, false if error

// this is the cleanest, fastest, and most effecient way that i could come up with for eliminating dupes
// originally the following two mysql queries were used:
// $qgood = 'select goodId from '.$this->pre.$this->tbl_good.' where goodId not in (select min(goodId) from '.$this->pre.$this->tbl_good.' group by ip, port);'; // spiffy except slow as fuck
// $qnew  = 'select newId from '.$this->pre.$this->tbl_new.' where newId not in (select min(newId) from '.$this->pre.$this->tbl_new.' group by ip, port);';
// which resulted in mysql using 100% cpu, and taking 23 seconds to find the dupes out of a table with only 3000 proxies
// clearly any query taking that long to run is unacceptable, and eliminating the dupes in php is much, much faster.
// we iterate through the entire result set, md5'ing the ip.port combo, and keeping track of md5's that we've seen before
// e.g. - dupes.  Once a dupe is found, it is immediately deleted 
// this function was timed eliminating duplicate proxies from a table of over 3000 new proxies in under a second, a significant improvement by any standard
	
function query_dupenukem($table) {
	if(strcmp($table,TBL_NEW) == 0) { // eliminate duplicates from new table
		$x=0;
		$query = "SELECT newid, ip, port FROM ".$this->pre.$table;
		$query_id = $this->query($query);
		$arrayHashes = array();
		
		while ($row = $this->fetch_array($query_id, $query)) {
			$hash = md5($row['ip'].$row['port']);	// md5 to the rescue again
	        if (!isset($arrayHashes[$hash])) {		// If the md5 didn't come up, keep track of it, otherwise its a dupe
	            $arrayHashes[$hash] = $hash;		// save the current element hash
	        } else {
		        $q = 'DELETE from '.$this->pre.$table.' where newid='.$row['newid'];
				if(!$this->query($q)) {
						return false;
				}
	        }
			$x++;
		}
		$q = 'DELETE from '.$this->pre.$table.' where ip="" OR port=""';	// get rid of any blank rows while we are here
		$this->query($q);
	} elseif(strcmp($table,TBL_BANNED) == 0) {	// eliminate duplicates from banned table
		$x=0;
		$query = "SELECT bannedid, ip FROM ".$this->pre.$table;
		$query_id = $this->query($query);
		$arrayHashes = array();
		
		while ($row = $this->fetch_array($query_id, $query)) {
			$hash = md5($row['ip']);				// md5 to the rescue again
	        if (!isset($arrayHashes[$hash])) {		// If the md5 didn't come up, keep track of it, otherwise its a dupe
	            $arrayHashes[$hash] = $hash;		// save the current element hash
	        } else {
		        $q = 'DELETE from '.$this->pre.$table.' where bannedid='.$row['bannedid'];
				if(!$this->query($q)) {
						return false;
				}
	        }
			$x++;
		}
		$q = 'DELETE from '.$this->pre.$table.' where ip=""';	// get rid of any blank rows while we are here
		$this->query($q);
	} elseif(strcmp($table,TBL_INACTIVE) == 0) {	// eliminate duplicates from inactive table
		$x=0;
		$query = "SELECT inactiveid, ip, port FROM ".$this->pre.$table;
		$query_id = $this->query($query);
		$arrayHashes = array();
		
		while ($row = $this->fetch_array($query_id, $query)) {
			$hash = md5($row['ip'].$row['port']);	// md5 to the rescue again
	        if (!isset($arrayHashes[$hash])) {		// If the md5 didn't come up, keep track of it, otherwise its a dupe
	            $arrayHashes[$hash] = $hash;		// save the current element hash
	        } else {
		        $q = 'DELETE from '.$this->pre.$table.' where inactiveid='.$row['inactiveid'];
				if(!$this->query($q)) {
						return false;
				}
	        }
			$x++;
		}
		$q = 'DELETE from '.$this->pre.$table.' where ip=""';				// get rid of any blank rows while we are here
		$this->query($q);
	} else {	// eliminate duplicates from good table
		$x=0;
		$query = "SELECT goodid, ip, port FROM ".$this->pre.$table;
		$query_id = $this->query($query);
		$arrayHashes = array();
		
		while ($row = $this->fetch_array($query_id, $query)) {
			$hash = md5($row['ip'].$row['port']);	// md5 to the rescue again
	        if (!isset($arrayHashes[$hash])) {		// If the md5 didn't come up, keep track of it, otherwise its a dupe
	            $arrayHashes[$hash] = $hash;		// save the current element hash
	        } else {
		        $q = 'DELETE from '.$this->pre.$table.' where goodid='.$row['goodid'];
				if(!$this->query($q)) {
						return false;
				}
	        }
			$x++;
		}
		
		$q = 'DELETE from '.$this->pre.$table.' where ip="" OR port=""';	// get rid of any blank rows while we are here
		$this->query($q);
	}
	
	$q = 'ALTER table '.$this->pre.$table.' auto_increment = 0';	// reset auto-increment field so the numbers dont get too high
	$this->free_result($query_id);	
	return true;

}#-#query_dupenukem()


#-#############################################
# desc: truncates a table
# param: table (no prefix)
# returns: true on success, false if error
function query_truncate($table) {
	$q="TRUNCATE ".$this->pre.$table.";";
	if($this->query($q)){
		return true;
	}
	else return false;

}#-#query_truncate()


#-#############################################
# desc: insert new proxies into the database
# Param: (array), string
function insertProxies($ellist,$time) {
	$tehtbl = $this->tbl_new;
	foreach($ellist as $tehlist) {
		$datas['ip']        = $tehlist[0];
		$datas['port']      = $tehlist[1];
		$datas['timestamp'] = $time;
		$this->query_insert($tehtbl, $datas);
	}
}#-#insertProxies()


#-#############################################
# desc: insert banned proxies into the database
# Param: (array), string
function insertBannedProxies($ellist,$time) {
	$tehtbl = $this->tbl_banned;
	foreach($ellist as $tehlist) {
		$datas['ip']        = $tehlist[0];
		$datas['timestamp'] = $time;
		$this->query_insert($tehtbl, $datas);
	}
}#-#insertBannedProxies()


#-#############################################
# desc: insert a good (known) proxy into the database
# Param: (array), string
function insertGoodProxy($ellist,$time) {
	$tehtbl = $this->tbl_good;
	$datas['ip']        = $ellist['ip'];
	$datas['port']      = $ellist['port'];
	$datas['type']	    = $ellist['type'];
	$datas['timestamp'] = $time;
	$this->query_insert($tehtbl, $datas);
}#-#removeProxy()


#-#############################################
# desc: copy a row from one table to another table
# Param: (string,string,string)
function copyProxy($tab1, $tab2, $ip) {
	$tab1 = $this->escape($tab1);
	$tab2 = $this->escape($tab2);
	$ip   = $this->escape($ip);
	$sql  = "INSERT INTO $tab1 (ip, port, type, alevel, recent, timestamp) SELECT ip, port, type, alevel, recent, timestamp FROM $tab2 WHERE ip='$ip'";
	$this->query($sql);
}#-#copyProxy()


#-#############################################
# desc: remove a specific proxy from the database
# Param: (array)
function removeProxy($ip, $proxykind) {
	$proxykind = $this->escape($proxykind);
	$ip        = $this->escape($ip);
	$sql       = "DELETE FROM ".$this->pre.$proxykind." WHERE ip='$ip'";
	$this->query($sql);
}#-#removeProxy()


#-#############################################
# desc: return a certain number of proxies
# Param: int, string
# returns: mysql resultset
function returnProxies($numreturn, $proxykind) {
	$proxykind = $this->escape($proxykind);
	$numreturn = $this->escape($numreturn);
	
	if(strcmp($proxykind,'anon')) {	// anything but anonymous proxies
		$sql = "SELECT * FROM ".$this->pre.$proxykind." LIMIT $numreturn";
	} else {	// they want anonymous proxies
		$sql = "SELECT * FROM ".$this->pre.$this->tbl_good." WHERE alevel='anonymous' LIMIT $numreturn";
	}
	return $this->query($sql);
}#-#returnProxies()


#-#############################################
# Desc: counts the number of new and good ips in the database
# Param: none
# returns: (array) $arr[good] $arr[new]
function countIps() {
	$tblnew  = $this->pre.$this->tbl_new;
	$tblgood = $this->pre.$this->tbl_good;
	$tblban  = $this->pre.$this->tbl_banned;
	$tblinac = $this->pre.$this->tbl_inactive;
	$sql     = "SELECT (SELECT count(*) FROM ".$tblnew.") AS cntnew, (SELECT count(*) FROM ".$tblgood.") ";
	$sql    .= "AS cntgood, (SELECT count(*) FROM ".$tblban.") AS cntban, (SELECT count(*) FROM ".$tblinac.") AS cntinactive, (SELECT count(*) FROM ".$tblgood." WHERE alevel='anonymous') AS cntanon;";
	$cntIps  = $this->query_first($sql);
	$this->cntnewips      = $cntIps['cntnew'];
	$this->cntgoodips     = $cntIps['cntgood'];
	$this->cntbadips      = $cntIps['cntban'];
	$this->cntinactiveips = $cntIps['cntinactive'];
	$this->cntanonips     = $cntIps['cntanon'];
}#-#countIps()


#-#############################################
# desc: throw an error message
# param: [optional] any custom error to display
function oops($msg='') {
	if($this->link_id>0){
		$this->error=mysql_error($this->link_id);
		$this->errno=mysql_errno($this->link_id);
	}
	else{
		$this->error=mysql_error();
		$this->errno=mysql_errno();
	}
	?>
		<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
		<tr><th colspan=2>Database Error</th></tr>
		<tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></td></tr>
		<?php if(strlen($this->error)>0) echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</td></tr>'; ?>
		<tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td></tr>
		<tr><td align="right">Script:</td><td><a href="<?php echo @$_SERVER['REQUEST_URI']; ?>"><?php echo @$_SERVER['REQUEST_URI']; ?></a></td></tr>
		<?php if(strlen(@$_SERVER['HTTP_REFERER'])>0) echo '<tr><td align="right">Referer:</td><td><a href="'.@$_SERVER['HTTP_REFERER'].'">'.@$_SERVER['HTTP_REFERER'].'</a></td></tr>'; ?>
		</table>
	<?php
}#-#oops()


}//CLASS Database
###################################################################################################

?>
