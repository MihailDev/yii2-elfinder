<?php
/**
 * @var array $options
 */

// run elFinder
$connector = new elFinderConnector(new elFinder($options));
$connector->run();