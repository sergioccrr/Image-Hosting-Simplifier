<?php

function _header($code) {
	$code = (int) $code;
	$codes = array(
		403=>'Forbidden',
		404=>'Not Found',
		500=>'Internal Server Error'
	);
	if (!array_key_exists($code, $codes)) {
		die(sprintf('Unknown code: %s', $code));
	}
	header(sprintf('%s %s %s', $_SERVER['SERVER_PROTOCOL'], $code, $codes[$code]), true, $code);
}

function cloudapp($url) {
	$opts = array(
		'http'=>array(
			'method'=>'GET',
			'header'=>"Accept: application/json\r\n"
		)
	);
	$context = stream_context_create($opts);
	$result = @file_get_contents(sprintf('%s%s', 'http://cl.ly/', $url), false, $context); // URL hardcoded due to security reasons
	if ($result === false && isset($http_response_header) && preg_match('#HTTP/[0-9].[0-9] 404 Not Found#i', $http_response_header[0])) {
		return array(false, 404);
	}
	if ($result === false) {
		return array(false, 0);
	}
	$json = json_decode($result, true);
	if ($json === false || !isset($json['content_url'])) {
		return array(false, 0);
	}
	return array(true, $json['content_url']);
}


$uri = $_SERVER['REQUEST_URI'];

if (empty($uri) || $uri == '/') {
	echo 'Hey you!';
	return;
}

$get = cloudapp($uri);

if ($get[0] === false && $get[1] === 404) {
	_header(404);
	echo '<b>Error 404.</b> Not Found.';
	return;
}

if ($get[0] === false) {
	_header(500);
	echo '<b>Error.</b> Unable to load data from cl.ly.';
	return;
}

$head = @get_headers($get[1], true);
if ($head === false) {
	_header(500);
	echo '<b>Error.</b> Unable to get headers.';
	return;
}

// ToDo: and what if this is an array instead of a string?
if (!isset($head['Location'])) {
	_header(500);
	echo '<b>Error.</b> Unable to find original URL of the picture.';
	return;
}

printf('<img src="%s" alt="Error">', $head['Location']);
