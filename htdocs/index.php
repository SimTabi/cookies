<?php
require_once('../src/Cookies.php');
$KEY = 'test';
$handler = new Cookies\Cookies($KEY);
session_set_save_handler($handler, true);
session_start();
if (!isset($_SESSION['a'])) {
    $_SESSION['a'] = 0;
} else {
    $_SESSION['a']++;
}
var_dump($_SESSION);
