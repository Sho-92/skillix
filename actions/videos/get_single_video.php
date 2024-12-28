<?php
require_once '../../includes/db.php';

try {
    // 動画IDがリクエストに含まれているかチェック
    if (isset($_GET['id'])) {
        $videoId = $_GET['id'];

        // 特定の動画を取得するSQL
        $stmt = $pdo->prepare("SELECT id, title, url, category FROM videos WHERE id = :id");
        $stmt->bindParam(':id', $videoId, PDO::PARAM_INT);
        $stmt->execute();

        $video = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($video) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($video, JSON_UNESCAPED_UNICODE);
        } else {
            // 動画が見つからない場合
            http_response_code(404); // Not Found
            echo json_encode(['error' => '動画が見つかりません。']);
        }
    } else {
        // 動画IDが渡されていない場合
        http_response_code(400); // Bad Request
        echo json_encode(['error' => '動画IDが指定されていません。']);
    }
} catch (Exception $e) {
    // エラーハンドリング
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
}
?>