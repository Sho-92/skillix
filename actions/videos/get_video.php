<?php
require_once '../../includes/db.php';

try {
    // 動画一覧を取得
    $stmt = $pdo->prepare("SELECT id, title, url, category, created_at FROM videos ORDER BY created_at DESC");
    $stmt->execute();
    $videos = $stmt->fetchAll();

    header('Content-Type: application/json; charset=utf-8'); // ヘッダーを明示
    echo json_encode($videos, JSON_UNESCAPED_UNICODE); // 日本語をそのまま返す

} catch (Exception $e) {
    // エラーハンドリング
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
}
?>
