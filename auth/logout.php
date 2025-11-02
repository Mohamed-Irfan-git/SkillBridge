<?php
    session_start();
 // Delete login detail cookies
    setcookie('user_email', '', time() - 3600, "/");
    setcookie('user_name', '', time() - 3600, "/");
    setcookie('user_id', '', time() - 3600, "/");
    setcookie('user_role', '', time() - 3600, "/");
    session_unset();
    session_destroy();
    header("Location: ./login.php");
    exit();