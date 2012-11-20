<?php

function _get($url, $headers=array()) {
	$opts = array('http'=>array('method'=>'GET'));
	if (!empty($headers)) {
		$tmp = implode($headers, "\r\n");
		$opts['http']['header'] = sprintf("%s\r\n", $tmp);
	}
	$context = stream_context_create($opts);
	$out = @file_get_contents($url, false, $context);
	$code = NULL;
	if (isset($http_response_header) && preg_match('#^HTTP/\d.\d (\d{3}) #', $http_response_header[0], $tmp)) {
		$code = (int) $tmp[1];
	}
	return array($out, $code);
}

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

function ext($str) {
	$str = parse_url($str, PHP_URL_PATH);
	$str = explode('.', $str);
	$str = end($str);
	return $str;
}

function cloudapp($url) {
	$result = _get(sprintf('%s%s', 'http://cl.ly/', $url), array('Accept: application/json')); // URL hardcoded due to security reasons
	if ($result[0] === false && $result[1] === 404) {
		return array(false, 404);
	}
	if ($result[0] === false) {
		return array(false, 0);
	}
	$json = json_decode($result[0], true);
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

if (!isset($head['Location'])) {
	_header(500);
	echo '<b>Error.</b> Unable to find original URL of the file.';
	return;
}

if (is_array($head['Location'])) {
	$fileURL = $head['Location'][0];
} else {
	$fileURL = $head['Location'];
}

$imgs = array('png','gif','jpg');
if (in_array(ext($fileURL), $imgs)) {
	printf('<img src="%s" alt="Error">', $fileURL);
} else {
	printf('<a href="%s">%s</a>', $fileURL, $fileURL);
}
