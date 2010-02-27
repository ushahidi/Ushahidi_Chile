<?php defined('SYSPATH') or die('No direct script access.');

// Detect Gzip
$gz = "";
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
{
	$gz  = strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false ? '.gz' : '';
}
Kohana::config_set('settings.gz', $gz);