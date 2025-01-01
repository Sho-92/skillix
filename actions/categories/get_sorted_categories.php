<?php
// カテゴリをposition順に並べる

// DB接続設定
require_once '../../includes/db.php';

try {
    // SQLクエリ: カテゴリをposition順にソート
    $stmt = $pdo->query("
        SELECT id, name, position
        FROM categories
        ORDER BY position ASC
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSONでフロントエンドに返す
    header('Content-Type: application/json');
    echo json_encode($categories);
} catch (PDOException $e) {
    // エラーハンドリング
    http_response_code(500);
    echo json_encode(['error' => 'データベースエラー: ' . $e->getMessage()]);
}

?>