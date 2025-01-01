<?php

require_once '../../includes/db.php'; // データベース接続

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // 必要なパラメータが存在するか確認
    if (!isset($data['order']) || !isset($data['category'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'error' => '無効なデータ形式です。']);
        exit;
    }

    $order = $data['order'];
    $categoryName = $data['category']; // 文字列として取得

    try {
        // カテゴリーIDを取得
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name");
        $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            error_log("Category not found: {$categoryName}");
            throw new Exception("指定されたカテゴリーが見つかりません: {$categoryName}");
        }

        $categoryId = (int)$category['id'];
        // error_log("Resolved categoryId: " . $categoryId);

        $pdo->beginTransaction(); // トランザクション開始

        $position = 1; // 順序の開始番号
        // 該当カテゴリーの動画の順序を更新
        foreach ($order as $videoId) {
            $stmt = $pdo->prepare("UPDATE videos SET position = :position WHERE id = :id AND category_id = :category_id");
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->bindParam(':id', $videoId, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                error_log("Failed to update video ID {$videoId} in category {$categoryId} to position {$position}");
                throw new Exception("更新に失敗しました: videoId={$videoId}, categoryId={$categoryId}");
            }

            $position++;
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