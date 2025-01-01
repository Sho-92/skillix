<?php
require_once '../../includes/db.php';

try {
    // JSONデータを受け取る
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['video_id'])) {
        throw new Exception('動画IDが指定されていません。');
    }

    $videoId = $data['video_id']; // 削除する動画のID

    // 動画が存在するか確認
    $stmt = $pdo->prepare("SELECT id, category_id FROM videos WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $videoId);
    $stmt->execute();
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$video) {
        throw new Exception('指定された動画が見つかりません。');
    }

    // 動画が属していたカテゴリーIDを取得
    $categoryId = $video['category_id'];

    // 動画を削除
    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = :id");
    $stmt->bindParam(':id', $videoId);
    $stmt->execute();

    // カテゴリー内の動画が他にない場合、カテゴリーも削除
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $categoryId);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // 他に動画がない場合、そのカテゴリーを削除
    if ($count == 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $categoryId);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // エラー時にはJSON形式でエラーメッセージを返す
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
