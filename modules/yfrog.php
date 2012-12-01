<?php

function loadURL($url) {
	$get = do_get(
		sprintf('http://twitter.yfrog.com/%s', $url)
	);
	if ($get[0] === false && $get[1] === 404) {
		return array(1);
	}
	if ($get[0] === false) {
		return array(2);
	}
	if (preg_match('#Site.LP.setMode\(\'photo\'\);#', $get[0])) {
		$pattern = '#<meta property="og:image" content="(.+)" />#';
		$isImage = true;
	} else {
		$pattern = '#<div id="share-links" class=\'pw-widget pw-size-large\' pw:pinterest-image="(.+)">#';
		$isImage = false;
	}
	if (!preg_match($pattern, $get[0], $tmp)) {
		return array(2);
	}
	return array(false, $tmp[1], $isImage);
}
