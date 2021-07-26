<?php
require_once("loader/router.php");

// set your current directory path
$path=dirname($_SERVER['PHP_SELF']);
$set_app_path=$path;

//  or manual
// $set_app_path="/path1/path2/";

get('/index', 'views/index.php');
any('/404','views/404.php');
