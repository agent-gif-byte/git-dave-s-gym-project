<?php
// 1. Resume the existing session so PHP knows who is logged in
session_start();

// 2. Clear all session variables (removes user ID, name, role)
$_SESSION = array();

// 3. Destroy the session completely on the server
session_destroy();

// 4. Redirect the user back to the login page with a logged-out message
header("Location: index.php?msg=loggedout");
exit();
?>