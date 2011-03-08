<?php

	/*
		index.php - start page for proxybot

		what: proxybot 0.3
		why:  to facilitate grabbing/checking fresh proxies with extreme ease
		how:  scours the intertubes for only the finest proxies 
		who:  hax0rd by hysterix
	*/

include_once('includes/config.php');	// config and include files
ob_start();

if(isset($_REQUEST[PROXY_REQ_VAR])) {	// we are receiving a request from an external source as a get or post var
	echo $pb->return_env_variables($_SERVER);
	exit;	// dont wast bandwidth loading bullshit
}

echo $header;
echo $logo;

$pb->displayMenu();   // display the entire menu

if(isset($_POST['output'])) {	// hit output button
	if(isset($_REQUEST['proxytype'])) {
		$tehproxtype = $_REQUEST['proxytype'];
	} else {
		$tehproxtype = TBL_GOOD;	// they are trying to play games
	}
	echo '<script type="text/javascript">changetab(\'menu2\');</script>';	// highlight the output tab
	$proxylisttype = $pb->returnProxyList($_REQUEST['listtype']);
	$yatemplol     = $pb->returnProxies(MAX_NEW_CHECK,$tehproxtype); // is 9001 finally the limit?  when i need 10000 proxies at a time, ill know the answer
	$finallist = array();
	$x=0;
	
	// im using this switch statement with nested while loops because i dont want to have an if statement fire off a bunch of times for each proxy
	switch($tehproxtype) {
			case 'anon':
				while ($cntIps = $pb->fetch_array($yatemplol)) {
					$tmparr[0] = $cntIps['ip'];
					$tmparr[1] = $cntIps['port'];
					$tmparr[2] = $cntIps['type'];
					$tmparr[3] = $cntIps['alevel'];
					$finallist[$x] = $tmparr;
					$x++;
				}
				break;
			case TBL_NEW:
				while ($cntIps = $pb->fetch_array($yatemplol)) {
					$tmparr[0] = $cntIps['ip'];
					$tmparr[1] = $cntIps['port'];
					$finallist[$x] = $tmparr;
					$x++;
				}
				break;
			case TBL_INACTIVE:
				while ($cntIps = $pb->fetch_array($yatemplol)) {
					$tmparr[0] = $cntIps['ip'];
					$tmparr[1] = $cntIps['port'];
					$tmparr[2] = $cntIps['type'];
					$tmparr[3] = $cntIps['alevel'];
					$finallist[$x] = $tmparr;
					$x++;
				}
				break;
			case TBL_BANNED:
				while ($cntIps = $pb->fetch_array($yatemplol)) {
					$tmparr[0] = $cntIps['ip'];
					$finallist[$x] = $tmparr;
					$x++;
				}
				break;
			case TBL_GOOD:
				default:
				while ($cntIps = $pb->fetch_array($yatemplol)) {
					$tmparr[0] = $cntIps['ip'];
					$tmparr[1] = $cntIps['port'];
					$tmparr[2] = $cntIps['type'];
					$finallist[$x] = $tmparr;
					$x++;
				}
				break;
	}
	
	if(!empty($finallist)) { // we have some ip's in a list
		$proxymsg = $tehproxtype.' proxies found!<br /><br />';
		$_SESSION['ltype']     = $proxylisttype;
		$_SESSION['ptype']     = $tehproxtype;
		$_SESSION['prevpage']  = 'menu2';
		$_SESSION['proxylist'] = $finallist;
		echo '<script type="text/javascript">window.location = "output.php";</script>';
	} else {
		echo '<div class="warnmsg1 warnmsgstyle" id="warnid"> No '.$tehproxtype.' proxies found!<br /><br />
			  <div class="yabutton"><a href="index.php">search again</a></div></div>';
	}

	echo '<script type="text/javascript">
		document.getElementById(\'loader\').style.visibility = \'hidden\';'."\n".'
		document.getElementById(\'loader\').innerHTML = \'\';'."\n".'
		</script>';

	if(isset($arrError)) { // there were some errors
		foreach($arrError as $teherrors) {
			echo $teherrors;
		}
	}
} elseif(isset($_POST['test'])) { // hit test button
	$script_start = $pb->microtime_float();
	if(isset($_REQUEST['proxytype'])) {
		switch($_REQUEST['proxytype']) {
			case "anonprox":
				$numreturn = MAX_GOOD_CHECK;
				$proxtype  = TBL_GOOD;
				$type	   = 'anonprox';
				break;
			case "inactprox":
				$numreturn = MAX_INAC_CHECK;
				$proxtype  = TBL_INACTIVE;
				$type	   = 'inactprox';
				break;
			case "goodprox":
				$numreturn = MAX_GOOD_CHECK;
				$proxtype  = TBL_GOOD;
				$type	   = 'goodprox';
				break;
			case "newprox":
			default:
				$numreturn = MAX_NEW_CHECK;
				$proxtype = TBL_NEW;
				$type	   = 'newprox';
				break;
		}
	} else {
		$numreturn = MAX_NEW_CHECK;
		$proxtype  = TBL_NEW;
	}

	echo '<script type="text/javascript">changetab(\'menu1\');</script>'; // highlight the test tab
	if(strcmp($type,'anonprox')) {	// they do not want to test anonymity level
		echo '<div class="tehtestoutput boxloaderbigger boxloader" id="testoutput">';
		echo '<h2>&nbsp;Proxy checking in progress!</h2><br />&nbsp;&nbsp;
		      <img src="images/loader.gif" />
		      <div class="ohm"><img src="images/ohm.png" /></div><br/ >';
		ob_flush(); // lets get this out to the browser faster 
		flush();	// so users dont think the page is freezing up
		echo '<div class="miniwarnmsg" id="tehgoodresults"></div><br />
		      <div class="miniwarnmsg" id="tehbadresults"></div>';
		
		$yacntr  = 0;
		$arrProx = array();
		
		if(AUTO_DUPE_REMOVE == 1) {	// make sure their are no duplicates before testing
			if(!$pb->query_dupenukem($proxtype)) {
				echo '<div class="warnmsg2 warnmsgstyle" id="warnid">Error eliminating duplicates!</div>'; // there was some sort of a problem
			}
		}
		
		$yatemplol = $pb->returnProxies($numreturn,$proxtype);
		if((strcmp($proxtype,TBL_GOOD) == 0) || (strcmp($proxtype,TBL_INACTIVE) == 0)) {	// proxytype is good or inactive
			while ($cntIps = $pb->fetch_array($yatemplol)) {
				if (isset($cntIps['ip'])) {
					$userAgent = $pb->retAgentString($pb->uagent);
					$arrProx[$yacntr] = array('ip' => $cntIps['ip'], 'port' => $cntIps['port'], 'type' => $cntIps['type'], 'useragent' => $userAgent);
					$yacntr++;
				}
			}
		} else {	// proxytype is new, build three per single ip:port
			while ($cntIps = $pb->fetch_array($yatemplol)) {
				if (isset($cntIps['ip'])) {
					$userAgent = $pb->retAgentString($pb->uagent);
					$arrProx[$yacntr] = array('ip' => $cntIps['ip'], 'port' => $cntIps['port'], 'type' => 'socks4', 'useragent' => $userAgent);
					$yacntr++;
					$arrProx[$yacntr] = array('ip' => $cntIps['ip'], 'port' => $cntIps['port'], 'type' => 'socks5', 'useragent' => $userAgent);
					$yacntr++;
					$arrProx[$yacntr] = array('ip' => $cntIps['ip'], 'port' => $cntIps['port'], 'type' => 'http', 'useragent' => $userAgent);
					$yacntr++;
				}
			}
		}
		
		echo "</div>";
		if(empty($arrProx)) {	// if they want to search for 0 proxies, supress errors
			echo '<script type="text/javascript">
				document.getElementById(\'tehgoodresults\').style.visibility = \'hidden\';'."\n".'
				document.getElementById(\'tehbadresults\').style.visibility = \'hidden\';'."\n".'
				document.getElementById(\'testoutput\').style.visibility = \'hidden\';'."\n".'
				document.getElementById(\'tehgoodresults\').innerHTML = \'\';'."\n".'
				document.getElementById(\'tehbadresults\').innerHTML = \'\';'."\n".'
				document.getElementById(\'testoutput\').innerHTML = \'\';'."\n".'
			     </script>';
			echo '</div><div class="warnmsg3 warnmsgstyle" id="warnid"> No '.$proxtype.' proxies found!<br /><br />
				  <div class="yabutton"><a href="index.php">search again</a></div></div>';
			echo '</body></html>'."\n";
			exit;
		}
		
		$yatmpvar   = $pb->check($arrProx,$proxtype);
		$script_end = $pb->microtime_float();
		echo '<script type="text/javascript">
				document.getElementById(\'tehgoodresults\').style.visibility = \'hidden\';'."\n".'
				document.getElementById(\'tehbadresults\').style.visibility = \'hidden\';'."\n".'
				document.getElementById(\'testoutput\').style.visibility = \'hidden\';'."\n".'
				document.getElementById(\'tehgoodresults\').innerHTML = \'\';'."\n".'
				document.getElementById(\'tehbadresults\').innerHTML = \'\';'."\n".'
				document.getElementById(\'testoutput\').innerHTML = \'\';'."\n".'
			     </script>';
		if($yatmpvar) {	// funtion completed without errors
			echo '</div><div class="warnmsg4 warnmsgstyle" id="warnid">Testing took '.bcsub($script_end, $script_start, 3).' seconds!<br /><br />
			<div class="yabutton"><a href="index.php?tab=menu2">good proxies</a></div></div>';
			ob_flush();
			flush();
		} else {
			echo '</div><div class="warnmsg5 warnmsgstyle" id="warnid">Error checking proxies!</div>';
		}
	} else {	// they want to test anonymity level
		echo '<div class="tehtestoutput boxloaderbigger boxloader" id="testoutput">';
		echo '<h2>&nbsp;Proxy checking in progress!</h2><br />&nbsp;&nbsp;
		      <img src="images/loader.gif" />
		      <div class="ohm"><img src="images/ohm.png" /></div><br/ >';
		ob_flush(); // lets get this out to the browser faster 
		flush();	// so users dont think the page is freezing up
		echo '<div class="miniwarnmsg" id="tehgoodresults"></div><br />
		      <div class="miniwarnmsg" id="tehbadresults"></div><br />
		      <div class="miniwarnmsg" id="tehanonresults"></div>';
		
		if(AUTO_DUPE_REMOVE == 1) {	// make sure their are no duplicates before testing
			if(!$pb->query_dupenukem($proxtype)) {
				$_SESSION['warnmsg'] = 'Error eliminating duplicates!'; // there was some sort of a problem
			}
		}
		
		if(isset($_POST['anonchecktype'])) {
			$yatemplol = array();
			$i=0;
			$ips = $pb->returnProxies($numreturn,$proxtype);	// grab the list of ips
			while ($cntIps = $pb->fetch_array($ips)) {
				$userAgent = $pb->retAgentString($pb->uagent);
				$yatemplol[$i]['ip'] = $cntIps['ip'];
				$yatemplol[$i]['port'] = $cntIps['port'];
				$yatemplol[$i]['type'] = $cntIps['type'];
				$yatemplol[$i]['useragent'] = $userAgent;
				$i++;
			}
			
			if(empty($yatemplol)) {	// if they want to test 0 proxies, supress errors
				echo '<script type="text/javascript">
					document.getElementById(\'tehgoodresults\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'tehbadresults\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'tehanonresults\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'testoutput\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'tehgoodresults\').innerHTML = \'\';'."\n".'
					document.getElementById(\'tehbadresults\').innerHTML = \'\';'."\n".'
					document.getElementById(\'tehanonresults\').innerHTML = \'\';'."\n".'
					document.getElementById(\'testoutput\').innerHTML = \'\';'."\n".'
				     </script>';
				echo '</div><div class="warnmsg7 warnmsgstyle" id="warnid"> No '.$proxtype.' proxies found!<br /><br />
					  <div class="yabutton"><a href="index.php">search again</a></div></div>';
				echo '</body></html>'."\n";
				exit;
			}
			
			if(strcmp($_POST['anonchecktype'],'internal' )) {	// they want to check externally
				$chkurl = 'external';
			} else {	// they want to check internally
				$chkurl = 'internal';
			}
			
			$yatmpvar   = $pb->test_proxies($yatemplol,$proxtype,$chkurl);
			$script_end = $pb->microtime_float();
			
			echo '<script type="text/javascript">
					document.getElementById(\'tehgoodresults\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'tehbadresults\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'tehanonresults\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'testoutput\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'tehgoodresults\').innerHTML = \'\';'."\n".'
					document.getElementById(\'tehbadresults\').innerHTML = \'\';'."\n".'
					document.getElementById(\'tehanonresults\').innerHTML = \'\';'."\n".'
					document.getElementById(\'testoutput\').innerHTML = \'\';'."\n".'
				     </script>';
			if($yatmpvar) {	// funtion completed without errors
				echo '</div><div class="warnmsg8 warnmsgstyle" id="warnid">Testing took '.bcsub($script_end, $script_start, 3).' seconds!<br /><br /><div class="yabutton"><a href="index.php?tab=menu2">good proxies</a></div></div>';
				ob_flush();
				flush();
			} else {
				if(isset($_SESSION['warnmsg'])) {
					echo "</div><div class='warnmsg15 warnmsgstyle' id=\"warnid\">{$_SESSION['warnmsg']}</div>";
				} else {
					echo '</div><div class="warnmsg9 warnmsgstyle" id="warnid">Error checking proxies!</div>';
				}
			}
		}
		ob_flush();
		flush();
	}
} elseif(isset($_POST['search'])) {	// hit search button
	$script_start = $pb->microtime_float();
	echo '<script type="text/javascript">changetab(\'menu0\');</script>'; // highlight the search tab
	echo '<div class="tehsearchoutput boxloader" id="searchoutput"><br />
		  <h2>&nbsp;&nbsp;Proxy scraping in progress!</h2><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	      <img src="images/loader.gif" />
	      <div class="ohm ohmsearch"><img src="images/ohm.png" /></div><br/ >';
	ob_flush();
	flush();
	
	$proxylisttype = $pb->returnProxyList($_REQUEST['listtype']);	// make sure request vars are clean
	$sitestoscour  = $pb->returnSitesScour($_REQUEST);				// make sure request vars are clean
	$finallist     = $pb->returnFinalList($sitestoscour);			// scrape sites
	$finallist 	   = $pb->arrayUnique($finallist);					// eliminate the dupes before moving on
	if(AUTO_BAN == 1) {												// remove banned proxies
		$finallist = $pb->autoBan($finallist);
	}
	$script_end    = $pb->microtime_float();						// stop the timer

	if(!empty($finallist)) { // we have some ip's in a list
		$_SESSION['ltype']    = $proxylisttype;
		$_SESSION['ptype']    = TBL_NEW;
		$_SESSION['prevpage'] = 'menu0';
		
		$proxymsg = 'Returned '.count($finallist).' proxies in: '.bcsub($script_end, $script_start, 4).' seconds!<br /><br />';
		switch($proxylisttype) {
			case "csv":
				$_SESSION['proxylist'] = $finallist;
				$proxymsg .= "<div class='yabutton'><a href='output.php'>csv proxies</a></div><br />"; 
				break;
			case "mysql":
				$pb->insertProxies($finallist,time());
				if(AUTO_DUPE_REMOVE == 1) {	// there could be duplcates after inserting into db
					if(!$pb->query_dupenukem(TBL_NEW)) {
						$proxymsg .= 'Error eliminating duplicates!<br />'; // there was some sort of a problem
					}
				}
				$_SESSION['proxmsg'] = $proxymsg;
				echo '<script type="text/javascript">window.location = "index.php?tab=menu1"</script>';
				break;
			case "plaintext":
			default:
				$_SESSION['proxylist'] = $finallist;
				$proxymsg .=  "<div class='yabutton'><a href='output.php'>plaintext proxies</a></div><br />";  // default
				break;
		}
	} else {
		$proxymsg  = 'proxybot search failed!<br /><br />';
		$proxymsg .= '<div class="yabutton"><a href="index.php">search again</a></div>';
	}
	ob_flush();
	flush();
	
	echo '</div><script type="text/javascript">
					document.getElementById(\'searchoutput\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'searchoutput\').innerHTML = \'\';'."\n".'
				</script>';
	
	if(isset($proxymsg)) {
		echo "<div class='warnmsg10 warnmsgstyle' id=\"warnid\">$proxymsg</div>";
	}

	if(isset($arrError)) { // there were some errors
		foreach($arrError as $teherrors) {
			echo $teherrors;
		}
	}
	ob_flush();
	flush();
} elseif(isset($_POST['maintain'])) {	// hit maintain button
	echo '<script type="text/javascript">changetab(\'menu3\');</script>'; // highlight the database tab
	$script_start = $pb->microtime_float();
	$dbmaintain   = $pb->maintainDatabase($_REQUEST);
	$script_end   = $pb->microtime_float();
	$exectime	  = bcsub($script_end, $script_start, 4);
		
	if(!isset($dbmaintain) || ($dbmaintain == '')) {	// the extra check for '' should not be needed as i am throwing back false, but php does indeed suck :/
		echo "<div class='warnmsg11 warnmsgstyle' id=\"warnid\">Database maintenance error!</div>";
	} else {
		print_r($dbmaintain);
		if($dbmaintain == 0) {
			$msg = 'Successful duplicate clearing took: ';
		} else {
			$msg = 'Successful truncate took: ';
		}
		$_SESSION['dbmaintainmsg'] = $msg.$exectime.' seconds!';
		echo '<script type="text/javascript">window.location = "index.php?tab=menu3";</script>';		
	}
} elseif(isset($_POST['banned'])) {	// hit search for planet lab proxies button
	$script_start = $pb->microtime_float();
	echo '<script type="text/javascript">changetab(\'menu4\');</script>'; // highlight the banned tab
	echo '<div class="tehbannedoutput boxloader" id="searchoutput"><br />
		  <h2>&nbsp;&nbsp;Proxy scraping in progress!</h2><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	      <img src="images/loader.gif" />
	      <div class="ohm"><img src="images/ohm.png" /></div><br/ >';
	ob_flush();
	flush();
	
	$proxylisttype = $pb->returnProxyList($_REQUEST['listtype']);	// make sure request vars are clean
	$sitestoscour  = $pb->returnSitesScour($_REQUEST);				// make sure request vars are clean
	$finallist     = $pb->returnFinalList($sitestoscour);			// scrape sites
	$finallist 	   = $pb->arrayUnique($finallist);					// eliminate the dupes before moving on
	$script_end    = $pb->microtime_float();

	if(!empty($finallist)) { // we have some ip's in a list
		$_SESSION['ltype']    = $proxylisttype;
		$_SESSION['ptype']    = TBL_BANNED;
		$_SESSION['prevpage'] = 'menu3';
		$proxymsg = 'Returned '.count($finallist).' planetlab/codeen proxies in: '.bcsub($script_end, $script_start, 4).' seconds!<br /><br />';
		switch($proxylisttype) {
			case "mysql":
				$pb->insertBannedProxies($finallist,time());
				if(AUTO_DUPE_REMOVE == 1) {	// there could be duplcates after inserting into db
					if(!$pb->query_dupenukem(TBL_BANNED)) {
						$proxymsg .= 'Error eliminating duplicates!<br />'; // there was some sort of a problem
					}
				}
				$_SESSION['proxmsg'] = $proxymsg;
				echo '<script type="text/javascript">window.location = "index.php?tab=menu2"</script>';
				break;
			case "plaintext":
			default:
				$_SESSION['proxylist'] = $finallist;
				$proxymsg .=  "<div class='yabutton'><a href='output.php'>plaintext proxies</a></div><br />";  // default
				break;
		}
	} else {
		$proxymsg = 'proxybot search failed!<br /><br />';
		$proxymsg .= '<a href="index.php">search again</a>';
	}
	ob_flush();
	flush();
	
	echo '</div><script type="text/javascript">
					document.getElementById(\'searchoutput\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'searchoutput\').innerHTML = \'\';'."\n".'
				</script>';
	
	if(isset($proxymsg)) {
		echo "<div class='warnmsg12 warnmsgstyle' id=\"warnid\">$proxymsg</div>";
	}

	if(isset($arrError)) {	// there were some errors
		foreach($arrError as $teherrors) {
			echo $teherrors;
		}
	}
	ob_flush();
	flush();
} else {
	if(isset($_REQUEST['tab'])) {	// override the default tab
		switch($_REQUEST['tab']) {
			case "menu0":
				$menufun = $_REQUEST['tab'];
				break;
			case "menu1":
				$menufun = $_REQUEST['tab'];
				break;
			case "menu2":
				$menufun = $_REQUEST['tab'];
				break;
			case "menu3":
				$menufun = $_REQUEST['tab'];
				break;
			case "menu4":
				$menufun = $_REQUEST['tab'];
				break;
			default:
				$menufun = 'menu0';	// they are trying to play games
				break;
		}
	} else {
		$menufun = 'menu0';	// default
	}
	
	echo '<script type="text/javascript">changetab(\''.$menufun.'\');</script>';	// highlight whatever tab thats requested
	if(isset($_SESSION['dbmaintainmsg'])) {
		echo "<div class='warnmsg13 warnmsgstyle' id=\"warnid\">{$_SESSION['dbmaintainmsg']}</div>";
		unset($_SESSION['dbmaintainmsg']);
	}
	
	if(isset($_SESSION['proxmsg'])) {	// its easier this way than passing through get
		echo '<script type="text/javascript">
					document.getElementById(\'searchoutput\').style.visibility = \'hidden\';'."\n".'
					document.getElementById(\'searchoutput\').style.visibility = \'hidden\';'."\n".'
			  </script>';
		echo "<div class='warnmsg14 warnmsgstyle' id=\"warnid\">{$_SESSION['proxmsg']}</div>";
		unset($_SESSION['proxmsg']);
	}
}

echo '</body></html>'."\n";	// footer
ob_flush();
flush();

?>
