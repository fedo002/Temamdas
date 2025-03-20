<?php
session_start();

if (isset($_POST['language'])) {
    $_SESSION['selected_language'] = $_POST['language'];
    echo 'success';
}
?>