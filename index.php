<?php

require('includes/functions.php');

$uri = $_SERVER['REQUEST_URI'];
$uri = (isset($uri[0]) && $uri[0] == '/') ? substr($uri, 1) : $uri;
$host = strtolower($_SERVER['HTTP_HOST']);

switch ($host) {
	case 'cl.ly':
		$name = 'cloudapp';
		break;
	case 'pics.lockerz.com':
		$name = 'lockerz';
		break;
	case 'twitter.yfrog.com':
		$name = 'yfrog';
		break;
	default:
		header(403);
		return;
}

$ico = sprintf('statics/favicon-%s.ico', $name);

if ($uri == 'favicon.ico' && file_exists($ico)) {
	// favicon.ico (if exists)
	header(sprintf('Location: http://%s/%s', $host, $ico));
	return;
} elseif ($uri == 'favicon.ico') {
	// favicon.ico (if not exists)
	_header(404);
	return;
} elseif (empty($uri)) {
	// index
	echo 'Hey you!';
	return;
}

require(sprintf('modules/%s.php', basename($name)));
$result = loadURL($uri);

if ($result[0] === 1) {
	_header(404);
	echo '<b>Error 404.</b> Not Found.';
	return;
} elseif($result[0] !== false) {
	_header(500);
	echo '<b>Error 500.</b> Unable to get data from hosting service.';
	return;
}

if ($result[2] === true) {
	printf('<img src="%s" alt="Error">', $result[1]);
} else {
	printf('<a href="%s">%s</a>', $result[1], $result[1]);
}
