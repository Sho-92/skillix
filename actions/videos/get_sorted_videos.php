<?php
// videosテーブルのposition順に配置する

// DB接続設定
require_once '../../includes/db.php';

try {
    // SQLクエリ: カテゴリIDごとにposition順にソート
    $stmt = $pdo->query("
        SELECT v.id, v.title, v.url, v.category_id, v.position, c.name AS category
        FROM videos v
        LEFT JOIN categories c ON v.category_id = c.id
        ORDER BY v.category_id ASC, v.position ASC
    ");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // データをカテゴリごとにグループ化
    $categorizedVideos = [];
    foreach ($videos as $video) {
        $category = $video['category'] ?? '未分類'; // カテゴリがNULLの場合は "未分類"
        if (!isset($categorizedVideos[$category])) {
            $categorizedVideos[$category] = [];
        }
        $categorizedVideos[$category][] = $video;
    }

    // JSONでフロントエンドに返す
    header('Content-Type: application/json');
    echo json_encode($categorizedVideos);
} catch (PDOException $e) {
    // エラーハンドリング
    http_response_code(500);
    echo json_encode(['error' => 'データベースエラー: ' . $e->getMessage()]);
}
