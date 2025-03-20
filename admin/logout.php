<?php
session_start(); // Oturumu başlat
session_unset(); // Tüm oturum değişkenlerini temizle
session_destroy(); // Oturumu yok et

header("Location: login.php"); // Kullanıcıyı login.php'ye yönlendir
exit();
