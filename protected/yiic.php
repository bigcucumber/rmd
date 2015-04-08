<?php

// change the following paths if necessary
$yiic=dirname(__FILE__).'/../../framework1.1.14/yiic.php';
$env = array('develop','produce');
$current = $env[0];
$config=dirname(__FILE__).'/config/' . $current . '/console.php';

require_once($yiic);
