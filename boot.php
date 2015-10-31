<?php
session_start();

/**
 * pilih state aplikasi
 * 		- maintenance
 * 		- live
 * @var string
 */
// $STATE = 'maintenance';
$STATE = 'live';

/**
 * Root Folder aplikasi
 * @var string
 */
$ROOT = dirname(__FILE__);

require $ROOT . '/core.php';

/**
 * check application is maintenance or live
 * @var $state
 * @return void
 */
if ($STATE == 'maintenance') {
	R::to('maintenance');
}

/**
 * define database connection configuration
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'monev');
