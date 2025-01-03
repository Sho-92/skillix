<?php

require_once '../../includes/db.php'; // データベース接続

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    error_log("受信データ: " . print_r($data, true));

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSONデコードエラー: " . json_last_error_msg());
        echo json_encode(['success' => false, 'error' => '無効なJSONデータ']);
        exit;
    }

    // category_id フィールドの検証
    $categoryId = 0; // カテゴリーIDの初期値
    if (isset($data['category'])) {
        if (is_array($data['category']) && isset($data['category']['id'])) {
            // categoryが配列の場合、idを使う
            $categoryId = (int)$data['category']['id'];
        } elseif (is_numeric($data['category'])) {
            // categoryが数値（category_id）の場合
            $categoryId = (int)$data['category'];
        } elseif (is_string($data['category'])) {
            // categoryが文字列（カテゴリー名）の場合
            $categoryName = (string)$data['category'];

            // カテゴリー名からIDを取得
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name");
            $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
            $stmt->execute();
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                error_log("Category not found: {$categoryName}");
                echo json_encode(['success' => false, 'error' => '指定されたカテゴリーが見つかりません']);
                exit;
            }

            $categoryId = (int)$category['id'];
        } else {
            error_log("categoryフィールドが不正な形式: " . print_r($data['category'], true));
            echo json_encode(['success' => false, 'error' => 'categoryフィールドが不正な形式']);
            exit;
        }
    }

    // orderフィールドの検証
    $order = $data['order'] ?? [];
    if (!is_array($order) || count($order) === 0) {
        error_log("orderフィールドが不正な形式または空: " . print_r($order, true));
        echo json_encode(['success' => false, 'error' => 'orderフィールドが不正な形式または空']);
        exit;
    }

    try {
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
