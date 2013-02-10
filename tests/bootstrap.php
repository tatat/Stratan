<?php

define('SRC_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'src');

foreach(glob(SRC_DIR . DIRECTORY_SEPARATOR . '*.php') as $filename)
	require_once $filename;
