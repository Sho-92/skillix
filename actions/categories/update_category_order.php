<?php
require_once '../../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // リクエストに "order" が含まれていることを確認
    if (!isset($data['order']) || !is_array($data['order'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => '無効なデータ形式です。']);
        exit;
    }

    $order = $data['order'];

    try {
        $pdo->beginTransaction(); // トランザクション開始

        // カテゴリー順序を更新
        foreach ($order as $item) {
          $categoryId = $item['categoryId']; // フロントエンドから送信されたIDを取得
          $position = $item['position']; // 新しいpositionをそのまま使用

          error_log("Updating Category ID: $categoryId with Position: $position");

          // IDとpositionを更新
          $stmt = $pdo->prepare("UPDATE categories SET position = :position WHERE id = :id");
          $stmt->bindParam(':position', $position, PDO::PARAM_INT);
          $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
          $stmt->execute();

          error_log("Update executed for ID: $categoryId");

          }

          $pdo->commit(); // トランザクションを確定
          echo json_encode(['success' => true]); // 成功レスポンス
    } catch (Exception $e) {
        $pdo->rollBack(); // エラー発生時にロールバック
        error_log("カテゴリー順序更新エラー: " . $e->getMessage()); // ログに記録
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>