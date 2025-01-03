<?php
require_once '../../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // デバッグ: データをログに出力
    error_log('受信したデータ: ' . print_r($data, true));

    // リクエストに "order" が含まれていることを確認
    if (!isset($data['order']) || !is_array($data['order'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => '無効なデータ形式です。']);
        exit;
    }

    $newCategoryOrder = $data['order'];

    try {
        $pdo->beginTransaction(); // トランザクション開始

    // カテゴリー順序を更新
    foreach ($newCategoryOrder as  $item) {

         // `categoryId` が存在しない場合の対応
         if (!isset($item['categoryId']) || !isset($item['position'])) {
            error_log('カテゴリーIDまたは位置データが不足しています: ' . print_r($item, true));
            continue; // 次のループへ
        }
        
        // categoryId と position を取得
        $categoryId = (int)$item['categoryId']; // カテゴリーID
        $position = (int)$item['position']; // 新しい位置

        error_log("カテゴリーID: $categoryId の位置を更新: $position");  // IDとpositionを更新

        $stmt = $pdo->prepare("UPDATE categories SET position = :position WHERE id = :id");
        $stmt->bindParam(':position', $position, PDO::PARAM_INT);
        $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        // 更新された行数を確認
        $updatedRows = $stmt->rowCount();
        error_log("Rows updated: $updatedRows"); // 更新された行数をログに記録

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