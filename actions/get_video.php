<?php
require_once '../includes/db.php';

// 動画一覧を取得
$stmt = $pdo->prepare("SELECT id, title, url FROM videos ORDER BY id DESC");
$stmt->execute();
$videos = $stmt->fetchAll();

header('Content-Type: application/json; charset=utf-8'); // ヘッダーを明示
echo json_encode($videos, JSON_UNESCAPED_UNICODE); // 日本語をそのまま返す
?>
