<?php
session_start();
session_unset();
session_destroy();

// Redirect to homepage instead of login page
header("Location: index.html");
exit;
?>
