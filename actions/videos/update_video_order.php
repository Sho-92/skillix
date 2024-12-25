<?php
require_once '../../includes/db.php'; // データベース接続

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // クライアントから送信されたデータを取得
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['order']) || !is_array($data['order'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => '無効なデータ形式です。']);
        exit;
    }

    $order = $data['order']; // 新しい順序の配列 (例: [3, 1, 4, 2])

    try {
        $pdo->beginTransaction(); // トランザクション開始

        // 各動画の順序を更新
        foreach ($order as $position => $videoId) {
            $stmt = $pdo->prepare("UPDATE videos SET position = :position WHERE id = :id");
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->bindParam(':id', $videoId, PDO::PARAM_INT);
            $stmt->execute();
        }

        $pdo->commit(); // トランザクションを確定

        echo json_encode(['success' => true]); // 成功レスポンス
    } catch (Exception $e) {
        $pdo->rollBack(); // エラー発生時にロールバック
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
