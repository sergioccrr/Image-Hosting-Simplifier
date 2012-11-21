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
		return false;
	}
	header(sprintf('%s %s %s', $_SERVER['SERVER_PROTOCOL'], $code, $codes[$code]), true, $code);
}

function ext($str) {
	$str = parse_url($str, PHP_URL_PATH);
	$str = explode('.', $str);
	$str = end($str);
	return $str;
}
