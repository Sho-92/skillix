<?php
require_once '../../includes/db.php';

try {
    // JSONデータを受け取る
    $data = json_decode(file_get_contents('php://input'), true);
    $videoId = $data['video_id'];
    $title = $data['title'];
    $url = $data['url'];
    $categoryId = (int)$data['category_id']; // category_idを整数として受け取る
    
    // 動画情報を更新
    $stmt = $pdo->prepare("UPDATE videos SET title = :title, url = :url, category_id = :category_id WHERE id = :id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindParam(':id', $videoId);

    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
