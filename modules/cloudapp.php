<?php

function loadURL($url) {
	$get = _get(
		sprintf('%s%s', 'http://cl.ly/', $url),
		array('Accept: application/json')
	);
	if ($get[0] === false && $get[1] === 404) {
		return array(1);
	}
	if ($get[0] === false) {
		return array(2);
	}
	$json = json_decode($get[0], true);
	if ($json === false || !isset($json['content_url'])) {
		return array(2);
	}
	$head = @get_headers($json['content_url'], true);
	if ($head === false || !isset($head['Location'])) {
		return array(2);
	}
	if (is_array($head['Location'])) {
		$fileURL = $head['Location'][0];
	} else {
		$fileURL = $head['Location'];
	}
	$imgs = array('png','gif','jpg');
	if (in_array(ext($fileURL), $imgs)) {
		$isImage = true;
	} else {
		$isImage = false;
	}
	return array(false, $fileURL, $isImage);
}
