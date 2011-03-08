<?php

	/*
		proxybot.class.php - proxybot class
	*/

include_once('database.class.php');	 // mysql wrapper
include_once('config.inc.php');		 // mysql db config info
include_once('userAgentString.php'); // list of user agent strings

class proxybot extends Database {

var $cntnewips   = '';	// count of new ip addresses
var $cntgoodips  = '';  // count of good ip addresses
var $cntanonips  = '';	// count of anon ip addresses
var $uagent 	 = ''; 	// an array of the user agent string
var $cheader	 = '';  // curl header

#-#############################################
# desc: constructor
function __construct($server, $user, $pass, $database, $pre='', $tbl_good, $tbl_new, $tbl_inactive, $tbl_banned, $needle, $userAgentString, $chead) {
	$this->server=$server;
	$this->user=$user;
	$this->pass=$pass;
	$this->database=$database;
	$this->pre=$pre;
	$this->tbl_good=$tbl_good;
	$this->tbl_new=$tbl_new;
	$this->tbl_inactive=$tbl_inactive;
	$this->tbl_banned=$tbl_banned;
	$this->needle=$needle;
	$this->uagent=$userAgentString;
	$this->cheader=$chead;
	$this->connect(); // connect to the database
}#-#__construct()

#-#############################################
# desc: destructor
function __destruct() {
	$this->close(); // close the database connection
}#-#__destruct()


#-#############################################
# desc: display the main menu
# Param: none
function displayMenu() {
	$this->countIps();
	$this->outputMenu($this->cntnewips,$this->cntgoodips,$this->cntanonips,$this->cntinactiveips,$this->cntbadips);
}#-#displayMenu()


#-#############################################
# desc: echos out the menu itself
# Param: int, int
function outputMenu($new,$good,$anon,$inactive,$banned) {
		echo '<div class="mainform">'."\n";
		echo '<div class="menu"><ol id="toc">
	    	<li id="menu0"><a href="#" onclick="changetab(\'menu0\');"><span>search</span></a></li>
	    	<li id="menu1"><a href="#" onclick="changetab(\'menu1\');"><span>&nbsp;test&nbsp;</span></a></li>
	    	<li id="menu2"><a href="#" onclick="changetab(\'menu2\');"><span>output</span></a></li>
	    	<li id="menu4"><a href="#" onclick="changetab(\'menu4\');"><span>banned</span></a></li>
	    	<li id="menu3"><a href="#" onclick="changetab(\'menu3\');"><span>database</span></a></li>
			<li id="menu5"><a href="#" onclick="changetab(\'menu5\');"><span>&nbsp;help&nbsp;</span></a></li>
	      </ol></div>';
		echo '<div class="tehhidden" id="toerror"><span class="yaerror"></span></div>';
		echo '<div class="tehhidden" id="toscour">';
		echo '<div class="smlnewgood indexfx"><b>anon:'.$anon.'</b><br /><b>good:'.$good.'</b><br /><b>&nbsp;new:'.$new.'</b><br /><b>dead:'.$inactive.'</b><br /><b>&nbsp;&nbsp;b&:'.$banned.'</b><br /></div>';
		echo '<h2>Scrape New Proxies</h2>';
		echo '<form name="proxybotstart" action="'.$_SERVER['PHP_SELF'].'" method="post">
				<table cellspacing="3" cellpadding="1" border="0">
				<tr><td><b>Proxy list as:</b><br /><select name="listtype">
								<option value="mysql">mysql</option>
								<option value="plaintext">plaintext</option>
								<option value="csv">csv</option>';
								//<option value="email">email</option>
						echo '</td>
				</tr>
				<tr><td><br /><b>Sites to scour:</b></td></tr>
				<tr>
					<td><table class="cssfun" cellspacing="3" cellpadding="1" border="0">
						<tr><td>2ch.net</td><td><input type="checkbox" name="2ch" value="1" checked /></td></tr>
						<tr><td>aliveproxy.com</td><td><input type="checkbox" name="aliveproxy" value="1" checked /></td></tr>
						<tr><td>atomintersoft.com</td><td><input type="checkbox" name="atomintersoft" value="1" checked /></td></tr>
						<tr><td>comp-info.ru</td><td><input type="checkbox" name="compinfo" value="1" checked /></td></tr>
						<tr><td>cybersyndrome.net</td><td><input type="checkbox" name="cybersyndrome" value="1" checked /></td></tr>
						<tr><td>freeproxy.ch</td><td><input type="checkbox" name="freeproxych" value="1" checked /></td></tr>
						<tr><td>freeproxy.ru</td><td><input type="checkbox" name="freeproxy" value="1" checked /></td></tr>
						<tr><td>freeproxylists.com</td><td><input type="checkbox" name="freeproxylists" value="1" checked /></td></tr>
						<tr><td>getfreeproxy.info</td><td><input type="checkbox" name="getfreeproxy" value="1" checked /></td></tr>
						<tr><td>hidemyass.com</td><td><input type="checkbox" name="hidemyass" value="1" checked /></td></tr>
						<tr><td>ip-adress.com</td><td><input type="checkbox" name="ipaddress" value="1" checked /></td></tr>
						<tr><td>ipcn.org</td><td><input type="checkbox" name="ipcn" value="1" checked /></td></tr>
						<tr><td>j1f.net</td><td><input type="checkbox" name="j1f" value="1" checked /></td></tr>
						<tr><td>my-proxy.com</td><td><input type="checkbox" name="myproxy" value="1" checked /></td></tr>
						<tr><td>proxiesthatwork.com</td><td><input type="checkbox" name="proxiesthatwork" value="1" checked /></td></tr>
						<tr><td>proxy-heaven.blogspot.com</td><td><input type="checkbox" name="pheaven" value="1" checked /></td></tr>
						<tr><td>proxy-list.net</td><td><input type="checkbox" name="proxylist" value="1" checked /></td></tr>
						<tr><td>proxygo.com.ru</td><td><input type="checkbox" name="proxygo" value="1" checked /></td></tr>
						<tr><td>proxyleech.com</td><td><input type="checkbox" name="proxyleech" value="1" checked /></td></tr>
						<tr><td>proxylists.net</td><td><input type="checkbox" name="proxylists" value="1" checked /></td></tr>
						<tr><td>proxyserverfinder.com</td><td><input type="checkbox" name="proxyserverfinder" value="1" checked /></td></tr>
						<tr><td>rosinstrument.com</td><td><input type="checkbox" name="rosinstrument" value="1" checked /></td></tr>
						<tr><td>samair.ru</td><td><input type="checkbox" name="samair" value="1" checked /></td></tr>
						<tr><td>speedtest.at</td><td><input type="checkbox" name="speedtest" value="1" checked /></td></tr>
						<tr><td>xroxy.com</td><td><input type="checkbox" name="xroxy" value="1" checked /></td></tr>
					</table></td>
				</tr>';
		echo '<tr><td><br />&nbsp;&nbsp;<input id="lolpist" type="submit" value="search" /><input type="hidden" name="search" value="1" />';
	    echo '<div id="mclick"><a href="#" name="checkall" onClick="masterClick(1)">select: none</a></div></td></table>';
		echo '</form></div></div>'."\n";

		echo '<div class="tehhidden" id="totest"><h2>Proxy Testing</h2>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
		echo '<div class="smlnewgood"><b>anon:'.$anon.'</b><br /><b>good:'.$good.'</b><br /><b>&nbsp;new:'.$new.'</b><br /><b>dead:'.$inactive.'</b><br /><b>&nbsp;&nbsp;b&:'.$banned.'</b></div>';
		echo '<table>';
		if(SHOW_NEW_CHECK == 1) { echo  '<tr><td><b> &nbsp;-Max new checked: &nbsp;'.MAX_NEW_CHECK.'</b><br />'; if(SHOW_GOOD_CHECK == 0) { echo'</td></tr><tr><td>&nbsp;</td></tr>'; } }
		if(SHOW_GOOD_CHECK == 1) { echo  '<tr><td><b> &nbsp;-Max good checked: '.MAX_GOOD_CHECK.'</b></td></tr>'; if(SHOW_NEW_CHECK == 0) { echo'</td></tr><tr><td>&nbsp;</td></tr>'; } else { echo'</td></tr><tr><td>&nbsp;</td></tr>'; } } 
		echo '<tr><td>Test <b>New</b> Proxies: </td><td><input type="radio" name="proxytype" value="newprox" onClick="formfun(0)" checked></td></tr><tr>';
		echo '<td>Test <b>Good</b> Proxies:</td><td><input type="radio" name="proxytype" value="goodprox" onClick="formfun(1)"></td></tr>';
		echo '<tr><td>Test <b>Dead</b> Proxies: </td><td><input type="radio" name="proxytype" value="inactprox" onClick="formfun(2)"></td></tr><tr>';
		echo '<td>Test <b>Anonymity </b>Level:</td><td><input type="radio" name="proxytype" value="anonprox" onClick="formfun(3)"></td></tr><tr><td>';
		echo '<br /><input type="submit" value="test" /><input type="hidden" name="test" value="1" />';
		echo '</td></tr></table>';
		echo '<div class="tehhidden" id="anonhiddenfun"><table><tr><td>- internal anon check:</td><td><input type="radio" name="anonchecktype" value="internal" checked /></td></tr>
			  <tr><td>- external anon check:</td><td><input type="radio" name="anonchecktype" value="external" /></td></tr></table></div></div>';
		echo '</form>';
		echo '<div class="tehhidden" id="toadmin"><h2>Proxy Display</h2>';
		echo '<div class="smlnewgood"><b>anon:'.$anon.'</b><br /><b>good:'.$good.'</b><br /><b>&nbsp;new:'.$new.'</b><br /><b>dead:'.$inactive.'</b><br /><b>&nbsp;&nbsp;b&:'.$banned.'</b></div>';
		echo '<form name="proxybottest" action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
		echo '<table cellspacing="1" cellpadding="1" border="0">
				<tr><td><b>Proxy list:</b></td><td>&nbsp;&nbsp;&nbsp;<select name="proxytype">
								<option value="anon">anonymous</option>
								<option value="'.TBL_GOOD.'">good</option>
								<option value="'.TBL_NEW.'">new</option>
								<option value="'.TBL_INACTIVE.'">dead</option>
								<option value="'.TBL_BANNED.'">banned</option>';
								//<option value="email">email</option></td></tr>
				echo '</td></tr></table>';
		echo '<table cellspacing="1" cellpadding="1" border="0">
						<tr>
							<td>&nbsp;<td>&nbsp;&nbsp;&nbsp;<b>Output:</b>&nbsp;&nbsp;&nbsp;<select name="listtype">
								<option value="proxychains">proxychains</option>
								<option value="plaintext">plaintext</option>
								<option value="csv">csv</option>';
								//<option value="email">email</option>
						echo '</td>
						</tr>
					</table><br /></td>
				</tr>';
	echo '<tr><td>&nbsp;&nbsp;<input id="lolpist" type="submit" value="output" />
	      <input type="hidden" name="output" value="1" /></table></form>';
	echo '</div></table>';
	echo '<div class="tehhidden" id="toban"><h2>Scrape for Banned Proxies</h2>';
	echo '<div class="smlnewgood"><b>anon:'.$anon.'</b><br /><b>good:'.$good.'</b><br /><b>&nbsp;new:'.$new.'</b><br /><b>dead:'.$inactive.'</b><br /><b>&nbsp;&nbsp;b&:'.$banned.'</b></div>';
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
	echo '<table cellspacing="3" cellpadding="1" border="0">
				<tr><td><b>Proxy list as:</b><br /><select name="listtype">
								<option value="mysql">mysql</option>
								<option value="plaintext">plaintext</option>';
								//<option value="email">email</option>
						echo '</td>
				</tr>
				<tr><td><br /><b>Sites to scour:</b></td></tr>
				<tr>
					<td><table class="banfun" cellspacing="3" cellpadding="1" border="0">
						<tr><td>fall.cs.princeton.edu - old</td><td><input type="checkbox" name="princeton" value="1" /></td></tr>
						<tr><td>rosinstrument.com - planetlab</td><td><input type="checkbox" name="rosinst" value="1" checked /></td></tr>
					</table></td>
				</tr>';
	echo '<tr><td><br />&nbsp;&nbsp;<input id="lolpist" type="submit" value="search" /><input type="hidden" name="banned" value="1" />';
	echo '</td></table>';
	echo '</form></div>'."\n";
	echo '<div class="tehhidden" id="todatabase"><h2>Database Maintenance</h2>';
	echo '<div class="smlnewgood"><b>anon:'.$anon.'</b><br /><b>good:'.$good.'</b><br /><b>&nbsp;new:'.$new.'</b><br /><b>dead:'.$inactive.'</b><br /><b>&nbsp;&nbsp;b&:'.$banned.'</b></div>';		
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">'."\n";
	echo '<table cellspacing="1" cellpadding="1" border="0">
				<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Table:</b></td><td>&nbsp;&nbsp;&nbsp;<select name="dbtable">
								<option value="both">new + good</option>
								<option value="'.TBL_NEW.'">new</option>
								<option value="'.TBL_GOOD.'">good</option>
								<option value="'.TBL_INACTIVE.'">dead</option>
								<option value="'.TBL_BANNED.'">banned</option>';
	echo '</td></tr></table>';
	echo '<table cellspacing="1" cellpadding="1" border="0">
						<tr>
							<td>&nbsp;&nbsp;&nbsp;&nbsp;<b>Action:</b</td><td>&nbsp;&nbsp;&nbsp;<select name="dbaction">
								<option value="unique">unique</option>
								<option value="truncate">truncate</option>';
						echo '</td>
						</tr>
					</table><br /></td>
				</tr>';
	echo '<tr><td>&nbsp;&nbsp;<input id="lolpist" type="submit" value="maintain" />
	      <input type="hidden" name="maintain" value="1" />';
	echo '</table></form></div>';
	echo '<div class="tehhidden" id="tohelp"><h2>Quick Help Menu</h2>';
	echo '<div class="smlnewgood"><b>anon:'.$anon.'</b><br /><b>good:'.$good.'</b><br /><b>&nbsp;new:'.$new.'</b><br /><b>dead:'.$inactive.'</b><br /><b>&nbsp;&nbsp;b&:'.$banned.'</b></div>
		  <br /><br /><br /><br /><br /><br />
		  <span class="helpfx">
	      * search &nbsp;&nbsp;- search for new proxies<br />
	      * test&nbsp;&nbsp;&nbsp;&nbsp; - test new or good proxies<br />
	      * output &nbsp; - display new or good proxies<br />
	      * banned &nbsp;&nbsp;- search for planetlab/codeen proxies<br />
	      * database - maintain the database<br />
	      * help&nbsp;&nbsp;&nbsp;&nbsp; - this help menu</span></div>';
}#-#outputMenu()


#-#############################################
# desc: return the proxylist requested
# Param: string
# returns: (array)
function returnProxyList($proxylist) {
	if(isset($proxylist)) { // how they want the proxy list data
		switch($proxylist) {
			case "csv":
				$plisttype = "csv"; 
				break;
			case "mysql":
				$plisttype = "mysql"; 
				break;
			case "email":
				$plisttype = "email";
				break;
			case "proxychains":
				$plisttype = "proxychains";
				break;
			case "plaintext":
			default:
				$plisttype = "plaintext";  // default
				break;
		}	
	} else {
		$plisttype = "plaintext"; // default
	}

	return $plisttype;
}#-#returnProxyList()


#-#############################################
# desc: return the sites to search through
# Param: array
# returns: (array) $arr[good] $arr[new]
function returnSitesScour($req) {
	$sites = array();  // which sites to check

	if(isset($req['2ch'])) { // search 2ch for proxies
		array_push($sites,"2ch");
	}

	if(isset($req['aliveproxy'])) { // search aliveproxy for proxies
		array_push($sites,"aliveproxy");
	}
		
	if(isset($req['atomintersoft'])) { // search atomintersoft for proxies
		array_push($sites,"atomintersoft");
	}
	
	if(isset($req['compinfo'])) { // search compinfo for proxies
		array_push($sites,"compinfo");
	}
	
	if(isset($req['cybersyndrome'])) { // search cybersyndrome for proxies
		array_push($sites,"cybersyndrome");
	}
	
	if(isset($req['freeproxych'])) { // search freeproxych for proxies
		array_push($sites,"freeproxych");
	}
	
	if(isset($req['freeproxy'])) { // search freeproxy for proxies
		array_push($sites,"freeproxy");
	}
	
	if(isset($req['freeproxylists'])) { // search freeproxylists for proxies
		array_push($sites,"freeproxylists");
	}
	
	if(isset($req['getfreeproxy'])) { // search getfreeproxy for proxies
		array_push($sites,"getfreeproxy");
	}
	
	if(isset($req['hidemyass'])) { // search hidemyass for proxies
		array_push($sites,"hidemyass");
	}
	
	if(isset($req['ipaddress'])) { // search ipaddress for proxies
		array_push($sites,"ipaddress");
	}
	
	if(isset($req['ipcn'])) { // search ipcn for proxies
		array_push($sites,"ipcn");
	}

	if(isset($req['j1f'])) { // search j1f for proxies
		array_push($sites,"j1f");
	}
	
	if(isset($req['myproxy'])) { // search myproxy for proxies
		array_push($sites,"myproxy");
	}
	
	if(isset($req['proxiesthatwork'])) { // search proxiesthatwork for proxies
		array_push($sites,"proxiesthatwork");
	}
	
	if(isset($req['proxygo'])) { // search proxygo for proxies
		array_push($sites,"proxygo");
	}
	
	if(isset($req['proxyleech'])) { // search proxyleech for proxies
		array_push($sites,"proxyleech");
	}
	
	if(isset($req['proxylist'])) { // search proxylist for proxies
		array_push($sites,"proxylist");
	}
	
	if(isset($req['proxylists'])) { // search proxylists for proxies
		array_push($sites,"proxylists");
	}
	
	if(isset($req['pheaven'])) { // search pheaven for proxies
		array_push($sites,"pheaven");
	}
	
	if(isset($req['proxyserverfinder'])) { // search proxyserverfinder for proxies
		array_push($sites,"proxyserverfinder");
	}

	if(isset($req['rosinstrument'])) { // search rosinstrument for proxies
		array_push($sites,"rosinstrument");
	}
	
	if(isset($req['samair'])) { // search samair for proxies
		array_push($sites,"samair");
	}
	
	if(isset($req['speedtest'])) { // search speedtest for proxies
		array_push($sites,"speedtest");
	}
	
	if(isset($req['xroxy'])) { // search xroxy for proxies
		array_push($sites,"xroxy");
	}
	
	if(isset($req['princeton'])) { // search princeton for planetlab proxies
		array_push($sites,"princeton");
	}

	if(isset($req['rosinst'])) { // search rosinst for proxies
		array_push($sites,"rosinst");
	}
	return $sites;
}#-#returnSitesScour()


#-#############################################
# desc: return the final proxy list
# Param: array
# returns: (array) $arr[good] $arr[new]
function returnFinalList($sites) {
	// grab the proxies
	foreach($sites as $proxsites) {
		$userAgent = $this->retAgentString($this->uagent); // lets hide our true identity
		switch($proxsites) {
			case "2ch":
				$ch2 = $this->grab_2ch($this->cheader,$userAgent);
				if(empty($ch2)) {
					$arrError[] = "<h3 class='alert'>2ch returned no proxies!</h3>";
				}
				break;
			case "aliveproxy":
				$aliveproxylist = $this->grab_aliveproxy($this->cheader,$userAgent);
				if(empty($aliveproxylist)) {
					$arrError[] = "<h3 class='alert'>aliveproxy returned no proxies!</h3>";
				}
				break;
			case "atomintersoft":
				$atomintersoft = $this->grab_atomintersoft($this->cheader,$userAgent);
				if(empty($atomintersoft)) {
					$arrError[] = "<h3 class='alert'>atomintersoft returned no proxies!</h3>";
				}
				break;
			case "compinfo":
				$compinfo = $this->grab_compinfo($this->cheader,$userAgent);
				if(empty($compinfo)) {
					$arrError[] = "<h3 class='alert'>compinfo returned no proxies!</h3>";
				}
				break;
			case "cybersyndrome":
				$cybersyndrome = $this->grab_cybersyndrome($this->cheader,$userAgent);
				if(empty($cybersyndrome)) {
					$arrError[] = "<h3 class='alert'>cybersyndrome returned no proxies!</h3>";
				}
				break;
			case "freeproxych":
				$freeproxych = $this->grab_freeproxych($this->cheader,$userAgent);
				if(empty($freeproxych)) {
					$arrError[] = "<h3 class='alert'>freeproxych returned no proxies!</h3>";
				}
				break;
			case "freeproxy":
				$freeproxy = $this->grab_freeproxy($this->cheader,$userAgent);
				if(empty($freeproxy)) {
					$arrError[] = "<h3 class='alert'>freeproxy returned no proxies!</h3>";
				}
				break;
			case "freeproxylists":
				$freeproxylists = $this->grab_freeproxylists($this->cheader,$userAgent);
				if(empty($freeproxylists)) {
					$arrError[] = "<h3 class='alert'>freeproxylists returned no proxies!</h3>";
				}
				break;
			case "getfreeproxy":
				$getfreeproxy = $this->grab_getfreeproxy($this->cheader,$userAgent);
				if(empty($getfreeproxy)) {
					$arrError[] = "<h3 class='alert'>getfreeproxy returned no proxies!</h3>";
				}
				break;
			case "ipaddress":
				$ipaddress = $this->grab_ipadress($this->cheader,$userAgent);
				if(empty($ipaddress)) {
					$arrError[] = "<h3 class='alert'>ipaddress returned no proxies!</h3>";
				}
				break;
			case "ipcn":
				$ipcn = $this->grab_ipcn($this->cheader,$userAgent);
				if(empty($ipcn)) {
					$arrError[] = "<h3 class='alert'>ipcn returned no proxies!</h3>";
				}
				break;
			case "j1f":
				$j1f = $this->grab_j1f($this->cheader,$userAgent);
				if(empty($j1f)) {
					$arrError[] = "<h3 class='alert'>j1f returned no proxies!</h3>";
				}
				break;
			case "proxiesthatwork":
				$proxiesthatwork = $this->grab_proxiesthatwork($this->cheader,$userAgent);
				if(empty($proxiesthatwork)) {
					$arrError[] = "<h3 class='alert'>proxiesthatwork returned no proxies!</h3>";
				}
				break;
			case "myproxy":
				$myproxy = $this->grab_myproxy($this->cheader,$userAgent);
				if(empty($myproxy)) {
					$arrError[] = "<h3 class='alert'>myproxy returned no proxies!</h3>";
				}
				break;
			case "proxygo":
				$proxygo = $this->grab_proxygo($this->cheader,$userAgent);
				if(empty($proxygo)) {
					$arrError[] = "<h3 class='alert'>proxygo returned no proxies!</h3>";
				}
				break;
			case "proxyleech":
				$proxyleech = $this->grab_proxyleech($this->cheader,$userAgent);
				if(empty($proxyleech)) {
					$arrError[] = "<h3 class='alert'>proxyleech returned no proxies!</h3>";
				}
				break;
			case "proxylist":
				$proxylist = $this->grab_proxylist($this->cheader,$userAgent);
				if(empty($proxylist)) {
					$arrError[] = "<h3 class='alert'>proxylist returned no proxies!</h3>";
				}
				break;
			case "proxylists":
				$proxylists = $this->grab_proxylists($this->cheader,$userAgent);
				if(empty($proxylists)) {
					$arrError[] = "<h3 class='alert'>proxylists returned no proxies!</h3>";
				}
				break;
			case "pheaven":
				$pheaven = $this->grab_pheaven($this->cheader,$userAgent);
				if(empty($pheaven)) {
					$arrError[] = "<h3 class='alert'>pheaven returned no proxies!</h3>";
				}
				break;
			case "proxyserverfinder":
				$proxyserverfinder = $this->grab_proxyserverfinder($this->cheader,$userAgent);
				if(empty($proxyserverfinder)) {
					$arrError[] = "<h3 class='alert'>proxyserverfinder returned no proxies!</h3>";
				}
				break;
			case "rosinstrument":
				$rosinstrument = $this->grab_rosinstrument($this->cheader,$userAgent);
				if(empty($rosinstrument)) {
					$arrError[] = "<h3 class='alert'>rosinstrument returned no proxies!</h3>";
				}
				break;
			case "samair":
				$samair = $this->grab_samair($this->cheader,$userAgent);
				if(empty($samair)) {
					$arrError[] = "<h3 class='alert'>samair returned no proxies!</h3>";
				}
				break;
			case "speedtest":
				$speedtest = $this->grab_speedtest($this->cheader,$userAgent);
				if(empty($speedtest)) {
					$arrError[] = "<h3 class='alert'>speedtest returned no proxies!</h3>";
				}
				break;
			case "xroxy":
				$xroxy = $this->grab_xroxy($this->cheader,$userAgent);
				if(empty($xroxy)) {
					$arrError[] = "<h3 class='alert'>xroxy returned no proxies!</h3>";
				}
				break;
			case "princeton":	// these are planet lab proxies
				$princeton = $this->grab_princeton($this->cheader,$userAgent);
				if(empty($princeton)) {
					$arrError[] = "<h3 class='alert'>princeton returned no proxies!</h3>";
				}
				break;
			case "rosinst":		// these are planet lab proxies
				$rosinst = $this->grab_rosinst($this->cheader,$userAgent);
				if(empty($rosinst)) {
					$arrError[] = "<h3 class='alert'>rosinst returned no proxies!</h3>";
				}
				break;
			case "hidemyass":
			default:
				$hidemyasslist = $this->grab_hidemyassproxy($this->cheader,$userAgent);
				if(empty($hidemyasslist)) {
					$arrError[] = "<h3 class='alert'>hidemyass returned no proxies!</h3>";
				}
				break;
		}
	}
	
	$tehFinalList = array();

	// let our powers combine in a big cascade of shit
	if(isset($ch2)) { 
		if(is_array($ch2)) {
			$tehFinalList = array_merge($tehFinalList,$ch2);
		}
	}
	
	if(isset($aliveproxylist)) { 
		if(is_array($aliveproxylist)) {
			$tehFinalList = array_merge($tehFinalList,$aliveproxylist);
		}
	}
	
	if(isset($atomintersoft)) { 
		if(is_array($atomintersoft)) {
			$tehFinalList = array_merge($tehFinalList,$atomintersoft);
		}
	}
	
	if(isset($compinfo)) { 
		if(is_array($compinfo)) {
			$tehFinalList = array_merge($tehFinalList,$compinfo);
		}
	}
	
	if(isset($cybersyndrome)) { 
		if(is_array($cybersyndrome)) {
			$tehFinalList = array_merge($tehFinalList,$cybersyndrome);
		}
	}
	
	if(isset($freeproxych)) { 
		if(is_array($freeproxych)) {
			$tehFinalList = array_merge($tehFinalList,$freeproxych);
		}
	}
	
	if(isset($freeproxy)) { 
		if(is_array($freeproxy)) {
			$tehFinalList = array_merge($tehFinalList,$freeproxy);
		}
	}
	
	if(isset($freeproxylists)) { 
		if(is_array($freeproxylists)) {
			$tehFinalList = array_merge($tehFinalList,$freeproxylists);
		}
	}
	
	if(isset($getfreeproxy)) {
		if(is_array($getfreeproxy)) {
			$tehFinalList = array_merge($tehFinalList,$getfreeproxy);
		}
	}
	
	if(isset($hidemyasslist)) {
		if(is_array($hidemyasslist)) {
			$tehFinalList = array_merge($tehFinalList,$hidemyasslist);
		}
	}
	
	if(isset($ipaddress)) { 
		if(is_array($ipaddress)) {
			$tehFinalList = array_merge($tehFinalList,$ipaddress);
		}
	}
	
	if(isset($ipcn)) { 
		if(is_array($ipcn)) {
			$tehFinalList = array_merge($tehFinalList,$ipcn);
		}
	}

	if(isset($j1f)) { 
		if(is_array($j1f)) {
			$tehFinalList = array_merge($tehFinalList,$j1f);
		}
	}

	if(isset($myproxy)) { 
		if(is_array($myproxy)) {
			$tehFinalList = array_merge($tehFinalList,$myproxy);
		}
	}
	
	if(isset($proxiesthatwork)) { 
		if(is_array($proxiesthatwork)) {
			$tehFinalList = array_merge($tehFinalList,$proxiesthatwork);
		}
	}
	
	if(isset($proxyleech)) { 
		if(is_array($proxyleech)) {
			$tehFinalList = array_merge($tehFinalList,$proxyleech);
		}
	}
	
	if(isset($proxylist)) { 
		if(is_array($proxylist)) {
			$tehFinalList = array_merge($tehFinalList,$proxylist);
		}
	}
	
	if(isset($proxylists)) { 
		if(is_array($proxylists)) {
			$tehFinalList = array_merge($tehFinalList,$proxylists);
		}
	}
	
	if(isset($proxygo)) { 
		if(is_array($proxygo)) {
			$tehFinalList = array_merge($tehFinalList,$proxygo);
		}
	}
	
	if(isset($pheaven)) { 
		if(is_array($pheaven)) {
			$tehFinalList = array_merge($tehFinalList,$pheaven);
		}
	}
	
	if(isset($proxyserverfinder)) { 
		if(is_array($proxyserverfinder)) {
			$tehFinalList = array_merge($tehFinalList,$proxyserverfinder);
		}
	}
	
	if(isset($rosinstrument)) { 
		if(is_array($rosinstrument)) {
			$tehFinalList = array_merge($tehFinalList,$rosinstrument);
		}
	}
	
	if(isset($samair)) { 
		if(is_array($samair)) {
			$tehFinalList = array_merge($tehFinalList,$samair);
		}
	}
	
	if(isset($speedtest)) { 
		if(is_array($speedtest)) {
			$tehFinalList = array_merge($tehFinalList,$speedtest);
		}
	}
	
	if(isset($xroxy)) { 
		if(is_array($xroxy)) {
			$tehFinalList = array_merge($tehFinalList,$xroxy);
		}
	}
	
	if(isset($princeton)) { 
		if(is_array($princeton)) {
			$tehFinalList = array_merge($tehFinalList,$princeton);
		}
	}
	
	if(isset($rosinst)) { 
		if(is_array($rosinst)) {
			$tehFinalList = array_merge($tehFinalList,$rosinst);
		}
	}

	return $tehFinalList;
}#-#returnFinalList()


// setBrowser - determines how curl interacts with sites
// $proxy     - 0, 1, or ip of proxy; if 0 no proxy is used; if 1 grab and use a new proxy, otherwise the variable is the proxy
// $post      - 0, 1, or post string; if 0 dont post, if 1, use method get, otherwise post with the string
// $yaheader  - the http header information set in config.php 
// $agent     - an array of user agent strings set in userAgentString.php
// returns    - [0] - the curl options [1] - the user agent string used [2] - the ip of proxy used (if at all)
function setBrowser($proxy,$post,$yaheader,$yaagent) {
	$arrtmp = array();
	if(is_array($yaagent)) {
		$userAgent = $this->retAgentString($yaagent);
	} else {
		$userAgent = $yaagent;
	}
	$opts   = array(
		CURLOPT_HTTPHEADER	=> $yaheader,
		CURLOPT_USERAGENT	=> $userAgent,
		CURLOPT_HEADER		=> true,
		CURLOPT_SSL_VERIFYHOST	=> false,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_RETURNTRANSFER	=> true,		
		CURLOPT_FRESH_CONNECT	=> 1,		// prevent caching
		CURLOPT_ENCODING	=> "gzip"
	);
		
	// they want to use a proxy
	if(strcmp($proxy,0)) {  // $proxy not equal to 0
			
		if(isset($_SESSION['goodproxy'])) { 	 // if a good cached proxy already exists, use it
			$proxy = $_SESSION['goodproxy'];
		}

		if(strlen($proxy) > 1 ) {
			$prox = $proxy;
		} else {
			$proxythingy  = $this->grab_hidemyassproxy($yaheader,$userAgent);   // mask us when grabbing the proxy too
			$yaproxything = array_rand($proxythingy);
			$prox 	      = $yaproxything[0].':'.$yaproxything[1];
		}

		$tmpar = array( CURLOPT_PROXY	=>  $prox,
						CURLOPT_HEADER  => 1);		
		$this->array_push_associative($opts,$tmpar);
		$arrtmp[2] = $prox;  // return the proxy used
	}
	if(strcmp($post,0)) { // $post not equal to 0
		if(strlen($post) > 1 ) {  // use post method
			$tmpar = array( CURLOPT_POST	    =>  true,
							CURLOPT_POSTFIELDS  =>  $post );
		} else {	// use get method
			$tmpar = array( CURLOPT_HTTPGET	    =>  true );
		}
		$this->array_push_associative($opts,$tmpar);
	}
	
	$arrtmp[0] = $opts;	 // the curl options
	$arrtmp[1] = $userAgent; // the user agent used
	return $arrtmp;
}


// $retPortNum   - accepts a url curl result set to a hidemyass port number image and checks to see if we have that on file (ghetto rainbow tables!)
// $portimage	 - a png file of the port to be hashed
// $ghettoTables - an associative array; key => value; md5 hash of picture => portnumber
// returns       - either returns the port number if found or a 0 if failure
function retPortNum($portimage,$ghettoTables) {
	$newporthash = hash("md5",$portimage);  // dont need sha256, no collisions with so little files
	return $ghettoTables[$newporthash];	// more trickery, if doesnt exist, doesnt return!
}


// return a random user agent string as defined in the userAgentString.php file
// may perhaps extend this some day
function retAgentString($agent) {
	$randKey = array_rand($agent,1);
	$agentString = $agent[$randKey];
	return $agentString;
}


// Append associative array elements because php does not support this natively :/
function array_push_associative(&$arr) {
   $args = func_get_args();
   $ret = 0;
   foreach ($args as $arg) {
       if (is_array($arg)) {
           foreach ($arg as $key => $value) {
               $arr[$key] = $value;
               $ret++;
           }
       }else{
           $arr[$arg] = "";
       }
   }
   return $ret;
}


// str_replace only once; how is this not supported natively :/
function str_replace_once($search, $replace, $subject) {
	$firstChar = strpos($subject, $search);
	if($firstChar !== false) {
		$beforeStr = substr($subject,0,$firstChar);
		$afterStr = substr($subject, $firstChar + strlen($search));
		return $beforeStr.$replace.$afterStr;
    	} else {
    		return $subject;
    	}
}


/**
 * Create Unique Arrays using an md5 hash
 *
 * @param array $array
 * @return array
 */
function arrayUnique($array, $preserveKeys = false) { // by Dominik Jungowski 
    // Unique Array for return
    $arrayRewrite = array();
    // Array with the md5 hashes
    $arrayHashes = array();
    foreach($array as $key => $item) {
        // Serialize the current element and create a md5 hash
        $hash = md5(serialize($item));
        // If the md5 didn't come up yet, add the element to
        // to arrayRewrite, otherwise drop it
        if (!isset($arrayHashes[$hash])) {
            // Save the current element hash
            $arrayHashes[$hash] = $hash;
            // Add element to the unique Array
            if ($preserveKeys) {
                $arrayRewrite[$key] = $item;
            } else {
                $arrayRewrite[] = $item;
            }
        }
    }
    return $arrayRewrite;
}


function microtime_float() { // cute little function
    list($utime, $time) = explode(" ", microtime());
    return ((float)$utime + (float)$time);
}


function uniqueRand($n, $min = 0, $max = null) {
	if($max === null)
		$max = getrandmax();
	$array = range($min, $max);
	$return = array();
	
	$keys = array_rand($array, $n);
	foreach($keys as $key)
		$return[] = $array[$key];
	return $return;
}


function md_implode($array, $glue = '')
{
    if (is_array ($array))
    {
        $output = '';
        foreach ($array as $v)
        {
            $output .= $this->md_implode($v, $glue);
        }
        return $output;
    }
    else
    {
        return $array.$glue;
    }
}

function md_array_flatten($md_array)
{
    $flat_array = explode ('#|#',$this->md_implode($md_array,'#|#')); // "#|#" is a sample delimiter
    array_pop($flat_array); // to remove last empty element
    return $flat_array;
}


#-#############################################
# desc: remove banned ips
# Param: array
# returns: array if successful, and false if failure
function autoBan($yalist) {
	$tmparr    = array();
	$yatemplol = $this->returnProxies(9999999,$this->tbl_banned);
	
	while ($cntIps = $this->fetch_array($yatemplol)) {
			$tmparr[] = $cntIps['ip'];	// grab a list of all banned proxies
	}
	
	// these two for loops are slower than array_intersect; but then we cant ban entire ip ranges so its a trade off :/
	for($x=0;$x<count($yalist);$x++) {
		for($y=0;$y<count($tmparr);$y++) {
			if(stristr($yalist[$x][0],$tmparr[$y]) != false) {	// we use stristr so we can ban whole ip ranges
				unset($yalist[$x]);
				break;	// the array element is now gone; need to break out of loop
			}
		}
	}
	
	unset($yatemplol);
	unset($tmparr);
	unset($cntIps);

	if(isset($yalist)) {
		return $yalist;	// success
	} else {
		return false;	// failure
	}
}


/* these functions were stolen by me, from charon - rhino@project2025.com;  I lay no claim to the anonymity checker portion of proxybot */
// Actual proxyjudge part of the page
function return_env_variables($servervars)	// i had to modify this function to secure it
{
	$yavar = '<pre>'."\n";
	foreach ($servervars as $header => $value ) {
		if ((strpos($header , 'REMOTE')!== false || strpos($header , 'HTTP')!== false || strpos($header , 'REQUEST')!== false) && ( strpos($header , 'HTTP_HOST') !== 0)) {
			if(!stristr($value,'<') || !stristr($value,'?') || !stristr($value,'form')) {	// without this little check, i was able to drop
				if(!ctype_xdigit($value)) {	$yavar .= $header.' = '.$value."\n"; }			// a small php uploader onto the page! if my permissions were 
			} else {																		// not set correctly, i could have then uploaded a shell!
				$yavar .= $header." = \n";
			}
		}
	}
	$yavar .= '</pre>';
	return $yavar;
}


#-#############################################
# desc: test anonmity level of proxies
# i couldnt salvage the functions from charon because im making this capable of multi-threading.
# it just wasnt working out well trying to use functions that could only handle strings and not arrays
# Param: array,string,string
# returns: array if successful, and false if failure
function test_proxies($arrProx,$proxtype,$urltype) {
	$externurls = array(
		'http://www.emcy.it/garage/anoncheck/anoncheck.php',
		'http://www.ugtop.com/spill.shtml',
		'http://www3.wind.ne.jp/hassii/env.cgi',
		'http://www.xav.com/env.pl',
		'http://demo.nickname.net/demo/testpak/env.pl',
		'http://birdingonthe.net/cgi-bin/env.pl',
		'http://infohound.net/tools/env.pl',
		'http://www.mahoroba.ne.jp/cgi-bin/user-cgi/~furutani/env.pl',
		'http://www.wowwi.orc.ru/cgi-bin/env.pl',
		'http://www.outroom.de/scripts/env.pl',
		'http://www.shillout.de/scripts/env.pl',
		'http://www.shillout24.de/scripts/env.pl',
		'http://zerg.helllabs.net/cgi-bin/textenv.pl',
		'http://www.wylie.me.uk/cgi-bin/info.pl');
	
	$whatismyip = array(
		'http://www.whatismyip.org',
		'http://www.ipchicken.com',
		'http://www.ip-adress.com',
		'http://www.myglobalip.com',
		'http://www.ioerror.us/ip',
		'http://myipinfo.net',
		'http://www.knowmyip.com',
		'http://www.whatip.com',
		'http://www.findmyipaddress.info',
		'http://identifymyip.com',
		'http://myip.is',
		'http://www.findmyipaddress.com',
		'http://www.myipresolve.com',
		'http://my-i-p.com',
		'http://checkip.dyndns.org',
		'http://ipdragon.com',
		'http://www.broadbandreports.com/whois',
		'http://mijnip.com',
		'http://myip.4rev.net',
		'http://checkmyip.org',
		'http://www.whatsmyrealip.com',
		'http://followip.com',
		'http://www.iprivacytools.com/my-ip-address',
		'http://www.whatismyip.net');
	
	$listcount  = count($arrProx); 		       			// needed multiple times
	$numcon     = floor($listcount / MAX_CONNECTIONS);	// main loop counter
	$numcon	   += 1;						   			// this is how we do the remainder
	$yacntr     = 0;
	$cntrgood   = 0;
	$cntranon   = 0;
	$cntrbad    = 0;
	$arrprox4   = array(CURLOPT_PROXYTYPE => 'CURLPROXY_SOCKS4'); // if there are not quotes around these
	$arrprox5   = array(CURLOPT_PROXYTYPE => 'CURLPROXY_SOCKS5'); // this bullshit fails to work
	$arrhttp    = array(CURLOPT_PROXYTYPE => 'CURLPROXY_HTTP');	  // but of course http still worked adding to the confusion
	
	/* this check is done outside the for loops so we dont check our external ip hundreds of times :O */
	if(strcmp($urltype,'internal')) {	// connect externally
		if(!strcmp($_SERVER['SERVER_NAME'],'localhost')) {	// they are trying to externally connect to an external site; first we need to know external ip
			$userAgent = $this->retAgentString($this->uagent);
			$matches = $this->grabSimpleIps($whatismyip,$this->cheader,$userAgent,1);
			$tmpcntr = 0;
			while(!$matches) {
				if($tmpcntr > MAX_RETRYS*3) { break; } // so we dont get stuck in an infinte loop
				$matches = $this->grabSimpleIps($whatismyip,$this->cheader,$userAgent,1);
				$tmpcntr++;
			}
			$externip = $matches[0][0];
		}
	}

	for($i=0; $i < $numcon; $i++) {	// here be dragons
		$curl        = new CURL();  // create the curl instance
		$connectlist = array();
		for($j=0; $j < MAX_CONNECTIONS; $j++) {
			if($yacntr >= $listcount) { break; }	// this will happen now because we are looping one more extra time to get the last of them
			$opts    = $this->setBrowser(0,0,$this->cheader,$arrProx[$yacntr]['useragent']);  // set browser information
			$goodop  = $opts[0];
			$ipnport = $arrProx[$yacntr]['ip'].":".$arrProx[$yacntr]['port'];
			$yaar    = array(CURLOPT_CONNECTTIMEOUT  => PROXY_TIMEOUT,
							 CURLOPT_TIMEOUT		 => PROXY_TIMEOUT,
							 CURLOPT_PROXY			 => $ipnport);
								  
			if($arrProx[$yacntr]['type'] == "socks4") {
				$elprox    = $arrprox4;
				$proxytype = "socks4";
			}
			else if($arrProx[$yacntr]['type'] == "socks5") { 
				$elprox   = $arrprox5;
				$proxytype = "socks5";
			} else {
				$elprox   = $arrhttp;
				$proxytype = "http";
			}
			
			$connectlist[$j][0] = $arrProx[$yacntr]['ip'];
			$connectlist[$j][1] = $arrProx[$yacntr]['port'];
			$connectlist[$j][2] = $proxytype;
			
			$yacntr++;
			$this->array_push_associative($yamemkill,$goodop,$yaar,$elprox);
			foreach ($yamemkill as $key => $value) { // get rid of empty elements ruining our fun due to the offending function above
			     if (is_null($value) || $value== "") {
			       unset($yamemkill[$key]);
			     }
			 }
	
			if(strcmp($urltype,'internal')) {	// connect externally
				$myip    = $externip;			// make sure to check for external ip and not localhost
				$randkey = array_rand($externurls); // Select a random $externurls page
				$curl->addSession($externurls[$randkey],$yamemkill);
			} else {	// connect internally
				if(!strcmp($_SERVER['SERVER_NAME'],'localhost')) {	// they are trying to externally connect to an internal site
					$_SESSION['warnmsg'] = 'You cant connect external proxies to localhost!';
					return false;
				}
				$myip = $_SERVER['REMOTE_ADDR'];
				$url  = "{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}?".PROXY_REQ_VAR;
				$curl->addSession($url,$yamemkill);
			}
		}
		$result = $curl->exec();
		for($k=0; $k < count($result); $k++) { // loop through the result set checking for the needle
			if(($result[$k] == FALSE) || (strpos($result[$k],'REMOTE_ADDR') === false)) {	// are we at least getting the page
				// before removing, copy over to other table
				$this->copyProxy(TBL_INACTIVE,TBL_GOOD,$connectlist[$k][0]);	// copy from good to inactive table
				$this->removeProxy($connectlist[$k][0],$proxtype);  			// remove from good table
				$cntrbad++;
			} elseif(strpos($result[$k],$myip) === false) {
				$eltime = time();
				$where  = 'ip=\''.$connectlist[$k][0].'\'';
				$column = array('recent' => $eltime, 'alevel' => 'anonymous');	// update anonymous level
				$this->query_update(TBL_GOOD,$column,$where);					// update timestamp
				$cntranon++;
			} else {	// proxy works but is not anonymous, so it is essentially useless
				$eltime = time();
				$where  = 'ip=\''.$connectlist[$k][0].'\'';
				$column = array('recent' => $eltime, 'alevel' => 'non-anonymous');	// update anonymous level
				$this->query_update(TBL_GOOD,$column,$where);						// update timestamp
				$cntrgood++;
			}
		}

		echo '<script type="text/javascript">
					document.getElementById(\'tehanonresults\').innerHTML = \'Anonymous proxies:'.$cntranon.'\';'."\n".'</script>';
				
		echo '<script type="text/javascript">
					document.getElementById(\'tehgoodresults\').innerHTML = \'Working proxies:'.$cntrgood.'\';'."\n".'</script>';
		
		echo '<script type="text/javascript">
					document.getElementById(\'tehbadresults\').innerHTML = \'Failed proxies: '.$cntrbad.'\';'."\n".'</script>';
		
		ob_flush();
		flush();
	
		$curl->clear();  // remove the curl instance
		unset($curl);
	}
	return true;
}#-#test_proxies()


#-#############################################
# desc: truncate or eliminate duplicates
# Param: array
# returns: 0 or 1 if successful, and false if failure
function maintainDatabase($req) {
	// this potentially could be very bad if not handled properly
	// we are directly playing with request values here; if a lazy person were
	// to not check the incoming data, it could lead to sql injection.
	// im not even going to form the query with the data in any way; like other 
	// parts of this program, just a cascade of shit in a case statement; if data
	// does not match what im expecting, return failure; nothings' un-hackable but 
	// i should be preventing sql injections through this function at the minimum
	if(isset($req['dbtable'])) { // get the table
		switch($req['dbtable']) {
			case 'both':	// they want both tables
				$table = 'both';
				break;
			case $this->tbl_good:	// they want only table good
				$table = $this->tbl_good;
				break;
			case $this->tbl_new;		// they want only table new
				$table = $this->tbl_new;
				break;
			case $this->tbl_banned;		// they want only table banned
				$table = $this->tbl_banned;
				break;
			default:
				return false;	// they messed with the form data
				break;
		}
	} else { // this shouldnt happen; they are messing with form data
		return false;
	}
	
	if(isset($req['dbaction'])) { // get the action
		switch($req['dbaction']) {
			case 'unique':	 // they want to eliminate duplicates
				$action = 'unique';
				break;
			case 'truncate': // they want to truncate the entire table
				$action = 'truncate';
				break;
			default:
				return false;	// they messed with the form data
				break;
		}
	} else { // this shouldnt happen; they are messing with form data
		return false;
	}
	
	// if we get to this point, the user is not trying anything funny and we can proceed with db maintenance
	if(strcmp($action,'truncate')) {  // $action not equal to truncate; they want to eliminate duplicates
		$success = 0; // success for dupe elimination
		if(strcmp($table,'both')) {  // $action not equal to both, can only be either good, new, or banned
			if(!$this->query_dupenukem($table)) {
				return false; // there was some sort of a problem
			}
		} else { // need to eliminate dupes for both
			if($this->query_dupenukem($this->tbl_good)) {
				if(!$this->query_dupenukem($this->tbl_new)) {
					return false;
				}
			} else {
				return false; // there was some sort of a problem
			}
		}
	} else { // they want to truncate
		if(ALLOW_TRUNCATE === 1) {	// user is allowed in
			$success = 1; // success for truncate
			if(strcmp($table,'both')) {  // $action not equal to both, can only be either good, new, or banned
				if(!$this->query_truncate($table)) {
					return false; // there was some sort of a problem
				}
			} else { // need to truncate both
				if($this->query_truncate($this->tbl_good)) {
					if(!$this->query_truncate($this->tbl_new)) {
						return false;
					}
				} else {
					return false; // there was some sort of a problem
				}
			}
		} else {
			return false;
		}
	}
	
	
	if(isset($success)) {	// if we get to this point, all is well
		$this->countIps();	// update the ip count
		return $success;
	} else {
		return false;
	}
	
}#-#maintainDatabase()


#-#############################################
# desc: check a proxy, or with multi-threading, check a list.
#       time to incorporate this function as part of the main class and not an external script
#       fucking multi-threading, how does it work?
# Param: array,string
# returns: true if successful, false if failure
function check($arrProx,$proxtype) { // we be checkin
	if($proxtype == TBL_NEW) { $newmax = MAX_CONNECTIONS * 2; } else { $newmax = MAX_CONNECTIONS; }	// compensate for duplicates
	$listcount  = count($arrProx); 		       // needed multiple times
	$numcon     = floor($listcount / $newmax); // main loop counter
	$numcon	   += 1;						   // this is how we do the remainder
	$yacntr     = 0;
	$cntrgood   = 0;
	$cntrbad    = 0;
	$arrprox4   = array(CURLOPT_PROXYTYPE => 'CURLPROXY_SOCKS4'); // if there are not quotes around these
	$arrprox5   = array(CURLOPT_PROXYTYPE => 'CURLPROXY_SOCKS5'); // this bullshit fails to work
	$arrhttp    = array(CURLOPT_PROXYTYPE => 'CURLPROXY_HTTP');	// but of course http still worked adding to the confusion
	
	for($i=0; $i < $numcon; $i++) {	// here be dragons 
		$curl        = new CURL();  // create the curl instance
		$connectlist = array();
		
		for($j=0; $j < $newmax; $j++) {
			if($yacntr >= $listcount) { break; }	// this will happen now because we are looping one more extra time to get the last of them
			$opts    = $this->setBrowser(0,0,$this->cheader,$arrProx[$yacntr]['useragent']);  // set browser information
			$goodop  = $opts[0];
			$ipnport = $arrProx[$yacntr]['ip'].":".$arrProx[$yacntr]['port'];
			$yaar    = array(CURLOPT_CONNECTTIMEOUT  => PROXY_TIMEOUT,
							 CURLOPT_TIMEOUT		 => PROXY_TIMEOUT,
							 CURLOPT_PROXY			 => $ipnport);
								  
			if($arrProx[$yacntr]['type'] == "socks4") {
				$elprox    = $arrprox4;
				$proxytype = "socks4";
			}
			else if($arrProx[$yacntr]['type'] == "socks5") { 
				$elprox   = $arrprox5;
				$proxytype = "socks5";
			} else {
				$elprox   = $arrhttp;
				$proxytype = "http";
			}
			
			$connectlist[$j][0] = $arrProx[$yacntr]['ip'];
			$connectlist[$j][1] = $arrProx[$yacntr]['port'];
			$connectlist[$j][2] = $proxytype;
			
			$yacntr++;
			$this->array_push_associative($yamemkill,$goodop,$yaar,$elprox);
			foreach ($yamemkill as $key => $value) { // get rid of empty elements ruining our fun due to the offending function above
		      if (is_null($value) || $value== "") {
		        unset($yamemkill[$key]);
		      }
		    }
			$curl->addSession('http://www.google.com',$yamemkill);
		}
		
		$result = $curl->exec();
		if($proxtype == TBL_NEW) {	// we need to play games with the counter
			for($k=0; $k < count($result); $k++) {	// loop through the result set checking for the needle
				if(($result[$k] == FALSE) || (strpos($result[$k],$this->needle) === FALSE)) {
					$this->removeProxy($connectlist[$k][0],$proxtype);  // bad
					$cntrbad++;
				} else {
					$yatmpvar = array('ip' => $connectlist[$k][0], 'port' => $connectlist[$k][1], 'type' => $connectlist[$k][2]);
					$this->insertGoodProxy($yatmpvar,time());
					$this->removeProxy($connectlist[$k][0],$proxtype); // good new proxy
					$cntrgood++;
				}
			}
		} elseif($proxtype == TBL_INACTIVE) {
			for($k=0; $k < count($result); $k++) { // loop through the result set checking for the needle			
				if(($result[$k] == FALSE) || (strpos($result[$k],$this->needle) === FALSE)) {
					$cntrbad++;
				} else {
					$eltime = time();
					$where  = 'ip=\''.$connectlist[$k][0].'\'';
					$column = array('recent' => $eltime);
					$this->query_update($proxtype,$column,$where);				// update timestamp
					$this->copyProxy(TBL_GOOD,$proxtype,$connectlist[$k][0]);	// copy from inactive to good table (aka dont copy that floppy)
					$this->removeProxy($connectlist[$k][0],$proxtype); 			// good inactive proxy
					$cntrgood++;
				}
			}
		} else {
			for($k=0; $k < count($result); $k++) { // loop through the result set checking for the needle			
				if(($result[$k] == FALSE) || (strpos($result[$k],$this->needle) === FALSE)) {
					$this->copyProxy(TBL_INACTIVE,TBL_GOOD,$connectlist[$k][0]);	// copy from good to inactive table (aka dont copy that floppy)
					$this->removeProxy($connectlist[$k][0],$proxtype);  			// remove from good table
					$cntrbad++;
				} else {
					$eltime = time();
					$where  = 'ip=\''.$connectlist[$k][0].'\'';
					$column = array('recent' => $eltime);
					$this->query_update(TBL_GOOD,$column,$where);	// update timestamp
					$cntrgood++;
				}
			}
		}
		
		if($proxtype == TBL_NEW) {
			$cntrbads  = ceil($cntrbad / 3);	// this number isnt exact but meh
			$cntrgoods = ceil($cntrgood / 3);	// fuck it yo
		} else {
			$cntrbads  = $cntrbad;
			$cntrgoods = $cntrgood;
		}

		echo '<script type="text/javascript">
					document.getElementById(\'tehgoodresults\').innerHTML = \'Working proxies:'.$cntrgoods.'\';'."\n".'</script>';
		
		echo '<script type="text/javascript">
					document.getElementById(\'tehbadresults\').innerHTML = \'Failed proxies: '.$cntrbads.'\';'."\n".'</script>';
		
		ob_flush();
		flush();

		$curl->clear();  // remove the curl instance
		unset($curl);
	}
	
	if(AUTO_DUPE_REMOVE == 1) {	// make sure their are no duplicates after testing
		$this->query_dupenukem(TBL_GOOD);
	}
	
	return true;
}#-#check()


// takes a url (or array of urls), and searches the page for ip:port or just ip, and returns an array list
function grabSimpleIps($url,$yaheader,$agentstr, $noport=0) {
	if(is_array($url)) { // randomly choose a url out of the array before making the connection
		$randkey    = array_rand($url); // select a random $url page
		$urltocheck = $url[$randkey];
	} else { // its just a single url
		$urltocheck = $url;
	}
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($urltocheck,$opts[0]);
	
	$result = $curl->exec();  // this is the site returned

	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
		
	$matches = array();
	$goodmatches = array();
	$yacntr = 0;
	
	// Strip out ip:port and load into array $matches
	if($noport == 1) {	// search for ip
		$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b)|U';
		preg_match_all($pattern,$result,$matches);
	
		foreach($matches[0] as $matchfun) {
			$goodmatches[$yacntr][0] = $matchfun;
			$yacntr++;
		}
	} else {	// default: search for ip:port
		$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U';
		preg_match_all($pattern,$result,$matches);
	
		foreach($matches[0] as $matchfun) {
			$arrtmp = explode(":",$matchfun);
			$goodmatches[$yacntr][0] = $arrtmp[0];
			$goodmatches[$yacntr][1] = $arrtmp[1];
			$yacntr++;
		}
	}

	unset($result);
	unset($agentstr);

	if(empty($goodmatches)) { // bark at them
		return false;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//  	Scrape Proxy IP addresses from hidemyass.com	//
//////////////////////////////////////////////////////////
// hidemyass.com has been 0wned by hysterix
// grab_hidemyassproxy - grabs a list of proxies from hidemyass
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_hidemyassproxy($yaheader,$agentstr) {
	// a dark version, and a light version of each port awaits thou
	$ghetto_tables = array(  // long ghetto_tables array
		'528316fbe176e5183a7e8d6dfa1e8642' => '53',	//    /\___/\
		'1911682ccb7ac363e6709db3c2b5a657' => '53',	//   /       \
		'28b033e5bb6c2310dbcc2a29d2568fea' => '80',	//  |  #    # |
		'f15b3f5fff30bfc09c2b4bd763c41f15' => '80',	//  \     @   |
		'36dc8567f3a83626d2d9feffe60de541' => '81',	//   \   _|_ /
		'7533231cf9ccaf782fb1b2924d7b380f' => '81',	//   /       \______
		'e1093fa240351d778e941b6cd69f9eeb' => '88',	//  / _______ ___   \
		'c587302b5cb4bc425627bf9f6314acc0' => '88',	//  |_____   \   \__/
		'79236b68978a7ec7e003f42d8477a4ed' => '443',	//  |    \__/  
		'00760ce34beb3a8a8b6e2e06f4a10607' => '443',	//  |       |
		'33dc2070baed61a6725fa59bb0833ea3' => '444',	//  |       |
		'2028a73636d5ceeba00c778b0194bbdb' => '444',	//  |       |
		'452c329de7cdd8c604c261040af17e36' => '445',	//  |       |
		'8e40957c520df1a315108c6a5b834346' => '445',	//  |       |
		'2e94387d03a3c7c40d28661c2d66a046' => '465',	//  |       |
		'4b4bd028af4fd21c6a7d5e3ae4a7b16b' => '465',	//  |       |
		'63eb5612bea46aebdc903d6fd10f2254' => '554',	//  |       |
		'3f19442a3f9f2f5a7f9be369d6381b5e' => '554',	//  |       |
		'7917755811b8b59619096184c14275d5' => '808',	//  |       |
		'a77e46133be016df522034b4b423d16f' => '808',	//  |       |
		'28e1476d6f569c9a418742e93830ffbd' => '847',	//  |       |
		'714d1028e551e8f9dfa803df17812706' => '847',	//  |       |
		'bbdcef56c147094c4d480732a9d16834' => '1025',	//  |       |
		'66247055577a00feea36820d86246c51' => '1025',	//	|		|
		'3fb212fb53a63bc20d7c08fbc621e8b8' => '1080',	//  |       |
		'b41b30c418fb1cacefbc381f8130b87f' => '1080',	//  |       |
		'7ce7866ab10e0442a5346ddc3d87d9e1' => '1081',	//  |       |
		'f37a535849709986c7079b9dc00db847' => '1081',	//  |       |
		'509c7a046b60659e8729885045e4b081' => '1086',	//  |       |
		'b1531e03771376b3dff1e07a6d10802c' => '1086',	//  |       |
		'15c75f02a6299ef7c5c635499ab9f57f' => '1088',	//  |       |
		'97741eb52864c83d807685fcba167d7a' => '1088',	//  |       |
		'8ec9b0fd06f1697fe1acf16be143bb95' => '1092',	//  |       |
		'37230b95501b98c51221700ee48b0041' => '1092',	//  |       |
		'4edbfeeb2fe8dfedc3880f344a967e3c' => '1175',	//  |       |
		'de02ed9b7c6f177cadf0031a813de8bf' => '1175',	//  |       |
		'e52cc44d3e07330000c6bd72a0dc237c' => '1337',	//  |       |
		'31a1e268876f6d14e560230039b4b8b9' => '1337',	//  |       |
		'83f4ebede25d5df95424313bd248bf7d' => '1388',	//  |       |
		'708b6d548c532e537915ebb6f4c22138' => '1388',	//  |       |
		'aa750e4bee82af01741d7e4adbfe1767' => '1390',	//  |       |
		'f8548f15e14765c5d28d930add01b150' => '1390',	//  |       |
		'cc03685591754fc2ce7f8f07447a2e46' => '1413',	//  |       |
		'6d735c6f5538a5eb8a49b4cc28c50354' => '1413',	//  |       |
		'cace5ba82111f3a85591de723553abb4' => '1432',	//  |       |
		'24c899c0a4ae19d1190835db6c4a375f' => '1432',	//  |       |
		'13900c4d6ba4d9030ccbffefd2057c19' => '1562',	//  |       |
		'51b45b3f1eea4e6fac94532f3f393260' => '1562',	//  |       |
		'4d0caed66be74fa253fce3feba2c55d1' => '1762',	//  |       |
		'fb0978c95f2164a33b81ab73517464f3' => '1762',	//  |       |
		'8f126caf67194041e191a63b70589fd6' => '1896',	//  |       |
		'cd0712cc4dc879c24ff0f6052eafe5cf' => '1896',	//  |       |
		'6cbe32bf23b43da141a96b81a6f7126d' => '2278',	//  |       |
		'17b069d0b4c4fef03949516634ce3468' => '2278',	//  |       |
		'6f4e7a8c4472483b3a4d360682a84a79' => '2301',	//  |       |
		'8f67cd12fd7bc40632e31d1d8f79da3c' => '2301',	//  |       |
		'4175ca8301114c1b8e2ae810f188de10' => '2448',	//  |       |
		'db4b8be8e4c19db8e641dd36f9728808' => '2448',	//  |       |
		'30a0150aab861d4f8e040fe830cd94e3' => '2479',	//  |       |
		'9e216e98fc1d7309ee2396cea81fa468' => '2479',	//  |       |
		'cd9f93e5fb75deffa611279d08cfa8b1' => '2562',	//  |       |
		'8b03ff8c84ba8768d9f1a5b0fa21c1db' => '2562',	//  |       |
		'22b56a78144814a70a9c67bbc9ddc80e' => '3124',	//  |       |
		'2e1a240131e56f46f8aff7f98ffc6dbb' => '3124',	//  |       |
		'a254c332d1b5d11133c556d65742eaf5' => '3127',	//  |       |
		'f512c037a46a6ebffcfce49042b5292b' => '3127',	//  |       |
		'afb1a67d9ed18d380601c83a5329792a' => '3128',	//  |       |
		'fa017f1edc0841a8ce40ff0f42d724a0' => '3128',	//  |       |
		'c197d9077a160d24d1a6cbde180339d6' => '3129',	//  |       |
		'a980f251552457b24bfc7b7515a9b3c9' => '3129',	//  |       |
		'2c6d62deb92675d9c16a33b378a42ab7' => '3246',	//  |       |
		'b1ff1e417c366308a0a538c43534f2e4' => '3246',	//  |       |
		'4ccfdf29ff8dbb9aae470f5f3edd193f' => '4148',	//  |       |
		'112844343f066ba21a21b43d4b04467a' => '4148',	//  |       |
		'63beff3a38062c3883076159302bdb33' => '4292',	//  |       |
		'8cc877efae2b17a738d74605d965d5b8' => '4292',	//  |       |
		'343433f89d9522bd1ca802220a637678' => '5065',	//  |       |
		'f1dc67a873d95e0b59e737e50e188434' => '5065',	//  |       |
		'24a727e079f45a4aa11dc87d4c523e21' => '5555',	//  |       |
		'4565bace2b47cca3d364eec8597ccf1a' => '5555',	//  |       |
		'3401aa89a246c3a66994ab3fdf0d9891' => '6051',	//  |       |
		'2c0d1f2a951137e5a33ec29ac84fd037' => '6051',	//  |       |
		'cc578d154ead31405b22f2b666f23bdb' => '6052',	//  |       |
		'7d41e96225fdc0ccb8da1897ad607610' => '6052',	//  |       |
		'3d88f13362cc2b2540cf4a3165ecdd74' => '6414',	//  |       |
		'91d3d03cd76f007092ba86a846322b12' => '6414',	//  |       |
		'dfd43fe89d3978cf9b8c334d621e8d7b' => '6536',	//  |       |
		'0f28a4202900b6c2191e825cff56bf55' => '6536',	//  |       |
		'f54de9bd958836a94217317d597af57d' => '6588',	//  |       |
		'0db84186f01712e47c85ffd06b7f661b' => '6588',	//  |       |
		'1efb504e30691d6277c3e899191279b9' => '6649',	//  |       |
		'f9c844209f7816c16cf77f2298e476f8' => '6649',	//  |       |
		'c1865c575ac4645f47b603deaf98ca60' => '6654',	//  |       |
		'a580f19646451c4d832303b596fe6248' => '6666',	//  |       |
		'ea8e167e8287537354e83356593af333' => '7212',	//  |       |
		'a46eb86534b0caca7520f4251233adff' => '7212',	//  |Longcat|
		'af35170879a8fc61b89d104516351dcd' => '7679',	//  |  is   |
		'2910a82577b9a005fd2903e13d1bff03' => '7679',	//  | long  |
		'56e0d773be5601ed2395f89234952f01' => '8000',	//  |       |
		'e69ca1ad59f94e2dd2013ec8fae9af03' => '8000',	//  |       |
		'209c05a18a7e4225c767398af1684008' => '8001',	//  |       |
		'ae7ac00e4f4466d91476a30b44e7e3f1' => '8001',	//  |       |
		'79bc01079790cb632eaca1fedd68ed21' => '8002',	//  |       |
		'9be8cdfaf6f602fdadfe48dff925797e' => '8002',	//  |       |
		'd093aea7289824a16f165acb487fc23e' => '8008',	//  |       |
		'd772c332823bfa3fc4a27273a206ec8b' => '8008',	//  |       |
		'ada22479d992d237e230b09ca121b801' => '8080',	//  |       |
		'fc79f35eeefb7105ebdaf319e2257b7e' => '8080',	//  |       |
		'2a6b4db45e5bde60abfd59f9db09c395' => '8081',	//  |       |
		'6fb5c495d638f4dffa8efcecbe20a67d' => '8081',	//  |       |
		'45527cc8666af638eac5021beb4137b2' => '8082',	//  |       |
		'e3721c1dd86511d686d96fc8524450f9' => '8082',	//  |       |
		'e488059d9336e8b4ca1e89852a2a5bda' => '8085',	//  |       |
		'a7049674569921eccf4093a73712367a' => '8085',	//  |       |
		'00af4668183daf7ddc6d01487d9bd506' => '8086',	//  |       |
		'3545bc1b026bd9b3cd18383b5511c1da' => '8086',	//  |       |
		'4e73f3ab197c4c4a8052d0d9dc3d4fc8' => '8088',	//  |       |
		'f35e96b10160c358617af4f9d97a1889' => '8088',	//  |       |
		'f49290ffaf32a2b5a9587883a071bf82' => '8081',	//  |       |
		'213931996d5243b689fcc6cab0afa5ae' => '8081',	//  |       |
		'7917c66d6464e864b1db23d07baba463' => '8089',	//  |       |
		'ee83db4bb77a7592cca9df8b1f0779af' => '8089',	//  |       |
		'8ad93b0c23998d86f972415d4f695b43' => '8090',	//  |       |
		'666f2496877028df38aa02da1af0bed7' => '8090',	//  |       |
		'df12bfdd82497416a8fae8f9ed68cb3c' => '8109',	//  |       |
		'e31043ff27d80576fd2beb959971b83c' => '8109',	//  |       |
		'31df49347cfb2ce4c8f6b32a569044ba' => '8118',	//  |       |
		'aba5e37fb0404ad776364f2dc2cc033f' => '8118',	//  |       |
		'4df4c42f34a121ba25e5adc5432bb1d2' => '8123',	//  |       |
		'd4e682b85b4933512bec4ed039bda08b' => '8123',	//  |       |
		'744b1b1f7e9807a1afcf4edd86ac3c5d' => '8125',	//  |       |
		'be44090263968b1af9958a7241f8347b' => '8125',	//  |       |
		'323f6226bea0b3fa5b19ec4283d23864' => '8126',	//  |       |
		'2bd980c40fe3b5d1b1cbda960f65d206' => '8126',	//  |       |
		'1e1d2a53e8ecf720d90adbe750de3d2d' => '8129',	//  |       |
		'db047a1d9ee6867ca22d8975fd8422a2' => '8129',	//  |       |  
		'77459abae454671781d120acc50f2661' => '8129',	//	|		|
		'07da4d99cd092dd17e49c6e340918d56' => '8129',	//	|		|
		'b40f21bcc445d5e431c1e5d9db3282a2' => '8132',	//  |       |
		'bb63743577f1e32dafe3cba6968c9eaf' => '8132',	//  |       |
		'e7319587752ac80e32f3c8c617c416b3' => '8134',	//  |       |
		'ab19b833a9b4353c3398a35a2f5c0e7a' => '8134',	//  |       |
		'a313e8b550dc8bd3298a57e73fab196f' => '8136',	//  |       |
		'4e158dfbe4f3f7df1ccc3709535ccf50' => '8136',	//  |       |
		'3fda05fdafc57916ace630df2bfe5a74' => '8137',	//  |       |
		'a364e01193059ea23233ff49619492dc' => '8137',	//  |       |
		'0b269894b8fc6b74adb92840528b4e33' => '8118',	//  |       |
		'09c376ebe328558e8319eac2b3cd2371' => '8118',	//  |       |
		'5f3a6709f837b0533f1b030461505d93' => '8133',	//  |       |
		'098ecafc2c49ccbc364ea47676a0db93' => '8133',	//  |       |
		'6a20d96a6895ace31a827a5faa545769' => '8135',	//  |       |
		'27e3259488f8f007b401e2d9d5fb955e' => '8135',	//  |       |
		'08e94ebb733f22797e272fc9ced8cdbb' => '8138',	//  |       |
		'9a95cf6a66a404b5f15e172379fea6fe' => '8138',	//  |       |
		'2ce0f70b5d2c1fc219bfeaddbefaa0b0' => '8223',	//  |       |
		'3ed73976f615100191852b8b9955f4c5' => '8223',	//  |       |
		'd3c86ed361d3a445805819afec91acc4' => '8230',	//  |       |
		'05fd17190e57a2f3bfdcaf22ee3973c3' => '8230',	//  |       |
		'659dd55bf806fa7d21d3ef052b7c65dd' => '8403',	//  |       |
		'5615176572fb2cfe96e2e5958e0cf15f' => '8403',	//  |       |
		'70187a912bc48e975ed95bacfd4bfe0e' => '8628',	//  |       |
		'055bbf375c1a8416e44f596fbb08a855' => '8628',	//  |       |
		'5c3bd335a1996abd31372b8cbfc13019' => '8888',	//  |       |
		'33d4890bc05f3003b171b4ee9fd6dd12' => '8888',	//  |       |
		'81f2a722e1dc23dc3535d5aa452f42b2' => '9000',	//  |       |
		'940e31e0d2d9a3407a748bc91fec3b0d' => '9000',	//  |       |
		'513428e08619a0aaf2c200f67e5561f8' => '9027',	//  |       |
		'69c4bc6b99fcf70829b08e72e47cf2ba' => '9027',	//  |       |
		'760e597c220fc366014ca07ff74a60bf' => '9039',	//	|		|
		'a7742195af90513d0c3eed4d63cb6336' => '9039',	//	|		|
		'b84d77eeb47f5e41dd3bfe624626ed14' => '9090',	//  |       |
		'997ea73c02b9c1ab1dec7d1f13a315e8' => '9090',	//  |       |
		'e517db056ea9014a05c2ba4dc3a17fb3' => '9100',	//  |       |
		'58be75d0113e97fd1c7f7341c9929ba2' => '9100',	//  |       |
		'b44952d72bf8d2517be0e93f6412fd73' => '9415',	//  |       |
		'df87f32f807c61b377f52934c1fda5e3' => '9415',	//  |       |
		'3bb7f947a7a680800da5d08dd0fa968d' => '9492',	//  |       |
		'f26ddb2f8db621e0d7fc30f843761928' => '9492',	//  |       |
		'68d8fa446510fe89a5d1a5d657f22b76' => '10080',	//  |       |
		'5f3a70dd8453e4fac81e67835f75d29e' => '10080',	//  |       |
		'10fdb1bb0b8eb2f5349fb2a598f91d29' => '11011',	//  |       |
		'0a35a8f571203d11e0a5ba9cff26362d' => '11011',	//  |       |
		'c565dbb052265a63b92bbc4bf55f1496' => '11022',	//  |       |
		'eab3556a3964b6b790c179cfbfcfb59d' => '11022',	//  |       |
		'90a2390568e1709b496f8e1af979cd4f' => '11055',	//  |       |
		'1264316bdb66831570602cc239e92d36' => '11055',	//  |       |
		'ca425b07142e3f46990438ada7455015' => '11825',	//  |       |
		'1db6f7b88c2583694514276f0896ad3f' => '11825',	//  |       |
		'ad9bf85d161d6174d7c366efa2c45663' => '16589',	//  |       |
		'55c7502d85abbad1e939fcd1f90a6cd0' => '16589',	//  |       |
		'8030c17a930764f7d8059402677b3b92' => '17327',	//  |       |
		'aa8e1ae75556771dc06a13d35e3eb76b' => '17327',	//  |       |
		'1b5fc45b4a3c0ffc19aa61a219d30502' => '18231',	//  |       |
		'd3a6a41a4511ab2d8856cfa4b4b0f2c7' => '18231',	//  |       |
		'0247fe232cd0d083beda44b8c08647f0' => '18233',	//  |       |
		'0235c4649a11a7f73fd27a744d5c463c' => '18233',	//  |  THE  |
		'2910a82577b9a005fd2903e13d1bff03' => '18888',	//  | GAME  |
		'cbfac0cfdcd933396448957b32d43338' => '18888',	//  |       |
		'704bf964bcac509aaed680a2dbb04f1f' => '19671',	//	|		|
		'e6792da0cb7f3d2bf497d4ee599883b8' => '19671',	//	|		|
		'1b657586ac477e237fbec4469c429ea8' => '27129',	//  |       |
		'43dbf77ccb83ae5dda3d3113f4213370' => '27129',	//  |       |
		'd1a8e3611350a9504e37a3ee1fb08fc7' => '29505',	//  |       |
		'd577e14490c68ba49c4878a3260287ad' => '29505',	//  |       |
		'211e9938dfb2778efd690fdec4c4cf5a' => '31332',	//  |       |
		'93d1756623b25e5866f463949cd69fc8' => '31332',	//  |       |
		'2add86275ff8f7939e60c1ac2bf6027d' => '31333',	//  |       |
		'5855d06a1545e01d812b69303fe04636' => '31333',	//  |       |
		'd35b453ab2147cd6be01f900db4fdc81' => '31337',	//  /        \
		'6b871070a9c303569a3ef193cde2fa04' => '31337',	// /   ____   \
		'50fda9cc6fcf82f37684242c13521c92' => '49400',	// |  /    \  |
		'8d66c802588738c4260ca5d12c49fa26' => '49400',	// | |      | |
		'fe2ca2b8c285c97a214e748410b0f8ba' => '65208',	///  |      |  \
		'68c8b41718ba1da4be0f7cd12b22adf7' => '65208'	//\__/      \__/
	);		// is long :)
	
	// Gather an array of proxy lists (over 9000 proxies in this list)
	// no /hide-planetlab/ url anymore :/
	// but no matter; a blacklist has been implemented so fuck em'
	
	// we be timin!
	//$script_start = $this->microtime_float();
	//$script_end   = $this->microtime_float();
	
	//$script_start = $this->microtime_float();
	if(HIDEMYASS_RAPE_LEVEL > 25) {	// lol too high
		$rlevel = 25;
	} elseif(HIDEMYASS_RAPE_LEVEL <= 1) {
		$hma = 'http://hidemyass.com/proxy-list/1';
	} else {
		$rlevel = HIDEMYASS_RAPE_LEVEL;
	}
	
	$proxyUrl = array();
	$curl     = new CURL();  // create the curl instance
	$opts     = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping, or do we? :)
	$curl->retry = MAX_RETRYS;
	
	if(!isset($hma)) {
		$hma = $this->uniqueRand($rlevel,1,25);	// random numbers between 1-25 (the highest page was 45 one day, today its 28, so lets keep it low)
		for($i=0;$i<$rlevel;$i++) {	// how badly do we want to rape hidemyass today? set in config.php today!
		    $proxyUrl[] = "http://hidemyass.com/proxy-list/".$hma[$i];
			//echo 'scraping '.$proxyUrl[$i].'<br />';
			$curl->addSession($proxyUrl[$i],$opts[0]);
		}
	} else {
		//echo 'scraping '.$hma.'<br />';
		$curl->addSession($hma,$opts[0]);
	}
	
	//$script_end   = $this->microtime_float();
	//$exectime	  = bcsub($script_end, $script_start, 4);
	//echo "the exectime of the first section (loops) was: $exectime<br />";
	//ob_flush(); // lets get this out to the browser faster 
	//flush();	// so users dont think the page is freezing up
	
	$script_start = $this->microtime_float();
	$result = $curl->exec();  // this is the site (or sites) returned
	$rscnt  = count($result);
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl
	
	$script_end   = $this->microtime_float();
	$exectime	  = bcsub($script_end, $script_start, 4);
	//echo "the exectime of the second section (url grab) was: $exectime<br />";
	//ob_flush(); // lets get this out to the browser faster 
	//flush();	// so users dont think the page is freezing up
	$script_start = $this->microtime_float();
	$ipmatches   = array();
	$portmatches = array();
	$portpath    = array();
	$secondaryp  = array();
	
	$result = $this->md_array_flatten($result);		// flatten array
	
	for($i=0;$i<$rscnt;$i++) {	// loop through the array of sites
		$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b)|U';
		preg_match_all($pattern,$result[$i],$ipmatches[$i]);	// grab ips
	}
	
	for($i=0;$i<$rscnt;$i++) {	// loop through the array of sites
		$pattern = '|(\/proxy-list\/img\/port\/\d{1,20}?\/\d{1}?)|U';	// "/proxy-list/img/port/3986484/0"
		preg_match_all($pattern,$result[$i],$portmatches[$i]);			// grab port urls
	}
	
	for($i=0;$i<count($ipmatches);$i++) {
		unset($ipmatches[$i][1]);	// eliminate second element which are duplicates;  not doing this was
		unset($portmatches[$i][1]);	// slowing us down by half because we had double the urls to scrape!
	}								// it was slowing us down by a magnitude of three with HIDEMYASS_HASHIFIER set on
									// because proxybot will request the other colored port image for hashing purposes!
	$ipmatches   = $this->md_array_flatten($ipmatches);		// flatten array
	$portmatches = $this->md_array_flatten($portmatches);	// flatten array
	
	for($i=0;$i<count($ipmatches);$i++) {	// build url path
		$portpath[$i] = 'http://hidemyass.com'.$portmatches[$i];
	}
	
	$curl = new CURL();  // create the curl instance
	$curl->retry = MAX_RETRYS;
	
	for($i=0;$i<count($portpath);$i++) {	// let's multi-thread the image request to speed things up
		$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1
		$curl->addSession($portpath[$i],$opts[0]);
	}
	
	//$script_end   = $this->microtime_float();
	//$exectime	  = bcsub($script_end, $script_start, 4);
	//echo "the exectime of the third section (loolps) was: $exectime<br />";
	//ob_flush(); // lets get this out to the browser faster 
	//flush();	// so users dont think the page is freezing up
	//$script_start = $this->microtime_float();
	
	$result2 = $curl->exec();	// this is an image port
	$curl->clear();  			// remove the curl instance
	unset($curl);
	
	//$script_end   = $this->microtime_float();
	//$exectime	  = bcsub($script_end, $script_start, 4);
	//echo "the exectime of the fourth section (img port get) was: $exectime<br />";
	//ob_flush(); // lets get this out to the browser faster 
	//flush();	// so users dont think the page is freezing up
	//$script_start = $this->microtime_float();

	for($i=0;$i<count($result2);$i++) {	// loop through image ports
		$pattern = '|(.{1,)|U';							  // i was like a nub md5'ing the image along with the header data....doh!
		$result2[$i] = preg_replace($pattern, ':', $result2[$i]); // but thats okay because it inspired me to make it really easy to collect new hashes
		
		if(is_array($result2[$i])) { 
			$tmp = $result2[$i][0];
			if(isset($result2[$i][1])){
				$tmp .= $result2[$i][1];
			}
			$result2[$i] = $tmp;
		}
		
		$pattern = "\r\n\r\n";
		$tmppos = strpos($result2[$i],$pattern);
							    					    
		if (!$tmppos === false) {
			$actualimg = substr($result2[$i],$tmppos+4);
		}
		
		$actualimg .= 'T';	// this is MADNESS! Talk about insanity....I already corrupted the md5s....AGAIN...AFTER REDOING IT A THIRD/FOURTH FUCKING TIME; I AM AND IDIOT
		
		if(HIDEMYASS_HASHIFIER === 1) {	// dont do all this bullshit unless we need to
			$lastport =  substr($portpath[$i],0,-1);			// fix up the secondary url
			$last     = $portpath[$i][strlen($portpath[$i])-1];	// grab the last character in the string 
			if($last == 0) {
				$lastport .= '1';
			} else {
				$lastport .= '0';
			}
			
			$curl = new CURL();  // create the curl instance
			$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1
			$curl->retry = MAX_RETRYS;
			$curl->addSession($lastport,$opts[0]);
			
			$result3 = $curl->exec();	// this is an array of image ports returned
			$curl->clear();  			// remove the curl instance
			unset($curl);
			
			$pattern  	 = '|(.{1,)|U';		// i was like a nub md5'ing the image along with the header data....doh!
			
			$result3 	 = preg_replace($pattern, ':', $result3);
			if(is_array($result3)) { 
				$tmp = $result3[0];
				if(isset($result3[1])){
					$tmp .= $result3[1];
				}
				$result3 = $tmp;
			}
			
			$pattern = "\r\n\r\n";
			$tmppos = strpos($result3,$pattern);
								    
			if (!$tmppos === false) {
			    $actualimg2 = substr($result3,$tmppos+4);
			}
			
			$actualimg2 .= 'T';	// apparently i was concatenating an array incorrectly and somehow was appending the 
								// letter 'T' to the end of the port image hash.  If you don't append this letter to
								// the image before hashing it, the hash will be wrong; I don't want to redo the hashes again :/
								// debugging this was a bitch
			$portnum  = $this->retPortNum($actualimg,$ghetto_tables);
			$portnum2 = $this->retPortNum($actualimg2,$ghetto_tables);
			if((!isset($portnum)) || (!isset($portnum2))) { // failed! display easy-to-use-hash-image-converter-9000
				if(isset($hma)) { echo 'url was: <pre>'; print_r($hma); echo '</pre><br />'; }
				echo 'actual img '.$actualimg.'<br />';
				echo 'actual img2 '.$actualimg2.'<br />';
				
				$afterencode = base64_encode($actualimg);
				$yamemkil = '<a href='.$portpath[$i].'><img src="data:image/png;base64,'.$afterencode.' "></a>';
				
				$thehash = hash("md5",$actualimg);
				echo "$thehash actualimg $yamemkil<br />";
				
				$afterencode2 = base64_encode($actualimg2);
				$yamemkil2 = '<a href='.$lastport.'><img src="data:image/png;base64,'.$afterencode2.' "></a>';
				
				$thehash = hash("md5",$actualimg2);
				echo "$thehash actualimg2 $yamemkil2<br /><br />";
				$exitstatus = true;
			}
			ob_flush(); // lets get this out to the browser faster 
			flush();	// so users dont think the page is freezing up
		}

		// ingenuity wins out yet again; hysterix - 3 ; hidemyass - 1 <-- they get one point for making me change urls, strip out header info, and re-hash the ports
		// they cant even slow us down anymore
		$portnum = $this->retPortNum($actualimg,$ghetto_tables);
		if(isset($portnum)) {	// everything worked; we only want ip's with ports
			$matches[$i][0] = $ipmatches[$i];
			$matches[$i][1] = $portnum;
		}
	}
	
	if(isset($exitstatus) && (HIDEMYASS_HASHIFIER === 1)) { // dont do all this bullshit unless we need to
		echo "<br /><br />There were some matches at least:<br />";
		print_r($matches);
		unset($result3);
		exit;	// we need this exit if they want to copy paste hashes, because without this, the new page loads and all the hashes go bye-bye
	}
	
	unset($result2);
	unset($agentstr);
	
	if(count($matches) < 1) { // bark at them
		return 0;
	} else {
		return $matches; // return something
	}
}


//////////////////////////////////////////////////////////
//   Scrape Proxy IP addresses from proxy-list.net      //
//////////////////////////////////////////////////////////

// grab_proxylist - grabs a list of proxies from proxylist
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_proxylist($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://www.proxy-list.net/anonymous-proxy-lists.shtml',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//  Scrape Proxy IP addresses from freeproxylists.com   //
//////////////////////////////////////////////////////////

// this site gets raped pretty bad; especially because they are trying to prevent it!
// grab_freeproxylists - grabs a list of proxies from freeproxylists
// we have to jump through hoops to get these but its worth it
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_freeproxylists($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.freeproxylists.com/elite.html";		// step one: grab elite proxy lists
	$proxyUrl[1] = "http://www.freeproxylists.com/anonymous.html";  // step one: grab anonymous proxy lists
	$proxyUrl[2] = "http://www.freeproxylists.com/https.html"; 		// step one: grab https proxy lists
	$proxyUrl[3] = "http://www.freeproxylists.com/standard.html"; 	// step one: grab standard proxy lists
	$proxyUrl[4] = "http://www.freeproxylists.com/socks.html"; 		// step one: grab socks proxy lists
	
	$randkey = array_rand($proxyUrl); // Select a random $proxyUrl page
	
	switch($randkey) {	// change up our keyword depending on what kind of proxy we happened to pick
			case 0:
				$ptype = 'elite';
				break;
			case 1:
				$ptype = 'anon';
				break;
			case 2:
				$ptype = 'https';
				break;
			case 3:
				$ptype = 'standard';
				break;
			case 4:
			default:
				$ptype = 'socks';
				break;
				
	}
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[$randkey],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned

	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}

	$matches    = array();
	$newmatches = array();							// step two: grab the urls we want
	$pattern    = "/".$ptype."\/\b\d{1,20}?\b/";	// proxytype/1276703446.html
	$preplpat	= '/'.$ptype.'\//';					// 
	preg_match_all($pattern,$result,$matches);		// extract only proxytype/*.html urls
													// we be bypassin'
	foreach($matches[0] as $matchfun) { 		 	// we are removing /proxytype and building a url to bypass the js block
		$newmatches[] = 'http://www.freeproxylists.com/load_'.$ptype.'_'. preg_replace($preplpat,'',$matchfun).'.html'; // <-- clever girl
	}
	
	$yacntr = 0;
	$goodmatches = array();				// step three: ????
	foreach($newmatches as $matchfun) { //iterate through all new urls and grab ips:port
		$curl = new CURL();  			// create the curl instance
		$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping
	
		$curl->retry = MAX_RETRYS;
		$curl->addSession($matchfun,$opts[0]);	// proxytype proxy url
	
		$result = $curl->exec();  // this is the site returned
		
		$curl->clear();  // remove the curl instance
		unset($curl);	 // unset curl
		
		if(is_array($result)) { 
			$result = $result[0];
			if(isset($result[1])){
				$result .= $result[1];
			}
		}

		$matchessqr = array();
		$tmpmatch 	 = array();									// step four:  profit!
		$yacntr = 0;
		
		$pattern  	 = '|(&lt;/td&gt;&lt;td&gt;)|U';			// remove &lt;/td&gt;&lt;td&gt; and replace with colon
		$resultset	 = preg_replace($pattern, ':', $result);
				
		$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U'; // grab the ip:port combo
		preg_match_all($pattern,$resultset,$matchessqr);
				
		$webeloopin = 0;
		while(empty($matchessqr[0])) {	// sometimes loading the site quickly will result in blanks (dos protection?); load her again, and you will get it
			if($webeloopin > MAX_RETRYS*3) { break; }	// if we be loopin more than the max number of times squared, break out of the loop
			$curl = new CURL();  			// create the curl instance
			$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping
		
			$curl->retry = MAX_RETRYS;
			$curl->addSession($matchfun,$opts[0]);	// proxytype proxy url
		
			$result = $curl->exec();  // this is the site returned
			
			$curl->clear();  // remove the curl instance
			unset($curl);	 // unset curl
			
			if(is_array($result)) { 
				$result = $result[0];
				if(isset($result[1])){
					$result .= $result[1];
				}
			}
	
			$matchessqr = array();
			$tmpmatch 	 = array();									// step four:  profit!
			$yacntr = 0;
			
			$pattern  	 = '|(&lt;/td&gt;&lt;td&gt;)|U';			// remove &lt;/td&gt;&lt;td&gt; and replace with colon
			$resultset	 = preg_replace($pattern, ':', $result);
					
			$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U'; // grab the ip:port combo
			preg_match_all($pattern,$resultset,$matchessqr);
			$webeloopin++;
		}

		foreach($matchessqr[0] as $matchfunsqr) {
				$arrtmp = explode(":",$matchfunsqr);
				$goodmatches[$yacntr][0] = $arrtmp[0];
				$goodmatches[$yacntr][1] = $arrtmp[1];
				$yacntr++;
		}
	}
	
	$goodmatches = $this->arrayUnique($goodmatches);
	unset($result);
	unset($agentstr);

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//      Scrape Proxy IP addresses from xroxy.com	    //
//////////////////////////////////////////////////////////

// grab_xroxy - grabs a list of proxies from xroxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_xroxy($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.xroxy.com/rss"; // good ol' xroxy bans you if you load this page too much!  be prepared for xroxy to fail after more than one load
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[0],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned

	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$matches = array();
	$goodmatches = array();
	$yacntr = 0;

	// Strip out IP's and load into array $matches
	// rss frameworks? we dont need no stinkin rss frameworks! <-- if you dont get that reference, you are a piece of shit
	//$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})|U';
	//$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U';
	//$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b\r\n\t\d{1,7}\b)|U';
	//$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b.{1,10}\r\n\t.{1,10}\b\d{1,7}\b)|U';
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b.{1,5}.{1,10}?\n\t.{1,10}\b\d{1,7}\b)|U';
	preg_match_all($pattern,$result,$matches);
	
	$pattern = '|(</.{1,6}>\n\t<.{1,6}>)|U'; 
	
	foreach($matches[0] as $matchfun) {
		$result = preg_replace($pattern, ':', $matchfun);   // remove closing tag, newline, tab, opening tag, and replace with colon
		$arrtmp = explode(":",$result);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}

	unset($result);
	unset($agentstr);

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from ip-adress.com     //
//////////////////////////////////////////////////////////

// grab_ipadress - grabs a list of proxies from ip-adress
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_ipadress($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.ip-adress.com/proxy_list/?k=type"; // highest anonyminity first
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[0],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$matches = array();
	$goodmatches = array();
	$yacntr = 0;
	
	//$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b.{1,10}\r\n\t.{1,10}\b\d{1,7}\b)|U'; 
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}?\b)|U';  //they are not trying to fight us
	preg_match_all($pattern,$result,$matches);

	$yalol  = 0;
	foreach($matches[0] as $matchfun) {
		if($yalol > 2) {	// this is a sham; I want to save the ip:port every third iteration
			$arrtmp = explode(":",$matchfun);
			$goodmatches[$yacntr][0] = $arrtmp[0];
			$goodmatches[$yacntr][1] = $arrtmp[1];
		$yalol = 0;
		}
		$yacntr++;
		$yalol++;
	}
	
	unset($result);
	unset($agentstr);

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//    Scrape Proxy IP addresses from aliveproxy.com  	//
//////////////////////////////////////////////////////////

// grab_aliveproxy - grabs a list of proxies from aliveproxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_aliveproxy($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.aliveproxy.com/socks5-list/";
	$proxyUrl[1] = "http://www.aliveproxy.com/fastest-proxies/";
	$proxyUrl[2] = "http://www.aliveproxy.com/high-anonymity-proxy-list/";
	$proxyUrl[3] = "http://www.aliveproxy.com/anonymous-proxy-list/";
	$proxyUrl[4] = "http://www.aliveproxy.com/ru-proxy-list/";
	$proxyUrl[5] = "http://www.aliveproxy.com/proxy-list-port-3128/";
	$proxyUrl[6] = "http://www.aliveproxy.com/proxy-list-port-8000/";
	$proxyUrl[7] = "http://www.aliveproxy.com/proxy-list-port-8080/";
	$proxyUrl[8] = "http://www.aliveproxy.com/proxy-list-port-81/";
	$proxyUrl[9] = "http://www.aliveproxy.com/proxy-list-port-80/";
	
	$matches = $this->grabSimpleIps($proxyUrl,$yaheader,$agentstr);
	
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//  	Scrape Proxy IP addresses from freeproxy.ru		//
//////////////////////////////////////////////////////////

// grab_freeproxy - grabs a list of proxies from freeproxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_freeproxy($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://onlinechecker.freeproxy.ru/free_proxy_lists.php',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//  	Scrape Proxy IP addresses from samair.ru		//
//////////////////////////////////////////////////////////

// grab_samair - grabs a list of proxies from samair
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_samair($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.samair.ru/proxy/fresh-proxy-list.htm";

	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[0],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches = array();
	$yacntr = 0;
	
	$pattern = '|(<.{1,25}?>)|U'; 					//<span class="proxy48842">
	$result = preg_replace($pattern, '', $result);   // remove open span tag

	$pattern = '|(</.{1,10}?>)|U'; 			//</span>
	$result = preg_replace($pattern, '', $result);   // remove open span tag
	
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3} \d{1,7}\b)|U';	// Strip out IP's and load into array $matches
	preg_match_all($pattern,$result,$matches);

	foreach($matches[0] as $matchfun) {
		$arrtmp = explode(" ",$matchfun);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}

	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//  	Scrape Proxy IP addresses from speedtest.at		//
//////////////////////////////////////////////////////////

// grab_speedtest - grabs a list of proxies from speedtest
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_speedtest($yaheader,$agentstr) {
	$proxyUrl[0] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=0";
	$proxyUrl[1] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=25";
	$proxyUrl[2] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=50";
	$proxyUrl[3] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=75";
	$proxyUrl[4] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=100";
	$proxyUrl[5] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=125";
	$proxyUrl[6] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=150";
	$proxyUrl[7] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=175";
	$proxyUrl[8] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=200";
	$proxyUrl[9] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=225";
	$proxyUrl[10] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=250";
	$proxyUrl[11] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=275";
	$proxyUrl[12] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=300";
	$proxyUrl[13] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=325";
	$proxyUrl[14] = "http://proxy.speedtest.at/proxyOnlyAnonymous.php?offset=350";
	
	$matches = $this->grabSimpleIps($proxyUrl,$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//  	Scrape Proxy IP addresses from ipcn.org			//
//////////////////////////////////////////////////////////

// grab_ipcn - grabs a list of proxies from ipcn
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_ipcn($yaheader,$agentstr) {
	$proxyUrl[0] = "http://proxy.ipcn.org/proxya.html";
	$proxyUrl[1] = "http://proxy.ipcn.org/proxya2.html";
	$proxyUrl[2] = "http://proxy.ipcn.org/proxyb.html";
	$proxyUrl[3] = "http://proxy.ipcn.org/proxyb2.html";
	$proxyUrl[4] = "http://proxy.ipcn.org/proxyc.html";
	$proxyUrl[5] = "http://proxy.ipcn.org/proxyc2.html";
	$proxyUrl[6] = "http://proxy.ipcn.org/proxylist.html";
	$proxyUrl[7] = "http://proxy.ipcn.org/proxylist2.html";
	
	$matches = $this->grabSimpleIps($proxyUrl,$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from proxygo.com.ru	//
//////////////////////////////////////////////////////////

// grab_proxygo - grabs a list of proxies from proxygo
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_proxygo($yaheader,$agentstr) {
	$proxyUrl[0] = "http://proxygo.com.ru";
		
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[0],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches = array();
	$yacntr = 0;
	
	$pattern = '|(</.{1,20}?\r\n\t{1,10}?.{1,20}?>)|U'; // </FONT></TD>
	$result = preg_replace($pattern, ':', $result);     //	    <TD><FONT size=-1>
	
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U';	// Strip out IP's and load into array $matches
	preg_match_all($pattern,$result,$matches);

	foreach($matches[0] as $matchfun) {
		$arrtmp = explode(":",$matchfun);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}

	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from comp-info.ru		//
//////////////////////////////////////////////////////////

// grab_compinfo - grabs a list of proxies from comp-info
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_compinfo($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://comp-info.ru/proxy',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from my-proxy.com		//
//////////////////////////////////////////////////////////

// grab_myproxy - grabs a list of proxies from my-proxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_myproxy($yaheader,$agentstr) {
	$proxyUrl[0] = "http://proxies.my-proxy.com/proxy-list-s1.html";
	$proxyUrl[1] = "http://proxies.my-proxy.com/proxy-list-s2.html";
	$proxyUrl[2] = "http://proxies.my-proxy.com/proxy-list-s3.html";
	$proxyUrl[3] = "http://proxies.my-proxy.com/proxy-list-socks4.html";
	$proxyUrl[4] = "http://proxies.my-proxy.com/proxy-list-socks5.html";
	$proxyUrl[5] = "http://proxies.my-proxy.com/proxy-list-1.html";
	$proxyUrl[6] = "http://proxies.my-proxy.com/proxy-list-2.html";
	$proxyUrl[7] = "http://proxies.my-proxy.com/proxy-list-3.html";
	$proxyUrl[8] = "http://proxies.my-proxy.com/proxy-list-4.html";
	$proxyUrl[9] = "http://proxies.my-proxy.com/proxy-list-5.html";
	$proxyUrl[10] = "http://proxies.my-proxy.com/proxy-list-6.html";
	$proxyUrl[11] = "http://proxies.my-proxy.com/proxy-list-7.html";
	$proxyUrl[12] = "http://proxies.my-proxy.com/proxy-list-8.html";
	$proxyUrl[13] = "http://proxies.my-proxy.com/proxy-list-9.html";
	$proxyUrl[14] = "http://proxies.my-proxy.com/proxy-list-10.html";
	
	$matches = $this->grabSimpleIps($proxyUrl,$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from proxylists.net	//
//////////////////////////////////////////////////////////

// grab_proxylists - grabs a list of proxies from proxylists
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_proxylists($yaheader,$agentstr) {
	$proxyUrl[0] = "http://proxylists.net/http_highanon.txt";
	$proxyUrl[1] = "http://proxylists.net/socks4.txt";
	$proxyUrl[2] = "http://proxylists.net/socks5.txt";
	$matches = $this->grabSimpleIps($proxyUrl,$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//    	 Scrape Proxy IP addresses from j1f.net		    //
//////////////////////////////////////////////////////////

// grab_j1f - grabs a list of proxies from j1f
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_j1f($yaheader,$agentstr) {
	$proxyUrl[0] = "http://proxylist.j1f.net/http.html";
	$proxyUrl[1] = "http://proxylist.j1f.net/socks4.html";
	$proxyUrl[2] = "http://proxylist.j1f.net/socks5.html";
	
	$randkey = array_rand($proxyUrl); // Select a random $proxyUrl page
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[$randkey],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches = array();
	$yacntr = 0;
	
	$pattern = '|(</.{1,11}?>)|U'; // </a></td><td>
	$result = preg_replace($pattern, ':', $result);
	
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U';	// Strip out IP's and load into array $matches
	preg_match_all($pattern,$result,$matches);

	foreach($matches[0] as $matchfun) {
		$arrtmp = explode(":",$matchfun);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}

	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from freeproxy.ch      //
//////////////////////////////////////////////////////////

// grab_freeproxych - grabs a list of proxies from freeproxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_freeproxych($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://www.freeproxy.ch/proxy.txt',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//   Scrape Proxy IP addresses from atomintersoft.com   //
//////////////////////////////////////////////////////////

// grab_atomintersoft - grabs a list of proxies from atomintersoft
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_atomintersoft($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://www.atomintersoft.com/free_proxy_list',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//  Scrape Proxy IP addresses from proxiesthatwork.com  //
//////////////////////////////////////////////////////////

// grab_proxiesthatwork - grabs a list of proxies from proxiesthatwork
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_proxiesthatwork($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://proxiesthatwork.com',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//   Scrape Proxy IP addresses from cybersyndrome.net	//
//////////////////////////////////////////////////////////

// grab_cybersyndrome - grabs a list of proxies from cybersyndrome
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_cybersyndrome($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.cybersyndrome.net/plr5.html";
	$proxyUrl[1] = "http://www.cybersyndrome.net/pla5.html";
	$matches = $this->grabSimpleIps($proxyUrl,$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//    Scrape Proxy IP addresses from proxyleech.com     //
//////////////////////////////////////////////////////////

// grab_proxyleech - grabs a list of proxies from proxyleech
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_proxyleech($yaheader,$agentstr) {
	$proxyUrl[0] = "http://www.proxyleech.com/page/1.php";
	$proxyUrl[1] = "http://www.proxyleech.com/page/2.php";
	$proxyUrl[2] = "http://www.proxyleech.com/page/3.php";
	$proxyUrl[3] = "http://www.proxyleech.com/page/4.php";
	$proxyUrl[4] = "http://www.proxyleech.com/page/5.php";
	$proxyUrl[5] = "http://www.proxyleech.com/page/6.php";
	$proxyUrl[6] = "http://www.proxyleech.com/page/7.php";
	$proxyUrl[7] = "http://www.proxyleech.com/page/8.php";
	$proxyUrl[8] = "http://www.proxyleech.com/page/9.php";
	$proxyUrl[9] = "http://www.proxyleech.com/page/10.php";
	
	$randkey = array_rand($proxyUrl); // Select a random $proxyUrl page
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl[$randkey],$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches = array();
	$yacntr = 0;
	
	$pattern = '|(</.{1,11}?></.{1,4}?><.{1,4}?><.{1,30}?>)|U'; // </font></td><td><font color="#333333">
	$result = preg_replace($pattern, ':', $result);
	
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U';	// Strip out IP's and load into array $matches
	preg_match_all($pattern,$result,$matches);

	foreach($matches[0] as $matchfun) {
		$arrtmp = explode(":",$matchfun);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}

	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//   Scrape Proxy IP addresses from getfreeproxy.info	//
//////////////////////////////////////////////////////////

// grab_getfreeproxy - grabs a list of proxies from getfreeproxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_getfreeproxy($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://getfreeproxy.info/en/list',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//    	 Scrape Proxy IP addresses from 2ch.net  	    //
//////////////////////////////////////////////////////////

// grab_2ch - grabs a list of proxies from 2ch
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_2ch($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://hack72.2ch.net/otakara.cgi',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


////////////////////////////////////////////////////////////
//Scrape Proxy IP addresses from proxy-heaven.blogspot.com//
////////////////////////////////////////////////////////////

// grab_pheaven - grabs a list of proxies from proxy-heaven.blogspot.com
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_pheaven($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://proxy-heaven.blogspot.com',$yaheader,$agentstr);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
// Scrape Proxy IP addresses from proxyserverfinder.com //
//////////////////////////////////////////////////////////

// grab_proxyserverfinder - grabs a list of proxies from proxyserverfinder.com
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_proxyserverfinder($yaheader,$agentstr) {
	$proxyUrl = "http://www.proxyserverfinder.com";
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl,$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches     = array();
	$results     = array();
	$yalink		 = array();
	$yacntr      = 0;
	
	$pattern = '|(<a.{1,100}?>Proxy.{1,20}?</a>)|U';	// grab the proxy list link (url looks like it changes so we need to do this part)
	preg_match_all($pattern,$result,$results);
	
	$pattern = '|(".{1,100}?")|';	// grab the proxy list link url
	preg_match($pattern,$results[0][1],$yalink);
	
	$yalink = trim($yalink[0],'"');	// we finally have the url of the proxy list
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($yalink,$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$result=strtr($result,chr(13),'-'); 	// replace carriage return with dash
	$result=strtr($result,chr(10),chr(32)); // replace line feed with space
	
	$pattern = '|(</.{1,10}?>)|U';
	$result  = preg_replace($pattern, ':', $result);
	
	$pattern = '|(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,7}\b)|U';	// Strip out IP's and load into array $matches
	preg_match_all($pattern,$result,$matches);

	foreach($matches[0] as $matchfun) {
		$arrtmp = explode(":",$matchfun);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}

	unset($result);
	unset($results);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


//////////////////////////////////////////////////////////
//   Scrape Proxy IP addresses from rosinstrument.com   //
//////////////////////////////////////////////////////////

// grab_rosinstrument - grabs a list of proxies from rosinstrument.com
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_rosinstrument($yaheader,$agentstr) {
	$proxyUrl = "http://rosinstrument.com/proxy/l100.xml";
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl,$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches	 = array();
	$yacntr 	 = 0;
	
	$pattern = '/<title>(.*?)<\/title>/'; // grabs netblk-41-215-180-130.iconnect.zm:8080 or 66.190.144.90:27445
	preg_match_all($pattern,$result,$matches);
	
	for($i=0;$i<count($matches[1]);$i++) {
		$arrtmp = explode(":",$matches[1][$yacntr]);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}
	
	unset($goodmatches[0]);
	unset($goodmatches[1]);
	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


		/* ---		Planet lab proxies		--- */
//////////////////////////////////////////////////////////
//     Scrape Proxy IP addresses from princeton.edu     //
//////////////////////////////////////////////////////////

// grab_princeton - grabs a list of (planet lab) proxies from fall.cs.princeton.edu - note: old
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_princeton($yaheader,$agentstr) {
	$matches = $this->grabSimpleIps('http://fall.cs.princeton.edu/codeen/tabulator.cgi?table=table_all',$yaheader,$agentstr,1);
	if(isset($matches)) {
		return $matches;
	} else {
		return 0;	// bark at them
	}
}


//////////////////////////////////////////////////////////
//   Scrape Proxy IP addresses from rosinstrument.com   //
//////////////////////////////////////////////////////////

// grab_rosinst - grabs a list of (planet lab) proxies from rosinstrument.com
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
function grab_rosinst($yaheader,$agentstr) {
	$proxyUrl = "http://rosinstrument.com/proxy/plab100.xml";
	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,0,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($proxyUrl,$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	$goodmatches = array();
	$matches	 = array();
	$yacntr 	 = 0;
	
	$pattern = '/<title>(.*?)<\/title>/'; // grabs netblk-41-215-180-130.iconnect.zm:8080 or 66.190.144.90:27445
	preg_match_all($pattern,$result,$matches);
	
	for($i=0;$i<count($matches[1]);$i++) {
		$arrtmp = explode(":",$matches[1][$yacntr]);
		$goodmatches[$yacntr][0] = $arrtmp[0];
		$goodmatches[$yacntr][1] = $arrtmp[1];
		$yacntr++;
	}
	
	unset($goodmatches[0]);
	unset($goodmatches[1]);
	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}


// keep incase a future site requires a post request
//////////////////////////////////////////////////////////
//  	Scrape Proxy IP addresses from bestproxy		//
//////////////////////////////////////////////////////////

// grab_bestproxy - grabs a list of proxies from bestproxy
// $yaheader - the browser header
// $agentstr - the user agent string to mask as
// returns   - an array list - [0] - ip address [1] - port
// keep for a how to post example
/*
function grab_bestproxy($yaheader,$agentstr) {
	$tmploc = "http://www.bestproxylist.cn/index.php";
	$topost = "act=search&port=&type=high+anonymity&country=";	
	$curl = new CURL();  // create the curl instance
	$opts = $this->setBrowser(0,$topost,$yaheader,$agentstr);  // do not set first variable to 1; we don't need infinite looping

	$curl->retry = MAX_RETRYS;
	$curl->addSession($tmploc,$opts[0]);
	
	$result = $curl->exec();  // this is the site returned
	
	$curl->clear();  // remove the curl instance
	unset($curl);	 // unset curl

	if(is_array($result)) { 
		$result = $result[0];
		if(isset($result[1])){
			$result .= $result[1];
		}
	}
	
	//echo $result;exit;
	//$result = " <td>124.207.102.87</td> 
    //<td>80</td>";

	$matches = array();

	$pattern = '|(<td>\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b</td>.\n....<td>\d{1,8}</td>)|U';	// Strip out IP's and load into array $matches
	preg_match_all($pattern,$result,$matches);

	$goodmatches = array();	

	$yacntr = 0;
	$badchar = array('<td>','</td>');  // strip out the td tags (they were useful for the reg expression)
	foreach($matches[0] as $matchfun) {
		$arrtmp = explode("\n",$matchfun);
		//<td>67.228.235.79</td>:<td>3128</td>
		//$newphrase = str_replace($healthy, $yummy, $phrase);
		$goodmatches[$yacntr][0] = str_replace($badchar,"",trim($arrtmp[0]));
		$goodmatches[$yacntr][1] = str_replace($badchar,"",trim($arrtmp[1]));
		$yacntr++;
	}

	unset($result);
	unset($agentstr);		

	if(empty($goodmatches)) { // bark at them
		return 0;
	} else {
		return $goodmatches; // return something
	}
}
*/


}// class proxybot
$pb = new proxybot($config['server'],$config['user'],$config['pass'],$config['database'],$config['tablePrefix'],$config['tbl_good'],$config['tbl_new'], $config['tbl_inactive'], $config['tbl_banned'], $config['needle'], $userAgentString,$curlheader);  // create the proxybot class instance
?>