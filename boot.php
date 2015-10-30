<?php
session_start();

/**
 * pilih state aplikasi
 * 		- maintenance
 * 		- live
 * @var string
 */
$STATE = 'live';

/**
 * Root Folder aplikasi
 * @var string
 */
$ROOT = dirname(__FILE__);

/**
 * include composer autoload
 */
require_once $ROOT . '/vendor/autoload.php';

if ($STATE == 'maintenance') {
	R::to('maintenance');
}
