<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 受け取ったJSONデータをデコード
        $data = json_decode(file_get_contents('php://input'), true);
        $categoryName = $data['name'] ?? ''; // category_nameキーが存在しない場合のチェック

        // 受け取ったデータをログに出力
        error_log("Received Data: " . file_get_contents('php://input'));

        // カテゴリー名が空でないか、または適切な長さかを確認
        if (empty($categoryName)) {
            throw new Exception('カテゴリー名は必須です。');
        }

        // カテゴリーがすでに存在しないか確認
        error_log("Checking if category exists: " . $categoryName);

        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
        $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
        $stmt->execute();
        $existingCategory = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCategory) {
            throw new Exception('このカテゴリーはすでに存在します。');
        }

        // 現在の最大 position を取得
        $stmt = $pdo->query("SELECT MAX(position) AS max_position FROM categories");
        $maxPosition = (int)$stmt->fetch(PDO::FETCH_ASSOC)['max_position'];

        // 次の position を計算
        $newPosition = $maxPosition + 1;

        // カテゴリーを新しく追加
        $stmt = $pdo->prepare("INSERT INTO categories (name, position) VALUES (:name, :position)");
        $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
        $stmt->bindParam(':position', $newPosition, PDO::PARAM_INT);
        $stmt->execute();

        // 挿入したカテゴリーのIDを取得
        $categoryId = $pdo->lastInsertId();

        // 成功メッセージとカテゴリーIDを返す
        echo json_encode([
            'success' => true,
            'id' => $categoryId,
            'name' => $categoryName,
            'position' => $newPosition, // 新しいポジションを返す
        ]);
    } catch (Exception $e) {
        // エラーハンドリング
        http_response_code(400); // Bad Request
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // POST以外のリクエストが来た場合
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'POSTメソッドでリクエストしてください。']);
    error_log("Error: " . $e->getMessage());
}
?>
