<?php
// DB接続設定
// $host = 'localhost';    // MySQLのホスト名
// $db   = 'synclyee';        // データベース名
// $user = 'root';         // MySQLのユーザー名（rootがデフォルト）
// $pass = 'root';             // パスワード（rootがデフォルト）
// $charset = 'utf8mb4';   // 文字セット（UTF-8を使う）

// JawsDBの接続情報を解析(heroku)
$cleardb_url = parse_url(getenv("JAWSDB_URL"));
$host = $cleardb_url["host"]; // ホスト名（JawsDBのホスト）
$db   = ltrim($cleardb_url["path"], "/"); // データベース名
$user = $cleardb_url["user"]; // ユーザー名
$pass = $cleardb_url["pass"]; // パスワード
$charset = 'utf8mb4'; // 文字セット

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "データベースに接続成功";
} catch (\PDOException $e) {
    echo "接続失敗: " . $e->getMessage();
}
?>
