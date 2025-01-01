<?php
require_once '../../includes/db.php';

try {
    // 動画一覧をカテゴリーごとにposition順で取得
    $stmt = $pdo->prepare("
    SELECT v.id, v.title, v.url, COALESCE(c.name, '未分類') AS category, v.category_id, v.position
    FROM videos v
    LEFT JOIN categories c ON v.category_id = c.id
    ORDER BY v.category_id, v.position
    ");
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 動画をカテゴリーごとにグループ化
    $groupedVideos = [];
    foreach ($videos as $video) {
        $categoryId = $video['category_id'];
        // カテゴリーがまだグループに追加されていなければ初期化
        if (!isset($groupedVideos[$categoryId])) {
            $groupedVideos[$categoryId] = [
                'category' => $video['category'], // カテゴリー名
                'videos' => []
            ];
        }
        // 動画を対応するカテゴリーのリストに追加
        $groupedVideos[$categoryId]['videos'][] = [
            'id' => $video['id'],
            'title' => $video['title'],
            'url' => $video['url'],
            'position' => $video['position'],
        ];
    }

    // カテゴリー順に並べ替え
    usort($groupedVideos, function($a, $b) {
        return $a['category'] <=> $b['category']; // カテゴリー名でアルファベット順に並べ替え
    });

    // カテゴリー内の動画リストもposition順に並べ替え
    foreach ($groupedVideos as &$category) {
        usort($category['videos'], function($a, $b) {
            return $a['position'] <=> $b['position']; // 動画のposition順に並べ替え
        });
    }

    // デバッグ: 取得したデータをログに出力
    error_log("Grouped and Sorted Videos: " . print_r($groupedVideos, true));

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($groupedVideos, JSON_UNESCAPED_UNICODE); // 日本語をそのまま返す

} catch (Exception $e) {
    // エラーハンドリング
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
}
