<?php
	/*
Plugin Name: Linksextractor
Plugin URI: http://plugins.wirtschaftsinformatiker.cc/wp-linkextractor
Description: Place all your Links at the bottom of your post, automatically
Author: Marco Bischoff
Version: 0.5
Author URI: http://wirtschaftsinformatiker.cc
*/ 

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('linksextractor', false, dirname(plugin_basename(__FILE__)) . '/languages');


	function linksextractor_set_urls($content) {
		global $wp_query;
		$iStatus = get_post_meta($wp_query->post->ID, '_linkextracter_is_set', true);
		
		$sContent = $content;
		if($iStatus == 0) {
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
		else {
		 echo $sContent;
		}
	}

	function linksextractor_get_prop() {
		$iStatus = get_post_meta($_GET["post"], '_linkextracter_is_set', true);
		
		$out = '<label for="linkextractor">';
		$out .= '<select id="linkextractor" name="linkextractor"> ';
		if($iStatus == 0) {
			$out .= '<option selected="selected" value="0">'.__("Links extrahieren", "linksextractor").'</option>  ';
		} else {
			$out .= '<option value="0">'.__("Links extrahieren", "linksextractor").'</option>  ';
		}

		if($iStatus == 1) {
			$out .= '<option selected="selected" value="1">'.__("Links nicht extrahieren", "linksextractor").'</option>  ';
		} else {
			$out .= '<option value="1">'.__("Links nicht extrahieren", "linksextractor").'</option>  ';
		}

		$out .= '</select>  ';
		$out .= '</label>';
		echo $out;
				
	}
	
function linksextractor_set_prop($post_id,$post) {
	
	if(isset($_POST['linkextractor'])) {
	
		update_post_meta($post_id,'_linkextracter_is_set',$_POST['linkextractor']);
	}
}

function linksextractor_special_admin_init() {
      add_meta_box('linkextractor_box','Linkextractor', 'linksextractor_get_prop','post','side', 'default');
	  add_meta_box('linkextractor_box','Linkextractor', 'linksextractor_get_prop','page','side', 'default');

     add_action('save_post','linksextractor_set_prop');
	 add_action('edit_post','linksextractor_set_prop');
	}
	
	add_action('admin_menu','linksextractor_special_admin_init');
	add_action('the_content', 'linksextractor_set_urls');
?>