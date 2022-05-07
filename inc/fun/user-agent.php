<?php
$argon_comment_ua = get_option("argon_comment_ua");
$argon_comment_show_ua = Array();
if (strpos($argon_comment_ua, 'platform') !== false){
	$argon_comment_show_ua['platform'] = true;
}
if (strpos($argon_comment_ua, 'browser') !== false){
	$argon_comment_show_ua['browser'] = true;
}
if (strpos($argon_comment_ua, 'version') !== false){
	$argon_comment_show_ua['version'] = true;
}
function parse_ua_and_icon($userAgent){
	global $argon_comment_ua;
	global $argon_comment_show_ua;
	if ($argon_comment_ua == "" || $argon_comment_ua == "hidden"){
		return "";
	}
	$parsed = argon_parse_user_agent($userAgent);
	$out = "<div class='comment-useragent'>";
	if (isset($argon_comment_show_ua['platform']) && $argon_comment_show_ua['platform'] == true){
		if (isset($GLOBALS['UA_ICON'][$parsed['platform']])){
			$out .= $GLOBALS['UA_ICON'][$parsed['platform']] . " ";
		}else{
			$out .= $GLOBALS['UA_ICON']['Unknown'] . " ";
		}
		$out .= $parsed['platform'];
	}
	if (isset($argon_comment_show_ua['browser']) && $argon_comment_show_ua['browser'] == true){
		if (isset($GLOBALS['UA_ICON'][$parsed['browser']])){
			$out .= " " . $GLOBALS['UA_ICON'][$parsed['browser']];
		}else{
			$out .= " " . $GLOBALS['UA_ICON']['Unknown'];
		}
		$out .= " " . $parsed['browser'];
		if (isset($argon_comment_show_ua['version']) && $argon_comment_show_ua['version'] == true){
			$out .= " " . $parsed['version'];
		}
	}
	$out .= "</div>";
	return apply_filters("argon_comment_ua_icon", $out);
}
