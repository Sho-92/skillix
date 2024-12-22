<?php
session_start(); // セッション開始

// セッションを解除
session_unset(); // セッション変数を全て解除
session_destroy(); // セッションを破棄

// ログアウト後、ログインページ(index.php)にリダイレクト
header('Location: ../index.php');
exit;
?>
