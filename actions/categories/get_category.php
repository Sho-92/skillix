<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // カテゴリー一覧を取得
        $stmt = $pdo->prepare("SELECT DISTINCT category FROM videos WHERE category IS NOT NULL ORDER BY category ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($categories); // JSON形式で返す
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'エラー: ' . $e->getMessage()]);
    }
}
?>
