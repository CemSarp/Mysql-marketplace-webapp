<?php
// Session management helper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
