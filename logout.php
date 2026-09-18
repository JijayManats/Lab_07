<?php
require 'includes/initialize.php';
session_destroy();
header('Location: login.php');
exit;
?>
