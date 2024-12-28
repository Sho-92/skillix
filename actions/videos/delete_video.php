<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // JSONデータを取得
    $data = json_decode(file_get_contents('php://input'), true);

    // JSONデコードのエラーチェック
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'JSONデコードエラー']);
        exit;
    }    
    // IDが指定されていない場合のエラーハンドリング
    $videoId = $data['id'] ?? null;
    if (!$videoId) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => '動画IDが指定されていません。']);
        exit;
    }

    // データベースから削除
    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = :id");
    $stmt->bindParam(':id', $videoId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // 削除成功時は成功フラグを返す
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'データベースから削除できませんでした。']);
    }
}
?>
