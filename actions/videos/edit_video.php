<?php
require_once '../../includes/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $videoId = $data['video_id'];
    $title = $data['title'];
    $url = $data['url'];
    $category = $data['category'];

    // データを更新
    $stmt = $pdo->prepare("UPDATE videos SET title = :title, url = :url, category = :category WHERE id = :id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':id', $videoId);

    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>