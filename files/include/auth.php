<?php
session_start();
if(!isset($_SESSION['login'])){
    header('Location: ../login.php');
    exit();
}
if(!isset($_SESSION['is_super_user'])){
    header('Location: ../login.php');
    exit();
}
?>