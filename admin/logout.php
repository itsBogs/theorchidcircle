<?php
require_once __DIR__ . '/../db.php';
session_destroy();
// use a relative redirect so the app works regardless of server root/path
header('Location: login.php');
exit;

