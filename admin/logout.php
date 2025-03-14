<?php
session_start();
$_SESSION['is_logged'] = "non";
session_destroy();
header("location:login.php");
?>