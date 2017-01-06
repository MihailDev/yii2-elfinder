<?php
/**
 * @var array $options
 * @var array $plugin
 */

define('ELFINDER_IMG_PARENT_URL', \mihaildev\elfinder\Assets::getPathUrl());

// run elFinder
$connector = new elFinderConnector(new \mihaildev\elfinder\elFinderApi($options, $plugin));
$connector->run();