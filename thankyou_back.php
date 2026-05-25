<?php
session_start();
$_SESSION['skip_redirect'] = true;
header('Location: checkdetails.php');
exit;