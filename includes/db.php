<?php
// 環境変数からJawsDBの接続URLを取得
$cleardb_url = parse_url(getenv("JAWSDB_URL"));

// DB接続設定
$host = $cleardb_url["host"];   // Herokuのホスト
$db   = ltrim($cleardb_url["path"], '/');   // Herokuのデータベース名（URLのパス部分がデータベース名）
$user = $cleardb_url["user"];   // Herokuのユーザー名
$pass = $cleardb_url["pass"];   // Herokuのパスワード
$charset = 'utf8mb4';   // 文字セット（UTF-8を使う）

// // DB接続設定(ローカルDB)
// $host = 'localhost';    // MySQLのホスト名
// $db   = 'synclyee';        // データベース名
// $user = 'root';         // MySQLのユーザー名（rootがデフォルト）
// $pass = 'root';             // パスワード（rootがデフォルト）
// $charset = 'utf8mb4';   // 文字セット（UTF-8を使う）

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
