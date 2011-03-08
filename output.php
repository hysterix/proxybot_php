<?php

	/*
		output.php - outputs any kind of proxy list
	*/

	session_start(); 
	include_once('includes/config.php');	// config and include files

	echo $header; // header	
	echo $logo;
	if(isset($_SESSION['proxylist'])) {
		$proxcnt = count($_SESSION['proxylist']);
		if(isset($_SESSION['prevpage'])) {	// remember where they came from
			$menuback = $_SESSION['prevpage'];
		} else {
			$menuback = 'menu0';	// default
		}
		echo '<div class="plainmenu"><h1>plaintext ip list:</h1></div>';
		echo '<div class="proxynum"><a href="javascript:highlight(document.plainform.tehips);">'.$proxcnt.' proxies</a></div>';
		echo '<div class="dispbutton"><a href="index.php?tab='.$menuback.'">Go back</a></div>';
		echo '<div class="dispbutton2"><a href="javascript:highlight(document.plainform.tehips);">Select All</a></div>';
		echo '<div class="textoutput"><form name="plainform">
			  <textarea name="tehips" id="tehips" cols="50" rows="';
		if($proxcnt > 50) { echo '50'; } else { echo $proxcnt; }
		echo '">';
		
		switch($_SESSION['ltype']) {
			case 'csv':
				switch($_SESSION['ptype']) {
					case TBL_NEW:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0].",".$tehlist[1].",|\n"; // ip,port|
						}
						break;
					case TBL_INACTIVE:
					case 'anon':
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0].",".$tehlist[1].",".$tehlist[2];
							if(!empty($tehlist[3])){echo ','.$tehlist[3];}
							echo ",|\n"; // ip,port,type,alevel|
						}
						break;
					case TBL_BANNED:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0].",|\n";  // ip,|
						}
						break;
					case TBL_GOOD:
						default:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0].",".$tehlist[1].",".$tehlist[2];
							if(!empty($tehlist[3])){echo ','.$tehlist[3];}
							echo ",|\n"; // ip,port,type,alevel|
						}
						break;
				}
				break;
			case 'proxychains':
				switch($_SESSION['ptype']) {
					case TBL_NEW:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0]." ".$tehlist[1]."\n"; // ip port
						}
						break;
					case TBL_BANNED:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0]."\n";  // ip
						}
						break;
					case TBL_GOOD:
						default:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[2]." ".$tehlist[0]." ".$tehlist[1]."\n";  // type ip port
						}
						break;
				}
				break;
			case 'plaintext':
			default:
				switch($_SESSION['ptype']) {
					case TBL_NEW:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0].":".$tehlist[1]."\n"; // ip:port
						}
						break;
					case TBL_BANNED:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0]."\n";  // ip
						}
						break;
					case TBL_GOOD:
						default:
						foreach($_SESSION['proxylist'] as $tehlist) {
							echo $tehlist[0].":".$tehlist[1]."\n";  // ip:port
						}
						break;
				}
				break;
		}
		echo '</textarea></form></div>';
	}
?>