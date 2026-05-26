<?php
ini_set('display_errors', 0);
session_start();
session_unset();
session_destroy();
header('Location: ../../FrontEnd/login.php');
exit;
