<?php

function loadURL($url) {
	$get = _get(
		sprintf('http://twitter.yfrog.com/%s', $url)
	);
	if ($get[0] === false && $get[1] === 404) {
		return array(1);
	}
	if ($get[0] === false) {
		return array(2);
	}
	if (!preg_match('#<meta property="og:image" content="(.+)" />#', $get[0], $tmp)) {
		return array(2);
	}
	return array(false, $tmp[1], true);
}
