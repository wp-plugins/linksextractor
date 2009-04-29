<?php
	/*
Plugin Name: Linksextractor
Plugin URI: http://plugins.wirtschaftsinformatiker.cc/wp-linkextractor
Description: Place all your Links at the bottom of your post, automatically
Author: Marco Bischoff
Version: 0.3
Author URI: http://wirtschaftsinformatiker.cc
*/ 

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('linksextractor', false, dirname(plugin_basename(__FILE__)) . '/languages');


	function linksextracter_set_urls($content) {
		$sContent = $content;
		$sZeile = $content;
		$aLinks = array();
		$sPattern = '=^(.*)<a(.*)href\="?(\S+)"([^>]*)>(.*)</a>(.*)$=msi';
		$sNew = $sContent;
		$iCounter = 0;
	
		while (preg_match($sPattern, $sZeile, $sTxt)) {
		   $aLinks[$iCounter] = array();
		   $aLinks[$iCounter][0] = $sTxt[3];
		   $aLinks[$iCounter][1] = $sTxt[4];
		   $aLinks[$iCounter][2] = $sTxt[5];
		
		   $sZeile =  $sTxt[1]." hier war mal ein Link ".$sTxt[6];
		   $iCounter++;

		}
		
		asort($aLinks, SORT_NUMERIC);
		$iCounter = 1;
		
		foreach($aLinks as $sValue) {
			$sOld = $sValue[0]."\"".$sValue[1].">".$sValue[2]."</a>";
			$sRep = $sValue[0]."\"".$sValue[1].">".$sValue[2]."</a> [".$iCounter."]";
			
			$sNew = str_replace($sOld , $sRep  ,$sNew);
			$iCounter++;
		}
	
		echo $sNew;
		if(count($aLinks) != 0) {
			echo "<div class=\"links\">\n<p>".__('Quelle', 'linksextractor')."</p>\n<ul>\n";
		
			$iCounter = 1;
			
			foreach($aLinks as $sValue) {	
				echo "<li> [".$iCounter."] ".$sValue[0]."</li>";
				$iCounter++;
			}
			
			echo "</ul>\n</div>";
		}
	}

	add_action('the_content', 'linksextracter_set_urls');
?>