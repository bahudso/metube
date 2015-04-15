<?php

require_once 'config.php';
require_once 'classes/cPresUser.php';
require_once 'classes/cBusUser.php';

$oBusiness = new cBusUser();
$oPresentation = new cPresUser();

session_start();
?>
