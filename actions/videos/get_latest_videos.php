<?php
require_once '../../includes/db.php';

try {
    // 最新動画を取得するためにcreated_at順で並べ替え
    $stmt = $pdo->prepare("
    SELECT v.id, v.title, v.url, COALESCE(c.name, '未分類') AS category, v.category_id, v.position, v.created_at
    FROM videos v
    LEFT JOIN categories c ON v.category_id = c.id
    ORDER BY v.created_at DESC 
    LIMIT 5 
    ");
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // デバッグ: 取得したデータをログに出力
    error_log("Latest Videos: " . print_r($videos, true));

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($videos, JSON_UNESCAPED_UNICODE); // 日本語をそのまま返す

} catch (Exception $e) {
    // エラーハンドリング
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
}
?>
