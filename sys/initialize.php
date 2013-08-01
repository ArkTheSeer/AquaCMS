<?php
// =============================================================================
// AQUA CMS
// basic setup and initializing
// =============================================================================
// setting error reports
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// include system files
require_once 'functions.php';
// include files from the 'sys' directory
includeSystemFiles();
// basic configuration
require_once 'config.php';
// start rendering the page
startHTML();
?>
